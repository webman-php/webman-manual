# 컨트롤러

PSR4 규격에 따라 컨트롤러 클래스 네임스페이스는 `plugin\{플러그인 식별자}`로 시작합니다. 예를 들어

컨트롤러 파일을 만듭니다 `plugin/foo/app/controller/FooController.php`。

```php
<?php
namespace plugin\foo\app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('안녕하세요 인덱스');
    }
    
    public function hello(Request $request)
    {
        return response('안녕하세요 웹맨');
    }
}
```

`http://127.0.0.1:8787/app/foo/foo`에 접근하면 페이지가 `안녕하세요 인덱스`로 반환됩니다.

`http://127.0.0.1:8787/app/foo/foo/hello`에 접근하면 페이지가 `안녕하세요 웹맨`으로 반환됩니다.


## URL 접근
어플리케이션 플러그인의 URL 주소 경로는 모두 `/app`로 시작하고, 그 뒤에 플러그인 식별자가 오며, 그 다음에 구체적인 컨트롤러 및 메소드가 옵니다.
예를 들어, `plugin\foo\app\controller\UserController`의 URL 주소는 `http://127.0.0.1:8787/app/foo/user`입니다.
