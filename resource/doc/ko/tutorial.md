# 간단한 예제

## 문자열 반환
**컨트롤러 생성**

다음과 같이 파일 `app/controller/UserController.php` 을 생성합니다.

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // get 요청에서 name 매개변수를 가져오고, 매개변수가 전달되지 않았을 경우 $default_name을 반환합니다.
        $name = $request->get('name', $default_name);
        // 브라우저에 문자열을 반환합니다.
        return response('hello ' . $name);
    }
}
```

**접근**

브라우저에서 `http://127.0.0.1:8787/user/hello?name=tom`에 접속합니다.

브라우저는 `hello tom`을 반환합니다.

## JSON 반환
파일 `app/controller/UserController.php`을 다음과 같이 변경합니다.

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

**접근**

브라우저에서 `http://127.0.0.1:8787/user/hello?name=tom`에 접속합니다.

브라우저는 `{"code":0,"msg":"ok","data":"tom"}`를 반환합니다.

데이터를 JSON 도우미 함수를 사용하여 반환하면 자동으로 `Content-Type: application/json` 헤더가 추가됩니다.

## XML 반환
마찬가지로, 도우미 함수 `xml($xml)`을 사용하여 `xml` 응답과 함께 `Content-Type: text/xml` 헤더가 반환됩니다.

여기서 `$xml` 매개변수는 `xml` 문자열이나 `SimpleXMLElement` 객체가 될 수 있습니다.

## JSONP 반환
마찬가지로, 도우미 함수 `jsonp($data, $callback_name = 'callback')`을 사용하여 `jsonp` 응답이 반환됩니다.

## 뷰 반환
다음과 같이 파일 `app/controller/UserController.php`을 변경합니다.

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

다음과 같이 파일 `app/view/user/hello.html`을 생성합니다.

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

브라우저에서 `http://127.0.0.1:8787/user/hello?name=tom`에 접속하면 `hello tom`이라는 내용을 가진 HTML 페이지가 반환됩니다.

참고: webman은 기본적으로 PHP 원시 구문을 템플릿으로 사용합니다. 기타 뷰를 사용하려면 [뷰](view.md)를 참조하십시오.
