# Быстрый старт

По умолчанию в webman для работы с базой данных используется [illuminate/database](https://github.com/illuminate/database), то есть [база данных Laravel](https://learnku.com/docs/laravel/8.x/database/9400), используется также, как и в Laravel.

Конечно, вы можете обратиться к разделу [Использование других компонентов базы данных](others.md), чтобы использовать ThinkPHP или другие базы данных.

## Установка

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

После установки требуется выполнить restart (перезапуск) (reload не работает)

> **Совет**
> Если вам не нужна пагинация, события базы данных и вывод SQL, тогда достаточно выполнить
> `composer require -W illuminate/database`

## Настройка базы данных
`config/database.php`
```php
return [
    // По умолчанию используемая база данных
    'default' => 'mysql',

    // Настройки различных баз данных
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

## Использование
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
        return response("Привет, $name");
    }
}
```
