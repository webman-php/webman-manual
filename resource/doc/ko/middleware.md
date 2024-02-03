# 미들웨어
미들웨어는 일반적으로 요청이나 응답을 가로채는 데 사용됩니다. 예를 들어, 컨트롤러를 실행하기 전에 사용자 신분을 일관되게 확인하거나, 사용자가 로그인하지 않은 경우 로그인 페이지로 리디렉션하거나, 헤더에 특정 헤더를 추가하는 등의 작업을 수행할 수 있습니다. 또한, 특정 URI 요청의 비율을 통계 내는 것과 같은 작업도 가능합니다.

## 미들웨어 오니언 모델

```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     미들웨어1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               미들웨어2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         미들웨어3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── 요청 ───────────────────────> 컨트롤러 ─ 응답 ───────────────────────────> 클라이언트
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
미들웨어와 컨트롤러는 클래식한 오니언 모델을 구성하며, 미들웨어는 계층적으로 쌓인 오니언의 외부껍질에 해당하고, 컨트롤러는 오니언의 핵심 부분에 해당합니다. 위의 그림에서 요청은 마치 화살처럼 미들웨어 1, 2, 3을 통해 컨트롤러에 도달하고, 컨트롤러는 응답을 반환하며, 마지막에 응답은 3, 2, 1의 순서로 미들웨어를 통과하여 클라이언트에 반환됩니다. 즉, 각 미들웨어에서는 요청과 응답을 모두 얻을 수 있다는 뜻입니다.

## 요청 가로채기
가끔씩 특정 요청이 컨트롤러 계층으로 전달되지 않길 원할 때가 있는데, 예를 들어 특정 신분 확인 미들웨어에서 현재 사용자가 로그인하지 않은 것을 발견하면 요청을 직접 가로채고 로그인 응답을 반환할 수 있습니다. 이런 프로세스는 다음과 같이 표현됩니다.

```
                              
            ┌───────────────────────────────────────────────────────┐
            │                     미들웨어1                       │ 
            │     ┌───────────────────────────────────────────┐     │
            │     │          　 　 신분 확인 미들웨어              │     │
            │     │      ┌──────────────────────────────┐     │     │
            │     │      │         미들웨어3          │     │     │       
            │     │      │     ┌──────────────────┐     │     │     │
            │     │      │     │                  │     │     │     │
 　── 요청 ───────────┐   │     │       컨트롤러      │     │     │     │
            │     │ 응답　│     │                  │     │     │     │
   <─────────────────┘   │     └──────────────────┘     │     │     │
            │     │      │                              │     │     │
            │     │      └──────────────────────────────┘     │     │
            │     │                                           │     │
            │     └───────────────────────────────────────────┘     │
            │                                                       │
            └───────────────────────────────────────────────────────┘
```

위의 그림에서 요청이 신분 확인 미들웨어에 도달하면 로그인 응답이 생성되고, 응답이 신분 확인 미들웨어를 통해 다시 미들웨어1로 돌아가 브라우저로 반환됩니다.

## 미들웨어 인터페이스
미들웨어는 `Webman\MiddlewareInterface` 인터페이스를 구현해야 합니다.
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
즉, `process` 메서드를 반드시 구현해야 하며, 이 메서드는 `support\Response` 객체를 반드시 반환해야 합니다. 기본적으로 이 객체는 `$handler($request)`에 의해 생성됩니다(요청이 계속하여 오니언의 핵심에 도달함), 또한 `response()` `json()` `xml()` `redirect()` 등의 도우미 함수로 생성된 응답(요청이 오니언의 핵심에서 멈춤)이 될 수도 있습니다.

## 미들웨어에서 요청 및 응답 가져오기
미들웨어에서는 요청을 가져오거나, 컨트롤러 이후에 생성된 응답을 가져올 수 있기 때문에, 미들웨어 내부는 세 가지 부분으로 나눌 수 있습니다.
1. 요청 가로채기 단계, 즉 요청 처리 전 단계
2. 컨트롤러 요청 처리 단계
3. 응답 반환 단계, 즉 요청 처리 후 단계

미들웨어에서의 세 가지 단계의 구현은 다음과 같습니다.
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
        echo '여기는 요청 가로채기 단계, 즉 요청 처리 전입니다';
        
        $response = $handler($request); // 요청이 오니언의 핵심에 도달할 때까지 계속하여 진행
        
        echo '여기는 응답 반환 단계, 즉 요청 처리 후입니다';
        
        return $response;
    }
}
```
## 예시: 인증 미들웨어
`app/middleware/AuthCheckTest.php` 파일을 생성합니다. (디렉터리가 없는 경우 직접 생성하세요)
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
            return $handler($request);
        }

        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        if (!in_array($request->action, $noNeedLogin)) {
            return redirect('/user/login');
        }

        return $handler($request);
    }
}
```

`app/controller/UserController.php`에 새로운 컨트롤러를 작성합니다.
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
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

`config/middleware.php`에 전역 미들웨어를 추가합니다.
```php
return [
    // 전역 미들웨어
    '' => [
        // ... 다른 미들웨어는 생략합니다
        app\middleware\AuthCheckTest::class,
    ]
];
```

## 예시: CORS 요청 미들웨어
`app/middleware/AccessControlTest.php` 파일을 생성합니다. (디렉터리가 없는 경우 직접 생성하세요)
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
            'Access-Control-Allow-Origin' => $request->header('origin', '*'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
        ]);
        
        return $response;
    }
}
```

`config/middleware.php`에 전역 미들웨어를 추가합니다.
```php
return [
    // 전역 미들웨어
    '' => [
        // ... 다른 미들웨어는 생략합니다
        app\middleware\AccessControlTest::class,
    ]
];
```

## 설명
- 미들웨어는 전역 미들웨어, 애플리케이션 미들웨어(다중 애플리케이션 모드에서만 유효, [다중 애플리케이션](multiapp.md) 참조), 라우팅 미들웨어로 나눌 수 있습니다.
- 현재 단일 컨트롤러 미들웨어를 지원하지 않지만, 미들웨어 내에서 `$request->controller`를 확인하여 비슷한 기능을 구현할 수 있습니다.
- 미들웨어 구성 파일 위치: `config/middleware.php`
- 전역 미들웨어는 `''` 키 아래에 구성됩니다.
- 애플리케이션 미들웨어는 해당하는 애플리케이션 이름 아래에 구성됩니다. 예를 들면,

```php
return [
    // 전역 미들웨어
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // api 애플리케이션 미들웨어(다중 애플리케이션 모드에서만 유효)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## 라우팅 미들웨어
특정 라우트 또는 그룹 라우트에 미들웨어를 설정할 수 있습니다.
예를 들어, `config/route.php`에 다음과 같이 설정합니다.
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

## 미들웨어 생성자로 인자 전달

> **참고**
> 이 기능은 webman-framework >= 1.4.8을 필요로 합니다.

1.4.8 버전 이후, 구성 파일에서 미들웨어를 직접 인스턴스화하거나 익명 함수를 사용하여 생성자를 통해 매개변수를 미들웨어에 전달할 수 있습니다.
예를 들어 `config/middleware.php`에서 다음과 같이 구성할 수 있습니다.
```php
return [
    // 전역 미들웨어
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // api 애플리케이션 미들웨어(다중 애플리케이션 모드에서만 유효)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

라우팅 미들웨어도 생성자를 통해 매개변수를 전달할 수 있습니다. 예를 들어,
`config/route.php`에서 다음과 같이 설정할 수 있습니다.
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## 미들웨어 실행 순서
- 미들웨어 실행 순서는 `전역 미들웨어` -> `애플리케이션 미들웨어` -> `라우팅 미들웨어`입니다.
- 여러 개의 전역 미들웨어가 있는 경우, 실제로 구성된 순서대로 실행됩니다(애플리케이션 미들웨어 및 라우팅 미들웨어도 마찬가지).
- 404 요청은 미들웨어를 활성화하지 않습니다. 여기에는 전역 미들웨어도 포함됩니다.

## 라우트에서 미들웨어로 전달되는 매개변수(route->setParams)

**라우트 설정 `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**미들웨어(전역 미들웨어로 가정)**
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
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## 미들웨어에서 컨트롤러로 매개변수 전달

가끔 미들웨어에서 생성된 데이터를 컨트롤러에서 사용해야 할 수 있습니다. 이 경우, 컨트롤러로 매개변수를 넘기는 방식으로 `$request` 객체에 속성을 추가할 수 있습니다. 예를 들어:

**미들웨어**
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

**컨트롤러:**
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
## 미들웨어에서 현재 요청 경로 정보 가져오기
> **주의**
> webman-framework >= 1.3.2 이상이 필요합니다.

우리는 `$request->route`를 사용하여 라우팅 객체를 가져와서 해당 정보를 얻기 위해 해당 메소드를 호출할 수 있습니다.

**라우트 구성**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**미들웨어**
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
        // 요청이 어떤 라우트에도 일치하지 않으면(기본 라우트 제외), $request->route 는 null입니다.
        // 예를 들어 브라우저가 /user/111 주소를 방문하면 다음과 같은 정보가 출력됩니다.
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

> **주의**
> `$route->param()` 메소드는 webman-framework >= 1.3.16 이상이 필요합니다.


## 미들웨어에서 예외 처리 가져오기
> **주의**
> webman-framework >= 1.3.15 이상이 필요합니다.

비즈니스 처리 중 예외가 발생할 수 있으며, 미들웨어에서 `$response->exception()`을 사용하여 예외를 가져올 수 있습니다.

**라우트 구성**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('exception test');
});
```

**미들웨어:**
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

## 슈퍼 글로벌 미들웨어

> **주의**
> 이 기능은 webman-framework >= 1.5.16 이상이 필요합니다.

주 프로젝트의 전역 미들웨어는 주 프로젝트에만 영향을 미치며, [앱 플러그인](app/app.md)에는 영향을 미치지 않습니다. 때때로 우리는 모든 플러그인을 포함한 전역 미들웨어를 추가하고 싶을 때, 슈퍼 글로벌 미들웨어를 사용할 수 있습니다.

`config/middleware.php`에서 다음과 같이 설정합니다.
```php
return [
    '@' => [ // 주 프로젝트와 모든 플러그인에 전역 미들웨어 추가
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // 주 프로젝트에만 전역 미들웨어 추가
];
```

> **팁**
> `@` 슈퍼 글로벌 미들웨어는 주 프로젝트뿐 아니라 어떤 플러그인에서도 설정할 수 있으며, 예를 들어 `plugin/ai/config/middleware.php`에서 `@` 슈퍼 글로벌 미들웨어를 설정하면 주 프로젝트와 모든 플러그인에 영향을 미칩니다.

## 특정 플러그인에 미들웨어 추가하기

> **주의**
> 이 기능은 webman-framework >= 1.5.16 이상이 필요합니다.

때로는 특정 [앱 플러그인](app/app.md)에 미들웨어를 추가하고 싶지만(업데이트가 덮어쓰기될 수 있기 때문에) 플러그인의 코드를 변경하고 싶지 않은 경우, 주 프로젝트에서 해당 플러그인에 미들웨어를 구성할 수 있습니다.

`config/middleware.php`에서 다음과 같이 설정합니다.
```php
return [
    'plugin.ai' => [], // ai 플러그인에 미들웨어 추가
    'plugin.ai.admin' => [], // ai 플러그인의 admin 모듈에 미들웨어 추가
];
```

> **팁**
> 물론 특정 플러그인에서 이와 유사한 설정을 추가하여 다른 플러그인에도 영향을 미칠 수 있습니다. 예를 들어 `plugin/foo/config/middleware.php`에 위와 같은 설정을 추가하면 ai 플러그인에 영향을 미칠 수 있습니다.
