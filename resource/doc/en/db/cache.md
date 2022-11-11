# Cache

在webmannative templates [symfony/cache](https://github.com/symfony/cache)ascacheComponent。

> The redis extension must be installed for `php-cli` before using `symfony/cache`。

## Install
**php 7.x**
```php
composer require psr/container ^1.1.1 illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require psr/container ^1.1.1 illuminate/redis symfony/cache
```


## RedisConfigure
redisConfiguration file in`config/redis.php`
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

## Examples
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

> **Note**
> keyTry to add a prefix to avoid conflicts with other businesses that use redis

## Using other Cache components

[ThinkCache](https://github.com/top-think/think-cache)e.g. statistics about a [Define routes in](others.md#ThinkCache)
