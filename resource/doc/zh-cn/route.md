## 路由
## 默认路由规则
webman默认路由规则是 `http://127.0.0.1:8787/{控制器}/{动作}`。

默认控制器为`app\controller\IndexController`，默认动作为`index`。

例如访问：
- `http://127.0.0.1:8787` 将默认访问`app\controller\IndexController`类的`index`方法
- `http://127.0.0.1:8787/foo` 将默认访问`app\controller\FooController`类的`index`方法
- `http://127.0.0.1:8787/foo/test` 将默认访问`app\controller\FooController`类的`test`方法
- `http://127.0.0.1:8787/admin/foo/test` 将默认访问`app\admin\controller\FooController`类的`test`方法 (参考[多应用](multiapp.md))

另外webman从1.4开始支持更复杂的默认路由，例如
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

当您想改变某个请求路由时请更改配置文件 `config/route.php`。

如果你想关闭默认路由，在配置文件 `config/route.php`里最后一行加上如下配置：
```php
Route::disableDefaultRoute();
```

## 闭包路由
`config/route.php`里添加如下路由代码
```php
use support\Request;
Route::any('/test', function (Request $request) {
    return response('test');
});

```
> **注意**
> 由于闭包函数不属于任何控制器，所以`$request->app` `$request->controller` `$request->action` 全部为空字符串。

当访问地址为 `http://127.0.0.1:8787/test` 时，将返回`test`字符串。

> **注意**
> 路由路径必须以`/`开头，例如

```php
use support\Request;
// 错误的用法
Route::any('test', function (Request $request) {
    return response('test');
});

// 正确的用法
Route::any('/test', function (Request $request) {
    return response('test');
});
```


## 类路由
`config/route.php`里添加如下路由代码
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
当访问地址为 `http://127.0.0.1:8787/testclass` 时，将返回`app\controller\IndexController`类的`test`方法的返回值。


## 路由参数
如果路由中存在参数，通过`{key}`来匹配，匹配结果将传递到对应的控制器方法参数中(从第二个参数开始依次传递)，例如：
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
        return response('接收到参数'.$id);
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

// 匹配 /user /user/123 和 /user/abc   []表示可选
Route::any('/user[/{name}]', function (Request $request, $name = null) {
   return response($name ?? 'tom');
});

// 匹配 任意以/user/为前缀的请求
Route::any('/user/[{path:.+}]', function (Request $request) {
    return $request->path();
});

// 匹配所有options请求   :后填写正则表达式，表示此命名参数的正则规则
Route::options('[{path:.+}]', function () {
    return response('');
});
```
进阶用法总结

> `[]` 语法在 Webman 路由中主要用于处理可选路径部分或匹配动态路由，它让你能够为路由定义更复杂的路径结构和匹配规则
> 
> `:用于指定正则表达式`


## 路由分组

有时候路由包含了大量相同的前缀，这时候我们可以用路由分组来简化定义。例如：

```php
Route::group('/blog', function () {
   Route::any('/create', function (Request $request) {return response('create');});
   Route::any('/edit', function (Request $request) {return response('edit');});
   Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
});
```
等价与
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

## 路由中间件

我们可以给某个一个或某一组路由设置中间件。
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
> 在 webman-framework <= 1.5.6 时 `->middleware()` 路由中间件作用于 group 分组之后时候，当前路由必须在处于当前分组之下

```php
# 错误使用例子 (webman-framework >= 1.5.7 时此用法有效)
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
# 正确使用例子
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

## 资源型路由
```php
Route::resource('/test', app\controller\IndexController::class);

//指定资源路由
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

//非定义性资源路由
// 如 notify 访问地址则为any型路由 /test/notify或/test/notify/{id} 都可 routeName为 test.notify
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
> 暂时不支持group嵌套的路由生成url  

例如路由：
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
我们可以使用如下方法生成这个路由的url。
```php
route('blog.view', ['id' => 100]); // 结果为 /blog/100
```

视图里使用路由的url时可以使用此方法，这样不管路由规则如何变化，url都会自动生成，避免因路由地址调整导致大量更改视图文件的情况。


## 获取路由信息
> **注意**
> 需要 webman-framework >= 1.3.2

通过`$request->route`对象我们可以获取当前请求路由信息，例如

```php
$route = $request->route; // 等价与 $route = request()->route;
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
> 如果当前请求没有匹配到config/route.php中配置的任何路由，则`$request->route`为null，也就是说走默认路由时`$request->route`为null


## 处理404
当路由找不到时默认返回404状态码并输出`public/404.html`文件内容。

如果开发者想介入路由未找到时的业务流程，可以使用webman提供的回退路由`Route::fallback($callback)`方法。比如下面的代码逻辑是当路由未找到时重定向到首页。
```php
Route::fallback(function(){
    return redirect('/');
});
```
再比如当路由不存在时返回一个json数据，这在webman作为api接口时非常实用。
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

## 给404添加中间件

> **注意**
> 此特性需要 webman-framework >= 1.6.0

默认404请求不会走任何中间件，如果需要给404请求添加中间件，请参考以下代码。
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
````

相关链接 [自定义404 500页面](others/custom-error-page.md)

## 禁用默认路由

> **注意**
> 需要 webman-framework >= 1.6.0

```php
// 禁用主项目默认路由，不影响应用插件
Route::disableDefaultRoute();
// 禁用主项目的admin应用的路由，不影响应用插件
Route::disableDefaultRoute('', 'admin');
// 禁用foo插件的默认路由，不影响主项目
Route::disableDefaultRoute('foo');
// 禁用foo插件的admin应用的默认路由，不影响主项目
Route::disableDefaultRoute('foo', 'admin');
// 禁用控制器 [\app\controller\IndexController::class, 'index'] 的默认路由
Route::disableDefaultRoute([\app\controller\IndexController::class, 'index']);
```

## 路由接口
```php
// 设置$uri的任意方法请求的路由
Route::any($uri, $callback);
// 设置$uri的get请求的路由
Route::get($uri, $callback);
// 设置$uri的post请求的路由
Route::post($uri, $callback);
// 设置$uri的put请求的路由
Route::put($uri, $callback);
// 设置$uri的patch请求的路由
Route::patch($uri, $callback);
// 设置$uri的delete请求的路由
Route::delete($uri, $callback);
// 设置$uri的head请求的路由
Route::head($uri, $callback);
// 设置$uri的options请求的路由
Route::options($uri, $callback);
// 同时设置多种请求类型的路由
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// 分组路由
Route::group($path, $callback);
// 资源路由
Route::resource($path, $callback, [$options]);
// 禁用路由
Route::disableDefaultRoute($plugin = '');
// 回退路由，设置默认的路由兜底
Route::fallback($callback, $plugin = '');
// 获取所有路由信息
Route::getRoutes();
```
如果uri没有对应的路由(包括默认路由)，且回退路由也未设置，则会返回404。

## 多个路由配置文件
如果你想使用多个路由配置文件对路由进行管理，例如[多应用](multiapp.md)时每个应用下有自己的路由配置，这时可以通过`require`外部文件的方式加载外部路由配置文件。
例如`config/route.php`中
```php
<?php

// 加载admin应用下的路由配置
require_once app_path('admin/config/route.php');
// 加载api应用下的路由配置
require_once app_path('api/config/route.php');

```


