# 컨트롤러

PSR4 규격에 따라 컨트롤러 클래스 네임스페이스는 `plugin\{플러그인 식별자}`로 시작합니다. 예를 들어,

컨트롤러 파일을 만듭니다. `plugin/foo/app/controller/FooController.php`

```php
<?php
namespace plugin\foo\app\controller;

use support\Request;

class FooController
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

`http://127.0.0.1:8787/app/foo/foo`을 방문하면 페이지에 `hello index`가 반환됩니다.

`http://127.0.0.1:8787/app/foo/foo/hello`를 방문하면 페이지에 `hello webman`이 반환됩니다.


## URL 접근
애플리케이션 플러그인 URL 주소 경로는 모두 `/app`로 시작하여 플러그인 식별자를 두고, 그 다음에는 구체적인 컨트롤러 및 메소드가 따릅니다.
예를 들어, `plugin\foo\app\controller\UserController`의 URL 주소는 `http://127.0.0.1:8787/app/foo/user`입니다.
