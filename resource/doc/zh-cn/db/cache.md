# Cache

[webman/cache](https://github.com/webman-php/cache)是基于[symfony/cache](https://github.com/symfony/cache)开发的缓存组件，兼容协程和非协程环境，支持连接池。

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
`stores.driver`支持3种驱动，**file**、**redis**、**array**。

### file 文件驱动
此为默认驱动，可通过`'default' => 'xxx'`字段更改。

### redis 驱动
Redis存储，如需使用请先安装Redis组件，命令如下

```
composer require -W webman/redis
```
> **提示**
> 要想使用`illuminate/redis`请确保`php-cli`安装了Redis扩展，执行`php -m` 查看`php-cli`支持的扩展。

### stores.redis.connection
`stores.redis.connection` 对应的是`config/redis.php` 里对应的key。当使用redis时，会复用`webman/redis`的配置包括连接池配置。

建议在`config/redis.php`创建一个独立的key，例如cache类似如下

```php
<?php
return [
    'default' => [
        'password' => 'abc123',
        'host' => '127.0.0.1',
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [ // <===
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
    'default' => 'redis', // <=== 
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
### array 内存驱动
内存存储，性能最好，但是会占用内存，一般用于缓存数据量小的项目。

## 切换存储
可以通过如下代码手动切store，从而使用不同的存储驱动，例如
```php
Cache::store('redis')->set('key', 'value');
Cache::store('array')->set('key', 'value');
```

> **提示**
> symfony/cache 的key不允许包含字符"{}()/\@:"

## 使用其它Cache组件

[ThinkCache](https://github.com/webman-php/think-cache)组件使用参考 [其它数据库](others.md#ThinkCache)
