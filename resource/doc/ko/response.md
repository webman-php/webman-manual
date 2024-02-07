# 응답
응답은 실제로 `support\Response` 객체입니다. 이 객체를 쉽게 만들기 위해, webman은 몇 가지 도우미 함수를 제공합니다.

## 임의의 응답 반환

**예시**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

response 함수는 다음과 같이 구현됩니다.
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

또한 적절한 위치에서 `$response->cookie()`, `$response->header()`, `$response->withHeaders()`, `$response->withBody()`를 사용하여 빈 `response` 객체를 먼저 생성한 후에 응답 내용을 설정할 수 있습니다.
```php
public function hello(Request $request)
{
    // 객체 생성
    $response = response();
    
    // .... 비즈니스 로직 생략
    
    // 쿠키 설정
    $response->cookie('foo', 'value');
    
    // .... 비즈니스 로직 생략
    
    // HTTP 헤더 설정
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => '헤더 값 1',
                'X-Header-Tow' => '헤더 값 2',
            ]);

    // .... 비즈니스 로직 생략

    // 반환할 데이터 설정
    $response->withBody('반환할 데이터');
    return $response;
}
```

## JSON 반환
**예시**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```
json 함수는 다음과 같이 구현됩니다.
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```

## XML 반환
**예시**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        $xml = <<<XML
               <?xml version='1.0' standalone='yes'?>
               <values>
                   <truevalue>1</truevalue>
                   <falsevalue>0</falsevalue>
               </values>
               XML;
        return xml($xml);
    }
}
```
xml 함수는 다음과 같이 구현됩니다.
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## 뷰 반환
다음과 같이 'app/controller/FooController.php' 파일을 만듭니다.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return view('foo/hello', ['name' => 'webman']);
    }
}
```
다음과 같이 'app/view/foo/hello.html' 파일을 만듭니다.
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

## 리다이렉션
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return redirect('/user');
    }
}
```
redirect 함수는 다음과 같이 구현됩니다.
```php
function redirect($location, $status = 302, $headers = [])
{
    $response = new Response($status, ['Location' => $location]);
    if (!empty($headers)) {
        $response->withHeaders($headers);
    }
    return $response;
}
```

## 헤더 설정
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Header Value' 
        ]);
    }
}
```
또한 `header` 및 `withHeaders` 메서드를 사용하여 단일로 헤더를 설정할 수 있습니다.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => '헤더 값 1',
            'X-Header-Tow' => '헤더 값 2',
        ]);
    }
}
```
데이터를 반환하기 전에 헤더를 미리 설정하고 마지막으로 반환할 데이터를 설정할 수도 있습니다.
```php
public function hello(Request $request)
{
    // 객체 생성
    $response = response();
    
    // .... 비즈니스 로직 생략
    
    // HTTP 헤더 설정
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => '헤더 값 1',
                'X-Header-Tow' => '헤더 값 2',
            ]);

    // .... 비즈니스 로직 생략

    // 반환할 데이터 설정
    $response->withBody('반환할 데이터');
    return $response;
}
```

## 쿠키 설정
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->cookie('foo', 'value');
    }
}
```
데이터를 반환하기 전에 쿠키를 미리 설정하고 마지막으로 반환할 데이터를 설정할 수도 있습니다.
```php
public function hello(Request $request)
{
    // 객체 생성
    $response = response();
    
    // .... 비즈니스 로직 생략
    
    // 쿠키 설정
    $response->cookie('foo', 'value');
    
    // .... 비즈니스 로직 생략

    // 반환할 데이터 설정
    $response->withBody('반환할 데이터');
    return $response;
}
```
cookie 메서드의 완전한 인수는 다음과 같습니다.

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## 파일 스트림 반환
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->file(public_path() . '/favicon.ico');
    }
}
```

- webman은 대용량 파일을 전송하는 것을 지원합니다.
- 대용량 파일(2M 이상)의 경우, webman은 전체 파일을 한꺼번에 메모리로 읽는 것이 아니라 적절한 시기에 파일을 분할하여 읽고 전송합니다.
- webman은 클라이언트의 수신 속도에 따라 파일 읽기 및 전송 속도를 최적화하여 최대한 빠르게 파일을 전송하면서 메모리 사용량을 최소화합니다.
- 데이터 전송은 논블로킹 방식으로 이루어지며, 다른 요청 처리에 영향을 주지 않습니다.
- file 메서드는 자동으로 `if-modified-since` 헤더를 추가하고, 다음 요청 시에 `if-modified-since` 헤더를 확인하여 파일이 수정되지 않았다면 대역폭을 절약하기 위해 직접 304를 반환합니다.
- 전송된 파일은 브라우저에 적절한 `Content-Type` 헤더를 사용하여 자동으로 전송됩니다.
- 파일이 없으면 자동으로 404 응답으로 전환됩니다.
## 파일 다운로드
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', '파일명.ico');
    }
}
```

download 메소드는 file 메소드와 기본적으로 유사하지만 다음과 같은 차이가 있습니다:
1. 파일 이름을 설정한 후 파일이 다운로드됩니다. 브라우저에 표시되지 않습니다.
2. download 메소드는 `if-modified-since` 헤더를 확인하지 않습니다.

## 출력 얻기
일부 라이브러리는 파일 내용을 직접 표준 출력에 인쇄하며, 즉 데이터는 브라우저에 전송되지 않고 터미널에 인쇄됩니다. 이 경우 `ob_start();` `ob_get_clean();`를 사용하여 데이터를 변수에 캡처하고 나중에 데이터를 브라우저로 보낼 수 있습니다. 예시:

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // 이미지 생성
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  '간단한 텍스트 문자열', $text_color);

        // 출력 얻기 시작
        ob_start();
        // 이미지 출력
        imagejpeg($im);
        // 이미지 내용 가져오기
        $image = ob_get_clean();
        
        // 이미지 보내기
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
