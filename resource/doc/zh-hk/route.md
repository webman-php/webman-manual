# 路由
## 默認路由規則
webman默認路由規則是 `http://127.0.0.1:8787/{控制器}/{動作}`。

默認控制器為`app\controller\IndexController`，默認動作為`index`。

例如訪問：
- `http://127.0.0.1:8787` 將默認訪問`app\controller\IndexController`類的`index`方法
- `http://127.0.0.1:8787/foo` 將默認訪問`app\controller\FooController`類的`index`方法
- `http://127.0.0.1:8787/foo/test` 將默認訪問`app\controller\FooController`類的`test`方法
- `http://127.0.0.1:8787/admin/foo/test` 將默認訪問`app\admin\controller\FooController`類的`test`方法 (參考[多應用](multiapp.md))

另外webman從1.4開始支持更複雜的默認路由，例如
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

當您想改變某個請求路由時請更改配置文件 `config/route.php`。

如果你想關閉默認路由，在配置文件 `config/route.php`裡最後一行加上如下配置：
```php
Route::disableDefaultRoute();
```

## 閉包路由
`config/route.php`裡添加如下路由代碼
```php
use support\Request;
Route::any('/test', function (Request $request) {
    return response('test');
});

```
> **注意**
> 由於閉包函數不屬於任何控制器，所以`$request->app` `$request->controller` `$request->action` 全部為空字符串。

當訪問地址為 `http://127.0.0.1:8787/test` 時，將返回`test`字符串。

> **注意**
> 路由路徑必須以`/`開頭，例如

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


## 類路由
`config/route.php`裡添加如下路由代碼
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
當訪問地址為 `http://127.0.0.1:8787/testclass` 時，將返回`app\controller\IndexController`類的`test`方法的返回值。


## 註解路由

在控制器方法上使用註解定義路由，無需在 `config/route.php` 中配置。

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

可用註解：`#[Get]` `#[Post]` `#[Put]` `#[Delete]` `#[Patch]` `#[Head]` `#[Options]` `#[Any]`（任意方法）。路徑必須以 `/` 開頭。第二參數可指定路由名，用於 `route()` 生成 URL。

### 無參數註解：限制默認路由的請求方法

不帶路徑時，僅限制該動作可通過的 HTTP 方法，仍使用默認路由路徑：

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

未在註解中聲明的請求方法將返回 405。

多個帶路徑的註解會註冊為多條獨立路由：`#[Get('/a')] #[Post('/b')]` 會生成 GET /a 與 POST /b 兩條路由。

### 路由組前綴

在類上使用 `#[RouteGroup]` 為所有方法路由添加前綴：

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

### 自定義請求方法與路由名

```php
use support\annotation\route\Route;

#[Route('/user', ['GET', 'POST'], 'user.form')]
public function form() { ... }
```

### 中間件

控制器或方法上的 `#[Middleware]` 會作用於註解路由，用法同 `support\annotation\Middleware`。


## 路由參數
如果路由中存在參數，通過`{key}`來匹配，匹配結果將傳遞到對應的控制器方法參數中(從第二個參數開始依次傳遞)，例如：
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

更多例子：
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

// 匹配 /user /user/123 和 /user/abc   []表示可選
Route::any('/user[/{name}]', function (Request $request, $name = null) {
   return response($name ?? 'tom');
});

// 匹配 任意以/user/為前綴的請求
Route::any('/user/[{path:.+}]', function (Request $request) {
    return $request->path();
});

// 匹配所有options請求   :後填寫正則表達式，表示此命名參數的正則規則
Route::options('[{path:.+}]', function () {
    return response('');
});
```
進階用法總結

> `[]` 語法在 Webman 路由中主要用於處理可選路徑部分或匹配動態路由，它讓你能夠為路由定義更複雜的路徑結構和匹配規則
>
> `:用於指定正則表達式`


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

group嵌套使用

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('create');});
      Route::any('/edit', function (Request $request) {return response('edit');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   });  
});
```

## 路由中間件

我們可以給某個一個或某一組路由設置中間件。
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
# 錯誤使用例子 (webman-framework >= 1.5.7 時此用法有效)
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
# 正確使用例子
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

//指定資源路由
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

//非定義性資源路由
// 如 notify 訪問地址則為any型路由 /test/notify或/test/notify/{id} 都可 routeName為 test.notify
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




## url生成
> **注意**
> 暫時不支持group嵌套的路由生成url

例如路由：
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
我們可以使用如下方法生成這個路由的url。
```php
route('blog.view', ['id' => 100]); // 結果為 /blog/100
```

視圖裡使用路由的url時可以使用此方法，這樣不管路由規則如何變化，url都會自動生成，避免因路由地址調整導致大量更改視圖文件的情況。


## 獲取路由信息

通過`$request->route`對象我們可以獲取當前請求路由信息，例如

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
> 如果當前請求沒有匹配到config/route.php中配置的任何路由，則`$request->route`為null，也就是說走默認路由時`$request->route`為null


## 處理404
當路由找不到時默認返回404狀態碼並輸出404相關內容。

如果開發者想介入路由未找到時的業務流程，可以使用webman提供的回退路由`Route::fallback($callback)`方法。比如下面的代碼邏輯是當路由未找到時重定向到首頁。
```php
Route::fallback(function(){
    return redirect('/');
});
```
再比如當路由不存在時返回一個json數據，這在webman作為api接口時非常實用。
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

## 給404添加中間件

默認404請求不會走任何中間件，如果需要給404請求添加中間件，請參考以下代碼。
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

相關鏈接 [自定義404 500頁面](others/custom-error-page.md)

## 禁用默認路由

```php
// 禁用主項目默認路由，不影響應用插件
Route::disableDefaultRoute();
// 禁用主項目的admin應用的路由，不影響應用插件
Route::disableDefaultRoute('', 'admin');
// 禁用foo插件的默認路由，不影響主項目
Route::disableDefaultRoute('foo');
// 禁用foo插件的admin應用的默認路由，不影響主項目
Route::disableDefaultRoute('foo', 'admin');
// 禁用控制器 [\app\controller\IndexController::class, 'index'] 的默認路由
Route::disableDefaultRoute([\app\controller\IndexController::class, 'index']);
```

## 註解禁用默認路由

我們可以通過註解禁用某個控制器的默認路由，例如：

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

同樣的，我們也可以通過註解禁用某個控制器方法的默認路由，例如：

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

## 路由接口
```php
// 設置$uri的任意方法請求的路由
Route::any($uri, $callback);
// 設置$uri的get請求的路由
Route::get($uri, $callback);
// 設置$uri的post請求的路由
Route::post($uri, $callback);
// 設置$uri的put請求的路由
Route::put($uri, $callback);
// 設置$uri的patch請求的路由
Route::patch($uri, $callback);
// 設置$uri的delete請求的路由
Route::delete($uri, $callback);
// 設置$uri的head請求的路由
Route::head($uri, $callback);
// 設置$uri的options請求的路由
Route::options($uri, $callback);
// 同時設置多種請求類型的路由
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// 分組路由
Route::group($path, $callback);
// 資源路由
Route::resource($path, $callback, [$options]);
// 禁用路由
Route::disableDefaultRoute($plugin = '');
// 回退路由，設置默認的路由兜底
Route::fallback($callback, $plugin = '');
// 獲取所有路由信息
Route::getRoutes();
```
如果uri沒有對應的路由(包括默認路由)，且回退路由也未設置，則會返回404。

## 多個路由配置文件
如果你想使用多個路由配置文件對路由進行管理，例如[多應用](multiapp.md)時每個應用下有自己的路由配置，這時可以通過`require`外部文件的方式加載外部路由配置文件。
例如`config/route.php`中
```php
<?php

// 加載admin應用下的路由配置
require_once app_path('admin/config/route.php');
// 加載api應用下的路由配置
require_once app_path('api/config/route.php');

```
