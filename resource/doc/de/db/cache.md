# Cache

Webman verwendet standardmäßig [symfony/cache](https://github.com/symfony/cache) als Cache-Komponente.

> Die Installation von `symfony/cache` erfordert zunächst die Installation der Redis-Erweiterung für `php-cli`.

## Installation
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

Nach der Installation muss ein Neustart (reload ist unwirksam) durchgeführt werden.

## Redis-Konfiguration
Die Redis-Konfigurationsdatei befindet sich unter `config/redis.php`
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
> Vermeiden Sie mögliche Konflikte mit anderen Redis-basierten Geschäften, indem Sie den Schlüssel möglichst mit einem Präfix versehen.

## Verwendung anderer Cache-Komponenten

Die Verwendung der [ThinkCache](https://github.com/top-think/think-cache)-Komponente finden Sie unter [Andere Datenbanken](others.md#ThinkCache).
