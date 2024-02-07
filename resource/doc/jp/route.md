## ルーティング
## デフォルトのルーティングルール
webmanのデフォルトのルーティングルールは `http://127.0.0.1:8787/{controller}/{action}`です。

デフォルトのコントローラは `app\controller\IndexController` で、デフォルトのアクションは `index` です。

例えば：
- `http://127.0.0.1:8787` は `app\controller\IndexController` クラスの `index` メソッドにデフォルトでアクセスします。
- `http://127.0.0.1:8787/foo` は `app\controller\FooController` クラスの `index` メソッドにデフォルトでアクセスします。
- `http://127.0.0.1:8787/foo/test` は `app\controller\FooController` クラスの `test` メソッドにデフォルトでアクセスします。
- `http://127.0.0.1:8787/admin/foo/test` は `app\admin\controller\FooController` クラスの `test` メソッドにデフォルトでアクセスします（[multiple](multiapp.md)を参照）。

また、webmanは1.4からより複雑なデフォルトのルーティングをサポートしています。例えば
```php
app
├── admin
│   └── v1
│       └── v2
│           └── v3
│               └── controller
│                   └── IndexController.php
└── controller
    ├── v1
    │   └── IndexController.php
    └── v2
        └── v3
            └── IndexController.php
```

特定のリクエストのルーティングを変更したい場合は、`config/route.php` の設定ファイルを変更してください。

デフォルトのルーティングを無効にしたい場合は、`config/route.php` ファイルの最後の行に以下の設定を追加してください：
```php
Route::disableDefaultRoute();
```

## クロージャルーティング
`config/route.php` に以下のようなルーティングコードを追加してください。
```php
Route::any('/test', function ($request) {
    return response('test');
});
```

> **注意**
> クロージャ関数はどのコントローラにも属さないため、`$request->app` `$request->controller` `$request->action` はすべて空の文字列です。

`http://127.0.0.1:8787/test` にアクセスすると、`test` の文字列が返されます。

> **注意**
> ルーティングパスは `/` で始まる必要があります。例：
```php
// 間違った使用法
Route::any('test', function ($request) {
    return response('test');
});

// 正しい使用法
Route::any('/test', function ($request) {
    return response('test');
});
```

## クラスルーティング
`config/route.php` に以下のようなルーティングコードを追加してください。
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```

`http://127.0.0.1:8787/testclass` にアクセスすると、`app\controller\IndexController` クラスの `test` メソッドの返り値が返されます。

## ルーティングパラメータ
ルーティングにパラメータが含まれている場合、 `{key}` を使用してマッチングし、マッチング結果は対応するコントローラーメソッドパラメータに渡されます（2番目のパラメータから順に渡されます）。例：
```php
// /user/123 および /user/abc にマッチ
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('パラメータを受け取りました'.$id);
    }
}
```

さらに例：
```php
// /user/123 にマッチ、/user/abc にはマッチしない
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// /user/foobar にマッチ、/user/foo/bar にはマッチしない
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// /user、/user/123 、および /user/abc にマッチ
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// すべてのオプションリクエストにマッチ
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## ルーティンググループ
時にはルーティングには多くの共通の接頭辞が含まれていることがあります。このような場合は、ルーティンググループを使用して定義を簡素化できます。例：
```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
次のように同じものになります：
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

グループのネスト使用例：
```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
});
```

## ルーティングミドルウェア
特定の1つまたはグループのルーティングにミドルウェアを設定することができます。
例えば：
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

> **注意**：
> webman-framework <= 1.5.6 では `->middleware()` ルーティングのミドルウェアは、グループの後にルートがある場合にのみ有効です。

```php
# 間違った使用例 (webman-framework >= 1.5.7 からこの方法が有効になりました)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# 正しい使用例
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```
## リソースルート

```php
Route::resource('/test', app\controller\IndexController::class);

// リソースルートの指定
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// 未定義のリソースルート
// たとえば、notifyにアクセスすると、any型のルート /test/notifyまたは/test/notify/{id} のいずれかを使用できます。routeNameはtest.notifyです。
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| 動詞   | URI                 | アクション   | ルート名    |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |

## URL生成
> **注意** 
> 現時点では、グループにネストされたルートのURL生成はサポートされていません。  

たとえば、次のようなルートがあるとします。
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
このルートのURLを生成するには、次のようにします。
```php
route('blog.view', ['id' => 100]); // 結果は /blog/100 となります
```
ビューでルートのURLを使用する場合、この方法を使用すると、ルート規則が変わってもURLが自動的に生成されるため、多くのビューファイルを変更する必要がありません。

## ルート情報の取得
> **注意**
> webman-framework >= 1.3.2が必要です。

`$request->route`オブジェクトを使用すると、現在のリクエストのルート情報を取得できます。たとえば、

```php
$route = $request->route; // $route = request()->route;と同等です。
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // この機能には、webman-framework >= 1.3.16が必要です。
}
```
> **注意**
> 現在のリクエストがconfig/route.phpに設定されたどのルートにも一致しない場合、`$request->route`はnullになります。つまり、デフォルトのルートをたどる場合には、`$request->route`はnullになります。


## 404エラーの処理
ルートが見つからない場合、デフォルトでは404のステータスコードが返され、`public/404.html`ファイルの内容が出力されます。

開発者がルートが見つからない場合のビジネスプロセスに介入したい場合は、webmanが提供するフォールバックルート`Route::fallback($callback)`メソッドを使用できます。たとえば、以下のコードのロジックは、ルートが見つからない場合にホームページにリダイレクトします。
```php
Route::fallback(function(){
    return redirect('/');
});
```
また、ルートが存在しない場合にJSONデータを返すこともできます。これは、webmanがAPIエンドポイントとして使用される場合に非常に便利です。
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

関連リンク [カスタム404 500ページ](others/custom-error-page.md)

## ルートインターフェイス
```php
// $uriの任意のメソッドリクエストのルートを設定します。
Route::any($uri, $callback);
// $uriのgetリクエストのルートを設定します。
Route::get($uri, $callback);
// $uriのリクエストのルートを設定します。
Route::post($uri, $callback);
// $uriのputリクエストのルートを設定します。
Route::put($uri, $callback);
// $uriのpatchリクエストのルートを設定します。
Route::patch($uri, $callback);
// $uriのdeleteリクエストのルートを設定します。
Route::delete($uri, $callback);
// $uriのheadリクエストのルートを設定します。
Route::head($uri, $callback);
// 複数のリクエスト種別のルートを同時に設定します。
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// グループルート
Route::group($path, $callback);
// リソースルート
Route::resource($path, $callback, [$options]);
// デフォルトルートを無効にする
Route::disableDefaultRoute($plugin = '');
// フォールバックルート、デフォルトのルートを設定
Route::fallback($callback, $plugin = '');
```
もしuriに対応するルート（デフォルトのルートを含む）がなく、フォールバックルートも設定されていない場合、404が返されます。

## 複数のルート構成ファイル
ルートを複数の構成ファイルで管理したい場合は、たとえば[複数のアプリ](multiapp.md)の場合は、各アプリで独自のルート構成ファイルを持つようにしたいかもしれません。この場合は、外部のルート構成ファイルを`require`することで読み込むことができます。
たとえば、`config/route.php`では、次のようになります。
```php
<?php

// adminアプリのルート構成を読み込む
require_once app_path('admin/config/route.php');
// apiアプリのルート構成を読み込む
require_once app_path('api/config/route.php');

```
