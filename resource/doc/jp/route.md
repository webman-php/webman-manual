## ルーティング
## デフォルトのルーティングルール
webmanのデフォルトのルーティングルールは `http://127.0.0.1:8787/{コントローラ}/{アクション}` です。

デフォルトのコントローラは `app\controller\IndexController` であり、デフォルトのアクションは `index` です。

例えば、以下のようにアクセスできます：
- `http://127.0.0.1:8787` は`app\controller\IndexController`クラスの`index`メソッドにデフォルトでアクセスします。
- `http://127.0.0.1:8787/foo` は`app\controller\FooController`クラスの`index`メソッドにデフォルトでアクセスします。
- `http://127.0.0.1:8787/foo/test` は`app\controller\FooController`クラスの`test`メソッドにデフォルトでアクセスします。
- `http://127.0.0.1:8787/admin/foo/test` は`app\admin\controller\FooController`クラスの`test`メソッドにデフォルトでアクセスします（[複数アプリケーション](multiapp.md)を参照）。

また、webmanは1.4以降、複雑なデフォルトのルーティングをサポートしています
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

リクエストルーティングを変更したい場合は、`config/route.php`の構成ファイルを変更してください。

デフォルトのルーティングを無効にしたい場合は、`config/route.php`ファイルの最終行に次の構成を追加してください：
```php
Route::disableDefaultRoute();
```

## クロージャ・ルーティング
`config/route.php`に次のようなルートコードを追加します。
```php
Route::any('/test', function ($request) {
    return response('test');
});
```
> **注意**
> クロージャ関数はどのコントローラにも属していないため、`$request->app` `$request->controller` `$request->action` はすべて空の文字列です。

アドレスが `http://127.0.0.1:8787/test`の場合、`test`の文字列が返されます。

> **注意**
> ルートのパスは`/`で始まる必要があります。例：

```php
// 間違った使い方
Route::any('test', function ($request) {
    return response('test');
});

// 正しい使い方
Route::any('/test', function ($request) {
    return response('test');
});
```

## クラス・ルーティング
`config/route.php`に次のようなルートコードを追加します。
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
`http://127.0.0.1:8787/testclass`にアクセスすると、`app\controller\IndexController`クラスの`test`メソッドの戻り値が返されます。

## ルートパラメータ
ルートにパラメータが含まれている場合、`{key}`を使用して一致させ、一致結果は対応するコントローラメソッドのパラメータに渡されます（2番目のパラメータ以降に順番に渡されます）、例：
```php
// /user/123 または /user/abc に一致
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('パラメータを受け取りました：'.$id);
    }
}
```

その他の例：
```php
// /user/123 に一致、/user/abc には一致しない
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// /user/foobar に一致、/user/foo/bar には一致しない
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// /user、/user/123、/user/abcに一致
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// すべてのoptionsリクエストに一致
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## ルートグループ
時には、多くの共通の接頭辞が含まれる場合、ルートグループを使用して定義を簡素化できます。例：

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
これは以下と同等です：
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

グループをネストして使用する例：

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
});
```

## ルートミドルウェア
1つまたは複数のルートまたはルートグループにミドルウェアを設定できます。例：

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
> webman-framework <= 1.5.6 の場合、 `->middleware()` のルートミドルウェアはグループグループの後にルートが配置されている必要があります。

```php
# 間違った使用例（webman-framework >= 1.5.7 ではこの使用が有効）
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

## リソース型ルート
```php
Route::resource('/test', app\controller\IndexController::class);

//リソースルートを指定
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

//非定義型リソースルート
// たとえば notify アクセスアドレスはどちらでも型がないルート /test/notifyまたは/test/notify/{id} routeNameは test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| 動詞   | URI                 | アクション   | ルート名称    |
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
> 現時点ではグループの中でネストしたルートのURL生成はサポートされていません。

例えば、次のようなルートがある場合：
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
次の方法でこのルートのURLを生成できます。
```php
route('blog.view', ['id' => 100]); // 結果：/blog/100
```

ビューでルートのURLを使用するには、このメソッドを使用すると、どのようにルートの規則が変更されてもURLが自動的に生成されるため、大量のビューファイルを変更する必要がなくなります。

## ルート情報の取得
> **注意**
> webman-framework >= 1.3.2が必要です

`$request->route`オブジェクトを使用すると、現在のリクエストのルート情報を取得できます。例えば
```php
$route = $request->route; // 等価：$route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // この機能は webman-framework >= 1.3.16 が必要です
}
```

> **注意**
> もし現在のリクエストがconfig/route.phpに一致するルートがない場合、`$request->route`はnullになります。つまり、デフォルトのルートがある場合、`$request->route`はnullです


## 404エラーの処理
ルートが見つからない場合、デフォルトで404ステータスコードが返され、`public/404.html`の内容が出力されます。

開発者がルートが見つからない場合のビジネスプロセスに介入したい場合は、webmanが提供するフォールバックルート`Route::fallback($callback)`メソッドを使用できます。次のコードロジックは、ルートが見つからない場合にホームページにリダイレクトします。
```php
Route::fallback(function(){
    return redirect('/');
});
```
また、ルートが存在しない場合にJSONデータを返すこともできます。これはwebmanがAPIエンドポイントとして使用される場合に非常に便利です。
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

関連リンク [カスタム404 500ページ](others/custom-error-page.md)
## ルーティングインターフェイス
```php
// 任意のメソッドで$uriのルートを設定
Route::any($uri, $callback);
// GETリクエストの$uriのルートを設定
Route::get($uri, $callback);
// POSTリクエストの$uriのルートを設定
Route::post($uri, $callback);
// PUTリクエストの$uriのルートを設定
Route::put($uri, $callback);
// PATCHリクエストの$uriのルートを設定
Route::patch($uri, $callback);
// DELETEリクエストの$uriのルートを設定
Route::delete($uri, $callback);
// HEADリクエストの$uriのルートを設定
Route::head($uri, $callback);
// 複数のリクエストタイプのルートを同時に設定
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// グループ化されたルート
Route::group($path, $callback);
// リソースルート
Route::resource($path, $callback, [$options]);
// デフォルトのルートを無効化
Route::disableDefaultRoute($plugin = '');
// フォールバックルート、デフォルトのルートを設定
Route::fallback($callback, $plugin = '');
```
$uriに対応するルート（デフォルトのルートを含む）が存在しない場合、フォールバックルートが設定されていない場合は、404が返されます。

## 複数のルート設定ファイル
複数のルート設定ファイルを使用してルートを管理したい場合、たとえば[マルチアプリケーション](multiapp.md)で、それぞれのアプリケーションに独自のルート設定がある場合は、`require`を使用して外部のルート設定ファイルを読み込むことができます。
例えば`config/route.php`内で
```php
<?php

// adminアプリケーションのルート設定をロードする
require_once app_path('admin/config/route.php');
// apiアプリケーションのルート設定をロードする
require_once app_path('api/config/route.php');
```
