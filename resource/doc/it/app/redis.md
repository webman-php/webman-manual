# Redis
L'uso di Redis è simile a un database, ad esempio `plugin/foo/config/redis.php`
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
Quando utilizzato
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

Allo stesso modo, se si desidera riutilizzare la configurazione di Redis del progetto principale
```php
use support\Redis;
Redis::get('key');
// Supponendo che il progetto principale abbia anche una connessione cache configurata
Redis::connection('cache')->get('key');
```
