# 控制器


新建控制器文件 `app\controller\Foo.php`。

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

当访问 `http://127.0.0.1:8787/foo` 时，页面返回 `hello index`。

当访问 `http://127.0.0.1:8787/foo/hello` 时，页面返回 `hello webman`。

当然你可以通过路由配置来更改路由规则，参见[路由](route.md)。

## 说明
 - 框架会自动向控制器传递`support\Request` 对象，通过它可以获取用户输入数据(get post header等数据)，参见[请求](request.md)
 - 控制器里可以返回任意字符串或者`support\Response` 对象，但是不能返回其它类型的数据。
 - `support\Response` 对象可以通过`response()` `json()` `xml()` `jsonp()` `redirect()`等助手函数创建。
 
 
## 生命周期
 - 控制器的生命周期和进程的生命周期是一致的。
 - 框架启动时就会扫描并实例化app目录下的所有控制器。
 - 控制器一旦实例化后遍会常驻内存并复用。
 - 请求不会触发控制器`__construct()`方法，因为在框架启动时就实例化了。
 - 进程退出时实例化的控制器才会被释放。
 
## 控制器钩子 beforeAction afterAction
在传统框架中，每个请求都会重复实例化控制器，每个请求都会触发控制器的`__construct()`方法，很多开发者`__construct()`方法中做一些请求前的准备工作。webman也提供了类似的解决方案，让开发者可以介入请求前以及请求后的处理流程中。

为了介入请求流程，我们可以使用[中间键](middleware.md)


1、创建文件 `app\middleware\ActionHook.php`(middleware目录不存在请自行创建)

```php
<?php
namespace app\middleware;

use support\bootstrap\Container;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
class ActionHook implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        if ($request->controller) {
            $controller = Container::get($request->controller);
            if (method_exists($controller, 'beforeAction')) {
                $before_response = call_user_func([$controller, 'beforeAction'], $request);
                if ($before_response instanceof Response) {
                    return $before_response;
                }
            }
            $response = $next($request);
            if (method_exists($controller, 'afterAction')) {
                $after_response = call_user_func([$controller, 'afterAction'], $request, $response);
                if ($after_response instanceof Response) {
                    return $after_response;
                }
            }
            return $response;
        }
        return $next($request);
    }
}
```

2、在 config/middleware.php 中添加如下配置
```php
return [
    '' => [
	    // .... 这里省略了其它配置 ....
        app\middleware\ActionHook::class,
    ]
];
```

3、这样如果 controller包含了 beforeAction 或者 afterAction方法会在请求发生时自动被调用。
例如：
```php
<?php
namespace app\controller;
use support\Request;
class Index
{
    public function beforeAction(Request $request)
    {
        echo 'beforeAction';
        // 若果想终止执行Action就直接返回Response对象，不想终止则无需return
        // return response('终止执行Action');
    }

    public function afterAction(Request $request, $response)
    {
        echo 'afterAction';
        // 如果想串改结果，可以直接返回Response对象，不想串改则无需return
        // return response('afterAction'); 
    }

    public function index(Request $request)
    {
        return response('index');
    }
}
```
 

