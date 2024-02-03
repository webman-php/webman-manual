# 言語

多言語は、[symfony/translation](https://github.com/symfony/translation) コンポーネントを使用しています。

## インストール
```
composer require symfony/translation
```

## 言語パッケージの作成
webmanでは、デフォルトで言語パッケージは`resource/translations`ディレクトリに配置されます（存在しない場合は作成してください）。ディレクトリを変更する場合は、`config/translation.php`で設定してください。
それぞれの言語には、その言語用のサブディレクトリがあり、言語の定義は通常`messages.php`に配置されます。以下は例です：
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

すべての言語ファイルは次のように配列を返します。
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## 設定

`config/translation.php`

```php
return [
    // デフォルト言語
    'locale' => 'zh_CN',
    // フォールバック言語、現在の言語で翻訳が見つからない場合は、フォールバック言語の翻訳を試みます
    'fallback_locale' => ['zh_CN', 'en'],
    // 言語ファイルの保存先ディレクトリ
    'path' => base_path() . '/resource/translations',
];
```

## 翻訳

翻訳には`trans()`メソッドを使用します。

次のように言語ファイル `resource/translations/zh_CN/messages.php` を作成してください：
```php
return [
    'hello' => '你好 世界!',
];
```

ファイル `app/controller/UserController.php` を作成してください
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // 你好 世界!
        return response($hello);
    }
}
```

`http://127.0.0.1:8787/user/get` にアクセスすると、"你好 世界!" が返されます。

## デフォルト言語の変更

言語を切り替えるには、`locale()` メソッドを使用します。

言語ファイル `resource/translations/en/messages.php` を追加してください：
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
`http://127.0.0.1:8787/user/get` にアクセスすると、"hello world!" が返されます。

また、`trans()` 関数の第4引数を使用して一時的に言語を切り替えることもできます。前の例と以下の例は同じ意味です：
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

## 各リクエストで言語を明示的に設定する
translationはシングルトンですので、すべてのリクエストがこのインスタンスを共有しています。したがって、`locale()` を使用してデフォルト言語を設定すると、このプロセスの後続のすべてのリクエストに影響を与えます。したがって、各リクエストで言語を明示的に設定する必要があります。以下はそのために使用できるミドルウェアの例です。

ファイル`app/middleware/Lang.php`を作成してください（ディレクトリが存在しない場合は作成してください）：
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

`config/middleware.php` に以下のようなグローバルミドルウェアを追加してください：
```php
return [
    // グローバルミドルウェア
    '' => [
        // ... 他のミドルウェアはここに省略されています
        app\middleware\Lang::class,
    ]
];
```


## プレースホルダの使用
時には、翻訳が必要な変数を含む情報があります。例えば
```php
trans('hello ' . $name);
```
このような場合、プレースホルダを使用して処理します。

`resource/translations/zh_CN/messages.php` を以下のように変更してください：
```php
return [
    'hello' => '你好 %name%!',
];
```
翻訳する際は、プレースホルダに対応する値を第2引数で渡します。
```php
trans('hello', ['%name%' => 'webman']); // 你好 webman!
```

## 複数形の処理
いくつかの言語では、物の数量によって文の形が異なります。例えば、`There is %count% apple`、`%count%`が1の場合は文の形が正しく、1より大きい場合は間違っています。

このような場合には、複数形の形に関して**パイプ**(`|`)を使用します。

言語ファイル `resource/translations/en/messages.php` に`apple_count`を追加してください：
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

その上、数値の範囲を指定し、より複雑な複数形の規則を作成することもできます：
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## 言語ファイルの指定

言語ファイルのデフォルト名は`messages.php`ですが、実際には他の名前の言語ファイルを作成することもできます。

`resource/translations/zh_CN/admin.php` を以下のように作成してください：
```php
return [
    'hello_admin' => '你好 管理员!',
];
```

`trans()`の第3引数を使用して言語ファイルを指定できます（`.php`拡張子は省略）。
```php
trans('hello', [], 'admin', 'zh_CN'); // 你好 管理员!
```

## その他の情報
[symfony/translationマニュアル](https://symfony.com/doc/current/translation.html)を参照してください
