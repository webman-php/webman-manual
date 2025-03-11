# Cache

[webman/cache](https://github.com/webman-php/cache)是基于[symfony/cache](https://github.com/symfony/cache)开发的缓存组件，兼容协程和非协程环境，支持连接池。


> **注意**
> 当前手册为 webman v2 版本，如果您使用的是webman v1版本，请查看 [v1版本手册](/doc/webman-v1/db/cache.html)

## 安装

```php
composer require -W webman/cache
```

## 示例
```php
<?php
namespace app\controller;

use support\Request;
use support\Cache;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Cache::set($key, rand());
        return response(Cache::get($key));
    }
}
```

## 配置文件位置
配置文件在 `config/cache.php`，如果没有请手动创建。

## 配置文件内容
```php
<?php
return [
    'default' => 'file',
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => runtime_path('cache')
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default'
        ],
        'array' => [
            'driver' => 'array'
        ]
    ]
];
```
`stores.driver`支持4种驱动，**file**、**redis**、**array**、**apcu**。

### file 文件驱动
此为默认驱动，不依赖其它组件，支持跨进程共享缓存数据，不支持多服务器共享缓存数据。

### array 内存驱动
内存存储，性能最好，但是会占用内存，不支持跨进程跨服务器共享数据，进程重启后失效，一般用于缓存数据量小的项目。

### apcu 内存驱动
内存存储，性能仅次于 array，但是会占用内存，支持跨进程共享缓存数据，不支持多服务器共享缓存数据，进程重启后失效，一般用于缓存数据量小的项目。

> 需要安装并启用 [APCu 扩展](https://pecl.php.net/package/APCu)；不建议用于频繁进行缓存写入/删除的场景，会导致明显的性能下降。

### redis 驱动
依赖[webman/redis](./redis.md)组件，支持跨进程跨服务器共享缓存数据。

**stores.redis.connection**

`stores.redis.connection` 对应的是`config/redis.php` 里对应的key。当使用redis时，会复用`webman/redis`的配置包括连接池配置。

**建议在`config/redis.php`增加一个独立的配置，例如cache类似如下**

```php
<?php
return [
    'default' => [
        'password' => 'abc123',
        'host' => '127.0.0.1',
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [ // <==== 新增
        'password' => 'abc123',
        'host' => '127.0.0.1',
        'port' => 6379,
        'database' => 1,
        'prefix' => 'webman_cache-',
    ]
];
```

然后将`stores.redis.connection`设置为`cache`，`config/cache.php`最终配置类似如下
```php
<?php
return [
    'default' => 'redis', // <==== 
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => runtime_path('cache')
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache' // <====
        ],
        'array' => [
            'driver' => 'array'
        ]
    ]
];
```

## 切换存储
可以通过如下代码手动切store，从而使用不同的存储驱动，例如
```php
Cache::store('redis')->set('key', 'value');
Cache::store('array')->set('key', 'value');
```

> **提示**
> Key 名受 [PSR6](https://www.php-fig.org/psr/psr-6/#definitions) 限制不允许包含`{}()/\@:`中任一字符，但这一判断截至目前（`symfony/cache` 7.2.4）可暂时通过 PHP ini 配置 `zend.assertions=-1` 跳过。

## 使用其它Cache组件

[ThinkCache](https://github.com/webman-php/think-cache)组件使用参考 [其它数据库](others.md#ThinkCache)
