# Comienzo rápido

La base de datos webman utiliza por defecto [illuminate/database](https://github.com/illuminate/database), que es la base de datos de [Laravel](https://learnku.com/docs/laravel/8.x/database/9400) y se utiliza de la misma manera que Laravel.

Por supuesto, puedes consultar la sección [Uso de otros componentes de bases de datos](others.md) para utilizar ThinkPHP u otras bases de datos.

## Instalación

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

Después de la instalación, es necesario reiniciar (reload no es válido).

> **Nota**
> Si no se necesita paginación, eventos de base de datos y la impresión de SQL, solamente es necesario ejecutar
> `composer require -W illuminate/database`

## Configuración de la base de datos
`config/database.php`
```php
return [
    // Base de datos por defecto
    'default' => 'mysql',

    // Configuraciones para varias bases de datos
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

## Uso
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
        return response("Hola $name");
    }
}
```
