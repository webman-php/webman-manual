## 사용자 정의 404
웹맨은 404 오류가 발생하면 자동으로 `public/404.html` 파일의 내용을 반환하므로, 개발자는 직접 `public/404.html` 파일을 변경할 수 있습니다.

404의 내용을 동적으로 제어하려는 경우, 예를 들어 ajax 요청 시에는 JSON 데이터 `{"code:"404", "msg":"404 not found"}`를 반환하고 페이지 요청 시에는 `app/view/404.html` 템플릿을 반환하려는 경우 아래 예제를 참고하십시오.

> 다음은 PHP 원시 템플릿을 예로 들었지만, 다른 템플릿인 `twig`, `blade`, `think-tmplate`도 유사한 원리입니다.

**`app/view/404.html` 파일을 생성합니다**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 not found</title>
</head>
<body>
<?=htmlspecialchars($error)?>
</body>
</html>
```

**`config/route.php`에 아래 코드를 추가합니다:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // ajax 요청 시 JSON 반환
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // 페이지 요청 시 404.html 템플릿을 반환
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## 사용자 정의 500
**`app/view/500.html`을 생성합니다**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
사용자 정의 오류 템플릿:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**`app/exception/Handler.php`를 생성합니다**(디렉토리가 없는 경우 직접 만들어 주십시오)
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * 렌더링하여 반환
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // ajax 요청 시 JSON 데이터 반환
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // 페이지 요청 시 500.html 템플릿 반환
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**`config/exception.php`를 구성합니다**
```php
return [
    '' => \app\exception\Handler::class,
];
```
