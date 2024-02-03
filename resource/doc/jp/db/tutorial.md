# はじめに

webmanのデータベースはデフォルトで[illuminate/database](https://github.com/illuminate/database)、すなわち[laravelのデータベース](https://learnku.com/docs/laravel/8.x/database/9400)を使用しており、laravelと同様の使い方です。

もちろん、[その他のデータベースコンポーネントの使用](others.md)セクションを参照して、ThinkPHPやその他のデータベースの使用も可能です。

## インストール

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

インストール後にrestart（再起動）が必要です（reloadでは無効）。  

> **注意**
> ページネーション、データベースイベント、SQLの出力を必要としない場合は、以下のコマンドだけ実行すれば良いです。
> `composer require -W illuminate/database`

## データベースの設定
`config/database.php`
```php
return [
    // デフォルトのデータベース
    'default' => 'mysql',

    // さまざまなデータベースの設定
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

## 使用方法
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
