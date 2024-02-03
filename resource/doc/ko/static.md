## 정적 파일 처리
webman은 정적 파일 액세스를 지원하며, 정적 파일은 `public` 디렉토리에 위치합니다. 예를 들어 `http://127.0.0.1:8787/upload/avatar.png`에 액세스하면 실제로는 `{프로젝트 주 디렉토리}/public/upload/avatar.png`에 액세스하게 됩니다.

> **주의**
> webman 1.4부터 앱 플러그인을 지원하며, `/app/xx/파일명`으로 시작하는 정적 파일 액세스는 실제로 앱 플러그인의 `public` 디렉토리를 액세스하는 것입니다. 즉, webman >=1.4.0에서는 `{프로젝트 주 디렉토리}/public/app/`하위 디렉토리에 대한 액세스를 지원하지 않습니다.
> 더 자세한 내용은 [앱 플러그인](./plugin/app.md)을 참조하십시오.

### 정적 파일 지원 비활성화
정적 파일 지원이 필요 없는 경우 `config/static.php`를 열고 `enable` 옵션을 false로 변경하십시오. 비활성화된 경우 모든 정적 파일 액세스는 404를 반환합니다.

### 정적 파일 디렉토리 변경
webman은 기본적으로 public 디렉토리를 정적 파일 디렉토리로 사용합니다. 변경이 필요한 경우 `support/helpers.php`의 `public_path()` 헬퍼 함수를 수정하십시오.

### 정적 파일 미들웨어
webman에는 기본 정적 파일 미들웨어가 있으며 위치는 `app/middleware/StaticFile.php`입니다.
때로는 정적 파일에 대한 처리가 필요할 수 있습니다. 예를 들어 정적 파일에 교차 출처 HTTP 헤더를 추가하거나 점(`.`)으로 시작하는 파일에 액세스를 거부하는 등의 처리가 필요할 수 있습니다.

`app/middleware/StaticFile.php`의 내용은 다음과 유사합니다:
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
        // 숨겨진 파일인 경우 액세스 거부
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // 교차 출처 HTTP 헤더 추가
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
이 미들웨어가 필요한 경우 `config/static.php`에서 `middleware` 옵션을 사용하도록 설정해야 합니다.
