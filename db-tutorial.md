# 快速开始

webman数据库采用的[illuminate/database](https://github.com/illuminate/database)。

## 安装
`composer require illuminate/database`

## 数据库配置
`config/database.php`
```php
return [

    // 默认数据库
    'default' => 'mysql',

    // 各种数据库配置
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'test'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
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