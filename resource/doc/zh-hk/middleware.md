# 中間件
中間件一般用於攔截請求或者響應。例如執行控制器前統一驗證使用者身份，如使用者未登錄時跳轉到登錄頁面，例如響應中增加某個header頭。例如統計某個uri請求占比等等。

## 中間件洋蔥模型
```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── 請求 ───────────────────────> 控制器 ─ 響應 ───────────────────────────> 客戶端
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
中間件和控制器組成了一個經典的洋蔥模型，中間件類似一層一層的洋蔥表皮，控制器是洋蔥心。如圖所示請求像箭一樣穿越中間件1、2、3到達控制器，控制器返回了一個響應，然後響應又以3、2、1的順序穿出中間件最終返回給客戶端。也就是說在每個中間件裡我們既可以拿到請求，也可以獲得響應。

## 請求攔截
有時候我們不想某個請求到達控制器層，例如我們在某個身份驗證中間件發現當前使用者並沒有登錄，則我們可以直接攔截請求並返回一個登錄響應。那麼這個流程類似下面這樣

```
                              
            ┌───────────────────────────────────────────────────────┐
            │                     middleware1                       │ 
            │     ┌───────────────────────────────────────────┐     │
            │     │          　 　 身份驗證中間件                │     │
            │     │      ┌──────────────────────────────┐     │     │
            │     │      │         middleware3          │     │     │       
            │     │      │     ┌──────────────────┐     │     │     │
            │     │      │     │                  │     │     │     │
 　── 請求 ───────────┐   │     │       控制器      │     │     │     │
            │     │ 響應　│     │                  │     │     │     │
   <─────────────────┘   │     └──────────────────┘     │     │     │
            │     │      │                              │     │     │
            │     │      └──────────────────────────────┘     │     │
            │     │                                           │     │
            │     └───────────────────────────────────────────┘     │
            │                                                       │
            └───────────────────────────────────────────────────────┘
```
如圖所示請求到達身份驗證中間件後生成了一個登錄響應，響應從身份驗證中間穿越回中間件1然後返回給瀏覽器。

## 中間件接口
中間件必須實現`Webman\MiddlewareInterface`接口。
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
也就是必須實現`process`方法，`process`方法必須返回一個`support\Response`對象，默認這個對象由`$handler($request)`生成(請求將繼續向洋蔥心穿越)，也可以是`response()` `json()` `xml()` `redirect()`等助手函數生成的響應(請求停止繼續向洋蔥心穿越)。

## 中間件中獲取請求及響應
在中間件中我們可以獲得請求，也可以獲得執行控制器後的響應，所以中間件內部分為三個部分。
1. 請求穿越階段，也就是請求處理前的階段  
2. 控制器處理請求階段，也就是請求處理階段  
3. 響應穿出階段，也就是請求處理後的階段  

三個階段在中間件裡的體現如下
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Test implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        echo '這裡是請求穿越階段，也就是請求處理前';
        
        $response = $handler($request); // 繼續向洋蔥心穿越，直至執行控制器得到響應
        
        echo '這裡是響應穿出階段，也就是請求處理後';
        
        return $response;
    }
}
```
 
## 示例：身份驗證中間件
創建文件`app/middleware/AuthCheckTest.php` (如目錄不存在請自行創建) 如下：
```php
<?php
namespace app\middleware;

use ReflectionClass;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AuthCheckTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        if (session('user')) {
            // 已經登錄，請求繼續向洋蔥心穿越
            return $handler($request);
        }

        // 通過反射獲取控制器哪些方法不需要登錄
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // 訪問的方法需要登錄
        if (!in_array($request->action, $noNeedLogin)) {
            // 攔截請求，返回一個重定向響應，請求停止向洋蔥心穿越
            return redirect('/user/login');
        }

        // 不需要登錄，請求繼續向洋蔥心穿越
        return $handler($request);
    }
}
```

新建控制器 `app/controller/UserController.php`
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * 不需要登錄的方法
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request)
    {
        $request->session()->set('user', ['id' => 10, 'name' => 'webman']);
        return json(['code' => 0, 'msg' => 'login ok']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'ok', 'data' => session('user')]);
    }
}
```

> **注意**
> `$noNeedLogin`裡記錄了當前控制器不需要登錄就可以訪問的方法

在 `config/middleware.php` 中添加全局中間件如下：
```php
return [
    // 全局中間件
    '' => [
        // ... 這裡省略其它中間件
        app\middleware\AuthCheckTest::class,
    ]
];
```

有了身份驗證中間件，我們就可以在控制器層專心的寫業務代碼，不用就使用者是否登錄而擔心。

## 示例：跨域請求中間件
創建文件`app/middleware/AccessControlTest.php` (如目錄不存在請自行創建) 如下：
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
        // 如果是options請求則返回一個空響應，否則繼續向洋蔥心穿越，並得到一個響應
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // 給響應添加跨域相關的http頭
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', '*'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
        ]);
        
        return $response;
    }
}
```

> **提示**
> 跨域可能會產生OPTIONS請求，我們不想OPTIONS請求進入到控制器，所以我們為OPTIONS請求直接返回了一個空的響應(`response('')`)實現請求攔截。
> 如果你的接口需要設置路由，請使用`Route::any(..)` 或者 `Route::add(['POST', 'OPTIONS'], ..)`設置。

在 `config/middleware.php` 中添加全局中間件如下：
```php
return [
    // 全局中間件
    '' => [
        // ... 這裡省略其它中間件
        app\middleware\AccessControlTest::class,
    ]
];
```

> **注意**
> 如果ajax請求定義了header頭，需要在中間件裡 `Access-Control-Allow-Headers` 字段加入這個自定義header頭，否則會報 `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.`


## 說明
  
 - 中間件分為全局中間件、應用中間件(應用中間件僅在多應用模式下有效，參見[多應用](multiapp.md))、路由中間件
 - 目前不支持單個控制器的中間件(但可以在中間件中通過判斷`$request->controller`來實現類似控制器中間件功能)
 - 中間件配置文件位置在 `config/middleware.php`
 - 全局中間件配置在key `''` 下
 - 應用中間件配置在具體的應用名下，例如

```php
return [
    // 全局中間件
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // api應用中間件(應用中間件僅在多應用模式下有效)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## 路由中間件

我們可以給某個一個或某一組路由設置中間件。
例如在`config/route.php`中添加如下配置：
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
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

## 中間件構造函數傳參

> **注意**
> 此特性需要 webman-framework >= 1.4.8

1.4.8版本之後，配置文件支持直接實例化中間件或者匿名函數，這樣可以方便的通過構造函數向中間件傳參。
例如`config/middleware.php`裡也可以這樣配置
```
return [
    // 全局中間件
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // api應用中間件(應用中間件僅在多應用模式下有效)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

同理路由中間件也可以通過構造函數向中間件傳遞參數，例如`config/route.php`裡
```
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## 中間件執行順序
 - 中間件執行順序為`全局中間件`->`應用中間件`->`路由中間件`。
 - 有多個全局中間件時，按照中間件實際配置順序執行(應用中間件、路由中間件同理)。
 - 404請求不會觸發任何中間件，包括全局中間件

## 路由向中間件傳參(route->setParams)

**路由配置 `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**中間件(假設為全局中間件)**
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
        // 默認路由 $request->route 為null，所以需要判斷 $request->route 是否為空
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

 
## 中間件向控制器傳參

有時候控制器需要使用中