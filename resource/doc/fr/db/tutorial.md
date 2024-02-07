# Démarrage rapide

La base de données de webman utilise par défaut [illuminate/database](https://github.com/illuminate/database), c'est-à-dire la [base de données de Laravel](https://learnku.com/docs/laravel/8.x/database/9400), et son utilisation est similaire à celle de Laravel.

Bien sûr, vous pouvez consulter la section [Utilisation d'autres composants de base de données](others.md) pour utiliser ThinkPHP ou d'autres bases de données.

## Installation

Exécutez la commande `composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper` pour installer.

Après l'installation, il est nécessaire de redémarrer (reload est inopérant).

> **Remarque**
> Si vous n'avez pas besoin de pagination, d'événements de base de données ou de l'affichage des requêtes SQL, exécutez simplement
> `composer require -W illuminate/database`

## Configuration de la base de données
`config/database.php`
```php

return [
    // Base de données par défaut
    'default' => 'mysql',

    // Configurations des différentes bases de données
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'test',
            'username'    => 'root',
            'password'    => '',
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
            'options' => [
                \PDO::ATTR_TIMEOUT => 3
            ]
        ],
    ],
];
```

## Utilisation
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        $default_uid = 29;
        $uid = $request->get('uid', $default_uid);
        $name = Db::table('users')->where('uid', $uid)->value('username');
        return response("hello $name");
    }
}
```
