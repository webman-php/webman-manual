# Cache

webman utilise [symfony/cache](https://github.com/symfony/cache) par défaut en tant que composant de cache.

> Avant d'utiliser `symfony/cache`, il est nécessaire d'installer l'extension Redis pour `php-cli`.

## Installation
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

Après l'installation, un redémarrage (reload n'est pas efficace) est nécessaire.

## Configuration de Redis
Le fichier de configuration de Redis se trouve dans `config/redis.php`
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

## Exemple
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

> **Remarque**
> Il est préférable d'ajouter un préfixe à la clé pour éviter les conflits avec d'autres utilisations de Redis.

## Utilisation d'autres composants de Cache

Pour utiliser le composant [ThinkCache](https://github.com/top-think/think-cache), veuillez consulter [d'autres bases de données](others.md#ThinkCache).
