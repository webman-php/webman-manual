# Кэширование

По умолчанию webman использует компонент кэширования [symfony/cache](https://github.com/symfony/cache).

> Для использования `symfony/cache` необходимо предварительно установить расширение redis для `php-cli`.

## Установка
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

После установки необходимо выполнить restart (reload не действует).

## Настройка Redis
Файл настройки redis находится в `config/redis.php`
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

## Пример
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

> **Примечание**
> Рекомендуется добавить префикс к ключу, чтобы избежать конфликтов с другими использованиями redis.

## Использование других компонентов кэширования

Инструкции по использованию компонента [ThinkCache](https://github.com/top-think/think-cache) см. в разделе [Другие базы данных](others.md#ThinkCache).
