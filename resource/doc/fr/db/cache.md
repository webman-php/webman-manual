# Cache

webman utilise par défaut le composant de cache [symfony/cache](https://github.com/symfony/cache).

> Avant d'utiliser `symfony/cache`, assurez-vous d'avoir installé l'extension Redis pour `php-cli`.

## Installation
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

Après l'installation, veuillez redémarrer (reload ne fonctionne pas).

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
> Il est recommandé d'ajouter un préfixe à la clé pour éviter les conflits avec d'autres utilisations de Redis.

## Utilisation d'autres composants de Cache

Pour l'utilisation du composant [ThinkCache](https://github.com/top-think/think-cache), veuillez consulter la section "[Autres bases de données](others.md#ThinkCache)".
