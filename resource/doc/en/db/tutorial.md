# Quick Start

By default, webman uses [illuminate/database](https://github.com/illuminate/database), which is the database component of [laravel](https://learnku.com/docs/laravel/8.x/database/9400) and shares the same usage as laravel.

However, you can refer to the [Using Other Database Components](others.md) section to use ThinkPHP or other databases.

## Installation

Run `composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper` to install the required dependencies.

After installation, restart the server to apply the changes (reload will not work).

> **Note**
> If you don't need pagination, database events, or SQL printing, you only need to execute the following command:
> `composer require -W illuminate/database`

## Database Configuration
`config/database.php`
```php
return [
    // Default database
    'default' => 'mysql',

    // Various database configurations
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

## Usage
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