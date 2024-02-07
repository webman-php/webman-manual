# 中間件
中間件一般用於攔截請求或者響應。例如執行控制器前統一驗證使用者身份，如使用者未登錄時跳轉到登錄頁面，例如響應中增加某個header頭。例如統計某個uri請求佔比等等。

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
   ── Reqeust ────────────────────> Controller ─ Response ───────────────────────────> Client
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
中間件和控制器組成了一個經典的洋蔥模型，中間件類似一層一層的洋蔥表皮，控制器是洋蔥芯。如圖所示請求像箭一樣穿越中間件1、2、3到達控制器，控制器返回了一個響應，然後響應又以3、2、1的順序穿出中間件最終返回給客戶端。也就是說在每個中間件裡我們既可以拿到請求，也可以獲得響應。

## 請求攔截
有時候我們不想某個請求到達控制器層，例如我們在middleware2發現當前使用者並沒有登錄，則我們可以直接攔截請求並返回一個登錄響應。那麼這個流程類似下面這樣

```
                              
            ┌────────────────────────────────────────────────────────────┐
            │                         middleware1                        │ 
            │     ┌────────────────────────────────────────────────┐     │
            │     │                   middleware2                  │     │
            │     │          ┌──────────────────────────────┐      │     │
            │     │          │        middleware3           │      │     │       
            │     │          │    ┌──────────────────┐      │      │     │
            │     │          │    │                  │      │      │     │
   ── Reqeust ─────────┐     │    │    Controller    │      │      │     │
            │     │ Response │    │                  │      │      │     │
   <───────────────────┘     │    └──────────────────┘      │      │     │
            │     │          │                              │      │     │
            │     │          └──────────────────────────────┘      │     │
            │     │                                                │     │
            │     └────────────────────────────────────────────────┘     │
            │                                                            │
            └────────────────────────────────────────────────────────────┘
```

如圖所示請求到達middleware2後生成了一個登錄響應，響應從middleware2穿越回中間件1然後返回給客戶端。
## 中間件介面
中間件必須實現`Webman\MiddlewareInterface`介面。
```php
interface MiddlewareInterface
{
    /**
     * 處理傳入的伺服器請求
     *
     * 處理傳入的伺服器請求以產生回應。
     * 如果無法自行產生回應，則可以將其委派給提供的請求處理程序來執行。
     */
    public function process(Request $request, callable $handler): Response;
}
```
也就是必須實現`process`方法，`process`方法必須返回一個`support\Response`物件，預設這個物件由`$handler($request)`生成(請求將繼續向下傳遞)，也可以是由`response()` `json()` `xml()` `redirect()`等輔助函數生成的回應(請求停止向下傳遞)。

## 中間件取得請求及回應
在中間件中，我們可以取得請求，也可以取得執行控制器後的回應，因此中間件內部分為三個部分。
1. 請求通過階段，即請求處理前的階段
2. 控制器處理請求階段，即請求處理階段
3. 回應通過階段，即請求處理後的階段

中間件中這三個階段的體現如下
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
        echo '這裡是請求通過階段，即請求處理前';
        
        $response = $handler($request); // 繼續向下傳遞，直至執行控制器得到回應
        
        echo '這裡是回應通過階段，即請求處理後';
        
        return $response;
    }
}
```

## 範例：身份驗證中間件
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
            // 已經登入，請求繼續向下傳遞
            return $handler($request);
        }

        // 透過反射取得控制器哪些方法不需要登入
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // 存取的方法需要登入
        if (!in_array($request->action, $noNeedLogin)) {
            //攔截請求，返回一個重定向回應，請求停止向下傳遞
            return redirect('/user/login');
        }

        // 不需要登入，請求繼續向下傳遞
        return $handler($request);
    }
}
```

建立控制器 `app/controller/UserController.php`
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * 不需要登入的方法
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
> `$noNeedLogin`記錄了當前控制器不需要登入就可以存取的方法

在 `config/middleware.php` 中添加全域中間件如下：
```php
return [
    // 全域中間件
    '' => [
        // ... 這裡省略其他中間件
        app\middleware\AuthCheckTest::class,
    ]
];
```

有了身份驗證中間件，我們就可以在控制器層專心地寫業務代碼，不用擔心用戶是否登入。

## 範例：跨域請求中間件
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
        // 如果是options請求則返回一個空回應，否則繼續向下傳遞，並取得一個回應
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // 給回應添加跨域相關的http標頭
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
> 跨域可能會產生OPTIONS請求，我們不想OPTIONS請求進入控制器，所以我們為OPTIONS請求直接返回了一個空的回應(`response('')`)實現請求攔截。
> 如果您的介面需要設置路由，請使用`Route::any(..)` 或者 `Route::add(['POST', 'OPTIONS'], ..)`設置。

在 `config/middleware.php` 中添加全域中間件如下：
```php
return [
    // 全域中間件
    '' => [
        // ... 這裡省略其他中間件
        app\middleware\AccessControlTest::class,
    ]
];
```

> **注意**
> 如果ajax請求自定義了header頭，需要在中間件中將 `Access-Control-Allow-Headers` 欄位加入這個自定義header頭，否則會報` Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.`
## 說明

- 中間件分為全局中間件、應用中間件(應用中間件僅在多應用模式下有效，請參見[多應用](multiapp.md))、路由中間件
- 目前不支持單個控制器的中間件(但可以在中間件中通過判斷`$request->controller`來實現類似控制器中間件功能)
- 中間件配置文件位置在 `config/middleware.php`
- 全局中間件配置在key `''`下
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

我們可以給某個一個或某一組路由設定中間件。
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
> 此特性需要webman-framework >= 1.4.8

1.4.8版本之後，配置文件支持直接實例化中間件或者匿名函數，這樣可以方便的通過構造函數向中間件傳參。
例如`config/middleware.php`裡也可以這樣配置
```php
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
```php
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

有時候控制器需要使用中間件裡產生的數據，這時我們可以通過給`$request`對象添加屬性的方式向控制器傳參。例如：

**中間件**
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

class FooController
{
    public function index(Request $request)
    {
        return response($request->data);
    }
}
```
## 中間件獲取當前請求路由信息
> **注意**
> 需要 webman-framework >= 1.3.2

我們可以使用 `$request->route` 獲取路由物件，透過調用對應的方法獲取相應資訊。

**路由配置**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**中間件**
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
        // 如果請求沒有匹配任何路由(默認路由除外)，則 $request->route 為 null
        // 假設瀏覽器訪問地址 /user/111，則會打印如下信息
        if ($route) {
            var_export($route->getPath());       // /user/{uid}
            var_export($route->getMethods());    // ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD','OPTIONS']
            var_export($route->getName());       // user_view
            var_export($route->getMiddleware()); // []
            var_export($route->getCallback());   // ['app\\controller\\UserController', 'view']
            var_export($route->param());         // ['uid'=>111]
            var_export($route->param('uid'));    // 111 
        }
        return $handler($request);
    }
}
```

> **注意**
> `$route->param()`方法需要 webman-framework >= 1.3.16


## 中間件獲取異常
> **注意**
> 需要 webman-framework >= 1.3.15

業務處理過程中可能會產生異常，在中間件裡使用 `$response->exception()` 獲取異常。

**路由配置**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('exception test');
});
```

**中間件：**
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
        $exception = $response->exception();
        if ($exception) {
            echo $exception->getMessage();
        }
        return $response;
    }
}
```


## 超全域中間件

> **注意**
> 此特性要求 webman-framework >= 1.5.16

主項目的全域中間件只影響主項目，不會對[應用插件](app/app.md)產生影響。有時候我們想要加一個影響全域包括所有插件的中間件，則可以使用超全域中間件。

在`config/middleware.php`中配置如下：
```php
return [
    '@' => [ // 給主項目及所有插件增加全域中間件
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // 只給主項目增加全域中間件
];
```

> **提示**
> `@`超全域中間件不僅可以在主項目配置，也可以在某個插件裡配置，例如`plugin/ai/config/middleware.php`裡配置`@`超全域中間件，則也會影響主項目及所有插件。


## 給某個插件增加中間件

> **注意**
> 此特性要求 webman-framework >= 1.5.16

有時候我們想給某個[應用插件](app/app.md)增加一個中間件，又不想改插件的程式碼(因為升級會被覆蓋)，這時候我們可以在主項目中給它配置中間件。

在`config/middleware.php`中配置如下：
```php
return [
    'plugin.ai' => [], // 給ai插件增加中間件
    'plugin.ai.admin' => [], // 給ai插件的admin模組增加中間件
];
```

> **提示**
> 當然也可以在某個插件中加類似的配置去影響其它插件，例如`plugin/foo/config/middleware.php`裡加入如上配置，則會影響ai插件。
