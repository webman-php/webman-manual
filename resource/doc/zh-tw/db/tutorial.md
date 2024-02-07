# 快速開始

webman資料庫預設採用 [illuminate/database](https://github.com/illuminate/database)，也就是 [laravel的資料庫](https://learnku.com/docs/laravel/8.x/database/9400)，使用方法與laravel相同。

當然你可以參考 [使用其他資料庫組件](others.md) 章節使用ThinkPHP或其他資料庫。

## 安裝

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

安裝後需要restart重啟(reload無效)

> **提示**
> 如果不需要分頁、資料庫事件、列印SQL，則只需要執行
> `composer require -W illuminate/database`

## 資料庫配置
`config/database.php`
```php

return [
    // 預設資料庫
    'default' => 'mysql',

    // 各種資料庫配置
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
