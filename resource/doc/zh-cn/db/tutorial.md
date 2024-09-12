# 快速开始

webman数据库默认采用的是 [illuminate/database](https://github.com/illuminate/database)，也就是[laravel的数据库](https://learnku.com/docs/laravel/8.x/database/9400)，用法与laravel相同。

当然你可以参考[使用其它数据库组件](others.md)章节使用ThinkPHP或者其它数据库。

## 安装

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper laravel/serializable-closure`

安装后需要restart重启(reload无效)

> **提示**
> 如果不需要分页、数据库事件、打印SQL，则只需要执行
> `composer require -W illuminate/database laravel/serializable-closure`

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
            'options' => [
                \PDO::ATTR_TIMEOUT => 3
            ]
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
