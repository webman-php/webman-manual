# 緩存

webman默認使用 [symfony/cache](https://github.com/symfony/cache) 作為緩存組件。

> 使用`symfony/cache`之前必須先給`php-cli`安裝redis擴展。

## 安裝
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

安裝後需要restart重啟(reload無效)


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
> key儘量加一個前綴，避免與其它使用redis的業務衝突

## 使用其它Cache組件

[ThinkCache](https://github.com/top-think/think-cache)組件使用參考 [其它數據庫](others.md#ThinkCache)
