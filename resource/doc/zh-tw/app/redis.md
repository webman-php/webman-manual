# Redis
Redis用法與資料庫類似，例如 `plugin/foo/config/redis.php`
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
使用時
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

同樣的，如果想複用主專案的Redis配置
```php
use support\Redis;
Redis::get('key');
// 假設主專案還配置了一個cache連線
Redis::connection('cache')->get('key');
```
