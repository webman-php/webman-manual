# Casbinアクセス制御ライブラリ webman-permission

## 説明

これは [PHP-Casbin](https://github.com/php-casbin/php-casbin) を基にした強力で効率的なオープンソースのアクセス制御フレームワークであり、`ACL`、`RBAC`、`ABAC`などのアクセス制御モデルをサポートしています。

## プロジェクトアドレス

https://github.com/Tinywan/webman-permission

## インストール

```php
composer require tinywan/webman-permission
```
> この拡張機能はPHP 7.1+および[ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998)が必要です。公式マニュアル：https://www.workerman.net/doc/webman#/db/others

## 設定

### サービスの登録
`config/bootstrap.php` という新しい構成ファイルを作成し、次のような内容にします：

```php
// ...
webman\permission\Permission::class,
```

### モデル構成ファイル

`config/casbin-basic-model.conf` という新しい構成ファイルを作成し、次のような内容にします：

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

### ポリシー構成ファイル

`config/permission.php` という新しい構成ファイルを作成し、次のような内容にします：

```php
<?php

return [
    /*
     * デフォルトのアクセス権限
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * モデル設定
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // アダプター
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * データベース設定
            */
            'database' => [
                // データベース接続名、未記入の場合はデフォルト設定
                'connection' => '',
                // ポリシーテーブル名（接頭辞なし）
                'rules_name' => 'rule',
                // ポリシーテーブルの完全な名前
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## 早速始めましょう

```php
use webman\permission\Permission;

// ユーザーに権限を追加
Permission::addPermissionForUser('eve', 'articles', 'read');
// ユーザーに役割を追加
Permission::addRoleForUser('eve', 'writer');
// ルールに権限を追加
Permission::addPolicy('writer', 'articles','edit');
```

次のように、ユーザーがそのような権限を持っているかどうかを確認できます。

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // eveに記事の編集を許可
} else {
    // リクエストを拒否し、エラーを表示
}
````

## 認可ミドルウェア

`app/middleware/AuthorizationMiddleware.php`（ディレクトリが存在しない場合は作成してください）を次のように作成します：

```php
<?php

/**
 * 認可ミドルウェア
 * @Author ShaoBo Wan (Tinywan)
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
				throw new \Exception('申し訳ありませんが、このAPIにアクセス権限がありません');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('認可エラー'. $exception->getMessage());
		}
		return $next($request);
	}
}
```

`config/middleware.php` に次のようにグローバルミドルウェアを追加します：

```php
return [
    // グローバルミドルウェア
    '' => [
        // ... 他のミドルウェアは省略
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## 謝辞

[Casbin](https://github.com/php-casbin/php-casbin)、すべてのドキュメントは[公式ウェブサイト](https://casbin.org/)でご覧いただけます。

## ライセンス

このプロジェクトは[Apache 2.0ライセンス](LICENSE)のもとでライセンスされています。
