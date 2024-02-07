## ビュー
webmanはデフォルトでPHPネイティブ構文をテンプレートとして使用し、`opcache`を有効にすると最高のパフォーマンスが得られます。PHPネイティブテンプレート以外にも、webmanでは[Twig](https://twig.symfony.com/doc/3.x/)、 [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)、 [think-template](https://www.kancloud.cn/manual/think-template/content)テンプレートエンジンを提供しています。

## opcacheの有効化
ビューを使用する際には、php.iniファイル内の`opcache.enable`および`opcache.enable_cli`の2つのオプションを有効にすることを強くお勧めします。これにより、テンプレートエンジンの最高のパフォーマンスが得られます。

## Twigのインストール
1. Composerを使用してインストールします。

```bash
composer require twig/twig
```

2. 設定ファイル`config/view.php`を以下のように変更します。

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
> **ヒント**
> その他の設定オプションは、`options`を介して渡すことができます。例えば、

```php
return [
    'handler' => Twig::class,
    'options' => [
        'debug' => false,
        'charset' => 'utf-8'
    ]
];
```

## Bladeのインストール
1. Composerを使用してインストールします。

```bash
composer require psr/container ^1.1.1 webman/blade
```

2. 設定ファイル`config/view.php`を以下のように変更します。

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## think-templateのインストール
1. Composerを使用してインストールします。

```bash
composer require topthink/think-template
```

2. 設定ファイル`config/view.php`を以下のように変更します。

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```
> **ヒント**
> その他の設定オプションは、`options`を介して渡すことができます。例えば、

```php
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'view_suffix' => 'html',
        'tpl_begin' => '{',
        'tpl_end' => '}'
    ]
];
```

## ネイティブPHPテンプレートエンジンの例
以下のようにファイル`app/controller/UserController.php`を作成します。

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

新しいファイル`app/view/user/hello.html`は以下のようになります。

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

## Twigテンプレートエンジンの例
設定ファイル`config/view.php`を以下のように変更します。

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

ファイル`app/controller/UserController.php`は以下のようになります。

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

ファイル`app/view/user/hello.html`は以下のようになります。

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{name}}
</body>
</html>
```

詳細なドキュメントは[Twig](https://twig.symfony.com/doc/3.x/)を参照してください。

## Bladeテンプレートの例
設定ファイル`config/view.php`を以下のように変更します。

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php`は以下のようになります。

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

ファイル`app/view/user/hello.blade.php`は以下のようになります。

> 注：bladeテンプレートの拡張子は`.blade.php`です。

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{$name}}
</body>
</html>
```

詳細なドキュメントは [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)を参照してください。

## ThinkPHPテンプレートの例
設定ファイル`config/view.php`を以下のように変更します。

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php`は以下のようになります。

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

ファイル`app/view/user/hello.html`は以下のようになります。

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {$name}
</body>
</html>
```

詳細なドキュメントは [think-template](https://www.kancloud.cn/manual/think-template/content)を参照してください。

## テンプレートへの値の割り当て
`view(テンプレート, 変数配列)`を使用してテンプレートに値を割り当てる他に、任意の場所で`View::assign()`を呼び出してテンプレートに値を割り当てることもできます。例えば、

```php
<?php
namespace app\controller;

use support\Request;
use support\View;

class UserController
{
    public function hello(Request $request)
    {
        View::assign([
            'name1' => 'value1',
            'name2'=> 'value2',
        ]);
        View::assign('name3', 'value3');
        return view('user/test', ['name' => 'webman']);
    }
}
```

`View::assign()`はいくつかのシーンで非常に便利です。例えば、あるシステムでは各ページのヘッダーに現在のログイン者情報が表示される必要があります。すべてのページで`view('テンプレート', ['user_info' => 'ユーザー情報']);`経由で情報を割り当てるのは非常に面倒です。解決策は、ユーザー情報をミドルウェアで取得し、`View::assign()`を使用してテンプレートにユーザー情報を割り当てることです。

## ビューファイルのパスについて

#### コントローラー
コントローラーが`view('テンプレート名',[]);`を呼び出すとき、ビューファイルは以下の規則に従って検索されます：

1. 複数のアプリケーションを持たない場合、`app/view/`に対応するビューファイルを使用します。
2. [複数のアプリケーション](multiapp.md)を持つ場合、`app/アプリ名/view/`に対応するビューファイルを使用します。

つまり、`$request->app`が空の場合は、`app/view/`以下のビューファイルが、空ではない場合は`app/{$request->app}/view/`以下のビューファイルが使用されます。

#### 無名関数
無名関数の場合、`$request->app`が空であり、どのアプリケーションにも属さないため、`app/view/`以下のビューファイルが使用されます。たとえば `config/route.php`でルートを定義する場合、

```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
とすると、`app/view/user.html`がテンプレートファイルとして使用されます（Bladeテンプレートを使用する場合は`app/view/user.blade.php`が使用されます）。

#### アプリの指定
複数のアプリケーションモードでテンプレートを再利用できるようにするために、`view($template, $data, $app = null)`には第三引数`$app`が提供されており、どのアプリケーションディレクトリのテンプレートを使用するかを指定できます。例えば `view('user', [], 'admin');`は`app/admin/view/`以下のビューファイルが強制的に使用されます。

## Twigの拡張

> **注意**
> この機能はwebman-framework>=1.4.8が必要です。

`view.extension`のコールバックを設定することで、Twigビューインスタンスを拡張することができます。たとえば、`config/view.php`は以下のようになります。

```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // 拡張の追加
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // フィルタの追加
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // 関数の追加
    }
];
```
## 拡張ブレード
> **注意**
> この機能には、webman-framework>=1.4.8 が必要です。
同様に、`view.extension`コールバックを設定することで、ブレードビューインスタンスを拡張することができます。たとえば、`config/view.php`は以下のようになります。

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // ブレードにディレクティブを追加する
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## ブレードコンポーネントの使用

> **注意**
> webman/blade>=1.5.2 が必要です。

警告コンポーネントを追加する必要があるとします。

**`app/view/components/Alert.php` を作成します**
```php
<?php

namespace app\view\components;

use Illuminate\View\Component;

class Alert extends Component
{
    
    public function __construct()
    {
    
    }
    
    public function render()
    {
        return view('components/alert')->rawBody();
    }
}
```

**`app/view/components/alert.blade.php`を作成します**
```php
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php` は次のコードに似ています**

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        $blade->component('alert', app\view\components\Alert::class);
    }
];
```

以上で、Alertブレードコンポーネントが設定されました。テンプレート内で使用すると次のようになります。
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>

<x-alert/>

</body>
</html>
```

## think-templateを拡張する
think-template では、`view.options.taglib_pre_load` を使用してタグライブラリを拡張できます。例えば次のようにします。

```php
<?php
use support\view\ThinkPHP;
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'taglib_pre_load' => your\namspace\Taglib::class,
    ]
];
```

詳細はこちら [think-template tag extension](https://www.kancloud.cn/manual/think-template/1286424) を参照してください。
