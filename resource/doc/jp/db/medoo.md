## Medoo

Medooは軽量なデータベース操作プラグインです。[Medoo公式ウェブサイト](https://medoo.in/)。

## インストール
`composer require webman/medoo`

## データベースの設定
設定ファイルの場所は `config/plugin/webman/medoo/database.php` です。

## 使用例
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Medoo\Medoo;

class Index
{
    public function index(Request $request)
    {
        $user = Medoo::get('user', '*', ['uid' => 1]);
        return json($user);
    }
}
```

> **ヒント**
> `Medoo::get('user', '*', ['uid' => 1]);`
> は
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## 複数のデータベースの設定

**設定**  
`config/plugin/webman/medoo/database.php` に新しい設定を追加し、キーは任意のものを使用します。ここでは `other` を使用しています。

```php
<?php
return [
    'default' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
    //ここでotherの設定を追加
    'other' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
];
```

**使用例**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## 詳細なドキュメント
[Medoo公式ドキュメント](https://medoo.in/api/select) を参照してください。
