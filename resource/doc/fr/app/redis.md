# Redis
L'utilisation de Redis est similaire à celle d'une base de données, par exemple dans `plugin/foo/config/redis.php`
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
Lors de l'utilisation
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

De même, si vous souhaitez réutiliser la configuration Redis du projet principal
```php
use support\Redis;
Redis::get('key');
// Supposez que le projet principal a également configuré une connexion cache
Redis::connection('cache')->get('key');
```
