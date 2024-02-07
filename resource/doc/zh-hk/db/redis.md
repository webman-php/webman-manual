# Redis

webman的redis組件默認使用的是[illuminate/redis](https://github.com/illuminate/redis)，也就是laravel的redis庫，用法與laravel相同。

在使用`illuminate/redis`之前必須先給`php-cli`安裝redis擴展。

> **注意**
> 使用命令`php -m | grep redis`查看`php-cli`是否裝了redis擴展。注意：即使你在`php-fpm`安裝了redis擴展，不代表你在`php-cli`可以使用它，因為`php-cli`和`php-fpm`是不同的應用程序，可能使用的是不同的`php.ini`配置。使用命令`php --ini`來查看你的`php-cli`使用的是哪個`php.ini`配置文件。

## 安裝

```php
composer require -W illuminate/redis illuminate/events
```

安裝後需要restart重啟(reload無效)


## 配置
redis配置文件在`config/redis.php`
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

## 示例
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
等價於
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

> **注意**
> 慎用`Redis::select($db)`接口，由於webman是常駐內存的框架，如果某一個請求使用`Redis::select($db)`切換數據庫後將會影響後續其他請求。多數據庫建議將不同的`$db`配置成不同的Redis連接配置。

## 使用多個 Redis 連接
例如配置文件`config/redis.php`
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
默認使用的是`default`下配置的連接，你可以用`Redis::connection()`方法選擇使用哪個redis連接。
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## 集群配置
如果你的應用使用 Redis 伺服器集群，你應該在 Redis 配置文件中使用 clusters 鍵來定義這些集群：
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
預設情況下，集群可以在節點上實現客戶端分片，允許你實現節點池以及創建大量可用內存。這裡要注意，客戶端共用不會處理失敗的情況；因此，這個功能主要適用於從另一個主數據庫獲取的緩存數據。如果要使用 Redis 原生集群，需要在配置文件下的 options 鍵中做出如下指定：

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

## 管道命令
當你需要在一個操作中給伺服器發送很多命令時，推薦你使用管道命令。 pipeline 方法接受一個 Redis 實例的閉包 。你可以將所有的命令發送給 Redis 實例，它們都會在一個操作中執行完成：
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
