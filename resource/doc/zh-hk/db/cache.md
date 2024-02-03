# 緩存

在webman預設使用 [symfony/cache](https://github.com/symfony/cache) 作為緩存組件。

> 在使用 `symfony/cache` 之前必須先為 `php-cli` 安裝 redis 擴展。

## 安裝
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

安裝後需要重啟（reload無效）

## Redis配置
redis配置文件在 `config/redis.php`
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
> key尽量加一個前綴，避免與其他使用redis的業務衝突

## 使用其他緩存組件

[ThinkCache](https://github.com/top-think/think-cache) 組件使用參考 [其它數據庫](others.md#ThinkCache)