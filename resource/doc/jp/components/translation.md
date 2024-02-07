# 言語

言語には [symfony/translation](https://github.com/symfony/translation) コンポーネントが使用されています。

## インストール
```bash
composer require symfony/translation
```

## 言語パッケージの作成
webman はデフォルトで言語パッケージを `resource/translations` ディレクトリに配置します（ディレクトリが存在しない場合は作成してください）。ディレクトリを変更する場合は、`config/translation.php` で設定します。
各言語には、そのサブフォルダに対応し、言語の定義はデフォルトで `messages.php` に配置されます。次のようになります：
```plaintext
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

すべての言語ファイルは、以下のように配列を返します：
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## 構成

`config/translation.php` に以下を設定します。

```php
return [
    // デフォルト言語
    'locale' => 'zh_CN',
    // フォールバック言語。現在の言語で翻訳が見つからない場合、フォールバック言語の翻訳を試みます
    'fallback_locale' => ['zh_CN', 'en'],
    // 言語ファイルを格納するフォルダ
    'path' => base_path() . '/resource/translations',
];
```

## 翻訳

翻訳には `trans()` メソッドを使用します。

次のように言語ファイル `resource/translations/zh_CN/messages.php` を作成します：
```php
return [
    'hello' => '你好世界！',
];
```

ファイル `app/controller/UserController.php` を作成します：
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // 你好世界！
        return response($hello);
    }
}
```

`http://127.0.0.1:8787/user/get` にアクセスすると、「你好世界！」が返されます。

## デフォルト言語を変更する

言語を変更するには `locale()` メソッドを使用します。

次のように言語ファイル `resource/translations/en/messages.php` を作成します：
```php
return [
    'hello' => 'hello world!',
];
```

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // 言語を切り替える
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
`http://127.0.0.1:8787/user/get` にアクセスすると、「hello world!」が戻ります。

また、`trans()` 関数の第4引数を使用して一時的に言語を切り替えることもできます。例えば、上記の例と以下の例は等価です：
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // 第4引数で言語を切り替える
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## 各リクエストで明示的に言語を設定する
translation はシングルトンのため、すべてのリクエストがこのインスタンスを共有します。そのため、`locale()` を使用してデフォルト言語を設定すると、そのプロセスの後続するすべてのリクエストに影響を与えます。そのため、各リクエストで言語を明示的に設定する必要があります。以下はそのためのミドルウェアの使用例です。

ファイル `app/middleware/Lang.php` を作成します（ディレクトリが存在しない場合は作成してください）：
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        locale(session('lang', 'zh_CN'));
        return $handler($request);
    }
}
```

`config/middleware.php` に以下のように、グローバルミドルウェアを追加します：
```php
return [
    // グローバルミドルウェア
    '' => [
        // ... 他のミドルウェアは省略
        app\middleware\Lang::class,
    ]
];
```

## プレースホルダの使用
時々、メッセージに翻訳が必要な変数が含まれていることがあります。例えば、
```php
trans('hello ' . $name);
```
このような場合はプレースホルダを使用して処理します。

`resource/translations/zh_CN/messages.php` を以下のように変更します：
```php
return [
    'hello' => '你好 %name%！',
```
翻訳時には、プレースホルダとその値を2番目のパラメータで渡します。
```php
trans('hello', ['%name%' => 'webman']); // 你好 webman!
```

## 複数の扱い
何種類かの言語は、数量によって異なる文が表示されます。例えば、`There is %count% apple` は `%count%` が1の場合正しい文になり、1より多い場合は間違った文になります。

このような場合、パイプ（`|`）で複数形を表記します。

言語ファイル `resource/translations/en/messages.php` に `apple_count` 項目を追加します:
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

さらに、数値の範囲を指定し、より複雑な複数形を作成することも可能です：
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples',
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## 言語ファイルの指定

言語ファイルのデフォルト名は `messages.php` ですが、実際には他の名前の言語ファイルを作成することができます。

`resource/translations/zh_CN/admin.php` を以下のように作成します：
```php
return [
    'hello_admin' => '你好 管理员！',
];
```

`trans()` の第三引数を使用して言語ファイルを指定できます（.php 拡張子は省略）。
```php
trans('hello', [], 'admin', 'zh_CN'); // 你好 管理员！
```

## その他の情報
詳細は [symfony/translation マニュアル](https://symfony.com/doc/current/translation.html) を参照してください。
