# 애플리케이션 플러그인
각 애플리케이션 플러그인은 완전한 애플리케이션이며, 소스 코드는 `{주 프로젝트}/plugin` 디렉토리에 저장됩니다.

> **팁**
> `php webman app-plugin:create {플러그인명}` 명령어를 사용하여 로컬에서 애플리케이션 플러그인을 만들 수 있습니다(웹맨/콘솔>=1.2.16 버전 필요).
예를 들어 `php webman app-plugin:create cms` 명령어를 사용하면 다음과 같은 디렉토리 구조가 생성됩니다.

```plaintext
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

애플리케이션 플러그인은 웹맨과 동일한 디렉터리 구조 및 구성 파일을 갖고 있습니다. 사실 애플리케이션 플러그인을 개발하는 것은 웹맨 프로젝트를 개발하는 것과 거의 유사한 경험이며, 다음 몇 가지 측면에 유의하면 됩니다.

## 네임스페이스
플러그인 디렉토리 및 명명은 PSR4 규격을 따릅니다. 플러그인은 plugin으로 시작하는 네임스페이스를 가지며, 예를 들어 `plugin\cms\app\controller\UserController`에서 cms는 플러그인의 소스 코드 주 디렉토리입니다.

## URL 접근
애플리케이션 플러그인의 URL 주소 경로는 모두 `/app`으로 시작합니다. 예를 들어 `plugin\cms\app\controller\UserController`의 URL 주소는 `http://127.0.0.1:8787/app/cms/user`입니다.

## 정적 파일
정적 파일은 `plugin/{플러그인}/public`에 위치하며, 예를 들어 `http://127.0.0.1:8787/app/cms/avatar.png`에 접근하는 것은 실제로 `plugin/cms/public/avatar.png` 파일을 가져오는 것입니다.

## 구성 파일
플러그인의 구성은 일반적인 웹맨 프로젝트와 동일하지만, 플러그인의 구성은 일반적으로 현재 플러그인에만 적용되며 주 프로젝트에는 일반적으로 영향을 미치지 않습니다. 
예를 들어 `plugin.cms.app.controller_suffix`의 값은 주로 플러그인의 컨트롤러 접미사에 영향을 끼치지만, 주 프로젝트에는 영향을 미치지 않습니다.
예를 들어 `plugin.cms.app.controller_reuse`의 값은 주로 플러그인의 컨트롤러 재사용 여부에 영향을 미치지만, 주 프로젝트에는 영향을 미치지 않습니다.
예를 들어 `plugin.cms.middleware`의 값은 주로 플러그인의 미들웨어에 영향을 미치지만, 주 프로젝트에는 영향을 미치지 않습니다.
예를 들어 `plugin.cms.view`의 값은 주로 플러그인에서 사용하는 뷰에 영향을 미치지만, 주 프로젝트에는 영향을 미치지 않습니다.
예를 들어 `plugin.cms.container`의 값은 주로 플러그인에서 사용하는 컨테이너에 영향을 미치지만, 주 프로젝트에는 영향을 미치지 않습니다.
예를 들어 `plugin.cms.exception`의 값은 주로 플러그인의 예외 처리 클래스에 영향을 미치지만, 주 프로젝트에는 영향을 미치지 않습니다.
그러나 라우팅은 전역적이므로 플러그인으로 설정한 라우팅은 전역에 영향을 미칩니다.

## 구성 가져오기
특정 플러그인 구성을 가져오는 방법은 `config('plugin.{플러그인}.{구체적인 설정}')`입니다. 예를 들어 `plugin/cms/config/app.php`의 모든 설정을 가져오는 방법은 `config('plugin.cms.app')`입니다. 
마찬가지로, 주 프로젝트나 다른 플러그인은 `config('plugin.cms.xxx')`를 사용하여 cms 플러그인의 구성을 가져올 수 있습니다.

## 지원하지 않는 설정
애플리케이션 플러그인은 `server.php`, `session.php` 구성을 지원하지 않으며, `app.request_class`, `app.public_path`, `app.runtime_path` 설정도 지원하지 않습니다.

## 데이터베이스
플러그인은 자체 데이터베이스를 구성할 수 있습니다. 예를 들어 `plugin/cms/config/database.php` 내용은 다음과 같습니다.
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql은 연결 이름입니다
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '데이터베이스',
            'username'    => '사용자명',
            'password'    => '비밀번호',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin은 연결 이름입니다
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '데이터베이스',
            'username'    => '사용자명',
            'password'    => '비밀번호',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
참조 방법은 `Db::connection('plugin.{플러그인}.{연결 이름}');`입니다. 예를 들어
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```
주 프로젝트의 데이터베이스를 사용하려면 직접 사용하면 됩니다. 예를 들어
```php
use support\Db;
Db::table('user')->first();
// 주 프로젝트에 admin 연결이 있을 경우
Db::connection('admin')->table('admin')->first();
```
> **팁**
> thinkorm도 비슷한 방식으로 사용됩니다.

## Redis
Redis 사용법은 데이터베이스와 유사합니다. 예를 들어 `plugin/cms/config/redis.php`는 다음과 같습니다.
```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ],
];
```
사용할 때는 다음과 같이 합니다.
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```
마찬가지로, 주 프로젝트의 Redis 구성을 재사용하려면 다음과 같이 합니다.
```php
use support\Redis;
Redis::get('key');
// 주 프로젝트가 cache 연결을 구성한 경우
Redis::connection('cache')->get('key');
```

## 로깅
로그 클래스 사용법은 데이터베이스 사용법과 유사합니다.
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```
주 프로젝트의 로그 구성을 재사용하려면 다음과 같이 사용하면 됩니다.
```php
use support\Log;
Log::info('로그 내용');
// 주 프로젝트에 test 로그 구성이 있는 경우
Log::channel('test')->info('로그 내용');
```

# 애플리케이션 플러그인 설치 및 제거
애플리케이션 플러그인을 설치하려면 플러그인 디렉토리를 `{주 프로젝트}/plugin` 디렉토리로 복사하면 됩니다. reload 또는 restart하여 적용해야 합니다.
제거할 때는 간단히 `{주 프로젝트}/plugin`에 해당 플러그인 디렉토리를 삭제하면 됩니다.
