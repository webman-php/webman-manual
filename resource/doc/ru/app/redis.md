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
При использовании
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

Точно так же, если вы хотите использовать конфигурацию Redis основного проекта повторно
```php
use support\Redis;
Redis::get('key');
// Предположим, что основной проект также настроил подключение cache
Redis::connection('cache')->get('key');
```
