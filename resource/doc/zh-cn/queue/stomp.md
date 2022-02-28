# Stomp组件

> **注意**
> 此部分内容移动至 [stomp队列插件](https://www.workerman.net/plugin/13)

## 说明

  Stomp是简单(流)文本定向消息协议，它提供了一个可互操作的连接格式，允许STOMP客户端与任意STOMP消息代理（Broker）进行交互。[workerman/stomp](https://github.com/walkor/stomp)实现了Stomp客户端，主要用于 RabbitMQ、Apollo、ActiveMQ 等消息队列场景。
 
  
## 项目地址

  https://github.com/webman-php/stomp
  
## 安装
 
  ```php
  composer require webman/stomp
  ```
  
## 配置

新建配置文件 `config/stomp.php` 内容类似如下：
  
```php
<?php
return [
    'default' => [
        'host'    => 'stomp://127.0.0.1:61613',
        'options' => [
            'vhost'    => '/',
            'login'    => 'guest',
            'passcode' => 'guest',
            'debug'    => true,
        ]
    ]
];
```

## 投递消息

```php
use Webman\Stomp\Client;
// 队列
$queue = 'examples';
// 数据（传递数组时需要自行序列化，比如使用json_encode，serialize等）
$data = json_encode(['to' => 'tom@gmail.com', 'content' => 'hello']);
// 执行投递
Client::send($queue, $data);
```

> 为了兼容其它项目，Stomp组件没有提供自动序列化反序列化功能，如果投递的是数组数据，需要自行序列化，消费的时候自行反序列化
  
## 消费消息

打开`config/process.php`添加以下配置，增加消费进程

```php
<?php
return [
    ...这里省略了其它配置...
    
    'stomp_consumer'  => [
        'handler'     => Webman\Stomp\Process\Consumer::class,
        'count'       => 2, // 进程数
        'constructor' => [
            // 消费者类目录
            'consumer_dir' => app_path() . '/queue/stomp'
        ]
    ]
];
```

新建 `app/queue/stomp/MyMailSend.php` (类名任意)。
```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // 队列名
    public $queue = 'examples';

    // 连接名，对应 config/stomp.php 里的连接`
    public $connection = 'default';

    // 值为 client 时需要调用$ack_resolver->ack()告诉服务端已经成功消费
    // 值为 auto   时无需调用$ack_resolver->ack()
    public $ack = 'auto';

    // 消费
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // 如果是数据是数组，需要自行反序列化
        var_export(json_decode($data, true)); // 输出 ['to' => 'tom@gmail.com', 'content' => 'hello']
        // 告诉服务端，已经成功消费
        $ack_resolver->ack(); // ack为 auto时可以省略此调用
    }
}
```

# Stomp Pro 组件

在 **Stomp** 组件基础上增加了延迟队列支持，使用官方插件 **rabbitmq_delayed_message_exchange**

## 原理说明

因为 **Stomp** 协议无法创建交换机，使用交换机模式创建的队列默认是随机 **auto-delete=ture** 类型非持久化队列。

### 创建自定义交换机和队列

使用 **php-amqplib/php-amqplib 通过 **AMQP** 协议，来创建自定义交换机和队列。

> 注意因为交换机和队列都是持久化创建的，如果同名要改动参数则会报错，解决办法 1、删除交换机和队列，2、改变命名空间名称

#### 交换机命名规范

{namespace}.{connection_name}.{exchange_name}

#### 队列命名规范

**routing_key** 等于队列名

{namespace}.{connection_name}.{queue_name}

### 使用 /amq/queue/[queuename] 模式客户端订阅

> 注意 queuename 里面不能包含 / 符号

这时候队列不由stomp自动进行创建，队列需要自定义且队列不存在失败，这种情况下无论是发送者还是接收者都不会产生队列。 但如果该队列不存在，接收者会报错。

### 使用 /exchange/[exchangename]/[routing_key] 模式客户端发送消息

> 注意 exchangename 里面不能包含 / 符号

通过发布消息，交换机需要手动创建，使用我们前面自己定义的交换机来发送消息。

## 项目地址

https://github.com/teamones-open/stomp-queue

## 安装

  ```php
  composer require teamones/stomp-queue
  ```
## 配置

配置在 Stomp 队列基础组件上面增加了amqp相关配置。

新建配置文件 `config/stomp.php` 内容类似如下：

```php
<?php

return [
    'default' => [
        'host' => 'stomp://127.0.0.1:61613',
        'options' => [
            'vhost'    => '/',
            'login'    => 'guest',
            'passcode' =>  'guest',
            'debug'    => false,
        ],
        'amqp' => [
            'host'           => '127.0.0.1',
            'port'           => 5672,
            'namespace'      => '',
            'exchange_name'  => 'exchange',
            'exchange_delay' => true
        ]
    ]
];
```

## 使用

创建消费者，进程等配置与Stomp 队列基础组件完全一致。

投递消息改动了第三个参数

```php
// $queue 队列名
// $data  发送的消息数据，一样需要自行序列化
// $delay 延迟时间单位是秒
// $headers 更多的参数
Client::send(string $queue, string $data, int $delay, array $headers=[]);
```

```php
use Webman\Stomp\Client;
// 队列
$queue = 'examples';
// 数据（传递数组时需要自行序列化，比如使用json_encode，serialize等）
$data = json_encode(['to' => 'tom@gmail.com', 'content' => 'hello']);
// 执行投递
Client::send($queue, $data, 10);
```

