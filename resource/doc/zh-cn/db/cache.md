# Cache

在webman默认使用 [symfony/cache](https://github.com/symfony/cache)作为cache组件。

> 使用`symfony/cache`之前必须先给`php-cli`安装redis扩展。

## 安装
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

安装后需要restart重启(reload无效)


## Redis配置
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

> **注意**
> key尽量加一个前缀，避免与其它使用redis的业务冲突。
> symfony/cache 的key不允许包含字符"{}()/\@:"

## 使用其它Cache组件

[ThinkCache](https://github.com/top-think/think-cache)组件使用参考 [其它数据库](others.md#ThinkCache)
