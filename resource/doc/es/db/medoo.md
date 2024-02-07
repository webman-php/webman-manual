## Medoo

Medoo es un plugin ligero para operaciones de base de datos, [sitio web de Medoo](https://medoo.in/).

## Instalación
`composer require webman/medoo`

## Configuración de la base de datos
El archivo de configuración se encuentra en `config/plugin/webman/medoo/database.php`

## Uso
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

> **Nota**
> `Medoo::get('user', '*', ['uid' => 1]);`
> es equivalente a
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## Configuración de múltiples bases de datos

**Configuración**  
Agregue una nueva configuración en `config/plugin/webman/medoo/database.php`, la clave es arbitraria, aquí se utiliza `other`.

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
    // Se ha agregado una nueva configuración llamada 'other'
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

**Uso**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## Documentación detallada
Consulte la [documentación oficial de Medoo](https://medoo.in/api/select)
