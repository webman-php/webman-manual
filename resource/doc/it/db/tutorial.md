# Inizio rapido

Il database di webman utilizza di default [illuminate/database](https://github.com/illuminate/database), ovvero il [database di Laravel](https://learnku.com/docs/laravel/8.x/database/9400), e ne condivide lo stesso utilizzo. 

Naturalmente puoi fare riferimento al capitolo [Utilizzare altri componenti di database](others.md) per usare ThinkPHP o altri database.

## Installazione

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

Dopo l'installazione è necessario eseguire il comando restart per riavviare (reload non funziona)

> **Nota**
> Se non è necessaria la paginazione, gli eventi del database o la stampa SQL, è sufficiente eseguire
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
