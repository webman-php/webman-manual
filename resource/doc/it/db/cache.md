# Cache

webman utilizza [symfony/cache](https://github.com/symfony/cache) come componente di cache per impostazione predefinita.

> È necessario installare l'estensione redis per `php-cli` prima di utilizzare `symfony/cache`.

## Installazione
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

Dopo l'installazione, è necessario restartare (il reload non è valido)

## Configurazione di Redis
Il file di configurazione di redis si trova in `config/redis.php`
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

## Esempio
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

> **Nota**
> Si consiglia di aggiungere un prefisso alla chiave per evitare conflitti con altri utilizzi di Redis.

## Utilizzo di altri componenti di Cache
Per utilizzare il componente [ThinkCache](https://github.com/top-think/think-cache), fare riferimento a [Altri database](others.md#ThinkCache).
