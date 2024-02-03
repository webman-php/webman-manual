## ビュー
webmanではデフォルトで、テンプレートとしてPHP純正の構文を使用し、`opcache`を有効にした場合に最高のパフォーマンスを実現します。PHP純正のテンプレート以外に、webmanは[Twig](https://twig.symfony.com/doc/3.x/)、[Blade](https://learnku.com/docs/laravel/8.x/blade/9377)、[think-template](https://www.kancloud.cn/manual/think-template/content)のテンプレートエンジンも提供しています。

## opcacheの有効化
ビューを使用する際には、php.iniの`opcache.enable`と`opcache.enable_cli`の2つのオプションを有効化することを強くお勧めします。これにより、テンプレートエンジンが最高のパフォーマンスを発揮します。

## Twigのインストール
1. composerを使用してインストールします。

   `composer require twig/twig`

2. 設定ファイル`config/view.php`を以下のように変更します。
   ```php
   <?php
   use support\view\Twig;
   
   return [
       'handler' => Twig::class
   ];
   ```
   > **注意**
   > その他の設定オプションは、`options`を介して渡すことができます。例えば
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
1. composerを使用してインストールします。

   `composer require psr/container ^1.1.1 webman/blade`

2. 設定ファイル`config/view.php`を以下のように変更します。
   ```php
   <?php
   use support\view\Blade;
   
   return [
       'handler' => Blade::class
   ];
   ```

## think-templateのインストール
1. composerを使用してインストールします。

   `composer require topthink/think-template`

2. 設定ファイル`config/view.php`を以下のように変更します。
   ```php
   <?php
   use support\view\ThinkPHP;
   
   return [
       'handler' => ThinkPHP::class,
   ];
   ```
   > **注意**
   > その他の設定オプションは、`options`を介して渡すことができます。例えば
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

## 素のPHPテンプレートエンジンの例
以下のように`app/controller/UserController.php`を作成します。

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

また、`app/view/user/hello.html`を以下のように作成します。

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

また、`app/controller/UserController.php`は以下のようになります。

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

テンプレートファイル`app/view/user/hello.html`は以下のようになります。

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

さらに詳しいドキュメントは[Twig](https://twig.symfony.com/doc/3.x/)を参照してください。

## Bladeテンプレートの例
設定ファイル`config/view.php`を以下のように変更します。

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

また、`app/controller/UserController.php`は以下のようになります。

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

そして、テンプレートファイル`app/view/user/hello.blade.php`は以下のようになります。

> 注意: bladeテンプレートの拡張子は`.blade.php`となります。

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

さらに詳しいドキュメントは[Blade](https://learnku.com/docs/laravel/8.x/blade/9377)を参照してください。

## think-templateの例
設定ファイル`config/view.php`を以下のように変更します。

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

また、`app/controller/UserController.php`は以下のようになります。

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

そして、テンプレートファイル`app/view/user/hello.html`は以下のようになります。

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

さらに詳しいドキュメントは[think-template](https://www.kancloud.cn/manual/think-template/content)を参照してください。

## テンプレートへの値の割り当て
`view(テンプレート, 変数配列)`を使用してテンプレートに値を割り当てるだけでなく、任意の位置で`View::assign()`を呼び出してテンプレートに値を割り当てることもできます。例:

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

`View::assign()`は、例えばあるシステムのすべてのページのヘッダー部分に現在のログイン者情報を表示する必要がある場合などに非常に便利です。各ページで`view('テンプレート', ['user_info' => 'ユーザー情報']);`を割り当てることは非常に面倒です。この問題を解決する方法は、ミドルウェアでユーザー情報を取得し、それを`View::assign()`を使用してテンプレートに割り当てることです。

## ビューのファイルパスについて

#### コントローラー
コントローラーが`view('template_name',[]);`を呼び出すとき、ビューのファイルは次のルールに従って検索されます：

1. アプリケーションが1つの場合、`app/view/` に対応するビューファイルが使用されます。
2. [マルチアプリケーション](multiapp.md)の場合、`app/アプリ名/view/`に対応するビューファイルが使用されます。

要するに、`$request->app`が空の場合は、`app/view/`以下のビューファイルが使用されます。それ以外の場合は、`app/{$request->app}/view/`以下のビューファイルが使用されます。

#### 無名関数
無名関数は`$request->app`が空であるため、アプリケーションに属していません。そのため、`app/view/`以下のビューファイルが使用されます。例えば、`config/route.php`で次のようにルートを定義した場合、

```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
`app/view/user.html`がテンプレートファイルとして使用されます（bladeテンプレートを使用している場合は、`app/view/user.blade.php`が使用されます）。

#### アプリケーションの指定
マルチアプリケーションモードでテンプレートを共有するためには、`view('user', [], 'admin');`のように、第3の引数である`$app`を使用してどのアプリケーションディレクトリのテンプレートを使用するか指定することができます。

## Twigの拡張
> **注意**
> この機能はwebman-framework>=1.4.8が必要です。

`view.extension`にコールバックを与えることでTwigのビューインスタンスを拡張することができます。例えば`config/view.php`は次のようになります。

```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // Extensionを追加
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // Filterを追加
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // 関数を追加
    }
];
```

## Bladeの拡張
> **注意**
> この機能はwebman-framework>=1.4.8が必要です。

同様に`view.extension`にコールバックを与えることでBladeのビューインスタンスを拡張することができます。例えば`config/view.php`は次のようになります。

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Bladeにディレクティブを追加する
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## Bladeでのcomponentコンポーネントの使用

> **注**
> webman/blade>=1.5.2が必要です

例えば、Alertコンポーネントを追加する必要がある場合

**`app/view/components/Alert.php`を新規作成します**

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

**`app/view/components/alert.blade.php`を新規作成します**

```
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php`は以下のようになります**

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

以上で、BladeコンポーネントAlertの設定が完了し、テンプレート内での使用方法は次のようになります

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


## think-templateの拡張
think-templateでは`view.options.taglib_pre_load`を使用してタグライブラリを拡張します。例えば

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

詳細は[think-templateのタグ拡張](https://www.kancloud.cn/manual/think-template/1286424)を参照してください。
