# 路由
## 預設路由規則
webman 的預設路由規則是 `http://127.0.0.1:8787/{控制器}/{動作}`。

預設控制器為 `app\controller\IndexController`，預設動作為 `index`。

例如訪問：
- `http://127.0.0.1:8787` 將預設訪問 `app\controller\IndexController` 類的 `index` 方法
- `http://127.0.0.1:8787/foo` 將預設訪問 `app\controller\FooController` 類的 `index` 方法
- `http://127.0.0.1:8787/foo/test` 將預設訪問 `app\controller\FooController` 類的 `test` 方法
- `http://127.0.0.1:8787/admin/foo/test` 將預設訪問 `app\admin\controller\FooController` 類的 `test` 方法 (參考[多應用](multiapp.md))

另外 webman 從 1.4 開始支援更複雜的預設路由，例如
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

當您想改變某個請求路由時請更改設定檔 `config/route.php`。

如果您想關閉預設路由，在設定檔 `config/route.php` 裡最後一行加上如下設定：
```php
Route::disableDefaultRoute();
```

## 閉包路由
在 `config/route.php` 裡添加如下路由程式碼
```php
use support\Request;
Route::any('/test', function (Request $request) {
    return response('test');
});

```
> **注意**
> 由於閉包函數不屬於任何控制器，所以 `$request->app` `$request->controller` `$request->action` 全部為空字串。

當訪問位址為 `http://127.0.0.1:8787/test` 時，將回傳 `test` 字串。

> **注意**
> 路由路徑必須以 `/` 開頭，例如

```php
use support\Request;
// 錯誤的用法
Route::any('test', function (Request $request) {
    return response('test');
});

// 正確的用法
Route::any('/test', function (Request $request) {
    return response('test');
});
```


## 類別路由
`config/route.php` 裡添加如下路由程式碼
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
當訪問位址為 `http://127.0.0.1:8787/testclass` 時，將回傳 `app\controller\IndexController` 類別的 `test` 方法的回傳值。


## 註解路由

在控制器方法上使用註解定義路由，無需在 `config/route.php` 中設定。

> **注意**
> 此功能需要 webman-framework >= v2.2.0

### 基本用法

```php
namespace app\controller;
use support\annotation\route\Get;
use support\annotation\route\Post;

class UserController
{
    #[Get('/user/{id}')]
    public function show($id)
    {
        return "user $id";
    }

    #[Post('/user')]
    public function store()
    {
        return 'created';
    }
}
```

可用註解：`#[Get]` `#[Post]` `#[Put]` `#[Delete]` `#[Patch]` `#[Head]` `#[Options]` `#[Any]`（任意方法）。路徑必須以 `/` 開頭。第二參數可指定路由名稱，用於 `route()` 產生 URL。

### 無參數註解：限制預設路由的請求方法

不帶路徑時，僅限制該動作可通過的 HTTP 方法，仍使用預設路由路徑：

```php
#[Post]
public function create() { ... }  // 僅允許 POST，路徑仍為 /user/create

#[Get]
public function index() { ... }   // 僅允許 GET
```

可組合多個註解，允許多種請求方法：

```php
#[Get]
#[Post]
public function form() { ... }  // 允許 GET 和 POST
```

未在註解中宣告的請求方法將回傳 405。

多個帶路徑的註解會註冊為多條獨立路由：`#[Get('/a')] #[Post('/b')]` 會產生 GET /a 與 POST /b 兩條路由。

### 路由群組前綴

在類別上使用 `#[RouteGroup]` 為所有方法路由添加前綴：

```php
use support\annotation\route\RouteGroup;
use support\annotation\route\Get;

#[RouteGroup('/api/v1')]
class UserController
{
    #[Get('/user/{id}')]  // 實際路徑 /api/v1/user/{id}
    public function show($id) { ... }
}
```

### 自訂請求方法與路由名稱

```php
use support\annotation\route\Route;

#[Route('/user', ['GET', 'POST'], 'user.form')]
public function form() { ... }
```

### 中介軟體

控制器或方法上的 `#[Middleware]` 會作用於註解路由，用法同 `support\annotation\Middleware`。


## 路由參數
如果路由中存在參數，透過 `{key}` 來匹配，匹配結果將傳遞到對應的控制器方法參數中（從第二個參數開始依次傳遞），例如：
```php
// 匹配 /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
use support\Request;

class UserController
{
    public function get(Request $request, $id)
    {
        return response('接收到參數'.$id);
    }
}
```

更多範例：
```php
use support\Request;
// 匹配 /user/123, 不匹配 /user/abc
Route::any('/user/{id:\d+}', function (Request $request, $id) {
    return response($id);
});

// 匹配 /user/foobar, 不匹配 /user/foo/bar
Route::any('/user/{name}', function (Request $request, $name) {
   return response($name);
});

// 匹配 /user /user/123 和 /user/abc   [] 表示可選
Route::any('/user[/{name}]', function (Request $request, $name = null) {
   return response($name ?? 'tom');
});

// 匹配 任意以 /user/ 為前綴的請求
Route::any('/user/[{path:.+}]', function (Request $request) {
    return $request->path();
});

// 匹配所有 options 請求   : 後填寫正規表示式，表示此命名參數的正規規則
Route::options('[{path:.+}]', function () {
    return response('');
});
```
進階用法總結

> `[]` 語法在 Webman 路由中主要用於處理可選路徑部分或匹配動態路由，它讓您能夠為路由定義更複雜的路徑結構和匹配規則
>
> `:` 用於指定正規表示式


## 路由分組

有時候路由包含了大量相同的前綴，這時候我們可以用路由分組來簡化定義。例如：

```php
Route::group('/blog', function () {
   Route::any('/create', function (Request $request) {return response('create');});
   Route::any('/edit', function (Request $request) {return response('edit');});
   Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
});
```
等價於
```php
Route::any('/blog/create', function (Request $request) {return response('create');});
Route::any('/blog/edit', function (Request $request) {return response('edit');});
Route::any('/blog/view/{id}', function (Request $request, $id) {return response("view $id");});
```

group 巢狀使用

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('create');});
      Route::any('/edit', function (Request $request) {return response('edit');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   });  
});
```

## 路由中介軟體

我們可以給某個或某一組路由設定中介軟體。
例如：
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# 錯誤使用範例 (webman-framework >= 1.5.7 時此用法有效)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('create');});
      Route::any('/edit', function (Request $request) {return response('edit');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# 正確使用範例
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('create');});
      Route::any('/edit', function (Request $request) {return response('edit');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```

## 資源型路由
```php
Route::resource('/test', app\controller\IndexController::class);

// 指定資源路由
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// 非定義性資源路由
// 如 notify 訪問位址則為 any 型路由 /test/notify 或 /test/notify/{id} 都可 routeName 為 test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| Verb   | URI                 | Action   | Route Name    |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |




## url 產生
> **注意**
> 暫時不支援 group 巢狀的路由產生 url

例如路由：
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
我們可以使用如下方法產生這個路由的 url。
```php
route('blog.view', ['id' => 100]); // 結果為 /blog/100
```

視圖裡使用路由的 url 時可以使用此方法，這樣不管路由規則如何變化，url 都會自動產生，避免因路由位址調整導致大量更改視圖檔案的情況。


## 取得路由資訊

透過 `$request->route` 物件我們可以取得當前請求路由資訊，例如

```php
$route = $request->route; // 等價於 $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param());
}
```

> **注意**
> 如果當前請求沒有匹配到 config/route.php 中設定的任何路由，則 `$request->route` 為 null，也就是說走預設路由時 `$request->route` 為 null


## 處理 404
當路由找不到時預設回傳 404 狀態碼並輸出 404 相關內容。

如果開發者想介入路由未找到時的業務流程，可以使用 webman 提供的回退路由 `Route::fallback($callback)` 方法。例如下面的程式碼邏輯是當路由未找到時重新導向到首頁。
```php
Route::fallback(function(){
    return redirect('/');
});
```
再例如當路由不存在時回傳一個 json 資料，這在 webman 作為 api 介面時非常實用。
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

## 給 404 添加中介軟體

預設 404 請求不會經過任何中介軟體，如果需要給 404 請求添加中介軟體，請參考以下程式碼。
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

相關連結 [自訂 404 500 頁面](others/custom-error-page.md)

## 停用預設路由

```php
// 停用主專案預設路由，不影響應用插件
Route::disableDefaultRoute();
// 停用主專案的 admin 應用的路由，不影響應用插件
Route::disableDefaultRoute('', 'admin');
// 停用 foo 插件的預設路由，不影響主專案
Route::disableDefaultRoute('foo');
// 停用 foo 插件的 admin 應用的預設路由，不影響主專案
Route::disableDefaultRoute('foo', 'admin');
// 停用控制器 [\app\controller\IndexController::class, 'index'] 的預設路由
Route::disableDefaultRoute([\app\controller\IndexController::class, 'index']);
```

## 註解停用預設路由

我們可以透過註解停用某個控制器的預設路由，例如：

```php
namespace app\controller;
use support\annotation\DisableDefaultRoute;

#[DisableDefaultRoute]
class IndexController
{
    public function index()
    {
        return 'index';
    }
}
```

同樣的，我們也可以透過註解停用某個控制器方法的預設路由，例如：

```php
namespace app\controller;
use support\annotation\DisableDefaultRoute;

class IndexController
{
    #[DisableDefaultRoute]
    public function index()
    {
        return 'index';
    }
}
```

## 路由介面
```php
// 設定 $uri 的任意方法請求的路由
Route::any($uri, $callback);
// 設定 $uri 的 get 請求的路由
Route::get($uri, $callback);
// 設定 $uri 的 post 請求的路由
Route::post($uri, $callback);
// 設定 $uri 的 put 請求的路由
Route::put($uri, $callback);
// 設定 $uri 的 patch 請求的路由
Route::patch($uri, $callback);
// 設定 $uri 的 delete 請求的路由
Route::delete($uri, $callback);
// 設定 $uri 的 head 請求的路由
Route::head($uri, $callback);
// 設定 $uri 的 options 請求的路由
Route::options($uri, $callback);
// 同時設定多種請求類型的路由
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// 分組路由
Route::group($path, $callback);
// 資源路由
Route::resource($path, $callback, [$options]);
// 停用路由
Route::disableDefaultRoute($plugin = '');
// 回退路由，設定預設的路由兜底
Route::fallback($callback, $plugin = '');
// 取得所有路由資訊
Route::getRoutes();
```
如果 uri 沒有對應的路由（包括預設路由），且回退路由也未設定，則會回傳 404。

## 多個路由設定檔
如果您想使用多個路由設定檔對路由進行管理，例如[多應用](multiapp.md)時每個應用下有自己的路由設定，這時可以透過 `require` 外部檔案的方式載入外部路由設定檔。
例如 `config/route.php` 中
```php
<?php

// 載入 admin 應用下的路由設定
require_once app_path('admin/config/route.php');
// 載入 api 應用下的路由設定
require_once app_path('api/config/route.php');
```
