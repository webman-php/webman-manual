# Quick Start

webman's default database is [illuminate/database](https://github.com/illuminate/database), which is the database used in Laravel, and its usage is the same as in Laravel.

Of course, you can refer to the "Using Other Database Components" section in the document to use ThinkPHP or other databases.

## Installation

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

After installation, restart is required (reload is invalid).

> **Tip**
> If you don't need pagination, database events, or print SQL, you only need to execute
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
