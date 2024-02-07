# Redis
Die Verwendung von Redis ist ähnlich wie die Verwendung einer Datenbank, z.B. `plugin/foo/config/redis.php`
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
Bei der Verwendung
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

Ebenso, wenn Sie die Redis-Konfiguration des Hauptprojekts wiederverwenden möchten
```php
use support\Redis;
Redis::get('key');
// Angenommen, das Hauptprojekt hat auch eine Cache-Verbindung konfiguriert
Redis::connection('cache')->get('key');
```
