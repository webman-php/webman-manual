# Redis消息队列组件 redis-queue

## 说明

基于Redis的消息队列，支持消息延迟处理。
  
## 项目地址

https://github.com/webman-php/redis-queue
  
## 安装
 
```php
composer require webman/redis-queue
```
  
## 配置

新建配置文件 `config/redis_queue.php` 内容类似如下：
  
```php
<?php
return [
    'default' => [
        'host' => 'redis://127.0.0.1:6379',
        'options' => [
            'auth'     => '',     // 密码，可选参数
            'db' => 0,      // 数据库
            'max_attempts'  => 5, // 消费失败后，重试次数
            'retry_seconds' => 5, // 重试间隔，单位秒
         ]
    ],
];
```

> 如果消费失败(发生了异常)，则消息会放入延迟队列，等待下次重试。重试次数通过参数 `max_attempts` 控制，重试间隔由
`retry_seconds` 和 `max_attempts`共同控制。比如`max_attempts`为5，`retry_seconds`为10，第1次重试间隔为`1*10`秒，第2次重试时间间隔为 `2*10秒`，第3次重试时间间隔为`3*10秒`，以此类推直到重试5次。如果超过了`max_attempts`设置测重试次数，则消息放入失败队列(key为`redis-queue-failed`)。

## 投递消息

```php
use Webman\RedisQueue\Client;
// 队列名
$queue = '/topic/send_mail';
// 数据，可以直接传数组，无需序列化
$data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
// 投递消息
Client::send($queue, $data);
// 投递延迟消息，消息会在60秒后处理
Client::send($queue, $data, 60);
```

有时候你需要在其它项目中投递消息并且无法使用`Webman\RedisQueue`，则可以参考以下函数向队列投递消息。

```php
function redis_queue_send($redis, $queue, $data, $delay = 0) {
    $queue_waiting = 'redis-queue-waiting';
    $queue_delay = 'redis-queue-delayed';
    $now = time();
    $package_str = json_encode([
        'id'       => rand(),
        'time'     => $now,
        'delay'    => 0,
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
  
## 消费消息

打开`config/process.php`添加以下配置，增加消费进程。

```php
<?php
return [
    ...这里省略了其它配置...
    
    'redis_consumer'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8, // 可以设置多进程
        'constructor' => [
            // 消费者类目录
            'consumer_dir' => app_path() . '/queue/redis'
        ]
    ]
];
```

消费进程可以同时消费多种不同的队列，也就是消费进程在`config/process.php`配置一次即可，新增队列消费者时只需要在`app/queue/redis`下新增对应的`Consumer`类即可，并用类属性`$queue`指定要消费的队列名，类似如下：

新建 `app/queue/redis/MyMailSend.php` (类名任意)。
```php
<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class MailSend implements Consumer
{
    // 要消费的队列名
    public $queue = '/topic/send_mail';

    // 连接名，对应 config/redis_queue.php 里的连接`
    public $connection = 'default';

    // 消费
    public function consume($data)
    {
        // 无需反序列化
        var_export($data); // 输出 ['to' => 'tom@gmail.com', 'content' => 'hello']
    }
}
```

  

