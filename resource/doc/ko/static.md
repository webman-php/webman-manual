## 정적 파일 처리
webman은 정적 파일 액세스를 지원하며, 정적 파일은 모두 `public` 디렉토리에 위치합니다. 예를 들어 `http://127.0.0.8787/upload/avatar.png`에 액세스하는 것은 실제로 `{프로젝트 기본 디렉토리}/public/upload/avatar.png`에 액세스하는 것입니다.

> **주의**
> webman 1.4부터는 애플리케이션 플러그인을 지원합니다. `/app/xx/파일명`으로 시작하는 정적 파일 액세스는 실제로 애플리케이션 플러그인의 `public` 디렉토리에 액세스하는 것을 의미합니다. 즉, webman >=1.4.0일 때는 `{프로젝트 기본 디렉토리}/public/app/`하위 디렉토리 액세스를 지원하지 않습니다.
> 자세한 내용은 [애플리케이션 플러그인](./plugin/app.md)을 참조하십시오.

### 정적 파일 지원 비활성화
정적 파일 지원이 필요하지 않은 경우 `config/static.php`를 열고 `enable` 옵션을 false로 변경하십시오. 비활성화된 후 모든 정적 파일 액세스는 404를 반환합니다.

### 정적 파일 디렉토리 변경
webman은 기본적으로 public 디렉토리를 정적 파일 디렉토리로 사용합니다. 변경이 필요한 경우 `support/helpers.php`의 `public_path()` 헬퍼 함수를 수정하십시오.

### 정적 파일 미들웨어
webman에는 기본적으로 정적 파일 미들웨어가 있으며, 위치는 `app/middleware/StaticFile.php`에 있습니다.
가끔 정적 파일에 대해 처리할 필요가 있는데, 이는 정적 파일에 크로스 도메인 헤더를 추가하거나, 점(`.`)으로 시작하는 파일 액세스를 거부하는 등의 작업이 필요한 경우에 사용됩니다.

`app/middleware/StaticFile.php`의 내용은 다음과 같습니다:
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // 숨겨진 파일인 .으로 시작하는 파일 액세스 거부
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // 크로스 도메인 헤더 추가
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
이 미들웨어가 필요한 경우에는 `config/static.php`의 `middleware` 옵션을 활성화해야 합니다.
