## Medoo

Medoo is a lightweight database operation plugin, [Medoo official website](https://medoo.in/).

## Installation
`composer require webman/medoo`

## Database Configuration
The configuration file is located at `config/plugin/webman/medoo/database.php`.

## Usage
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

> **Note**
> `Medoo::get('user', '*', ['uid' => 1]);`
> is equivalent to
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## Multiple Database Configuration

**Configuration**  
Add a new configuration in `config/plugin/webman/medoo/database.php`, the key can be anything, here we use `other`.

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
    // Add a new configuration 'other' here
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

**Usage**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## Detailed Documentation
Refer to [Medoo official documentation](https://medoo.in/api/select) for more information.
