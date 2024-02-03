# 설명

## 요청 객체 가져오기
webman은 자동으로 요청 객체를 action 메서드의 첫 번째 매개변수에 주입합니다. 예를 들어

**예시**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // get 요청에서 name 매개변수 가져오기, 매개변수가 전달되지 않으면 $default_name 반환
        $name = $request->get('name', $default_name);
        // 브라우저에 문자열 반환
        return response('hello ' . $name);
    }
}
```

`$request` 객체를 통해 요청과 관련된 모든 데이터를 가져올 수 있습니다.

**가끔은 다른 클래스에서 현재 요청의 `$request` 객체를 가져오고 싶을 때는 도우미 함수 `request()`를 사용하면 됩니다.**

## GET 요청 매개변수 가져오기

**전체 GET 배열 가져오기**
```php
$request->get();
```
요청에 GET 매개변수가 없으면 빈 배열을 반환합니다.

**GET 배열 중 하나의 값 가져오기**
```php
$request->get('name');
```
GET 배열에 해당 값이 없으면 null을 반환합니다.

또한 두 번째 매개변수로 기본값을 전달할 수 있으며, GET 배열에서 해당 값이 없으면 기본값을 반환합니다. 예:
```php
$request->get('name', 'tom');
```

## POST 요청 매개변수 가져오기

**전체 POST 배열 가져오기**
```php
$request->post();
```
요청에 POST 매개변수가 없으면 빈 배열을 반환합니다.

**POST 배열 중 하나의 값 가져오기**
```php
$request->post('name');
```
POST 배열에 해당 값이 없으면 null을 반환합니다.

GET 메서드와 마찬가지로 두 번째 매개변수로 기본값을 전달할 수 있으며, POST 배열에서 해당 값이 없으면 기본값을 반환합니다. 예:
```php
$request->post('name', 'tom');
```

## 원시 POST 요청 패킷 가져오기
```php
$post = $request->rawBody();
```
이 기능은 `php-fpm`의 `file_get_contents("php://input");` 작업과 유사합니다. http 원시 요청 패킷을 가져오는 데 유용합니다. 이는 `application/x-www-form-urlencoded` 형식이 아닌 post 요청 데이터를 가져올 때 유용합니다.


## 헤더 가져오기
**전체 헤더 배열 가져오기**
```php
$request->header();
```
요청에 헤더 매개변수가 없으면 빈 배열을 반환합니다. 모든 키는 소문자입니다.

**헤더 배열에서 하나의 값을 가져오기**
```php
$request->header('host');
```
헤더 배열에 해당 값이 없으면 null을 반환합니다. 모든 키는 소문자입니다.

GET 메서드와 마찬가지로 두 번째 매개변수로 기본값을 전달할 수 있으며, 헤더 배열에서 해당 값이 없으면 기본값을 반환합니다. 예:
```php
$request->header('host', 'localhost');
```

## 쿠키 가져오기
**전체 쿠키 배열 가져오기**
```php
$request->cookie();
```
요청에 쿠키 매개변수가 없으면 빈 배열을 반환합니다.

**쿠키 배열에서 하나의 값을 가져오기**
```php
$request->cookie('name');
```
쿠키 배열에 해당 값이 없으면 null을 반환합니다.

GET 메서드와 마찬가지로 두 번째 매개변수로 기본값을 전달할 수 있으며, 쿠키 배열에서 해당 값이 없으면 기본값을 반환합니다. 예:
```php
$request->cookie('name', 'tom');
```

## 모든 입력 가져오기
`post` 및 `get`을 포함한 셋트입니다.
```php
$request->all();
```

## 특정 입력 값 가져오기
`post` 및 `get`을 통해 특정 값을 얻습니다.
```php
$request->input('name', $default_value);
```

## 일부 입력 데이터 가져오기
`post` 및 `get`에서 일부 데이터를 가져옵니다.
```php
// username 및 password로 구성된 배열을 가져오고 해당 키가 없으면 무시합니다
$only = $request->only(['username', 'password']);
// avatar 및 age를 제외한 모든 입력을 가져옵니다
$except = $request->except(['avatar', 'age']);
```

## 파일 업로드 가져오기
**전체 업로드 파일 배열 가져오기**
```php
$request->file();
```

유사한 형식의 폼:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

`$request->file()`이 반환하는 형식은 다음과 같습니다:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
이는 `webman\Http\UploadFile` 인스턴스의 배열입니다. `webman\Http\UploadFile` 클래스는 PHP 내장 [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) 클래스를 상속하고 있으며 유용한 메서드를 제공합니다.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // 파일이 유효한지, true|false 등
            var_export($spl_file->getUploadExtension()); // 업로드 파일 확장자, 예: 'jpg'
            var_export($spl_file->getUploadMimeType()); // 업로드 파일 mime 타입, 예: 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // 업로드 오류 코드 가져오기, 예: UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // 업로드 파일 이름, 예: 'my-test.jpg'
            var_export($spl_file->getSize()); // 파일 크기 가져오기, 예: 13364, 바이트 단위
            var_export($spl_file->getPath()); // 업로드된 디렉토리 가져오기, 예: '/tmp'
            var_export($spl_file->getRealPath()); // 임시 파일 경로 가져오기, 예: `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**참고:** 

- 파일이 업로드된 후 일시 파일 이름은 `/tmp/workerman.upload.SRliMu`와 같이 지정됩니다.
- 업로드된 파일 크기는 [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html)에 의해 제한됩니다. 기본값은 10M이며 `config/server.php` 파일에서 `max_package_size`를 수정하여 기본값을 변경할 수 있습니다.
- 요청이 종료되면 일시 파일은 자동으로 삭제됩니다.
- 요청에 업로드된 파일이 없으면 `$request->file()`은 빈 배열을 반환합니다.
- 업로드된 파일은 `move_uploaded_file()` 메서드를 지원하지 않으며, 대신 `$file->move()` 메서드를 사용하세요. 아래 예시를 참조하세요.

### 특정 업로드 파일 가져오기
```php
$request->file('avatar');
```
파일이 존재하는 경우 해당 파일의 `webman\Http\UploadFile` 인스턴스를 반환하고, 그렇지 않은 경우 null을 반환합니다.

**예시**
```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function file(Request $request)
    {
        $file = $request->file('avatar');
        if ($file && $file->isValid()) {
            $file->move(public_path().'/files/myfile.'.$file->getUploadExtension());
            return json(['code' => 0, 'msg' => 'upload success']);
        }
        return json(['code' => 1, 'msg' => 'file not found']);
    }
}
```

## 호스트 가져오기
요청의 호스트 정보 가져오기.
```php
$request->host();
```
요청 주소가 표준이 아닌 80 또는 443 포트이면 호스트 정보에 포트가 포함될 수 있습니다. 예: `example.com:8080` . 포트가 필요 없을 경우 첫 번째 매개변수를 `true`로 전달하세요.

```php
$request->host(true);
```

## 요청 메서드 가져오기
```php
 $request->method();
```
리턴 값은 `GET`、`POST`、`PUT`、`DELETE`、`OPTIONS` 또는 `HEAD` 중 하나입니다.

## 요청 URI 가져오기
```php
$request->uri();
```
요청의 URI를 반환하며 path와 query string을 포함합니다.

## 요청 경로 가져오기

```php
$request->path();
```
요청의 경로 부분을 반환합니다.


## 요청 query string 가져오기

```php
$request->queryString();
```
요청의 query string을 반환합니다.

## 요청 URL 가져오기
`url()` 메서드는 쿼리 매개변수가 없는 URL을 반환합니다.
```php
$request->url();
```
`//www.workerman.net/workerman-chat`와 비슷한 값을 반환합니다.

`fullUrl()` 메서드는 쿼리 매개변수를 포함하는 URL을 반환합니다.
```php
$request->fullUrl();
```
`//www.workerman.net/workerman-chat?type=download`와 비슷한 값을 반환합니다.

> **참고:**
> `url()` 및 `fullUrl()`은 프로토콜 부분(즉, http 또는 https)을 반환하지 않습니다.
> `//`로 시작하는 주소는 브라우저에서 현재 사이트의 프로토콜을 자동으로 인식하여 http 또는 https로 요청을 보낼 것입니다.

nginx 프록시를 사용하는 경우 `proxy_set_header X-Forwarded-Proto $scheme;`를 nginx 설정에 추가하여 [nginx 프록시 참조](others/nginx-proxy.md)하십시오.
이런 식으로 `$request->header('x-forwarded-proto');`를 사용하여 http 또는 https 여부를 판단할 수 있게 됩니다. 예:
```php
echo $request->header('x-forwarded-proto'); // http 또는 https를 출력함
```

## 요청 HTTP 버전 가져오기

```php
$request->protocolVersion();
```
`1.1` 또는 `1.0` 문자열을 반환합니다.


## 요청 세션 ID 가져오기

```php
$request->sessionId();
```
알파벳과 숫자로 구성된 문자열을 반환합니다.


## 클라이언트의 IP 가져오기
```php
$request->getRemoteIp();
```

## 클라이언트의 포트 가져오기
```php
$request->getRemotePort();
```
## 요청 클라이언트의 실제 IP 주소 가져오기
```php
$request->getRealIp($safe_mode=true);
```

프로젝트가 프록시(예: nginx)를 사용하는 경우 `request->getRemoteIp()`를 사용하면 대리 서버 IP(예: `127.0.0.1` `192.168.x.x`)가 실제 클라이언트 IP가 아닌 경우가 많습니다. 이때 `request->getRealIp()`를 사용하여 클라이언트의 실제 IP를 가져올 수 있습니다.

`request->getRealIp()`는 HTTP 헤더의 `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, `via` 필드에서 실제 IP를 가져오려고 시도합니다.

> HTTP 헤더는 쉽게 위조될 수 있기 때문에 이 방법으로 얻은 클라이언트 IP는 100% 신뢰할 수 없으며, 특히 `$safe_mode`가 false인 경우 특히 그렇습니다. 프록시를 통해 클라이언트의 실제 IP를 안전하게 얻는 가장 신뢰할 수 있는 방법은 안전한 프록시 서버 IP를 알고 있고 실제 IP를 가져올 HTTP 헤더를 명확히 알고 있는 경우입니다. `request->getRemoteIp()`로 반환된 IP가 이미 알려진 안전한 프록시 서버임이 확인되면, 그런 다음 `request->header('실제 IP를 가져올 HTTP 헤더')`를 사용하여 실제 IP를 가져올 수 있습니다.


## 서버의 IP 주소 가져오기
```php
$request->getLocalIp();
```

## 서버의 포트 가져오기
```php
$request->getLocalPort();
```

## AJAX 요청인지 확인하기
```php
$request->isAjax();
```

## PJAX 요청인지 확인하기
```php
$request->isPjax();
```

## JSON 응답을 기대하는지 확인하기
```php
$request->expectsJson();
```

## 클라이언트가 JSON 응답을 수락하는지 확인하기
```php
$request->acceptJson();
```

## 요청의 플러그인 이름 가져오기
플러그인 요청이 아닌 경우 빈 문자열 `''`을 반환합니다.
```php
$request->plugin;
```
> 이 기능은 webman>=1.4.0 이상에서 사용 가능합니다.


## 요청의 애플리케이션 이름 가져오기
단일 애플리케이션인 경우 항상 빈 문자열 `''`을 반환하며, [멀티 애플리케이션](multiapp.md)인 경우 애플리케이션 이름을 반환합니다.
```php
$request->app;
```

> 익명 함수는 어떤 애플리케이션에도 속하지 않으므로 익명 함수 라우트에서의 요청의 `$request->app`은 항상 빈 문자열 `''`를 반환합니다.
> 익명 함수 라우트에 대한 자세한 내용은 [라우팅](route.md)을 참조하십시오.


## 요청의 컨트롤러 클래스 이름 가져오기
컨트롤러에 해당하는 클래스 이름을 가져옵니다.
```php
$request->controller;
```
`app\controller\IndexController`와 유사한 형태로 반환됩니다.

> 익명 함수는 어떤 컨트롤러에도 속하지 않으므로 익명 함수 라우트에서의 요청의 `$request->controller`은 항상 빈 문자열 `''`를 반환합니다.
> 익명 함수 라우트에 대한 자세한 내용은 [라우팅](route.md)을 참조하십시오.


## 요청의 메서드 이름 가져오기
요청에 해당하는 컨트롤러의 메서드 이름을 가져옵니다.
```php
$request->action;
```
`index`와 유사한 형태로 반환됩니다.

> 익명 함수는 어떤 컨트롤러에도 속하지 않으므로 익명 함수 라우트에서의 요청의 `$request->action`은 항상 빈 문자열 `''`를 반환합니다.
> 익명 함수 라우트에 대한 자세한 내용은 [라우팅](route.md)을 참조하십시오.


