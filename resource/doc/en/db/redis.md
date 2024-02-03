# Redis

By default, webman's redis component uses [illuminate/redis](https://github.com/illuminate/redis), which is the Redis library for Laravel, and its usage is the same as Laravel.

Before using `illuminate/redis`, you must install the Redis extension for `php-cli`.

> **Note**
> Use the command `php -m | grep redis` to check if the `php-cli` has the Redis extension installed. Note: Even if you have installed the Redis extension in `php-fpm`, it does not mean that you can use it in `php-cli` because `php-cli` and `php-fpm` are different applications and may use different `php.ini` configurations. Use the command `php --ini` to check which `php.ini` configuration file your `php-cli` is using.

## Installation

```php
composer require -W illuminate/redis illuminate/events
```

After installation, you need to restart (reload is not effective).


## Configuration
The Redis configuration file is located at `config/redis.php`.
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

## Redis Interface
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
> Use the `Redis::select($db)` interface with caution. Due to webman's framework being resident in memory, if one request uses `Redis::select($db)` to switch databases, it will affect subsequent requests. For multiple databases, it is recommended to configure different `$db` as different Redis connection configurations.

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
The default connection used is configured under `default`, and you can use the `Redis::connection()` method to select which Redis connection to use.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Cluster Configuration
If your application uses a Redis server cluster, you should use the clusters key in the Redis configuration file to define these clusters:
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

By default, the cluster can shard clients on nodes, allowing you to implement a node pool and create a large amount of available memory. However, note that the client sharing does not handle failing situations; therefore, this feature is mainly suitable for caching data obtained from another master database. If you want to use native Redis clustering, you need to make the following specification in the options key under the configuration file:

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

## Pipeline Command
When you need to send many commands to the server in one operation, it is recommended to use the pipeline command. The `pipeline` method takes a closure of a Redis instance. You can send all the commands to the Redis instance, and they will all be executed in one operation:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```