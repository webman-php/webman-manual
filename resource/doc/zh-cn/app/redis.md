# Redis
Redis用法与数据库类似，例如 `plugin/foo/config/redis.php`
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
使用时
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

同样的，如果想复用主项目的Redis配置
```php
use support\Redis;
Redis::get('key');
// 假设主项目还配置了一个cache连接
Redis::connection('cache')->get('key');
```