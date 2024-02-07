# Redis
Redis用法同資料庫相似，例如 `plugin/foo/config/redis.php`
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

同樣地，如果想複用主項目的Redis配置
```php
use support\Redis;
Redis::get('key');
// 假設主項目還配置了一個cache連接
Redis::connection('cache')->get('key');
```
