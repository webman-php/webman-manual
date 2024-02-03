# 응답
응답은 실제로 `support\Response` 객체입니다. 이 객체를 쉽게 생성하기 위해 webman은 몇 가지 도우미 함수를 제공합니다.

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

response 함수 구현은 다음과 같습니다:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

또한 공백 `response` 객체를 만들고, 해당 위치에서 `$response->cookie()`, `$response->header()`, `$response->withHeaders()`, `$response->withBody()`를 사용하여 반환 내용을 설정할 수 있습니다.

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
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
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
json 함수 구현은 다음과 같습니다.
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
xml 함수 구현은 다음과 같습니다:
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
다음과 같이 새 파일 `app/controller/FooController.php`를 만듭니다.

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

다음과 같이 새 파일 `app/view/foo/hello.html`을 만듭니다.

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

## 리다이렉트
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

redirect 함수 구현은 다음과 같습니다:
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
또는 `header` 및 `withHeaders` 메서드를 사용하여 단일 또는 일괄 헤더를 설정할 수 있습니다.

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
            'X-Header-One' => 'Header Value 1',
            'X-Header-Tow' => 'Header Value 2',
        ]);
    }
}
```
헤더를 먼저 설정하고 최종적으로 반환할 데이터를 설정할 수도 있습니다.

```php
public function hello(Request $request)
{
    // 객체 생성
    $response = response();
    
    // .... 비즈니스 로직 생략
    
    // HTTP 헤더 설정
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
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

쿠키를 먼저 설정하고 최종적으로 반환할 데이터를 설정할 수도 있습니다.

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

cookie 메소드의 전체 매개변수는 다음과 같습니다:

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

- webman은 엄청 큰 파일을 보낼 수 있습니다.
- 큰 파일(2M 이상)에 대해 webman은 전체 파일을 한 번에 메모리로 읽지 않고 적절한 시점에 파일을 분할하여 읽고 전송합니다.
- webman은 클라이언트의 수신 속도에 따라 파일을 읽고 보내는 속도를 최적화하여 최대한 빠르게 파일을 전송하면서 메모리 사용량을 최소화합니다.
- 데이터 전송은 논블로킹 방식으로 이루어지므로 다른 요청 처리에 영향을 주지 않습니다.
- file 메소드는 자동으로 `if-modified-since` 헤더를 추가하고 다음 요청 시에 `if-modified-since` 헤더를 검사하여 파일이 수정되지 않았다면 대역폭을 절약하기 위해 직접 304를 반환합니다.
- 전송된 파일은 브라우저에 알맞은 `Content-Type` 헤더를 사용하여 자동으로 보내집니다.
- 파일이 존재하지 않으면 자동으로 404 응답으로 전환됩니다.

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
download 메소드는 file 메소드와 기본적으로 유사하지만
1. 다운로드할 파일 이름을 설정하면 파일이 다운로드되어 브라우저에 표시되지 않습니다.
2. download 메소드는 `if-modified-since` 헤더를 확인하지 않습니다.

## 출력 가져오기
일부 라이브러리는 파일 내용을 직접 표준 출력으로 출력하기 때문에 데이터가 브라우저에 전송되지 않고 터미널에 인쇄됩니다. 이 경우 `ob_start()` 및 `ob_get_clean()`를 사용하여 데이터를 변수에 캡처한 다음 브라우저로 데이터를 보낼 수 있습니다.

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
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

        // 출력 가져오기 시작
        ob_start();
        // 이미지 출력
        imagejpeg($im);
        // 이미지 내용 얻기
        $image = ob_get_clean();
        
        // 이미지 전송
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
