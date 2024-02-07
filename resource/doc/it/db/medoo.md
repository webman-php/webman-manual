## Medoo

Medoo è una leggera libreria per l'operazione di database, [sito ufficiale di Medoo](https://medoo.in/).

## Installazione
`composer require webman/medoo`

## Configurazione del database
Il file di configurazione si trova in `config/plugin/webman/medoo/database.php`

## Utilizzo
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

> **Suggerimento**
> `Medoo::get('user', '*', ['uid' => 1]);`
> Equivale a
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## Configurazione dei database multipli

**Configurazione**
Aggiungere una nuova configurazione nel file `config/plugin/webman/medoo/database.php`, con una chiave arbitraria, in questo caso viene utilizzata `other`.

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
    // Qui è stata aggiunta una nuova configurazione denominata other
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

**Utilizzo**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## Documentazione dettagliata
Consulta la [documentazione ufficiale di Medoo](https://medoo.in/api/select)
