# 中间件
中间件一般用于拦截请求或者响应。例如执行控制器前统一验证用户身份，如用户未登录时跳转到登录页面。例如响应中增加某个header头。例如统计某个uri请求占比等等。

## 中间件洋葱模型

```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── 请求 ───────────────────────> 控制器 ─ 响应 ───────────────────────────> 客户端
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
中间件和控制器组成了一个经典的洋葱模型，中间件类似一层一层的洋葱表皮，控制器是洋葱芯。如果所示请求像箭一样穿越中间件1、2、3到达控制器，控制器返回了一个响应，然后响应又以3、2、1的顺序穿出中间件最终返回给客户端。也就是说在每个中间件里我们既可以拿到请求，也可以获得响应，这样我们就可以在中间件里做很多事情，例如拦截请求或者响应。

## 请求拦截
有时候我们不想某个请求到达控制器层，例如我们在某个身份验证中间件发现当前用户并没有登录，则我们可以直接拦截请求并返回一个登录响应。那么这个流程类似下面这样

```
                              
            ┌───────────────────────────────────────────────────────┐
            │                     middleware1                       │ 
            │     ┌───────────────────────────────────────────┐     │
            │     │          　 　 身份验证中间件                │     │
            │     │      ┌──────────────────────────────┐     │     │
            │     │      │         middleware3          │     │     │        
            │     │      │     ┌──────────────────┐     │     │     │
            │     │      │     │                  │     │     │     │
 　── 请求 ───────────┐   │     │       控制器      │     │     │     │
            │     │ 响应　│     │                  │     │     │     │
   <─────────────────┘   │     └──────────────────┘     │     │     │
            │     │      │                              │     │     │
            │     │      └──────────────────────────────┘     │     │
            │     │                                           │     │
            │     └───────────────────────────────────────────┘     │
            │                                                       │
            └───────────────────────────────────────────────────────┘
```

如图所示请求到达身份验证中间件后生成了一个登录响应，响应从身份验证中间穿越回中间件1然后返回给浏览器。

## 中间件接口
中间件必须实现`Webman\MiddlewareInterface`接口。
```php
interface MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, callable $handler): Response;
}
```
也就是必须实现`process`方法，`process`方法必须返回一个`support\Response`对象，默认这个对象由`$handler($request)`生成(请求将继续向洋葱芯穿越)，也可以可以是`response()` `json()` `xml()` `redirect()`等助手函数生成的响应(请求停止继续向洋葱芯穿越)。

 
## 示例：身份验证中间件
创建文件`app/middleware/AuthCheckTest.php` (如目录不存在请自行创建) 如下：
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AuthCheckTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $session = $request->session();
        // 用户未登录
        if (!$session->get('userinfo')) {
            // 拦截请求，返回一个重定向响应，请求停止向洋葱芯穿越
            return redirect('/user/login');
        }
        // 请求继续穿越
        return $handler($request);
    }
}
```

在 `config/middleware.php` 中添加全局中间件如下：
```php
return [
    // 全局中间件
    '' => [
        // ... 这里省略其它中间件
        app\middleware\AuthCheckTest::class,
    ]
];
```

有了身份验证中间件，我们就可以在控制器层专心的写业务代码，不用就用户是否登录而担心。

## 示例：跨域请求中间件
创建文件`app/middleware/AccessControlTest.php` (如目录不存在请自行创建) 如下：
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AccessControlTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('Origin', '*'),
            'Access-Control-Allow-Methods' => '*',
            'Access-Control-Allow-Headers' => '*',
        ]);
        
        return $response;
    }
}
```
根据洋葱模型，中间件里可以得到响应对象，我们直接调用响应对象的`withHeaders`方法给响应添加跨域的http头实现跨域。

> **提示**
> 跨域可能会产生OPTIONS请求，我们不想OPTIONS请求进入到控制器，所以我们为OPTIONS请求直接返回了一个空的响应(`response('')`)实现请求拦截。

在 `config/middleware.php` 中添加全局中间件如下：
```php
return [
    // 全局中间件
    '' => [
        // ... 这里省略其它中间件
        app\middleware\AccessControlTest::class,
    ]
];
```

> **注意**
> 跨域请求可能包含`OPTIONS`请求，如果你的跨域接口需要设置路由，请使用`Route::any(..)` 或者 `Route::add(['POST', 'OPTIONS'], ..)`设置。

> **注意**
> 如果ajax请求自定义了header头，需要在中间件里 `Access-Control-Allow-Headers` 字段加入这个自定义header头，否则会报` Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.`

> **注意**
> 如果业务发生了500则会产生跨域错误。

## 说明
  
 - 中间件分为全局中间件、应用中间件(应用中间件仅在多应用模式下有效，参见[多应用](multiapp.md))、路由中间件
 - 目前不支持单个控制器的中间件(但可以在中间件中通过判断`$request->controller`来实现类似控制器中间件功能)
 - 中间件配置文件位置在 `config/middleware.php`
 - 全局中间件配置在key `''` 下
 - 应用中间件配置在具体的应用名下，例如

```php
return [
    // 全局中间件
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // api应用中间件(应用中间件仅在多应用模式下有效)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## 路由中间件
> 注意：需要 `workerman/webman-framework` 版本 >= 1.0.12

我们可以给某个一个或某一组路由设置中间件。
例如在`config/route.php`中添加如下配置：
```php
Route::any('/admin', [app\admin\controller\Index::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($r, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

## 中间件执行顺序
 - 中间件执行顺序为`全局中间件`->`应用中间件`->`路由中间件`。
 - 有多个全局中间件时，按照中间件实际配置顺序执行(应用中间件、路由中间件同理)。
 - 404请求不会触发任何中间件，包括全局中间件
 
## 中间件向控制器传参

有时候控制器需要使用中间件里产生的数据，这时我们可以通过给`$request`对象添加属性的方式向控制器传参。例如：

**中间件：**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $request->data = 'some value';
        return $handler($request);
    }
}
```

**控制器：**
```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response($request->data);
    }
}
```

## 中间件获取当前请求路由信息
> **注意**
> 需要 webman-framework >= 1.3.2

我们可以使用 `$request->route` 获取路由对象，通过调用对应的方法获取相应信息。

**路由配置**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\User::class, 'view']);
```

**中间件：**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $route = $request->route;
        // 如果请求没有匹配任何路由(默认路由除外)，则 $request->route 为 null
        // 假设浏览器访问地址 /user/111，则会打印如下信息
        if ($route) {
            var_export($route->getPath());       // /user/{uid}
            var_export($route->getMethods());    // ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD','OPTIONS']
            var_export($route->getName());       // user_view
            var_export($route->getMiddleware()); // []
            var_export($route->getCallback());   // ['app\\controller\\User', 'view']
            var_export($route->param());         // ['uid'=>111]
            var_export($route->param('uid'));    // 111 
        }
        return $handler($request);
    }
}
```

> **注意**
> `$route->param()`方法需要 webman-framework >= 1.3.16


## 中间件获取异常
> **注意**
> 需要 webman-framework >= 1.3.15

业务处理过程中可能会产生异常，在中间件里使用 `$response->excption()` 获取异常。

**路由配置**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('exception test');
});
```

**中间件：**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $response = $handler($request);
        $excption = $response->excption();
        if ($excption) {
            echo $excption->getMessage();
        }
        return $response;
    }
}
```
