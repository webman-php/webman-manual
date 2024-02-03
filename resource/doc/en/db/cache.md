# Cache

webman uses [symfony/cache](https://github.com/symfony/cache) as the default cache component.

> Before using `symfony/cache`, make sure to install the redis extension for `php-cli`.

## Installation
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

After installation, restart is required (reload is not effective).

## Redis Configuration
The redis configuration file is located at `config/redis.php`
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

## Example
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
> It is recommended to add a prefix to the key to avoid conflicts with other businesses using redis.

## Using Other Cache Components

For the use of the [ThinkCache](https://github.com/top-think/think-cache) component, refer to [other databases](others.md#ThinkCache).