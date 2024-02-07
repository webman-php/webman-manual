# はじめに

webmanのデータベースはデフォルトで[illuminate/database](https://github.com/illuminate/database)、つまり[laravelのデータベース](https://learnku.com/docs/laravel/8.x/database/9400)を使用しており、laravelと同じように使用できます。

もちろん、他のデータベースコンポーネントを使用するには、[他のデータベースコンポーネントの使用](others.md)セクションを参照してください。

## インストール

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

インストール後、再起動（reloadでは無効）が必要です。

> **注**
> ページネーション、データベースイベント、SQLのプリントが不要な場合は、次のコマンドを実行してください。
> `composer require -W illuminate/database`

## データベースの設定
`config/database.php`
```php
return [
    // デフォルトデータベース
    'default' => 'mysql',

    // さまざまなデータベース設定
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'test',
            'username' => 'root',
            'password' => '',
            'unix_socket' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
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
