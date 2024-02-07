# Redis
Использование Redis аналогично использованию базы данных, например `plugin/foo/config/redis.php`
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
Использование:
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

То же самое, если вы хотите использовать конфигурацию Redis из главного проекта
```php
use support\Redis;
Redis::get('key');
// Предположим, что в основном проекте также сконфигурировано соединение с кэшем
Redis::connection('cache')->get('key');
```
