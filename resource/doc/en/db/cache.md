# Cache

webman uses [symfony/cache](https://github.com/symfony/cache) as the default cache component.

> Before using `symfony/cache`, you must install the redis extension for `php-cli`.

## Installation
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

After installation, you need to restart (reload is invalid).

## Redis Configuration
The redis configuration file is located at `config/redis.php`.
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

> **Note:**
> It is recommended to add a prefix to the key to avoid conflicts with other businesses that use redis.

## Using Other Cache Components

For the use of the [ThinkCache](https://github.com/top-think/think-cache) component, please refer to [Other Databases](others.md#ThinkCache).
