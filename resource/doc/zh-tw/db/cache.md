# 快取

在webman中，預設使用 [symfony/cache](https://github.com/symfony/cache) 作為快取組件。

> 在使用 `symfony/cache` 之前，必須在 `php-cli` 中安裝 Redis 擴展。

## 安裝
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

安裝後，需要進行重啟 (reload 無效)。

## Redis配置
Redis 的配置文件位於 `config/redis.php`
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
> key 尽量加一个前缀，避免與其他使用 Redis 的業務衝突。

## 使用其他快取組件

[ThinkCache](https://github.com/top-think/think-cache) 組件的使用請參考 [其他資料庫](others.md#ThinkCache)。