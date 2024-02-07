# Redis
O Redis é usado de forma similar a um banco de dados, por exemplo `plugin/foo/config/redis.php`
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

Quando em uso
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

Da mesma forma, se quiser reutilizar a configuração do Redis do projeto principal
```php
use support\Redis;
Redis::get('key');
// Supondo que o projeto principal também configurou uma conexão de cache
Redis::connection('cache')->get('key');
```
