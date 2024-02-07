# Schnellstart

Die webman-Datenbank verwendet standardmäßig [illuminate/database](https://github.com/illuminate/database), das auch als [Laravel-Datenbank](https://learnku.com/docs/laravel/8.x/database/9400) bekannt ist. Die Verwendung ist ähnlich wie bei Laravel.

Natürlich können Sie auch das Kapitel [Verwendung anderer Datenbankkomponenten](others.md) konsultieren, um ThinkPHP oder andere Datenbanken zu verwenden.

## Installation

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

Nach der Installation ist ein Neustart (reload ist nicht wirksam) erforderlich.

> **Hinweis**
> Wenn Sie keine Seitennummerierung, Datenbankereignisse und SQL-Druck benötigen, führen Sie einfach aus:
> `composer require -W illuminate/database`

## Datenbankkonfiguration

`config/database.php`
```php

return [
    // Standarddatenbank
    'default' => 'mysql',

    // Verschiedene Datenbankeinstellungen
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

## Verwendung
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
        return response("Guten Tag, $name");
    }
}
```
