# Cache

webman verwendet standardmäßig das [symfony/cache](https://github.com/symfony/cache) als Cache-Komponente.

> Bevor Sie `symfony/cache` verwenden, müssen Sie die Redis-Erweiterung für `php-cli` installieren.

## Installation
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

Nach der Installation muss neu gestartet werden (neuladen ist unwirksam).


## Redis-Konfiguration
Die Redis-Konfigurationsdatei befindet sich in `config/redis.php`
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

## Beispiel
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

> **Hinweis**
> Verwenden Sie möglichst einen Präfix für den Schlüssel, um Konflikte mit anderen Redis-basierten Geschäften zu vermeiden.

## Verwendung anderer Cache-Komponenten

Die Verwendung der [ThinkCache](https://github.com/top-think/think-cache)-Komponente finden Sie unter [anderen Datenbanken](others.md#ThinkCache)
