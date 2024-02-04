# Schnellstart

Die Datenbank von webman verwendet standardmäßig [illuminate/database](https://github.com/illuminate/database), das heißt die [Datenbank von Laravel](https://learnku.com/docs/laravel/8.x/database/9400) und wird genauso benutzt wie in Laravel.

Natürlich können Sie sich auch im Abschnitt [Verwendung anderer Datenbankkomponenten](others.md) über die Verwendung von ThinkPHP oder anderen Datenbanken informieren.

## Installation

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

Nach der Installation ist ein Neustart (reload ist ungültig) erforderlich.

> **Hinweis**
> Wenn keine Paginierung, keine Datenbankereignisse und kein SQL-Druck benötigt werden, führen Sie einfach aus:
> `composer require -W illuminate/database`

## Datenbankkonfiguration
`config/database.php`
```php
return [
    // Standarddatenbank
    'default' => 'mysql',

    // Verschiedene Datenbankkonfigurationen
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
        return response("hello $name");
    }
}
```
