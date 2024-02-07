# Redis
Redisの使用はデータベースに似ています。例えば、`plugin/foo/config/redis.php` に以下のように記述します。
```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ],
];
```

使用する際は以下のようにします。
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

同様に、メインプロジェクトのRedis設定を再利用したい場合は以下のようにします。
```php
use support\Redis;
Redis::get('key');
// メインプロジェクトでcache接続も設定されていると仮定します
Redis::connection('cache')->get('key');
```
