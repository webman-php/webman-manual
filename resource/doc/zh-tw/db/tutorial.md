# 快速開始

webman資料庫預設採用[illuminate/database](https://github.com/illuminate/database)，也就是[laravel的資料庫](https://learnku.com/docs/laravel/8.x/database/9400)的用法相同。

當然你也可以參考[使用其他資料庫組件](others.md)章節使用ThinkPHP或其他資料庫。

## 安裝

執行以下指令安裝所需套件：

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

安裝完成後需執行restart重啟（reload無效）。

> **提示**
> 若不需要分頁、資料庫事件、SQL輸出，只需執行以下指令:
> `composer require -W illuminate/database`

## 資料庫配置
`config/database.php`
```php
return [
    // 默認資料庫
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