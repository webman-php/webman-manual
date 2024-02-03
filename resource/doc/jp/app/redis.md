# Redis
Redisの使用法はデータベースと似ています、例えば`plugin/foo/config/redis.php`です。
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
使用する際は
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

同様に、メインプロジェクトのRedisの設定を再利用したい場合は
```php
use support\Redis;
Redis::get('key');
// メインプロジェクトでcache接続も設定されていると仮定します
Redis::connection('cache')->get('key');
```
