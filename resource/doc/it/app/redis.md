# Redis
L'uso di Redis è simile a quello di un database, ad esempio `plugin/foo/config/redis.php`
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
Utilizzo
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

Allo stesso modo, se si vuole riutilizzare la configurazione di Redis del progetto principale
```php
use support\Redis;
Redis::get('key');
// Supponendo che il progetto principale abbia anche una connessione alla cache configurata
Redis::connection('cache')->get('key');
```
