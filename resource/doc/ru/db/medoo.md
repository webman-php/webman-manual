## Medoo

Medoo - это легковесный плагин для работы с базами данных, [официальный сайт Medoo](https://medoo.in/).

## Установка
`composer require webman/medoo`

## Настройка базы данных
Файл конфигурации находится по пути `config/plugin/webman/medoo/database.php`.

## Использование
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Medoo\Medoo;

class Index
{
    public function index(Request $request)
    {
        $user = Medoo::get('user', '*', ['uid' => 1]);
        return json($user);
    }
}
```

> **Подсказка**
> `Medoo::get('user', '*', ['uid' => 1]);`
> эквивалентно
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## Настройка для нескольких баз данных

**Настройка**
В файле `config/plugin/webman/medoo/database.php` добавьте новую конфигурацию с произвольным ключом, здесь используется `other`.

```php
<?php
return [
    'default' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
    // Добавлена новая конфигурация под названием other
    'other' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
];
```

**Использование**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## Подробная документация
См. [официальную документацию Medoo](https://medoo.in/api/select)
