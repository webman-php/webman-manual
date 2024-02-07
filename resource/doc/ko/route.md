## 라우팅
## 기본 라우팅 규칙
webman의 기본 라우팅 규칙은 `http://127.0.0.1:8787/{컨트롤러}/{액션}` 입니다.

기본 컨트롤러는 `app\controller\IndexController` 이며, 기본 액션은 `index` 입니다.

예를 들어 다음과 같이 접속합니다:
- `http://127.0.0.1:8787` 은 `app\controller\IndexController` 클래스의 `index` 메서드를 기본적으로 접속합니다.
- `http://127.0.0.1:8787/foo` 는 `app\controller\FooController` 클래스의 `index` 메서드를 기본적으로 접속합니다.
- `http://127.0.0.1:8787/foo/test` 는 `app\controller\FooController` 클래스의 `test` 메서드를 기본적으로 접속합니다.
- `http://127.0.0.1:8787/admin/foo/test` 는 `app\admin\controller\FooController` 클래스의 `test` 메서드를 기본적으로 접속합니다 (참조: [다중 애플리케이션](multiapp.md))

또한 webman 1.4부터 더 복잡한 기본 라우팅을 지원합니다. 예를 들어,
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

요청 라우트를 변경하고 싶을 때는 설정 파일 `config/route.php`을 수정하십시오.

기본 라우팅을 비활성화하려면 설정 파일 `config/route.php`에 다음 구성을 추가하십시오:
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
> **주의**
> 클로저 함수는 어떤 컨트롤러에도 속하지 않기 때문에, `$request->app`, `$request->controller`, `$request->action`이 모두 빈 문자열입니다.

`http://127.0.0.1:8787/test` 주소로 접속하면 `test` 문자열이 반환됩니다.

> **주의**
> 라우트 경로는 반드시 `/`로 시작해야 합니다. 예를 들어,
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
`http://127.0.0.1:8787/testclass` 주소로 접속하면 `app\controller\IndexController` 클래스의 `test` 메서드의 반환값이 반환됩니다.

## 라우팅 매개변수
라우트에 매개변수가 있는 경우, `{key}`로 일치시키고 결과가 해당하는 컨트롤러 메서드 매개변수로 전달됩니다(두 번째 매개변수부터 전달됨), 예를 들어:
```php
// /user/123, /user/abc 일치
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('매개변수를 받았습니다'.$id);
    }
}
```

더 많은 예제:
```php
// /user/123 일치, /user/abc 불일치
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// /user/foobar 일치, /user/foo/bar 불일치
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// /user, /user/123, /user/abc 일치
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// 모든 options 요청 일치
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## 라우팅 그룹
가끔 라우트에는 많은 동일한 접두어가 포함될 때 라우팅 그룹을 사용하여 정의를 간소화할 수 있습니다. 예를 들어:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
아래와 같이 동일하게 작동합니다.
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

중첩하여 group을 사용할 수도 있습니다.
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
특정 하나 또는 여러 라우트에 미들웨어를 설정할 수 있습니다.
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

> **주의**:
> webman-framework의 경우 <= 1.5.6인 경우 `->middleware()` 라우트 미들웨어는 그룹 그룹 이후에 작동해야 하므로 현재 라우트가 해당 그룹에 속해 있어야 합니다.

```php
# 잘못된 사용 예시 (webman-framework >= 1.5.7 인 경우 이 사용법이 유효함)
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
# 올바른 사용 예시
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
// notify에 접속하면 any 유형의 /test/notify나 /test/notify/{id} 둘 다 가능, routeName은 test.notify
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
> **주의** 
> 현재 그룹 중첩된 라우팅의 URL 생성을 지원하지 않습니다.

예를 들어, 다음과 같은 라우트가 있다면:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
우리는 다음 방법을 사용하여 이 라우트의 URL을 생성할 수 있습니다.
```php
route('blog.view', ['id' => 100]); // 결과: /blog/100
```

뷰에서 라우트의 URL을 사용할 때 이 방법을 사용하면, 라우트 규칙이 변경되더라도 URL이 자동으로 생성되어 뷰 파일을 대량으로 변경할 필요가 없습니다.

## 라우트 정보 가져오기
> **주의**
> webman-framework >= 1.3.2 이상 버전이 필요합니다.

`$request->route` 객체를 통해 현재 요청의 라우트 정보를 가져올 수 있습니다. 예를 들어:

```php
$route = $request->route; // $route = request()->route; 와 같음
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // 이 기능은 webman-framework >= 1.3.16 이상 버전이 필요합니다
}
```
> **주의**
> 현재 요청이 config/route.php에 지정된 어떤 라우트와도 일치하지 않으면, 즉 기본 라우트로 이동할 경우, `$request->route`는 null입니다.

## 404 처리
라우트를 찾을 수 없을 때 기본적으로 404 상태 코드를 반환하고 `public/404.html` 파일 내용을 출력합니다.

개발자가 라우트를 찾을 수 없을 때의 비즈니스 프로세스를 개입하고 싶다면, webman이 제공하는 후속 라우트 `Route::fallback($callback)` 메서드를 사용할 수 있습니다. 예를 들어, 다음 코드는 라우트를 찾을 수 없을 때 홈페이지로 리디렉션합니다.
```php
Route::fallback(function(){
    return redirect('/');
});
```
다른 예로, 라우트가 없을 때 JSON 데이터를 반환하여 webman이 API 인터페이스로 사용될 때 매우 유용합니다.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```
관련 링크: [사용자 정의 404 500 페이지](others/custom-error-page.md)

## 라우트 인터페이스
```php
// uri의 모든 메서드 요청에 대한 라우트 설정
Route::any($uri, $callback);
// uri의 get 요청에 대한 라우트 설정
Route::get($uri, $callback);
// uri의 post 요청에 대한 라우트 설정
Route::post($uri, $callback);
// uri의 put 요청에 대한 라우트 설정
Route::put($uri, $callback);
// uri의 patch 요청에 대한 라우트 설정
Route::patch($uri, $callback);
// uri의 delete 요청에 대한 라우트 설정
Route::delete($uri, $callback);
// uri의 head 요청에 대한 라우트 설정
Route::head($uri, $callback);
// 여러 요청 유형 동시에 설정
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// 그룹 라우트
Route::group($path, $callback);
// 리소스 라우트
Route::resource($path, $callback, [$options]);
// 라우트 비활성화
Route::disableDefaultRoute($plugin = '');
// 후퇴 라우트, 기본 라우트 설정
Route::fallback($callback, $plugin = '');
```
URI에 해당하는 라우트(기본 라우트 포함)가 없고 후퇴 라우트도 설정되지 않으면 404가 반환됩니다.

## 여러 개의 라우트 설정 파일
여러 라우트 설정 파일을 사용하여 라우트를 관리하려는 경우, 예를 들어 [다중 애플리케이션](multiapp.md)에서 각 애플리케이션에 자체 라우트 설정이 있는 경우, 외부 라우트 설정 파일을 `require`하여 로드할 수 있습니다.
예를 들어 `config/route.php`에서:
```php
<?php

// admin 애플리케이션의 라우트 설정 로드
require_once app_path('admin/config/route.php');
// api 애플리케이션의 라우트 설정 로드
require_once app_path('api/config/route.php');
```
