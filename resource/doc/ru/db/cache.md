# Кэширование

По умолчанию webman использует [symfony/cache](https://github.com/symfony/cache) в качестве компонента кэширования.

> Перед использованием `symfony/cache` необходимо установить расширение redis для `php-cli`.

## Установка
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

После установки необходимо перезагрузить (перезапустить) сервер.

## Настройка Redis
Файл настройки Redis находится в `config/redis.php`
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
> Рекомендуется добавлять префикс к ключу, чтобы избежать конфликтов с другими использованиями Redis.

## Использование других компонентов кэширования

Использование компонента кэширования [ThinkCache](https://github.com/top-think/think-cache) описано в разделе [Другие базы данных](others.md#ThinkCache)
