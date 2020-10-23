# Stomp组件

## 说明

  Stomp是简单(流)文本定向消息协议，它提供了一个可互操作的连接格式，允许STOMP客户端与任意STOMP消息代理（Broker）进行交互。[workerman/stomp](https://github.com/walkor/stomp)实现了Stomp客户端，主要用于 RabbitMQ、Apollo、ActiveMQ 等消息队列场景。
 
  
## 项目地址

  https://github.com/walkor/stomp
  
## 安装
 
  ```php
  composer require workerman/stomp
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
            'debug'    => false,
        ]
    ]
];
```

## 投递消息

```php
use support\bootstrap\Stomp;

$queue = '/topic/send_mail';
Stomp::send($queue, json_encode(['to' => 'tom@gmail.com', 'content' => 'hello']));
```
  
## 消费消息

打开`config/process.php`添加以下配置，增加消费进程

```php
<?php
return [
    ...这里省略了其它配置...
    
    'stomp_consumer'  => [
        'handler'     => process\StompConsumer::class,
        'count'       => 1, // 进程数
        'constructor' => [
            // 消费者类目录
            'consumer_dir' => app_path() . '/stomp'
        ]
    ]
];
```

新建 `app/stomp/MyMailSend.php` (类名任意)。
```php
<?php

namespace app\stomp;

use Workerman\Stomp\AckResolver;

class MyMailSend
{
    // 队列名
    public $queue = '/topic/send_mail';

    // 连接名，对应 config/stomp.php 里的连接`
    public $connection = 'default';

    // 值为 client 时需要调用$ack_resolver->ack()告诉服务端已经成功消费
    // 值为 auto 时无需调用$ack_resolver->ack()
    public $ack = 'auto';

    // 消费
    public function consume($data, AckResolver $ack_resolver)
    {
        var_export(json_decode($data, true));
        $ack_resolver->ack(); // ack为 auto时可以省略此调用
    }
}
```
  

