# Redis
L'utilisation de Redis est similaire à celle d'une base de données, par exemple `plugin/foo/config/redis.php`
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
Utilisation
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

De même, si vous souhaitez réutiliser la configuration Redis du projet principal
```php
use support\Redis;
Redis::get('key');
// Supposons que le projet principal ait également configuré une connexion cache
Redis::connection('cache')->get('key');
```
