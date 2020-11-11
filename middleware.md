# 中间件
中间件一般用于拦截请求或者响应。例如验证用户身份，当判断用户未登录时跳转到登录页面。例如响应中增加某个header头。例如统计某个uri请求占比等等。

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
    public function process(Request $request, callable $next): Response;
}
```
也就是必须实现`process`方法，`process`方法必须返回一个`support\Response`对象，默认这个对象由`$next($request)`生成，也可以可以使用`response()` `json()` `xml()` `redirect()`等助手函数生成响应，实现响应拦截。

 
## 示例：用户身份验证中间件
创建文件`support/middleware/AuthCheckTest.php`如下：
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AuthCheckTest implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        $session = $request->session();
        if (!$session->get('userinfo')) {
            return redirect('/user/login');
        }
        return $next($request);
    }
}
```

在 `config/middleware.php` 中添加全局中间件如下：
```php
return [
    // 全局中间件
    '' => [
        // ... 这里省略其它中间件
        support\middleware\AuthCheckTest::class,
    ]
];
```

这个示例中判断当前请求的session中是否有userinfo数据，如果没有则跳转到登录页面，有的话则调用`$next($request)`继续正常流程。

> 注意：调用`$next($request)`将继续执行正常的业务流程，也就是继续调用下一个中间件(有的话)，直到调用到最终处理业务的控制器方法或函数。

## 示例：跨域请求中间件
创建文件`support/middleware/AccessControlTest.php`如下：
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AccessControlTest implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        $response = $request->method() == 'OPTIONS' ? response('') : $next($request);
        $response->withHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET,POST,PUT,DELETE,OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type,Authorization,X-Requested-With,Accept,Origin'
        ]);
        
        return $response;
    }
}
```

在 `config/middleware.php` 中添加全局中间件如下：
```php
return [
    // 全局中间件
    '' => [
        // ... 这里省略其它中间件
        support\middleware\AccessControlTest::class,
    ]
];
```

这样就允许在其它域名下调用当前站点 `/api` 开头的地址，不会报跨域错误。

## 说明
  
 - 中间件分为全局中间件和应用中间件(应用中间件仅在多应用模式下有效，参见[多应用](multiapp.md))
 - 目前不支持单个控制器的中间件(但可以在中间键中通过判断`$request->controller`来实现类似控制器中间键功能)
 - 中间件配置文件位置在 `config/middleware.php`
 - 全局中间件配置在key `''` 下
 - 应用中间件配置在具体的应用名下，例如
```php
return [
    // 全局中间件
    '' => [
        support\middleware\AuthCheckTest::class,
        support\middleware\AccessControlTest::class,
    ],
    // api应用中间件(应用中间件仅在多应用模式下有效)
    'api' => [
        support\middleware\ApiOnly::class,
    ]
];
```

## 中间件执行顺序
 - 中间件执行顺序为`全局中间件`->`应用中间件(有的话)`。
 - 有多个全局中间件时，按照中间件实际配置顺序执行。
 - 某应用中有多个应用中间件时，按照对应应用中间件实际配置顺序执行。
