# Redis

webman的redis组件基于[illuminate/redis](https://github.com/illuminate/redis)，也就是laravel的redis库，用法与laravel相同。

使用`illuminate/redis`之前必须先给`php-cli`安装redis扩展。

> 使用命令`php -m | grep redis`查看`php-cli`是否装了redis扩展。注意：即使你在`php-fpm`安装了redis扩展，不代表你在`php-cli`可以使用它，因为`php-cli`和`php-fpm`是不同的应用程序，可能使用的是不同的`php.ini`配置。使用命令`php --ini`来查看你的`php-cli`使用的是哪个`php.ini`配置文件。

## 安装
**适合php>=7.3**
```php
composer require vlucas/phpdotenv ^5.1.0
composer require illuminate/redis ^8.2.0
```

**适合php>=7.2**
```
composer require vlucas/phpdotenv ^4.0
composer require illuminate/redis ^7.0
```

## 配置
redis配置文件在`config/redis.php`
```php
return [
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_DB', 0),
    ]
];
```

在`.env`文件中配置好
```
REDIS_HOST
REDIS_PASSWORD
REDIS_PORT
REDIS_DB
```
等参数并重启webman。

## 示例
```php
<?php
namespace app\controller;

use support\Request;
use support\bootstrap\Redis;

class User
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Redis::set($key, rand());
        return response(Redis::get($key));
    }
}
```

## Redis接口
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
等价于
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

## 使用多个 Redis 连接
例如配置文件`config/redis.php`
```php
return [
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_DB', 0),
    ],

    'cache' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_CACHE_DB', 1),
    ],

]
```
默认使用的是`default`下配置的连接，你可以用`Redis::connection()`方法选择使用哪个redis连接。
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## 集群配置
如果你的应用使用 Redis 服务器集群，你应该在 Redis 配置文件中使用 clusters 键来定义这些集群：
```php
return [
    'clusters' => [
        'default' => [
            [
                'host' => env('REDIS_HOST', 'localhost'),
                'password' => env('REDIS_PASSWORD', null),
                'port' => env('REDIS_PORT', 6379),
                'database' => 0,
            ],
        ],
    ],

];
```

默认情况下，集群可以在节点上实现客户端分片，允许你实现节点池以及创建大量可用内存。这里要注意，客户端共享不会处理失败的情况；因此，这个功能主要适用于从另一个主数据库获取的缓存数据。如果要使用 Redis 原生集群，需要在配置文件下的 options 键中做出如下指定：

```php
return[
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
    ],

    'clusters' => [
        // ...
    ],
];
```

## 管道命令
当你需要在一个操作中给服务器发送很多命令时，推荐你使用管道命令。 pipeline 方法接受一个 Redis 实例的 闭包 。你可以将所有的命令发送给 Redis 实例，它们都会在一个操作中执行完成：
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
