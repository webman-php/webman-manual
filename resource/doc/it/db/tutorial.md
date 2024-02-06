# Inizio rapido

Il database di webman di default utilizza [illuminate/database](https://github.com/illuminate/database), cioè il [database di Laravel](https://learnku.com/docs/laravel/8.x/database/9400), e ne ha lo stesso utilizzo.

Naturalmente è possibile fare riferimento alla sezione [Uso di altri componenti di database](others.md) per utilizzare ThinkPHP o altri database.

## Installazione

Eseguire il comando `composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper` per installare i pacchetti necessari.

Dopo l'installazione è necessario restartare (reload non è valido).

> **Nota**
> Se non è necessario utilizzare la paginazione, gli eventi del database e stampare le query SQL, è sufficiente eseguire
> `composer require -W illuminate/database`

## Configurazione del database
`config/database.php`
```php

return [
    // Database predefinito
    'default' => 'mysql',

    // Configurazioni per vari database
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

## Utilizzo
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
        return response("ciao $name");
    }
}
```
