## Redisキュー

Redisをベースとしたメッセージキューであり、メッセージの遅延処理をサポートしています。

## インストール
`composer require webman/redis-queue`

## 設定ファイル
redis設定ファイルは `config/plugin/webman/redis-queue/redis.php` に自動生成され、以下のような内容となります：
```php
<?php
return [
    'default' => [
        'host' => 'redis://127.0.0.1:6379',
        'options' => [
            'auth' => '',         // パスワード、オプション
            'db' => 0,            // データベース
            'max_attempts'  => 5, // 消費に失敗した場合のリトライ回数
            'retry_seconds' => 5, // リトライ間隔、単位は秒
        ]
    ],
];
```

### 失敗時のリトライ
消費に失敗した場合（例外が発生した場合）、メッセージは遅延キューに入れられ、次回のリトライを待ちます。リトライ回数は `max_attempts` パラメータで制御され、リトライ間隔は `retry_seconds` および `max_attempts` で共同制御されます。たとえば、`max_attempts` が5で `retry_seconds` が10の場合、最初のリトライは `1*10` 秒後に行われ、2回目のリトライは `2*10` 秒後に行われ、3回目のリトライは `3*10` 秒後に行われ、以降、最大5回のリトライまで続きます。設定された `max_attempts` 回のリトライ回数を超えた場合、メッセージは`{redis-queue}-failed` という名前の失敗キューに入れられます。

## メッセージを送信（同期）
> **注意**
> webman/redis >= 1.2.0 が必要であり、redis拡張に依存しています。

```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Redis;

class Index
{
    public function queue(Request $request)
    {
        // キュー名
        $queue = 'send-mail';
        // データ、配列を直接渡すことができ、シリアライズは不要です
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // メッセージの送信
        Redis::send($queue, $data);
        // 遅延メッセージの送信、メッセージは60秒後に処理されます
        Redis::send($queue, $data, 60);

        return response('redis queue test');
    }

}
```
`Redis::send()` が成功した場合はtrueを返し、それ以外の場合はfalseを返すか、例外が発生します。

> **注意**
> 遅延キューの消費時間には誤差がある可能性があります。たとえば、消費速度が生産速度よりも遅い場合、キューが溜まり、遅延が生じる可能性があります。これを緩和するためには、消費プロセスを追加することができます。

## メッセージを送信（非同期）
```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Client;

class Index
{
    public function queue(Request $request)
    {
        // キュー名
        $queue = 'send-mail';
        // データ、配列を直接渡すことができ、シリアライズは不要です
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // メッセージの送信
        Client::send($queue, $data);
        // 遅延メッセージの送信、メッセージは60秒後に処理されます
        Client::send($queue, $data, 60);

        return response('redis queue test');
    }

}
```
`Client::send()` には戻り値がなく、非同期送信であり、メッセージの100%の到達を保証しません。

> **注意**
> `Client::send()` の原理は、ローカルメモリにキューを作成し、メッセージを非同期でredisに同期するものです（同期速度は非常に速く、約1万件のメッセージを1秒に処理します）。プロセスが再起動する場合、ローカルメモリ内のキューデータが同期されていない場合、メッセージが失われる可能性があります。`Client::send()` の非同期送信は、重要でないメッセージの送信に適しています。

> **注意**
> `Client::send()` は非同期処理であり、workermanの実行環境でのみ使用できます。コマンドラインスクリプトの場合は、同期インタフェース`Redis::send()` を使用してください。

## 他のプロジェクトでのメッセージ送信
他のプロジェクトでメッセージを送信する必要があり、`webman\redis-queue` を使用できない場合は、以下の関数を参考にしてキューにメッセージを送信できます。

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

ここで、`$redis` はredisインスタンスです。たとえば、redis拡張の使用方法は次のようになります：

```php
$redis = new Redis;
$redis->connect('127.0.0.1', 6379);
$queue = 'user-1';
$data= ['some', 'data'];
redis_queue_send($redis, $queue, $data);
````

## 消費
消費プロセスの設定ファイルは `config/plugin/webman/redis-queue/process.php` にあります。
消費者のディレクトリは `app/queue/redis/` にあります。

`php webman redis-queue:consumer my-send-mail` コマンドを実行すると、 `app/queue/redis/MyMailSend.php` ファイルが生成されます。

> **注意**
> コマンドが存在しない場合でも、手動で生成することができます。

```php
<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class MyMailSend implements Consumer
{
    // 消費するキュー名
    public $queue = 'send-mail';

    // 接続名、plugin/webman/redis-queue/redis.php内の接続に対応
    public $connection = 'default';

    // 消費
    public function consume($data)
    {
        // シリアライズは不要
        var_export($data); // ['to' => 'tom@gmail.com', 'content' => 'hello'] を表示
    }
}
```

> **注意**
> 消費中に例外やエラーが発生しない場合は、消費が成功したと見なされます。そうでない場合は消費が失敗し、リトライキューに入ります。
> redis-queueにはackメカニズムがなく、それを自動ack（例外またはエラーが発生しない場合）と見なすことができます。消費中に現在のメッセージを消費できないことを手動でマークしたい場合は、例外をスローしてリトライキューに入れることができます。これは実質的にはackメカニズムと同じです。

> **注意**
> 消費プロセスは複数のサーバーと複数のプロセスをサポートしており、同じメッセージは再度消費されません。消費したメッセージは自動でキューから削除されます。手動で削除する必要はありません。

> **注意**
> 消費プロセスは同時に複数の異なるキューを消費できます。新しいキューを追加するために`process.php`の設定を変更する必要はありません。新しいキューコンシューマを作成するには、`app/queue/redis`に対応する`Consumer`クラスを追加し、クラスのプロパティ`$queue`で消費するキュー名を指定するだけです。

> **注意**
> Windowsユーザーは`php windows.php`を実行してwebmanを起動する必要があります。そうしないと消費プロセスは起動しません。
## 異なるキューに異なるコンシューマープロセスを設定する
デフォルトでは、すべてのコンシューマーが同じコンシューマープロセスを共有します。しかし、時にはいくつかのキューを独立して消費する必要がある場合があります。たとえば、処理が遅いビジネスを1つのプロセスグループで処理し、処理が速いビジネスを別のプロセスグループで処理する必要がある場合があります。そのためには、コンシューマーを2つのディレクトリに分けることができます。`app_path() . '/queue/redis/fast'` と `app_path() . '/queue/redis/slow'`（コンシューマークラスの名前空間を適切に変更する必要があることに注意してください）のように、以下のように構成することができます：
```php
return [
    ...ここでは他の設定を省略しています...

    'redis_consumer_fast'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // コンシューマークラスのディレクトリ
            'consumer_dir' => app_path() . '/queue/redis/fast'
        ]
    ],
    'redis_consumer_slow'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // コンシューマークラスのディレクトリ
            'consumer_dir' => app_path() . '/queue/redis/slow'
        ]
    ]
];
```

ディレクトリの分類とそれに対応する設定により、異なるコンシューマーに異なるコンシューマープロセスを簡単に設定することができます。

## 複数のRedis構成
#### 設定
`config/plugin/webman/redis-queue/redis.php`
```php
<?php
return [
    'default' => [
        'host' => 'redis://192.168.0.1:6379',
        'options' => [
            'auth' => null,       // パスワード、文字列型、オプションパラメータ
            'db' => 0,            // データベース
            'max_attempts'  => 5, // 消費に失敗した場合のリトライ回数
            'retry_seconds' => 5, // リトライ間隔、秒単位
        ]
    ],
    'other' => [
        'host' => 'redis://192.168.0.2:6379',
        'options' => [
            'auth' => null,       // パスワード、文字列型、オプションパラメータ
            'db' => 0,             // データベース
            'max_attempts'  => 5, // 消費に失敗した場合のリトライ回数
            'retry_seconds' => 5, // リトライ間隔、秒単位
        ]
    ],
];
```

設定には、`other`というキーのRedis構成が追加されていることに注意してください。

#### 複数のRedisへのメッセージの配信

```php
// `default` キーにメッセージを配信する
Client::connection('default')->send($queue, $data);
Redis::connection('default')->send($queue, $data);
// と同義
Client::send($queue, $data);
Redis::send($queue, $data);

// `other` キーにメッセージを配信する
Client::connection('other')->send($queue, $data);
Redis::connection('other')->send($queue, $data);
```

#### 複数のRedisの消費
消費設定で `other` キーのキューからメッセージを消費する
```php
namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class SendMail implements Consumer
{
    // 消費するキューの名前
    public $queue = 'send-mail';

    // === ここで `other` に設定し、消費設定で `other` キーのキューからメッセージを消費することを表します ===
    public $connection = 'other';

    // 消費
    public function consume($data)
    {
        // デシリアライズは不要
        var_export($data);
    }
}
```

## よくある質問

**なぜ「Workerman\Redis\Exception: Workerman Redis Wait Timeout (600 seconds)」というエラーが発生するのか**

このエラーは、非同期の配信インターフェース`Client::send()`でのみ発生します。非同期の配信では、まずメッセージをローカルメモリに保存し、プロセスがアイドル状態のときにメッセージをRedisに送信します。メッセージの受信速度がメッセージの生成速度よりも遅い場合や、プロセスがずっと他の業務に忙しくて十分な時間を確保できないためにメモリのメッセージをRedisに同期できないと、メッセージが詰まることがあります。それにより、600秒以上のメッセージが詰まるとこのエラーが発生します。

解決策：メッセージの配信には同期の配信インターフェース`Redis::send()`を使用してください。
