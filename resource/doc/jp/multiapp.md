# マルチアプリケーション
プロジェクトが複数のサブプロジェクトに分かれることがあります。たとえば、ショッピングモールはショッピングモールのメインプロジェクト、ショッピングモールのAPIインターフェース、管理画面の3つのサブプロジェクトに分かれる場合があります。これらはすべて同じデータベース設定を使用します。

webmanでは、以下のようにappディレクトリをプランニングできます。
```text
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
`http://127.0.0.1:8787/shop/{controller}/{method}`のアドレスにアクセスすると、`app/shop/controller`内のコントローラーとメソッドにアクセスします。

`http://127.0.0.1:8787/api/{controller}/{method}`のアドレスにアクセスすると、`app/api/controller`内のコントローラーとメソッドにアクセスします。

`http://127.0.0.1:8787/admin/{controller}/{method}`のアドレスにアクセスすると、`app/admin/controller`内のコントローラーとメソッドにアクセスします。

さらに、webmanでは以下のようにappディレクトリをプランニングできます。
```text
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
このようにすると、`http://127.0.0.1:8787/{controller}/{method}`にアクセスすると、`app/controller`内のコントローラーとメソッドにアクセスします。パスにapiまたはadminが含まれている場合は、それぞれのディレクトリ内のコントローラーとメソッドにアクセスします。

マルチアプリケーションの場合、クラスの名前空間は`psr4`に準拠する必要があります。たとえば、`app/api/controller/FooController.php`ファイルのクラスは次のようになります。
```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
}
```

## マルチアプリケーションミドルウェアの設定
異なるアプリケーションに異なるミドルウェアを設定したい場合があります。たとえば、`api`アプリケーションにはクロスドメインミドルウェアが必要な場合や、`admin`には管理者ログインを確認するミドルウェアが必要な場合、`config/midlleware.php`の設定は次のようになります。
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
> 上記のミドルウェアは実在しない場合があります。ここではアプリケーションごとにミドルウェアをどのように設定するかを例示しています。

ミドルウェアの実行順序は `グローバルミドルウェア`->`アプリケーションミドルウェア`の順です。

ミドルウェアの開発については[ミドルウェアセクション](middleware.md)を参照してください。

## マルチアプリケーションの例外処理の設定
同様に、異なるアプリケーションごとに異なる例外処理クラスを設定したい場合があります。たとえば、`shop`アプリケーションでは例外が発生した場合にフレンドリーなメッセージを表示したい場合があります。`api`アプリケーションでは例外が発生した場合にページではなくJSON文字列を返したい場合、例外処理の設定ファイル`config/exception.php`は次のようになります。
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> ミドルウェアとは異なり、各アプリケーションには1つの例外処理クラスしか設定できません。

> 上記の例外処理クラスは実在しない場合があります。ここではアプリケーションごとに例外処理クラスをどのように設定するかを例示しています。

例外処理については、[例外処理セクション](exception.md)を参照してください。
