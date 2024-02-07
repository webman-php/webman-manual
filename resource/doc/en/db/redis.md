# Redis

webman's redis component uses [illuminate/redis](https://github.com/illuminate/redis) by default, which is the redis library for Laravel, and its usage is the same as Laravel.

Before using `illuminate/redis`, you must install the redis extension for `php-cli`.

> **Note**
> Use the command `php -m | grep redis` to check if the redis extension is installed for `php-cli`. Note: even if you have installed the redis extension for `php-fpm`, it does not mean that you can use it for `php-cli`, because `php-cli` and `php-fpm` are different applications and may use different `php.ini` configurations. Use the command `php --ini` to check which `php.ini` configuration file is used by your `php-cli`.

## Installation

```php
composer require -W illuminate/redis illuminate/events
```

After installation, you need to restart (reload is ineffective).


## Configuration
The redis configuration file is located in `config/redis.php`
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ]
];
```

## Example
```php
<?php
namespace app\controller;

use support\Request;
use support\Redis;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Redis::set($key, rand());
        return response(Redis::get($key));
    }
}
```

## Redis API
```php
Redis::append($key, $value)
Redis::bitCount($key)
Redis::decr($key, $value)
Redis::decrBy($key, $value)
Redis::get($key)
Redis::getBit($key, $offset)
Redis::getRange($key, $start, $end)
Redis::getSet($key, $value)
Redis::incr($key, $value)
Redis::incrBy($key, $value)
Redis::incrByFloat($key, $value)
Redis::mGet(array $keys)
Redis::getMultiple(array $keys)
Redis::mSet($pairs)
Redis::mSetNx($pairs)
Redis::set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
Redis::setBit($key, $offset, $value)
Redis::setEx($key, $ttl, $value)
Redis::pSetEx($key, $ttl, $value)
Redis::setNx($key, $value)
Redis::setRange($key, $offset, $value)
Redis::strLen($key)
Redis::del(...$keys)
Redis::exists(...$keys)
Redis::expire($key, $ttl)
Redis::expireAt($key, $timestamp)
Redis::select($dbIndex)
```
Equivalent to
```php
$redis = Redis::connection('default');
$redis->append($key, $value)
$redis->bitCount($key)
$redis->decr($key, $value)
$redis->decrBy($key, $value)
$redis->get($key)
$redis->getBit($key, $offset)
...
```

> **Note**
> Be careful when using the `Redis::select($db)` API. Since webman is a persistent memory framework, if a request uses `Redis::select($db)` to switch databases, it will affect subsequent requests. For multiple databases, it is recommended to configure different `$db` with different Redis connection configurations.

## Using Multiple Redis Connections
For example, in the configuration file `config/redis.php`
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ],

    'cache' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 1,
    ],

]
```
By default, the connection configured under `default` is used. You can use the `Redis::connection()` method to select which redis connection to use.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Cluster Configuration
If your application uses a Redis server cluster, you should define these clusters in the Redis configuration file using the `clusters` key:
```php
return [
    'clusters' => [
        'default' => [
            [
                'host'     => 'localhost',
                'password' => null,
                'port'     => 6379,
                'database' => 0,
            ],
        ],
    ],

];
```

By default, the cluster can implement client-side sharding on nodes, allowing you to implement node pools and create a large amount of available memory. Note that client sharing will not handle failure cases; therefore, this feature is mainly used for caching data obtained from another main database. If you want to use Redis native clustering, you need to make the following specifications in the options key in the configuration file:

```php
return[
    'options' => [
        'cluster' => 'redis',
    ],

    'clusters' => [
        // ...
    ],
];
```

## Pipeline Commands
When you need to send a lot of commands to the server in one operation, it is recommended to use pipeline commands. The `pipeline` method takes a closure of the Redis instance. You can send all the commands to the Redis instance, and they will all be executed in one operation:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
