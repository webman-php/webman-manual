# Redis

webman的redisThe component uses by default[illuminate/redis](https://github.com/illuminate/redis)，is alsolaravel的redis库，Usage withlaravelsame。

You must install the redis extension for `php-cli` before using `illuminate/redis`。

> Use the command `php -m | grep redis` to see if `php-cli` has the redis extension installed. Note: Even if you have the redis extension installed on `php-fpm`, it does not mean you can use it on `php-cli`, because `php-cli` and `php-fpm` are different applications and may use different `php.ini` configurations. Use the command `php --ini` to see which `php.ini` configuration file is used by your `php-cli` 。

## Install

```php
composer require psr/container ^1.1.1 illuminate/redis illuminate/events
```


## Configure
redisConfiguration file in`config/redis.php`
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

## Examples
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

## RedisInterface
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
> careful use`Redis::select($db)`Interface，due towebmanis a memory-resident framework，if a certain request uses`Redis::select($db)`Switching the database will affect other subsequent requests。Multiple databases are recommended to be different`$db`Configurea recordRedisConnectionsConfigure。

## Use multiple Redis connections
e.g. configuration file`config/redis.php`
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
The default connection used is the one configured under `default`, you can choose which redis connection to use with the `Redis::connection()` method。
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Cluster Configuration
If your application uses Redis server clusters, you should use the clusters key in the Redis configuration file to define these clusters：
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

is a product based on，clusters can implement client slicing on nodes，allows you to implement node pools and create large amounts of available memory。Here toNote，Client-side sharing does not handle failure cases；therefore，This feature is mainly applicable to cached data fetched from another master database。Recommended Redis Correspondence in，Required inConfigureRule lookup options The following is specified in the key：

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

## Pipeline command
when you need to send many commands to the server in one operation，DefaultPipeline command。 pipeline in the exception default by Redis business notification 。You can send all commands to Redis instance，They will all be executed in one operation to complete：
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
