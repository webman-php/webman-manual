# Redis

Redis usage is similar to a database, for example `plugin/foo/config/redis.php`:

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

To use it:

```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

Similarly, if you want to reuse the Redis configuration of the main project:

```php
use support\Redis;
Redis::get('key');
// Assuming the main project also configured a cache connection
Redis::connection('cache')->get('key');
```
