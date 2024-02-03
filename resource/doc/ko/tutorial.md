# 간단한 예시

## 문자열 반환
**컨트롤러 생성**

다음과 같이 `app/controller/UserController.php` 파일을 생성합니다.

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // get 요청으로부터 name 매개변수를 가져오고, 만약 name 매개변수가 전달되지 않았을 경우 $default_name을 반환합니다.
        $name = $request->get('name', $default_name);
        // 브라우저에 문자열 반환
        return response('hello ' . $name);
    }
}
```

**접속**

브라우저에서 `http://127.0.0.1:8787/user/hello?name=tom`에 접속하면 브라우저에서 `hello tom`을 반환합니다.

## JSON 반환
`app/controller/UserController.php` 파일을 다음과 같이 수정합니다.

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return json([
            'code' => 0, 
            'msg' => 'ok', 
            'data' => $name
        ]);
    }
}
```

**접속**

브라우저에서 `http://127.0.0.1:8787/user/hello?name=tom`에 접속하면 `{"code":0,"msg":"ok","data":"tom"}`이 반환됩니다.

데이터를 JSON으로 반환하는 json 도우미 함수를 사용하면 자동으로 `Content-Type: application/json` 헤더가 추가됩니다.

## XML 반환
비슷하게, 도우미 함수 `xml($xml)`를 사용하면 `Content-Type: text/xml` 헤더가 포함된 `xml` 응답이 반환됩니다.

여기서, `$xml` 매개변수는 `xml` 문자열 또는 `SimpleXMLElement` 객체가 될 수 있습니다.

## JSONP 반환
비슷하게, `jsonp($data, $callback_name = 'callback')` 도우미 함수를 사용하면 `jsonp` 응답이 반환됩니다.

## 뷰 반환
`app/controller/UserController.php` 파일을 다음과 같이 수정합니다.

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return view('user/hello', ['name' => $name]);
    }
}
```

다음과 같이 `app/view/user/hello.html` 파일을 생성합니다.

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

브라우저에서 `http://127.0.0.1:8787/user/hello?name=tom`에 접속하면 `hello tom` 내용의 html 페이지가 반환됩니다.

참고: webman은 기본적으로 PHP 네이티브 구문을 템플릿으로 사용합니다. 다른 뷰를 사용하려면 [뷰(view.md)](view.md)를 참조하십시오.
