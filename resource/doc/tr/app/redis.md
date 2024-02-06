# Redis
Redis, veritabanı gibi kullanılır, örneğin `plugin/foo/config/redis.php`
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
Kullanımı
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

Benzer şekilde, ana projenin Redis yapılandırmasını yeniden kullanmak istiyorsanız
```php
use support\Redis;
Redis::get('key');
// Ana projenin bir cache bağlantısı yapılandırdığını varsayalım
Redis::connection('cache')->get('key');
```
