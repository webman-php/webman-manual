## 라우팅
## 기본 라우팅 규칙
webman의 기본 라우팅 규칙은 `http://127.0.0.1:8787/{컨트롤러}/{액션}`입니다.

기본 컨트롤러는 `app\controller\IndexController`이고, 기본 액션은 `index`입니다.

예를 들어, 다음을 방문합니다:
- `http://127.0.0.1:8787`은 `app\controller\IndexController` 클래스의 `index` 메서드를 기본적으로 방문합니다.
- `http://127.0.0.1:8787/foo`는 `app\controller\FooController` 클래스의 `index` 메서드를 기본적으로 방문합니다.
- `http://127.0.0.1:8787/foo/test`는 `app\controller\FooController` 클래스의 `test` 메서드를 기본적으로 방문합니다.
- `http://127.0.0.1:8787/admin/foo/test`는 `app\admin\controller\FooController` 클래스의 `test` 메서드를 기본적으로 방문합니다 (참조: [다중 앱](multiapp.md)).

또한 webman은 1.4부터 더 복잡한 기본 라우팅을 지원합니다. 예를 들어,
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

요청 라우팅을 변경하려면 설정 파일인 `config/route.php`를 변경하십시오.

기본 라우팅을 비활성화하려면 설정 파일인 `config/route.php`의 마지막 줄에 다음 구성을 추가하십시오:
```php
Route::disableDefaultRoute();
```

## 클로저 라우팅
`config/route.php`에 다음과 같은 라우팅 코드를 추가하십시오.
```php
Route::any('/test', function ($request) {
    return response('test');
});
```
> **참고**
> 클로저 함수는 컨트롤러에 속하지 않으므로 `$request->app`, `$request->controller`, `$request->action`이 모두 빈 문자열입니다.

주소가 `http://127.0.0.1:8787/test`인 경우 `test` 문자열이 반환됩니다.

> **참고**
> 라우팅 경로는 반드시 `/`로 시작해야 합니다.

```php
// 잘못된 사용법
Route::any('test', function ($request) {
    return response('test');
});

// 올바른 사용법
Route::any('/test', function ($request) {
    return response('test');
});
```

## 클래스 라우팅
`config/route.php`에 다음과 같은 라우팅 코드를 추가하십시오.
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
주소가 `http://127.0.0.1:8787/testclass`인 경우 `app\controller\IndexController` 클래스의 `test` 메서드의 반환값이 반환됩니다.

## 라우팅 매개변수
라우팅에 매개변수가 있는 경우 `{key}`를 사용하여 매칭하고, 매칭 결과는 해당 컨트롤러 메서드 매개변수로 전달됩니다(두 번째 매개변수부터 차례로 전달). 예를 들어:
```php
// /user/123 또는 /user/abc를 매치
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('매개변수 '.$id.'를 받았습니다');
    }
}
```

더 많은 예시:
```php
// /user/123을 매치, /user/abc는 매치되지 않음
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// /user/foobar를 매치, /user/foo/bar는 매치되지 않음
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// /user, /user/123, /user/abc를 매치
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// 모든 옵션 요청을 매치
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## 라우팅 그룹
때로는 라우팅에 많은 동일한 접두어가 포함되어 있으며, 이때 라우팅 그룹을 사용하여 정의를 단순화할 수 있습니다. 예를 들어:
```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
다음과 같에 동일합니다.
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

중첩하여 그룹을 사용하는 예:
```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
});
```

## 라우팅 미들웨어
특정한 하나 또는 그룹의 라우팅에 미들웨어를 설정할 수 있습니다.
예를 들어:
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

> **참고**: 
> webman-framework <= 1.5.6에서 `->middleware()` 라우팅 미들웨어가 그룹 그룹 후에 분류하는 경우, 현재 라우팅은 현재 그룹 내에 있어야 합니다.

```php
# 잘못된 사용 예 (webman-framework >= 1.5.7의 경우 이 사용법이 유효함)
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
# 올바른 사용 예
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

## 리소스형 라우팅
```php
Route::resource('/test', app\controller\IndexController::class);

// 리소스 라우팅 지정
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// 정의되지 않은 리소스 라우팅
// 예를 들어 notify 접근 주소는 any형 라우팅인 /test/notify 또는 /test/notify/{id} 모두 routeName은 test.notify입니다
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| 동사   | URI                 | 액션   | 라우트 이름    |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |

## URL 생성
> **참고**
> 일시적으로 그룹 내부의 라우팅을 생성하는 것은 지원되지 않습니다.

예를 들어 다음과 같은 라우팅이 있습니다:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
다음 방법을 사용하여 이 라우팅의 URL을 생성할 수 있습니다.
```php
route('blog.view', ['id' => 100]); // 결과: /blog/100
```

뷰에서 라우팅의 URL을 사용할 때 이 메서드를 사용하면 라우팅 주소가 변경되어도 URL이 자동으로 생성되므로 뷰 파일을 대량 수정하는 일을 피할 수 있습니다.

## 라우팅 정보 가져오기
> **참고**
> webman-framework >= 1.3.2 필요

`$request->route` 객체를 사용하여 현재 요청의 라우팅 정보를 가져올 수 있습니다. 예를 들어:

```php
$route = $request->route; // $route = request()->route;와 동일함
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // 이 기능은 webman-framework >= 1.3.16이 필요합니다.
}
```

> **참고**
> 현재 요청이 `config/route.php`에 구성된 라우팅과 일치하지 않는 경우 `$request->route`가 null이며, 즉 기본 라우팅을 사용할 때 `$request->route`가 null임을 의미합니다.

## 404 처리
라우팅을 찾을 수 없는 경우 기본적으로 404 상태 코드가 반환되고 `public/404.html` 파일의 내용이 출력됩니다.

개발자가 라우팅을 찾지 못했을 때의 비즈니스 프로세스에 개입하고 싶다면 webman이 제공하는 후속 라우팅 `Route::fallback($callback)` 메서드를 사용할 수 있습니다. 예를 들어 다음 코드는 라우팅을 찾지 못했을 때 홈 페이지로 리디렉션합니다.
```php
Route::fallback(function(){
    return redirect('/');
});
```
다음은 라우팅이 없을 때 JSON 데이터를 반환하는 예시로, 이는 webman이 API 인터페이스로 사용될 때 매우 유용합니다.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

관련 링크 [사용자 지정 404 500 페이지](others/custom-error-page.md)
## 라우팅 인터페이스
```php
// 임의의 메서드 요청에 대한 라우팅을 설정합니다
Route::any($uri, $callback);
// GET 요청에 대한 라우팅을 설정합니다
Route::get($uri, $callback);
// POST 요청에 대한 라우팅을 설정합니다
Route::post($uri, $callback);
// PUT 요청에 대한 라우팅을 설정합니다
Route::put($uri, $callback);
// PATCH 요청에 대한 라우팅을 설정합니다
Route::patch($uri, $callback);
// DELETE 요청에 대한 라우팅을 설정합니다
Route::delete($uri, $callback);
// HEAD 요청에 대한 라우팅을 설정합니다
Route::head($uri, $callback);
// 여러 종류의 요청 유형에 대한 라우팅을 동시에 설정합니다
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// 그룹화된 라우팅을 설정합니다
Route::group($path, $callback);
// 리소스 라우팅을 설정합니다
Route::resource($path, $callback, [$options]);
// 라우팅을 비활성화합니다
Route::disableDefaultRoute($plugin = '');
// 폴백 라우팅을 설정하여 기본적인 라우팅을 지정합니다
Route::fallback($callback, $plugin = '');
```
만약 uri에 해당하는 라우팅이 없거나(기본 라우팅 포함), 폴백 라우팅이 설정되어 있지 않은 경우 404를 반환합니다.

## 다중 라우팅 구성 파일
라우팅을 관리하기 위해 다중 라우팅 구성 파일을 사용하려면, 예를 들어 [다중 애플리케이션](multiapp.md)을 사용하여 각 애플리케이션에 고유한 라우팅 구성이 있는 경우에는 `require`를 사용하여 외부 라우팅 구성 파일을 로드할 수 있습니다.
예를 들어, `config/route.php`에서
```php
<?php

// admin 애플리케이션의 라우팅 구성을로드합니다
require_once app_path('admin/config/route.php');
// api 애플리케이션의 라우팅 구성을 로드합니다
require_once app_path('api/config/route.php');
```
