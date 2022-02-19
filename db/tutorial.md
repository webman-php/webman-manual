# 快速开始

webman数据库默认采用的是 [illuminate/database](https://github.com/illuminate/database)，也就是[laravel的数据库](https://learnku.com/docs/laravel/8.x/database/9400)，用法与laravel相同。

当然你可以参考[使用其它数据库组件](others.md)章节使用ThinkPHP或者其它数据库。

## 安装

```php
composer require illuminate/database
composer require illuminate/pagination
composer require illuminate/events
```

## 数据库配置
`config/database.php`
```php

return [
    // 默认数据库
    'default' => 'mysql',

    // 各种数据库配置
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
        ],
    ],
];
```


## 使用
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class User
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
