# 快取

webman預設使用 [symfony/cache](https://github.com/symfony/cache) 作為快取元件。

> 在使用 `symfony/cache` 前，必須先為 `php-cli` 安裝 Redis 擴充功能。

## 安裝
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

安裝後需要 restart 重新啟動（reload 無效）。

## Redis配置
Redis配置文件位於`config/redis.php`
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

## 範例
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
> key尽量加一个前缀，避免与其它使用redis的业务冲突

## 使用其他快取元件

[ThinkCache](https://github.com/top-think/think-cache) 元件使用參考[其他資料庫](others.md#ThinkCache)。
