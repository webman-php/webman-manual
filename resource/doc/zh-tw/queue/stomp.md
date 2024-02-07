## Stomp隊列

Stomp是簡單(流)文字定向消息協定，它提供了一個可互操作的連接格式，允許STOMP客戶端與任意STOMP消息代理（Broker）進行交互。[workerman/stomp](https://github.com/walkor/stomp)實現了Stomp客戶端，主要用於 RabbitMQ、Apollo、ActiveMQ 等消息隊列場景。

## 安裝
`composer require webman/stomp`

## 配置
配置文件在 `config/plugin/webman/stomp` 下

## 投遞消息
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Stomp\Client;

class Index
{
    public function queue(Request $request)
    {
        // 隊列
        $queue = 'examples';
        // 數據（傳遞數組時需要自行序列化，比如使用json_encode，serialize等）
        $data = json_encode(['to' => 'tom@gmail.com', 'content' => 'hello']);
        // 執行投遞
        Client::send($queue, $data);

        return response('redis queue test');
    }

}
```
> 為了兼容其它項目，Stomp組件沒有提供自動序列化反序列化功能，如果投遞的是數組數據，需要自行序列化，消費的時候自行反序列化

## 消費消息
新建 `app/queue/stomp/MyMailSend.php` (類名任意，符合psr4規範即可)。
```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // 隊列名
    public $queue = 'examples';

    // 連接名，對應 stomp.php 裡的連接
    public $connection = 'default';

    // 值為 client 時需要調用$ack_resolver->ack()告訴服務端已經成功消費
    // 值為 auto   時無需調用$ack_resolver->ack()
    public $ack = 'auto';

    // 消費
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // 如果是數據是數組，需要自行反序列化
        var_export(json_decode($data, true)); // 輸出 ['to' => 'tom@gmail.com', 'content' => 'hello']
        // 告訴服務端，已經成功消費
        $ack_resolver->ack(); // ack為 auto時可以省略此調用
    }
}
```

# rabbitmq開啟stomp協定
rabbitmq默認沒有開啟stomp協定，需要執行以下命令開啟
```
rabbitmq-plugins enable rabbitmq_stomp
```開啟後stomp的端口默認為61613。
