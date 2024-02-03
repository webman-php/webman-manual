# 보안

## 실행 사용자
nginx 실행 사용자와 유사한 권한을 갖는 사용자로 실행 사용자를 설정하는 것이 좋습니다. 실행 사용자는 `config/server.php`의 `user` 및 `group`에 설정됩니다. 비슷하게 사용자 지정 프로세스의 사용자는 `config/process.php`의 `user` 및 `group`를 통해 지정됩니다. 모니터 프로세스는 고도의 권한이 필요하기 때문에 실행 사용자를 설정하지 않아야 합니다.

## 컨트롤러 규정
`controller` 디렉토리 또는 하위 디렉토리에는 컨트롤러 파일만을 놓아두어야 하며, 다른 종류의 클래스 파일을 놓아둘 경우 [컨트롤러 접두사](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)가 활성화되지 않은 경우 URL에서 불법한 액세스로 인해 예측할 수 없는 결과를 초래할 수 있습니다. 예를 들어 `app/controller/model/User.php`은 실제로 모델 클래스이지만 잘못된 위치에 놓여 있는 경우 [컨트롤러 접미사](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)가 활성화되지 않은 경우 사용자가 `/model/user/xxx`와 같은 경로를 통해 `User.php`의 임의 메서드에 액세스할 수 있습니다. 이러한 상황을 완전히 방지하기 위해, 어떤 것이 컨트롤러 파일인지 명확히 표시하기 위해 [컨트롤러 후행접미사](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)를 사용하는 것을 강력히 권장합니다.


## XSS 필터링
일반성을 고려하여, webman은 요청에 대한 XSS 이스케이프를 수행하지 않습니다. webman은 렌더링할 때 XSS 이스케이프를 강력히 권장하며, 데이터베이스에 입력하기 전에 이스케이프를 수행하지 않는 것이 좋습니다. 또한 트윅, 블레이드, 싱크-템플릿 등의 템플릿은 자동으로 XSS 이스케이프를 수행하므로 수동으로 이스케이프할 필요가 없습니다. 매우 편리합니다.

> **팁**
> 데이터베이스에 입력하기 전에 XSS 이스케이프를 수행하면 애플리케이션 플러그인의 호환성 문제가 발생할 수 있습니다.


## SQL 인젝션 방지
SQL 인젝션을 방지하기 위해 ORM을 사용하는 것이 좋습니다. (예: [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html), [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html)) 가능한 한 직접 SQL을 조립하는 것을 피하십시오.

## nginx 프록시
애플리케이션을 외부 사용자에게 노출해야 하는 경우, webman 앞에 nginx 프록시를 추가하여 일부 불법 HTTP 요청을 필터링하고 보안을 강화하는 것이 좋습니다. 자세한 내용은 [nginx 프록시](nginx-proxy.md)를 참조하십시오.
