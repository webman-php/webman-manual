## Medoo

Medoo ist ein leichtgewichtiges Datenbank-Betriebsplugin, [Medoo-Website](https://medoo.in/).

## Installation
`composer require webman/medoo`

## Datenbankkonfiguration
Die Konfigurationsdatei befindet sich unter `config/plugin/webman/medoo/database.php`.

## Verwendung
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

> **Hinweis**
> `Medoo::get('user', '*', ['uid' => 1]);`
> Entspricht
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## Mehrfache Datenbankkonfiguration

**Konfiguration**
Fügen Sie unter `config/plugin/webman/medoo/database.php` eine neue Konfiguration hinzu. Der Schlüssel kann beliebig sein, hier wird `other` verwendet.

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
    // Eine neue Konfiguration mit dem Namen 'other' hinzufügen
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

**Verwendung**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## Ausführliche Dokumentation
Siehe [offizielle Medoo-Dokumentation](https://medoo.in/api/select)
