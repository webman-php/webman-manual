## 路由
## 預設路由規則
webman的預設路由規則是 `http://127.0.0.1:8787/{控制器}/{動作}`。

預設控制器為 `app\controller\IndexController`，預設動作為 `index`。

例如訪問：
- `http://127.0.0.1:8787` 將預設訪問 `app\controller\IndexController` 類的 `index` 方法
- `http://127.0.0.1:8787/foo` 將預設訪問 `app\controller\FooController` 類的 `index` 方法
- `http://127.0.0.1:8787/foo/test` 將預設訪問 `app\controller\FooController` 類的 `test` 方法
- `http://127.0.0.1:8787/admin/foo/test` 將預設訪問 `app\admin\controller\FooController` 類的 `test` 方法 (參考[多應用](multiapp.md))

另外webman從1.4開始支持更複雜的預設路由，例如
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

如果你想關閉預設路由，在配置文件 `config/route.php` 裡最後一行加上如下配置：
```php
Route::disableDefaultRoute();
```

## 閉包路由
在 `config/route.php` 裡添加如下路由代碼
```php
Route::any('/test', function ($request) {
    return response('test');
});

```
> **注意**
> 由於閉包函數不屬於任何控制器，所以`$request->app` `$request->controller` `$request->action` 全部為空字符串。

當訪問地址為 `http://127.0.0.1:8787/test` 時，將返回`test`字符串。

> **注意**
> 路由路徑必須以`/`開頭，例如

```php
// 錯誤的用法
Route::any('test', function ($request) {
    return response('test');
});

// 正確的用法
Route::any('/test', function ($request) {
    return response('test');
});
```


## 類路由
`config/route.php` 裡添加如下路由代碼
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
當訪問地址為 `http://127.0.0.1:8787/testclass` 時，將返回`app\controller\IndexController` 類的`test`方法的返回值。


## 路由參數
如果路由中存在參數，通過`{key}`來匹配，匹配結果將傳遞到對應的控制器方法參數中(從第二個參數開始依次傳遞)，例如：
```php
// 匹配 /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('接收到參數'.$id);
    }
}
```

更多例子：
```php
// 匹配 /user/123, 不匹配 /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// 匹配 /user/foobar, 不匹配 /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// 匹配 /user /user/123 和 /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// 匹配所有options請求
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## 路由分組

有時候路由包含了大量相同的前綴，這時候我們可以用路由分組來簡化定義。例如：

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
}
```
等價與
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

group嵌套使用

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
}
```
## 路由中介層

我們可以為單個或一組路由設置中介層。
例如：
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

> **注意**: 
> 在 webman-framework <= 1.5.6  時 `->middleware()` 路由中间件作用于 group 分组之后時候，當前路由必須在處於當前分组之下

```php
# 錯誤使用例子 (webman-framework >= 1.5.7  時此用法有效)
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
# 正確使用例子
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


## 資源型路由
```php
Route::resource('/test', app\controller\IndexController::class);

//指定資源路由
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

//非定義性資源路由
// 如 notify 訪問地址則為any型路由 /test/notify或/test/notify/{id} 都可 routeName為 test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| 動詞   | URI                 | 行為   | 路由名稱    |
|--------|---------------------|--------|-------------|
| GET    | /test               | index  | test.index  |
| GET    | /test/create        | create | test.create |
| POST   | /test               | store  | test.store  |
| GET    | /test/{id}          | show   | test.show   |
| GET    | /test/{id}/edit     | edit   | test.edit   |
| PUT    | /test/{id}          | update | test.update |
| DELETE | /test/{id}          | destroy| test.destroy|
| PUT    | /test/{id}/recovery | recovery | test.recovery |




## Url生成
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
在視圖裡使用路由的url時可以使用此方法，這樣不管路由規則如何變化，url都會自動生成，避免因路由地址調整導致大量更改視圖檔案的情況。


## 獲取路由信息
> **注意**
> 需要 webman-framework >= 1.3.2

通過`$request->route`對象我們可以獲取當前請求路由信息，例如

```php
$route = $request->route; // 等價與 $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // 此特性需要 webman-framework >= 1.3.16
}
```

> **注意**
> 如果當前請求沒有匹配到config/route.php中配置的任何路由，則`$request->route`為null，也就是說走預設路由時`$request->route`為null


## 處理404
當路由找不到時默認返回404狀態碼並輸出`public/404.html`檔案內容。

如果開發者想介入路由未找到時的業務流程，可以使用webman提供的回退路由`Route::fallback($callback)`方法。比如下面的程式邏輯是當路由未找到時重定向到首頁。

```php
Route::fallback(function(){
    return redirect('/');
});
```
再比如當路由不存在時返回一個json資料，這在webman作為api接口時非常實用。

```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

相關連結 [自定義404 500頁面](others/custom-error-page.md)

## 路由介面
```php
// 設置$uri的任意方法請求的路由
Route::any($uri, $callback);
// 設置$uri的get請求的路由
Route::get($uri, $callback);
// 設置$uri的請求的路由
Route::post($uri, $callback);
// 設置$uri的put請求的路由
Route::put($uri, $callback);
// 設置$uri的patch請求的路由
Route::patch($uri, $callback);
// 設置$uri的delete請求的路由
Route::delete($uri, $callback);
// 設置$uri的head請求的路由
Route::head($uri, $callback);
// 同時設置多種請求類型的路由
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// 分組路由
Route::group($path, $callback);
// 資源路由
Route::resource($path, $callback, [$options]);
// 禁用路由
Route::disableDefaultRoute($plugin = '');
// 回退路由，設置預設的路由兜底
Route::fallback($callback, $plugin = '');
```
如果uri沒有對應的路由(包括預設路由)，且回退路由也未設置，則會返回404。
## 多個路由配置檔案
如果您想要使用多個路由配置檔案來管理路由，例如[多應用](multiapp.md)時每個應用程式都有自己的路由配置，這時可以通過`require`外部檔案的方式載入外部路由配置檔案。
例如在`config/route.php`中
```php
<?php

// 載入admin應用程式下的路由配置
require_once app_path('admin/config/route.php');
// 載入api應用程式下的路由配置
require_once app_path('api/config/route.php');

```
