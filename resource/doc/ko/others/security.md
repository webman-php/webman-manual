# 보안

## 실행 사용자
nginx와 같은 사용자와 권한이 낮은 사용자로 실행 사용자를 설정하는 것을 권장합니다. 실행 사용자는 `config/server.php` 파일의 `user` 및 `group`에 설정됩니다. 유사하게 사용자 지정 프로세스는 `config/process.php`의 `user` 및 `group`를 통해 지정됩니다. 
모니터 프로세스는 권한이 높아야만 정상적으로 작동하므로 실행 사용자를 설정하지 말아야 합니다.

## 컨트롤러 규칙
`controller` 디렉토리 또는 하위 디렉토리에는 컨트롤러 파일만 넣을 수 있으며, 다른 종류의 파일을 넣는 것이 금지됩니다. 그렇지 않으면 [컨트롤러 접미사](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)가 활성화되지 않은 경우에 URL로 비정상적인 액세스로 인해 예측할 수 없는 결과가 발생할 수 있습니다. 예를 들어 `app/controller/model/User.php`는 실제로 모델 클래스이지만 `controller` 디렉토리에 잘못되어 있을 경우 [컨트롤러 접미사](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)가 활성화되지 않았다면 사용자는 `/model/user/xxx`와 같은 방식으로 `User.php`의 임의 메서드에 액세스할 수 있습니다. 
이러한 상황을 완전히 제거하기 위해 [컨트롤러 접미사](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)를 사용하여 컨트롤러 파일을 명확하게 표시하는 것을 강력히 권장합니다.

## XSS 필터링
일반적으로 웹맨은 요청에 대한 XSS 이스케이프를 수행하지 않습니다. 웹맨에서는 렌더링할 때 XSS 이스케이프를 권장하고, 데이터베이스에 입력하기 전에 이스케이프하는 것 대신에 twig, blade, think-template 등의 템플릿이 자동으로 XSS 이스케이프를 수행하여 수동 이스케이프가 필요하지 않습니다.

> **팁**
> 데이터베이스에 입력하기 전에 XSS 이스케이프를 수행하는 경우 애플리케이션 플러그인의 호환성 문제가 발생할 수 있습니다.

## SQL 인젝션 방지
SQL 인젝션을 방지하기 위해 ORM인 [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html) 또는 [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html)와 같은 것을 사용하여 SQL을 직접 조립하는 것을 피할 수 있습니다.

## nginx 프록시
응용 프로그램을 외부 사용자에게 노출해야 하는 경우, webman 앞에 nginx 프록시를 추가하는 것이 좋습니다. 이렇게하면 일부 비정상적인 HTTP 요청을 차단하여 보안을 강화할 수 있습니다. 자세한 내용은 [nginx 프록시](nginx-proxy.md)를 참조하십시오.
