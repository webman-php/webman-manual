# Cache

在webman默认使用 [symfony/cache](https://github.com/symfony/cache)作为cache组件。

> 使用`symfony/cache`之前必须先给`php-cli`安装redis扩展。

## 安装
```php
composer require illuminate/redis ^8.2.0
composer require symfony/cache ^5.2
```

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

class User
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Cache::set($key, rand());
        return response(Cache::get($key));
    }
}
```

## 使用其它Cache组件

[ThinkCache](https://github.com/top-think/think-cache)组件使用参考 [其它数据库](others.html)
