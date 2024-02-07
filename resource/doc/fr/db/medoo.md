## Medoo

Medoo est une bibliothèque de manipulation de base de données légère, [Site officiel de Medoo](https://medoo.in/).

## Installation
`composer require webman/medoo`

## Configuration de la base de données
Le fichier de configuration se trouve dans `config/plugin/webman/medoo/database.php`

## Utilisation
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

> **Remarque**
> `Medoo::get('user', '*', ['uid' => 1]);`
> équivaut à
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## Configuration de plusieurs bases de données

**Configuration**
Ajoutez une nouvelle configuration dans `config/plugin/webman/medoo/database.php` avec une clé quelconque, ici nous utilisons `other`.

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
    // Ajout d'une configuration autre
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

**Utilisation**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## Documentation détaillée
Voir [la documentation officielle de Medoo](https://medoo.in/api/select)
