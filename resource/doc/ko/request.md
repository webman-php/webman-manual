# 설명

## 요청 객체 가져오기
webman은 자동으로 요청 객체를 action 메서드의 첫 번째 매개변수로 주입합니다. 예를 들어,

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
        // get 요청에서 name 매개변수를 가져오고, 전달된 name 매개변수가 없으면 $default_name을 반환합니다.
        $name = $request->get('name', $default_name);
        // 브라우저에 문자열을 반환합니다.
        return response('hello ' . $name);
    }
}
```

`$request` 객체를 사용하여 요청과 관련된 모든 데이터를 얻을 수 있습니다.

**가끔은 다른 클래스에서 현재 요청의 `$request` 객체를 가져오고 싶을 때 `request()` 도우미 함수를 사용하면 됩니다**;

## Get 요청 매개변수 가져오기

**전체 get 배열 가져오기**
```php
$request->get();
```
get 요청이 없는 경우 빈 배열을 반환합니다.

**특정 키의 get 배열 값 가져오기**
```php
$request->get('name');
```
get 배열에 해당 값이 없는 경우 null을 반환합니다.

또한 get 메서드의 두 번째 매개변수로 기본값을 전달할 수 있으며, get 배열에서 해당 값이 없는 경우 기본값을 반환합니다. 예를 들어:
```php
$request->get('name', 'tom');
```

## Post 요청 매개변수 가져오기
**전체 post 배열 가져오기**
```php
$request->post();
```
post 요청이 없는 경우 빈 배열을 반환합니다.

**특정 키의 post 배열 값 가져오기**
```php
$request->post('name');
```
post 배열에 해당 값이 없는 경우 null을 반환합니다.

get 메서드와 마찬가지로 post 메서드의 두 번째 매개변수로 기본값을 전달하고, post 배열에서 해당 값이 없는 경우 기본값을 반환합니다. 예를 들어:
```php
$request->post('name', 'tom');
```


## 원본 post 바디 가져오기
```php
$post = $request->rawBody();
```
이 기능은 `php-fpm`의 `file_get_contents("php://input");` 작업과 유사합니다. http의 원시 post 요청 바디를 가져오는 데 유용합니다. 

## 헤더 가져오기
**전체 헤더 배열 가져오기**
```php
$request->header();
```
요청에 헤더가 없는 경우 빈 배열을 반환합니다. 모든 키는 소문자입니다.

**헤더 배열의 특정 값 가져오기**
```php
$request->header('host');
```
헤더 배열에 해당 값이 없는 경우 null을 반환합니다. 모든 키는 소문자입니다.

get 메서드와 마찬가지로 header 메서드의 두 번째 매개변수로 기본값을 전달하고, header 배열에서 해당 값이 없는 경우 기본값을 반환합니다. 예를 들어:
```php
$request->header('host', 'localhost');
```

## 쿠키 가져오기
**전체 쿠키 배열 가져오기**
```php
$request->cookie();
```
요청에 쿠키가 없는 경우 빈 배열을 반환합니다.

**쿠키 배열의 특정 값 가져오기**
```php
$request->cookie('name');
```
쿠키 배열에 해당 값이 없는 경우 null을 반환합니다.

get 메서드와 마찬가지로 cookie 메서드의 두 번째 매개변수로 기본값을 전달하고, cookie 배열에서 해당 값이 없는 경우 기본값을 반환합니다. 예를 들어:
```php
$request->cookie('name', 'tom');
```

## 모든 입력 가져오기
`post` 및 `get`을 포함하는 셋물고 있다.
```php
$request->all();
```

## 특정 입력 값 가져오기
`post` 및 `get`을 포함하는 셋물고 있다.
```php
$request->input('name', $default_value);
```

## 일부 입력 데이터 가져오기
`post` 및 `get`을 포함하는 셋물고 있다.
```php
// username 및 password로 구성된 배열을 가져오고, 해당 키가 없으면 무시합니다.
$only = $request->only(['username', 'password']);
// avatar 및 age 이외의 모든 입력을 가져옵니다.
$except = $request->except(['avatar', 'age']);
```

## 업로드 파일 가져오기
**전체 업로드 파일 배열 가져오기**
```php
$request->file();
```

유사한 양식:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

`$request->file()`의 형식은 다음과 유사합니다:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
이것은 `webman\Http\UploadFile` 인스턴스의 배열입니다. `webman\Http\UploadFile` 클래스는 PHP 기본[`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) 클래스를 상속하고 유용한 메서드를 제공합니다.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // 파일이 유효한지 여부, 예 : true|false
            var_export($spl_file->getUploadExtension()); // 업로드 파일 확장자, 예 : 'jpg'
            var_export($spl_file->getUploadMimeType()); // 업로드 파일 MIME 유형, 예 : 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // 업로드 오류 코드 가져오기, 예 : UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_NO_FILE, UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // 업로드 파일 이름, 예 : 'my-test.jpg'
            var_export($spl_file->getSize()); // 파일 크기 가져오기, 예 : 13364, 단위 바이트
            var_export($spl_file->getPath()); // 업로드된 디렉토리 가져오기, 예 : '/tmp'
            var_export($spl_file->getRealPath()); // 임시 파일 경로 가져오기, 예 : `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**참고 :**

- 파일이 업로드된 후에는 임시 파일 이름으로 저장됩니다. 예 : `/tmp/workerman.upload.SRliMu`
- 업로드 파일 크기는 [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html) 제한을 받으며 기본적으로 10M이며 `config/server.php` 파일에서 `max_package_size`를 수정하여 기본값을 변경할 수 있습니다.
- 요청이 끝나면 임시 파일이 자동으로 제거됩니다.
- 파일 업로드가 없는 경우 `$request->file()`은 빈 배열을 반환합니다.
- 업로드된 파일은 `move_uploaded_file()` 메서드를 지원하지 않으며, 대신 `$file->move()` 메서드를 사용하십시오. 아래 예시 참조

### 특정 업로드 파일 가져오기
```php
$request->file('avatar');
```
파일이 존재하는 경우 해당 파일의 `webman\Http\UploadFile` 인스턴스를 반환하고, 그렇지 않으면 null을 반환합니다.

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
## 호스트 정보 가져오기
요청의 호스트 정보를 가져옵니다.
```php
$request->host();
```
요청이 표준이 아닌 80번 또는 443번 포트가 아니라면 호스트 정보에 포트가 포함될 수 있습니다. 예를 들면 `example.com:8080`와 같습니다. 포트를 포함시키지 않으려면 첫 번째 매개변수에 `true`를 전달합니다.
```php
$request->host(true);
```

## 요청 메소드 가져오기
```php
 $request->method();
```
반환 값은 `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, `HEAD` 중 하나일 수 있습니다.

## 요청 URI 가져오기
```php
$request->uri();
```
패스와 쿼리 문자열을 포함한 요청의 URI를 반환합니다.

## 요청 경로 가져오기
```php
$request->path();
```
요청의 패스 부분을 반환합니다.

## 요청 쿼리 문자열 가져오기
```php
$request->queryString();
```
요청의 쿼리 문자열 부분을 반환합니다.

## 요청 URL 가져오기
`url()` 메소드는 `Query` 매개변수를 포함하지 않은 URL을 반환합니다.
```php
$request->url();
```
`//www.workerman.net/workerman-chat`와 같은 값을 반환합니다.

`fullUrl()` 메소드는 `Query` 매개변수를 포함한 URL을 반환합니다.
```php
$request->fullUrl();
```
`//www.workerman.net/workerman-chat?type=download`와 같은 값을 반환합니다.

> **참고**
> `url()`과 `fullUrl()`은 프로토콜 부분(http 또는 https)을 반환하지 않습니다. 
> 브라우저에서 `//example.com`와 같이`//`으로 시작하는 주소를 사용하면 자동으로 현재 사이트의 프로토콜을 인식합니다. http 또는 https로 요청을 자동으로 보냅니다.

Nginx 프록시를 사용하는 경우 Nginx 구성에 `proxy_set_header X-Forwarded-Proto $scheme;`를 추가해야 합니다. [Nginx 프록시 참고](others/nginx-proxy.md)하여, 이렇게 하면 `$request->header('x-forwarded-proto');`를 사용하여 http 또는 https 여부를 확인할 수 있습니다.
```php
echo $request->header('x-forwarded-proto'); // http 또는 https 출력
```

## 요청 HTTP 버전 가져오기
```php
$request->protocolVersion();
```
문자열 `1.1` 또는 `1.0`을 반환합니다.

## 요청 세션 ID 가져오기
```php
$request->sessionId();
```
알파벳과 숫자로 구성된 문자열을 반환합니다.

## 요청 클라이언트 IP 가져오기
```php
$request->getRemoteIp();
```

## 요청 클라이언트 포트 가져오기
```php
$request->getRemotePort();
```

## 요청 클라이언트 실제 IP 가져오기
```php
$request->getRealIp($safe_mode=true);
```
프로젝트가 대리자(예: Nginx)를 사용하는 경우,`$request->getRemoteIp()`를 사용하여 얻는 것은 대체로 대리자 서버 IP(예: `127.0.0.1` `192.168.x.x`)이며 클라이언트의 실제 IP가 아닙니다. 이때 `$request->getRealIp()`를 사용하여 클라이언트의 실제 IP를 확인할 수 있습니다. 

`$request->getRealIp()`는 HTTP 헤더의 `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, `via` 필드에서 실제 IP를 가져오려고 시도합니다.

> HTTP 헤더는 쉽게 위조될 수 있기 때문에, 이 메소드로 얻은 클라이언트 IP는 100% 신뢰할 수 없습니다. 특히 `$safe_mode`가 false일 때입니다. 대리자를 통해 클라이언트의 실제 IP를 확인하는 더 확실한 방법은 안전하다고 알려진 대리자 서버 IP를 확인하고, 어떤 HTTP 헤더가 실제 IP를 가지고 있는지 명확히 알고 있는 것입니다. 이후 `$request->getRemoteIp()`가 반환한 IP가 이미 알려진 안전한 대리자 서버임을 확인하고, `header('HTTP 헤더')`를 사용하여 실제 IP를 가져오면 됩니다.


## 서버 IP 가져오기
```php
$request->getLocalIp();
```

## 서버 포트 가져오기
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

## JSON 반환을 기대하는지 확인하기
```php
$request->expectsJson();
```

## 클라이언트가 JSON 반환을 수락하는지 확인하기
```php
$request->acceptJson();
```

## 요청한 플러그인 이름 가져오기
플러그인 요청이 아니면 빈 문자열(`''`)을 반환합니다.
```php
$request->plugin;
```
> 이 기능은 webman>=1.4.0에서 사용할 수 있습니다.

## 요청한 어플리케이션 이름 가져오기
단일 어플리케이션의 경우 항상 빈 문자열(`''`)을 반환하며, [멀티 어플리케이션](multiapp.md)의 경우 어플리케이션 이름을 반환합니다.
```php
$request->app;
```
> 클로저 함수는 어떤 어플리케이션에 속하지 않으므로, 클로저 라우트에서의 요청은 항상 빈 문자열(`''`)을 반환합니다.
> 클로저 라우트는 [라우트](route.md)를 참조하세요.

## 요청한 컨트롤러 클래스 이름 가져오기
컨트롤러에 해당하는 클래스 이름을 가져옵니다.
```php
$request->controller;
```
`app\controller\IndexController`와 같은 값을 반환합니다.

> 클로저 함수는 어떤 컨트롤러에 속하지 않으므로, 클로저 라우트에서의 요청은 항상 빈 문자열(`''`)을 반환합니다.
> 클로저 라우트는 [라우트](route.md)를 참조하세요.

## 요청한 메소드 이름 가져오기
요청에 해당하는 컨트롤러 메소드 이름을 가져옵니다.
```php
$request->action;
```
`index`와 같은 값을 반환합니다.

> 클로저 함수는 어떤 메소드에 속하지 않으므로, 클로저 라우트에서의 요청은 항상 빈 문자열(`''`)을 반환합니다.
> 클로저 라우트는 [라우트](route.md)를 참조하세요.  
