# Redis
webmanのRedisコンポーネントはデフォルトで[illuminate/redis](https://github.com/illuminate/redis)を使用しており、これはlaravelのRedisライブラリです。使用方法はlaravelと同じです。

`illuminate/redis`を使用する前に、`php-cli`にRedis拡張機能をインストールする必要があります。

> **注意**
> `php-cli`にRedis拡張機能がインストールされているかどうかを確認するには、`php -m | grep redis`コマンドを使用します。注意：`php-fpm`でRedis拡張機能をインストールしていても、`php-cli`で使用できるとは限りません。なぜなら、`php-cli`と`php-fpm`は異なるアプリケーションであり、異なる`php.ini`の設定を使用する可能性があるからです。使用する`php.ini`設定ファイルを確認するには、`php --ini`コマンドを使用します。

## インストール
```php
composer require -W illuminate/redis illuminate/events
```
インストール後に再起動が必要です（reloadは無効です）。

## 設定
Redisの構成ファイルは`config/redis.php`にあります。
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ]
];
```

## 例
```php
<?php
namespace app\controller;

use support\Request;
use support\Redis;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Redis::set($key, rand());
        return response(Redis::get($key));
    }
}
```

## Redisインターフェース
```php
Redis::append($key, $value)
Redis::bitCount($key)
Redis::decr($key, $value)
Redis::decrBy($key, $value)
Redis::get($key)
Redis::getBit($key, $offset)
Redis::getRange($key, $start, $end)
Redis::getSet($key, $value)
Redis::incr($key, $value)
Redis::incrBy($key, $value)
Redis::incrByFloat($key, $value)
Redis::mGet(array $keys)
Redis::getMultiple(array $keys)
Redis::mSet($pairs)
Redis::mSetNx($pairs)
Redis::set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
Redis::setBit($key, $offset, $value)
Redis::setEx($key, $ttl, $value)
Redis::pSetEx($key, $ttl, $value)
Redis::setNx($key, $value)
Redis::setRange($key, $offset, $value)
Redis::strLen($key)
Redis::del(...$keys)
Redis::exists(...$keys)
Redis::expire($key, $ttl)
Redis::expireAt($key, $timestamp)
Redis::select($dbIndex)
```
同等の操作は以下のようになります。
```php
$redis = Redis::connection('default');
$redis->append($key, $value)
$redis->bitCount($key)
$redis->decr($key, $value)
$redis->decrBy($key, $value)
$redis->get($key)
$redis->getBit($key, $offset)
...
```

> **注意**
> `Redis::select($db)`インターフェースを慎重に使用してください。webmanは常駐メモリのフレームワークであるため、あるリクエストで`Redis::select($db)`を使用してデータベースを切り替えると、後続の他のリクエストに影響を与える可能性があります。複数のデータベースを使用する場合は、異なる`$db`を異なるRedis接続構成に設定することをお勧めします。

## 複数のRedis接続を使用する
例えば構成ファイル`config/redis.php`は以下のようになります。
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ],

    'cache' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 1,
    ],

]
```
デフォルトでは、`default`で構成された接続が使用されますが、`Redis::connection()`メソッドを使用してどのredis接続を使用するかを選択できます。
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## クラスター構成
アプリケーションがRedisサーバークラスターを使用している場合、クラスターを定義するためにRedis構成ファイルで`clusters`キーを使用する必要があります。
```php
return [
    'clusters' => [
        'default' => [
            [
                'host'     => 'localhost',
                'password' => null,
                'port'     => 6379,
                'database' => 0,
            ],
        ],
    ],

];
```

デフォルトでは、クラスターはノード上でクライアントシャーディングを実装し、ノードプールを作成し、大量の利用可能なメモリを確保できます。ただし、クライアントシェアリングは失敗を処理しません。そのため、この機能は主に他のメインデータベースからキャッシュデータを取得する場合に適しています。Redisネイティブクラスターを使用するには、構成ファイルの`options`キーで次のように指定する必要があります。
```php
return[
    'options' => [
        'cluster' => 'redis',
    ],

    'clusters' => [
        // ...
    ],
];
```

## パイプラインコマンド
サーバーに多くのコマンドを送信する必要がある場合は、パイプラインコマンドを使用することをお勧めします。`pipeline`メソッドはRedisインスタンスのクロージャを受け入れます。すべてのコマンドをRedisインスタンスに送信すると、それらは1つの操作で実行されます。
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
