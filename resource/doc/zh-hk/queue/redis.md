## Redis隊列

基於Redis的消息隊列，支援消息延遲處理。

## 安裝
`composer require webman/redis-queue`

## 配置檔案
Redis配置檔會自動生成在 `config/plugin/webman/redis-queue/redis.php`，內容類似如下：
```php
<?php
return [
    'default' => [
        'host' => 'redis://127.0.0.1:6379',
        'options' => [
            'auth' => '',         // 密碼，可選參數
            'db' => 0,            // 數據庫
            'max_attempts'  => 5, // 消費失敗後，重試次數
            'retry_seconds' => 5, // 重試間隔，單位秒
        ]
    ],
];
```

### 消費失敗重試
如果消費失敗(發生了異常)，則消息會放入延遲隊列，等待下次重試。重試次數通過參數 `max_attempts` 控制，重試間隔由
`retry_seconds` 和 `max_attempts`共同控制。例如`max_attempts`為5，`retry_seconds`為10，第1次重試間隔為`1*10`秒，第2次重試時間間隔為 `2*10秒`，第3次重試時間間隔為`3*10秒`，以此類推直到重試5次。如果超過了`max_attempts`設置測重試次數，則消息放入key為`{redis-queue}-failed`的失敗隊列。

## 傳遞消息(同步)
> **注意**
> 需要webman/redis >= 1.2.0，依賴 redis擴展

```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Redis;

class Index
{
    public function queue(Request $request)
    {
        // 隊列名
        $queue = 'send-mail';
        // 數據，可以直接傳數組，無需序列化
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // 傳遞消息
        Redis::send($queue, $data);
        // 傳遞延遲消息，消息會在60秒後處理
        Redis::send($queue, $data, 60);

        return response('redis queue test');
    }

}
```
傳遞成功`Redis::send()` 返回true，否則返回false或者拋出異常。

> **提示**
> 延遲隊列消費時間可能會出現誤差，例如消費速度小於生產速度導致隊列積壓，進而導致消費延遲，緩解辦法是多開一些消費進程。

## 傳遞消息(異步)
```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Client;

class Index
{
    public function queue(Request $request)
    {
        // 隊列名
        $queue = 'send-mail';
        // 數據，可以直接傳數組，無需序列化
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // 傳遞消息
        Client::send($queue, $data);
        // 傳遞延遲消息，消息會在60秒後處理
        Client::send($queue, $data, 60);

        return response('redis queue test');
    }

}
```
`Client::send()` 沒有返回值，它屬於異步推送，它不保證消息%100送達redis。

> **提示**
> `Client::send()`原理是在本地內存建立一個內存隊列，異步將消息同步到redis(同步速度很快，每秒大概1萬筆消息)。如果進程重啟，恰好本地內存隊列裡數據沒有同步完畢，會造成消息遺失。`Client::send()`異步傳遞適合傳遞不重要的消息。

> **提示**
> `Client::send()`是異步的，它只能在workerman的運行環境中使用，命令行腳本請使用同步接口`Redis::send()`


## 在其他项目传递消息
有時候你需要在其他项目中傳遞消息並且無法使用`webman\redis-queue`，則可以參考以下函數向隊列傳遞消息。

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

其中，參數`$redis`為redis實例。例如redis擴展用法類似如下：
```php
$redis = new Redis;
$redis->connect('127.0.0.1', 6379);
$queue = 'user-1';
$data= ['some', 'data'];
redis_queue_send($redis, $queue, $data);
```
## 消費
消費進程配置文件在 `config/plugin/webman/redis-queue/process.php`。
消費者目錄在 `app/queue/redis/` 下。

執行命令`php webman redis-queue:consumer my-send-mail`則會生成文件`app/queue/redis/MyMailSend.php`

> **提示**
> 如果命令不存在也可以手動生成

```php
<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class MyMailSend implements Consumer
{
    // 要消費的隊列名
    public $queue = 'send-mail';

    // 連接名，對應 plugin/webman/redis-queue/redis.php 裡的連接
    public $connection = 'default';

    // 消費
    public function consume($data)
    {
        // 無需反序列化
        var_export($data); // 輸出 ['to' => 'tom@gmail.com', 'content' => 'hello']
    }
}
```

> **注意**
> 消費過程中沒有拋出異常和Error視為消費成功，否則消費失敗，進入重試隊列。
> redis-queue沒有ack機制，你可以把它看作是自動ack(沒有產生異常或Error)。如果消費過程中想標記當前消息消費不成功，可以手動拋出異常，讓當前消息進入重試隊列。這實際上和ack機制沒有區別。

> **提示**
> 消費者支持多服務器多進程，並且同一條消息**不會**被重復消費。消費過的消息會自動從隊列刪除，無需手動刪除。

> **提示**
> 消費進程可以同時消費多種不同的隊列，新增隊列不需要修改`process.php`中的配置，新增隊列消費者時只需要在`app/queue/redis`下新增對應的`Consumer`類即可，並用類屬性`$queue`指定要消費的隊列名

> **提示**
> windows用戶需要執行php windows.php 啟動webman，否則不會啟動消費進程

## 為不同的隊列設置不同的消費進程
默認情況下，所有的消費者共用相同的消費進程。但有時我們需要將一些隊列的消費獨立出來，例如消費慢的業務放到一組進程中消費，消費快的業務放到另外一組進程消費。爲此我們可以將消費者分爲兩個目錄，例如 `app_path() . '/queue/redis/fast'` 和 `app_path() . '/queue/redis/slow'` （注意消費類的命名空間需要做相應的更改），則配置如下：
```php
return [
    ...這裡省略了其他配置...
    
    'redis_consumer_fast'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // 消費者類目錄
            'consumer_dir' => app_path() . '/queue/redis/fast'
        ]
    ],
    'redis_consumer_slow'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // 消費者類目錄
            'consumer_dir' => app_path() . '/queue/redis/slow'
        ]
    ]
];
```
通過目錄分類以及相應的配置，我們就可以輕鬆的爲不同的消費者設置不同的消費進程。

## 多redis配置
#### 配置
`config/plugin/webman/redis-queue/redis.php`
```php
<?php
return [
    'default' => [
        'host' => 'redis://192.168.0.1:6379',
        'options' => [
            'auth' => null,       // 密碼，字符串類型，可選參數
            'db' => 0,            // 數據庫
            'max_attempts'  => 5, // 消費失敗後，重試次數
            'retry_seconds' => 5, // 重試間隔，單位秒
        ]
    ],
    'other' => [
        'host' => 'redis://192.168.0.2:6379',
        'options' => [
            'auth' => null,       // 密碼，字符串類型，可選參數
            'db' => 0,             // 數據庫
            'max_attempts'  => 5, // 消費失敗後，重試次數
            'retry_seconds' => 5, // 重試間隔，單位秒
        ]
    ],
];
```
注意配置裡增加了一個`other`爲key的redis配置

####  多redis投遞消息
```php
// 向 `default` 爲key的隊列投遞消息
Client::connection('default')->send($queue, $data);
Redis::connection('default')->send($queue, $data);
//  等同於
Client::send($queue, $data);
Redis::send($queue, $data);

// 向 `other` 爲key的隊列投遞消息
Client::connection('other')->send($queue, $data);
Redis::connection('other')->send($queue, $data);
```

#### 多redis消費
消費配置裡`other` 爲key的隊列投遞消息
```php
namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class SendMail implements Consumer
{
    // 要消費的隊列名
    public $queue = 'send-mail';

    // === 這裡設置爲other，代表消費配置裡other為key的隊列 ===
    public $connection = 'other';

    // 消費
    public function consume($data)
    {
        // 無需反序列化
        var_export($data);
    }
}
```

## 常見問題
**為什麽會有報錯 `Workerman\Redis\Exception: Workerman Redis Wait Timeout (600 seconds)`**

這個錯誤只會存在於異步投遞接口`Client::send()`中。異步投遞首先會將消息保存在本地內存中，當進程空閒時將消息發送給redis。如果redis接收速度慢於消息生產速度，或者進程一直忙於其他業務沒有足夠的時間將內存的消息同步給redis，就會導致消息擠壓。如果有消息擠壓超過600秒，就會觸發此錯誤。

解決方案：投遞消息使用同步投遞接口`Redis::send()`。
