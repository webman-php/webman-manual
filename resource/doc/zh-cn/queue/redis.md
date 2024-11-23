## Redis队列

基于Redis的消息队列，支持消息延迟处理。


## 安装
`composer require webman/redis-queue`

## 配置文件
redis配置文件自动生成在 `{主项目}/config/plugin/webman/redis-queue/redis.php`，内容类似如下：
```php
<?php
return [
    'default' => [
        'host' => 'redis://127.0.0.1:6379',
        'options' => [
            'auth' => '',         // 密码，可选参数
            'db' => 0,            // 数据库
            'max_attempts'  => 5, // 消费失败后，重试次数
            'retry_seconds' => 5, // 重试间隔，单位秒
        ]
    ],
];
```

### 消费失败重试
如果消费失败(发生了异常)，则消息会放入延迟队列，等待下次重试。重试次数通过参数 `max_attempts` 控制，重试间隔由
`retry_seconds` 和 `max_attempts`共同控制。比如`max_attempts`为5，`retry_seconds`为10，第1次重试间隔为`1*10`秒，第2次重试时间间隔为 `2*10秒`，第3次重试时间间隔为`3*10秒`，以此类推直到重试5次。如果超过了`max_attempts`设置测重试次数，则消息放入key为`{redis-queue}-failed`的失败队列。

## 投递消息(同步)
> **注意**
> 需要webman/redis >= 1.2.0，依赖 redis扩展

```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Redis;

class Index
{
    public function queue(Request $request)
    {
        // 队列名
        $queue = 'send-mail';
        // 数据，可以直接传数组，无需序列化
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // 投递消息
        Redis::send($queue, $data);
        // 投递延迟消息，消息会在60秒后处理
        Redis::send($queue, $data, 60);

        return response('redis queue test');
    }

}
```
投递成功`Redis::send()` 返回true，否则返回false或者抛出异常。

> **提示**
> 延迟队列消费时间可能会出现误差，例如消费速度小于生产速度导致队列积压，进而导致消费延迟，缓解办法是多开一些消费进程。

## 投递消息(异步)
```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Client;

class Index
{
    public function queue(Request $request)
    {
        // 队列名
        $queue = 'send-mail';
        // 数据，可以直接传数组，无需序列化
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // 投递消息
        Client::send($queue, $data);
        // 投递延迟消息，消息会在60秒后处理
        Client::send($queue, $data, 60);

        return response('redis queue test');
    }

}
```
`Client::send()` 没有返回值，它属于异步推送，它不保证消息%100送达redis。

> **提示**
> `Client::send()`原理是在本地内存建立一个内存队列，异步将消息同步到redis(同步速度很快，每秒大概1万笔消息)。如果进程重启，恰好本地内存队列里数据没有同步完毕，会造成消息丢失。`Client::send()`异步投递适合投递不重要的消息。

> **提示**
> `Client::send()`是异步的，它只能在workerman的运行环境中使用，命令行脚本请使用同步接口`Redis::send()`


## 在其他项目投递消息
有时候你需要在其它项目中投递消息并且无法使用`webman\redis-queue`，则可以参考以下函数向队列投递消息。

```php
function redis_queue_send($redis, $queue, $data, $delay = 0) {
    $queue_waiting = '{redis-queue}-waiting';
    $queue_delay = '{redis-queue}-delayed';
    $now = time();
    $package_str = json_encode([
        'id'       => rand(),
        'time'     => $now,
        'delay'    => $delay,
        'attempts' => 0,
        'queue'    => $queue,
        'data'     => $data
    ]);
    if ($delay) {
        return $redis->zAdd($queue_delay, $now + $delay, $package_str);
    }
    return $redis->lPush($queue_waiting.$queue, $package_str);
}
```

其中，参数`$redis`为redis实例。例如redis扩展用法类似如下：
```php
$redis = new Redis;
$redis->connect('127.0.0.1', 6379);
$queue = 'user-1';
$data= ['some', 'data'];
redis_queue_send($redis, $queue, $data);
````

## 消费
消费进程配置文件在 `{主项目}/config/plugin/webman/redis-queue/process.php`。
消费者目录在 `{主项目}/app/queue/redis/` 下。

执行命令`php webman redis-queue:consumer my-send-mail`则会生成文件`{主项目}/app/queue/redis/MyMailSend.php`

> **提示**
> 如果命令不存在也可以手动生成

```php
<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class MyMailSend implements Consumer
{
    // 要消费的队列名
    public $queue = 'send-mail';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';

    // 消费
    public function consume($data)
    {
        // 无需反序列化
        var_export($data); // 输出 ['to' => 'tom@gmail.com', 'content' => 'hello']
    }
    // 消费失败回调
    /* 
    $package = [
        'id' => 1357277951, // 消息ID
        'time' => 1709170510, // 消息时间
        'delay' => 0, // 延迟时间
        'attempts' => 2, // 消费次数
        'queue' => 'send-mail', // 队列名
        'data' => ['to' => 'tom@gmail.com', 'content' => 'hello'], // 消息内容
        'max_attempts' => 5, // 最大重试次数
        'error' => '错误信息' // 错误信息
    ]
    */
    public function onConsumeFailure(\Throwable $e, $package)
    {
        echo "consume failure\n";
        echo $e->getMessage() . "\n";
        // 无需反序列化
        var_export($package); 
    }
}
```

> **注意**
> 消费过程中没有抛出异常和Error视为消费成功，否则消费失败，进入重试队列。
> redis-queue没有ack机制，你可以把它看作是自动ack(没有产生异常或Error)。如果消费过程中想标记当前消息消费不成功，可以手动抛出异常，让当前消息进入重试队列。这实际上和ack机制没有区别。

> **提示**
> 消费者支持多服务器多进程，并且同一条消息**不会**被重复消费。消费过的消息会自动从队列删除，无需手动删除。

> **提示**
> 消费进程可以同时消费多种不同的队列，新增队列不需要修改`process.php`中的配置，新增队列消费者时只需要在`app/queue/redis`下新增对应的`Consumer`类即可，并用类属性`$queue`指定要消费的队列名

> **提示**
> windows用户需要执行php windows.php 启动webman，否则不会启动消费进程

> **提示**
> onConsumeFailure回调会在每次消费失败时触发，你可以在这里处理失败后的逻辑。

## 为不同的队列设置不同的消费进程
默认情况下，所有的消费者共用相同的消费进程。但有时我们需要将一些队列的消费独立出来，例如消费慢的业务放到一组进程中消费，消费快的业务放到另外一组进程消费。为此我们可以将消费者分为两个目录，例如 `app_path() . '/queue/redis/fast'` 和 `app_path() . '/queue/redis/slow'` （注意消费类的命名空间需要做相应的更改），则配置如下：
```php
return [
    ...这里省略了其它配置...
    
    'redis_consumer_fast'  => [ // key是自定义的，没有格式限制，这里取名redis_consumer_fast
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // 消费者类目录
            'consumer_dir' => app_path() . '/queue/redis/fast'
        ]
    ],
    'redis_consumer_slow'  => [  // key是自定义的，没有格式限制，这里取名redis_consumer_slow
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // 消费者类目录
            'consumer_dir' => app_path() . '/queue/redis/slow'
        ]
    ]
];
```

这样快业务消费者放到`queue/redis/fast`目录下，慢业务消费者放到`queue/redis/slow`目录下达到给队列指定消费进程的目的。

## 多redis配置
#### 配置
`config/plugin/webman/redis-queue/redis.php`
```php
<?php
return [
    'default' => [
        'host' => 'redis://192.168.0.1:6379',
        'options' => [
            'auth' => null,       // 密码，字符串类型，可选参数
            'db' => 0,            // 数据库
            'max_attempts'  => 5, // 消费失败后，重试次数
            'retry_seconds' => 5, // 重试间隔，单位秒
        ]
    ],
    'other' => [
        'host' => 'redis://192.168.0.2:6379',
        'options' => [
            'auth' => null,       // 密码，字符串类型，可选参数
            'db' => 0,             // 数据库
            'max_attempts'  => 5, // 消费失败后，重试次数
            'retry_seconds' => 5, // 重试间隔，单位秒
        ]
    ],
];
```

注意配置里增加了一个`other`为key的redis配置

####  多redis投递消息

```php
// 向 `default` 为key的队列投递消息
Client::connection('default')->send($queue, $data);
Redis::connection('default')->send($queue, $data);
//  等同于
Client::send($queue, $data);
Redis::send($queue, $data);

// 向 `other` 为key的队列投递消息
Client::connection('other')->send($queue, $data);
Redis::connection('other')->send($queue, $data);
```

#### 多redis消费
消费配置里`other` 为key的队列投递消息
```php
namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class SendMail implements Consumer
{
    // 要消费的队列名
    public $queue = 'send-mail';

    // === 这里设置为other，代表消费配置里other为key的队列 ===
    public $connection = 'other';

    // 消费
    public function consume($data)
    {
        // 无需反序列化
        var_export($data);
    }
}
```

## 常见问题

**为什么会有报错 `Workerman\Redis\Exception: Workerman Redis Wait Timeout (600 seconds)`**

这个错误只会存在于异步投递接口`Client::send()`中。异步投递首先会将消息保存在本地内存中，当进程空闲时将消息发送给redis。如果redis接收速度慢于消息生产速度，或者进程一直忙于其它业务没有足够的时间将内存的消息同步给redis，就会导致消息挤压。如果有消息挤压超过600秒，就会触发此错误。

解决方案：投递消息使用同步投递接口`Redis::send()`。
