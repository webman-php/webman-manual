# Quick Start

webmanThe database defaults to using  [illuminate/database](https://github.com/illuminate/database)，is also[laravelmost](https://learnku.com/docs/laravel/8.x/database/9400)，Usage withlaravelsame。

Of course you can refer to[UsageThis feature is disabled by default](others.md)ChapterUsageThinkPHPor other databases。

## Install

`composer require -W psr/container ^1.1.1 illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

> **hint**
> If you don't need paging, database events, or printing SQL, then just execute
> `composer require -W psr/container ^1.1.1 illuminate/database`

## Database configuration
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
