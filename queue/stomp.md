# Stomp组件

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
$queue = '/topic/send_mail';
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

class MailSend implements Consumer
{
    // 队列名
    public $queue = '/topic/send_mail';

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
  

