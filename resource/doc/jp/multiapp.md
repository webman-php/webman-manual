＃ マルチアプリケーション
プロジェクトが複数のサブプロジェクトに分割されることがあります。例えば、ショッピングモールはショッピングモールの本プロジェクト、ショッピングモールのAPIインターフェース、ショッピングモールの管理画面の3つのサブプロジェクトに分割される場合があります。それらはすべて同じデータベース設定を使用します。

webmanでは、アプリケーションディレクトリをこのように計画することができます：
```
app
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
`http://127.0.0.1:8787/shop/{controller}/{method}`にアクセスすると、`app/shop/controller`のコントローラーとメソッドにアクセスします。

`http://127.0.0.1:8787/api/{controller}/{method}`にアクセスすると、`app/api/controller`のコントローラーとメソッドにアクセスします。

`http://127.0.0.1:8787/admin/{controller}/{method}`にアクセスすると、`app/admin/controller`のコントローラーとメソッドにアクセスします。

webmanでは、アプリケーションディレクトリを以下のように計画することさえできます。
```
app
├── controller
├── model
├── view
│
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```

このようにアクセスすると、`app/controller`のコントローラーとメソッドにアクセスします。パスにapiまたはadminが含まれる場合、対応するディレクトリ内のコントローラーとメソッドにアクセスします。

マルチアプリケーションの場合、クラスの名前空間は`psr4`に従う必要があります。例えば、`app/api/controller/FooController.php`ファイルは以下のようになります：

```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## マルチアプリケーションミドルウェアの設定
時々、異なるアプリケーションに異なるミドルウェアを設定したい場合があります。例えば、`api`アプリケーションではクロスドメインミドルウェアが必要な場合がありますし、`admin`では管理者のログインをチェックするミドルウェアが必要な場合があります。そのため、`config/midlleware.php`の設定は次のようになるかもしれません：
```php
return [
    // グローバルミドルウェア
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // apiアプリケーションのミドルウェア
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // adminアプリケーションのミドルウェア
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> 上記のミドルウェアは実際に存在しないかもしれません。これは単なる例であり、アプリケーションごとにミドルウェアをどのように設定するかを説明するためのものです。

ミドルウェアの実行順序は `グローバルミドルウェア`->`アプリケーションミドルウェア` です。

ミドルウェアの開発は[ミドルウェアセクション](middleware.md)を参照してください。

## マルチアプリケーション例外処理の設定
同様に、異なるアプリケーションに異なる例外処理クラスを設定したい場合があります。例えば、`shop`アプリケーションでエラーが発生した際にはフレンドリーなメッセージページを提供したいかもしれません。`api`アプリケーションではページではなくJSON文字列を返したいかもしれません。異なるアプリケーションに異なる例外処理クラスを設定する場合、設定ファイル`config/exception.php`は次のようになります：
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> ミドルウェアとは異なり、各アプリケーションには1つの例外処理クラスしか設定できません。

> 上記の例外処理クラスは実際に存在しないかもしれません。これは単なる例であり、アプリケーションごとに例外処理をどのように設定するかを説明するためのものです。

例外処理の開発は[例外処理セクション](exception.md)を参照してください。
