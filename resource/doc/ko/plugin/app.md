# 어플리케이션 플러그인
각 어플리케이션 플러그인은 완전한 애플리케이션입니다. 소스 코드는 `{메인프로젝트}/plugin` 디렉터리에 위치합니다.

> **팁**
> `php webman app-plugin:create {플러그인 이름}` 명령을 사용함으로써(이 경우에는 webman/console>=1.2.16이 필요합니다) 로컬에 애플리케이션 플러그인을 생성할 수 있습니다.
> 예를 들어, `php webman app-plugin:create cms` 명령을 사용하면 다음과 같은 디렉터리 구조가 생성됩니다.

```
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

여기에서 플러그인은 webman과 동일한 디렉토리 구조와 구성 파일을 가지고 있는 것을 볼 수 있습니다. 사실 애플리케이션 플러그인을 개발하는 것은 웹맨 프로젝트를 개발하는 것과 거의 유사한 경험을 제공합니다. 단지 몇 가지 측면에 주의해야 합니다.

## 네임스페이스
플러그인 디렉터리와 네이밍은 PSR4 규격을 따릅니다. 플러그인은 plugin 디렉터리에 위치하기 때문에 네임스페이스는 보통 plugin으로 시작합니다. 예를 들어 `plugin\cms\app\controller\UserController`와 같이 cms는 플러그인의 소스 코드 주요 디렉토리입니다.

## URL 액세스
어플리케이션 플러그인의 URL 주소 경로는 항상 `/app`로 시작합니다. 예를 들어 `plugin\cms\app\controller\UserController`의 URL 주소는 `http://127.0.0.1:8787/app/cms/user`입니다.

## 정적 파일
정적 파일은 `plugin/{플러그인}/public`에 위치합니다. 예를 들어 `http://127.0.0.1:8787/app/cms/avatar.png`을 방문하는 경우 사실상 `plugin/cms/public/avatar.png` 파일을 가져오는 것입니다.

## 설정 파일
플러그인의 구성은 일반적인 webman 프로젝트와 동일하지만 플러그인의 구성은 일반적으로 현재 플러그인에만 영향을 미치며 주요 프로젝트에는 영향을 미치지 않습니다.
예를 들어 `plugin.cms.app.controller_suffix`의 값은 주로 플러그인의 컨트롤러 접미사에만 영향을 미치며 주요 프로젝트에는 영향을 미치지 않습니다.
예를 들어 `plugin.cms.app.controller_reuse`의 값은 주로 플러그인의 컨트롤러 재사용 여부에만 영향을 미치며 주요 프로젝트에는 영향을 미치지 않습니다.
예를 들어 `plugin.cms.middleware`의 값은 주로 플러그인의 미들웨어에만 영향을 미치며 주요 프로젝트에는 영향을 미치지 않습니다.
예를 들어 `plugin.cms.view`의 값은 주로 플러그인에서 사용하는 뷰에만 영향을 미치며 주요 프로젝트에는 영향을 미치지 않습니다.
예를 들어 `plugin.cms.container`의 값은 주로 플러그인에서 사용하는 컨테이너에만 영향을 미치며 주요 프로젝트에는 영향을 미치지 않습니다.
예를 들어 `plugin.cms.exception`의 값은 주로 플러그인의 예외 처리 클래스에만 영향을 미치며 주요 프로젝트에는 영향을 미치지 않습니다.
그러나 라우트는 전역적이기 때문에 플러그인 구성의 라우트도 전역적인 영향을 미칩니다.

## 구성 가져오기
특정 플러그인 구성을 가져오는 방법은 `config('plugin.{플러그인}.{구체적인 구성}');`입니다. 예를 들어 `config('plugin.cms.app')`를 사용하여 `plugin/cms/config/app.php`의 모든 구성을 가져올 수 있습니다.
마찬가지로 메인 프로젝트나 다른 플러그인은 `config('plugin.cms.xxx')`를 사용하여 cms 플러그인의 구성을 가져올 수 있습니다.

## 지원되지 않는 구성
어플리케이션 플러그인은 server.php, session.php 설정을 지원하지 않으며, `app.request_class`, `app.public_path`, `app.runtime_path` 구성도 지원하지 않습니다.

## 데이터베이스
플러그인은 자체 데이터베이스를 구성할 수 있습니다. 예를 들어 `plugin/cms/config/database.php`의 내용은 다음과 같습니다.
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql는 연결 이름입니다
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '데이터베이스',
            'username'    => '사용자이름',
            'password'    => '암호',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin은 연결 이름입니다
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '데이터베이스',
            'username'    => '사용자이름',
            'password'    => '암호',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
이를 사용하는 방법은 `Db::connection('plugin.{플러그인}.{연결이름}');`입니다. 예를 들어
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```
주요 프로젝트의 데이터베이스를 사용하려면 직접 사용하면 됩니다. 예를 들어
```php
use support\Db;
Db::table('user')->first();
// 주요 프로젝트에 admin 연결이 구성된 경우
Db::connection('admin')->table('admin')->first();
```
> **팁**
> thinkorm도 이와 유사한 방법으로 사용할 수 있습니다.

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
사용할 때는 다음과 같이 사용합니다.
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```
마찬가지로 주요 프로젝트의 Redis 구성을 재사용하려면 다음과 같이 사용합니다.
```php
use support\Redis;
Redis::get('key');
// 주요 프로젝트에 캐시 연결이 구성된 경우
Redis::connection('cache')->get('key');
```

## 로그
로그 클래스의 사용법도 데이터베이스 사용법과 유사합니다.
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```
주요 프로젝트의 로그 구성을 재사용하려면 다음과 같이 사용합니다.
```php
use support\Log;
Log::info('로그 내용');
// 주요 프로젝트에 test 로그 구성이 있는 경우
Log::channel('test')->info('로그 내용');
```

# 어플리케이션 플러그인 설치 및 제거
애플리케이션 플러그인을 설치할 때는 해당 플러그인 디렉터리를 `{메인프로젝트}/plugin` 디렉터리에 복사하면 됩니다. 다시 로드(reload)하거나 재시작(restart)해야 적용됩니다.
제거할 때는 `{메인프로젝트}/plugin` 디렉터리에서 해당 플러그인 디렉터리를 직접 삭제하면 됩니다.
