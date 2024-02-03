# Casbin アクセス制御ライブラリ webman-permission

## 説明

これは、[PHP-Casbin](https://github.com/php-casbin/php-casbin) に基づいており、`ACL`、`RBAC`、`ABAC` などのアクセス制御モデルをサポートする強力で効率的なオープンソースのアクセス制御フレームワークです。

## プロジェクトURL

https://github.com/Tinywan/webman-permission

## インストール

```php
composer require tinywan/webman-permission
```
> この拡張機能は PHP 7.1+ および [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998) が必要であり、公式マニュアルはこちら：https://www.workerman.net/doc/webman#/db/others

## 設定

### サービスの登録
以下のような内容の新しい設定ファイル `config/bootstrap.php` を作成します：

```php
    // ...
    webman\permission\Permission::class,
```

### モデルの設定ファイル
以下のような内容の新しい設定ファイル `config/casbin-basic-model.conf` を作成します：

```conf
[request_definition]
r = sub, obj, act

[policy_definition]
p = sub, obj, act

[role_definition]
g = _, _

[policy_effect]
e = some(where (p.eft == allow))

[matchers]
m = g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act
```

### ポリシーの設定ファイル
以下のような内容の新しい設定ファイル `config/permission.php` を作成します：

```php
<?php

return [
    /*
     * デフォルトの権限
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * モデルの設定
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // アダプタ 
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * データベース設定
            */
            'database' => [
                // データベース接続名、未指定の場合はデフォルトの設定
                'connection' => '',
                // ポリシーテーブル名（接頭辞を含まない）
                'rules_name' => 'rule',
                // ポリシーテーブルの完全な名前
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## クイックスタート

```php
use webman\permission\Permission;

// ユーザーに権限を追加する
Permission::addPermissionForUser('eve', 'articles', 'read');
// ユーザーにロールを追加する
Permission::addRoleForUser('eve', 'writer');
// ルールに権限を追加する
Permission::addPolicy('writer', 'articles', 'edit');
```

次のようにして、ユーザーがそのような権限を持っているかどうかを確認できます。

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // eve に記事の編集を許可する
} else {
    // リクエストを拒否してエラーを表示する
}
````

## 認可ミドルウェア

次のように、「app/middleware/AuthorizationMiddleware.php」（ディレクトリが存在しない場合は自分で作成してください）ファイルを作成します：

```php
<?php

/**
 * 認可ミドルウェア
 * @author ShaoBo Wan (Tinywan)
 * @datetime 2021/09/07 14:15
 */

declare(strict_types=1);

namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use Casbin\Exceptions\CasbinException;
use webman\permission\Permission;

class AuthorizationMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $next): Response
    {
        $uri = $request->path();
        try {
            $userId = 10086;
            $action = $request->method();
            if (!Permission::enforce((string) $userId, $uri, strtoupper($action))) {
                throw new \Exception('申し訳ございませんが、このAPIにアクセス権限がありません');
            }
        } catch (CasbinException $exception) {
            throw new \Exception('認可例外' . $exception->getMessage());
        }
        return $next($request);
    }
}
```

`config/middleware.php` にグローバルミドルウェアを以下のように追加します：

```php
return [
    // グローバルミドルウェア
    '' => [
        // ... 他のミドルウェアを省略
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## 謝辞

[Casbin](https://github.com/php-casbin/php-casbin)、その[公式ウェブサイト](https://casbin.org/)ですべてのドキュメントを確認できます。

## ライセンス

このプロジェクトは [Apache 2.0 ライセンス](LICENSE) の下でライセンスされています。
