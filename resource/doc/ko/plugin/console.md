# 명령줄

Webman 명령줄 컴포넌트

## 설치
```
composer require webman/console
```

## 목차

### 코드 생성
- [make:controller](#make-controller) - 컨트롤러 클래스 생성
- [make:model](#make-model) - 데이터베이스 테이블에서 모델 클래스 생성
- [make:crud](#make-crud) - 전체 CRUD 생성 (모델 + 컨트롤러 + 검증기)
- [make:middleware](#make-middleware) - 미들웨어 클래스 생성
- [make:command](#make-command) - 콘솔 명령 클래스 생성
- [make:bootstrap](#make-bootstrap) - 부트스트랩 초기화 클래스 생성
- [make:process](#make-process) - 사용자 정의 프로세스 클래스 생성

### 빌드 및 배포
- [build:phar](#build-phar) - 프로젝트를 PHAR 아카이브 파일로 패키징
- [build:bin](#build-bin) - 프로젝트를 독립 실행형 바이너리 파일로 패키징
- [install](#install) - Webman 설치 스크립트 실행

### 유틸리티 명령
- [version](#version) - Webman 프레임워크 버전 표시
- [fix-disable-functions](#fix-disable-functions) - php.ini의 비활성화 함수 수정
- [route:list](#route-list) - 등록된 모든 라우트 표시

### 앱 플러그인 관리 (app-plugin:*)
- [app-plugin:create](#app-plugin-create) - 새 앱 플러그인 생성
- [app-plugin:install](#app-plugin-install) - 앱 플러그인 설치
- [app-plugin:uninstall](#app-plugin-uninstall) - 앱 플러그인 제거
- [app-plugin:update](#app-plugin-update) - 앱 플러그인 업데이트
- [app-plugin:zip](#app-plugin-zip) - 앱 플러그인을 ZIP으로 패키징

### 플러그인 관리 (plugin:*)
- [plugin:create](#plugin-create) - 새 Webman 플러그인 생성
- [plugin:install](#plugin-install) - Webman 플러그인 설치
- [plugin:uninstall](#plugin-uninstall) - Webman 플러그인 제거
- [plugin:enable](#plugin-enable) - Webman 플러그인 활성화
- [plugin:disable](#plugin-disable) - Webman 플러그인 비활성화
- [plugin:export](#plugin-export) - 플러그인 소스 코드 내보내기

### 서비스 관리
- [start](#start) - Webman 워커 프로세스 시작
- [stop](#stop) - Webman 워커 프로세스 중지
- [restart](#restart) - Webman 워커 프로세스 재시작
- [reload](#reload) - 무중단 코드 리로드
- [status](#status) - 워커 프로세스 상태 확인
- [connections](#connections) - 워커 프로세스 연결 정보 조회

## 코드 생성

<a name="make-controller"></a>
### make:controller

컨트롤러 클래스를 생성합니다.

**사용법:**
```bash
php webman make:controller <이름>
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 예 | 컨트롤러 이름(접미사 제외) |

**옵션:**

| 옵션 | 단축 | 설명 |
|--------|----------|-------------|
| `--plugin` | `-p` | 해당 플러그인 디렉터리에 컨트롤러 생성 |
| `--path` | `-P` | 사용자 정의 컨트롤러 경로 |
| `--force` | `-f` | 파일이 존재하면 덮어쓰기 |
| `--no-suffix` | | "Controller" 접미사 추가 안 함 |

**예시:**
```bash
# app/controller에 UserController 생성
php webman make:controller User

# 플러그인에 생성
php webman make:controller AdminUser -p admin

# 사용자 정의 경로
php webman make:controller User -P app/api/controller

# 기존 파일 덮어쓰기
php webman make:controller User -f

# "Controller" 접미사 없이 생성
php webman make:controller UserHandler --no-suffix
```

**생성되는 파일 구조:**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function index(Request $request)
    {
        return response('hello user');
    }
}
```

**설명:**
- 컨트롤러는 기본적으로 `app/controller/` 디렉터리에 배치됩니다
- 설정된 컨트롤러 접미사가 자동으로 추가됩니다
- 파일이 존재하면 덮어쓸지 확인 메시지가 표시됩니다(이하 동일)

<a name="make-model"></a>
### make:model

데이터베이스 테이블에서 모델 클래스를 생성하며, Laravel ORM과 ThinkORM을 지원합니다.

**사용법:**
```bash
php webman make:model [이름]
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 아니오 | 모델 클래스명, 대화형 모드에서는 생략 가능 |

**옵션:**

| 옵션 | 단축 | 설명 |
|--------|----------|-------------|
| `--plugin` | `-p` | 해당 플러그인 디렉터리에 모델 생성 |
| `--path` | `-P` | 대상 디렉터리(프로젝트 루트 기준 상대 경로) |
| `--table` | `-t` | 테이블명 지정, 테이블명이 규칙에 맞지 않을 때 명시적으로 지정 권장 |
| `--orm` | `-o` | ORM 선택: `laravel` 또는 `thinkorm` |
| `--database` | `-d` | 데이터베이스 연결명 지정 |
| `--force` | `-f` | 기존 파일 덮어쓰기 |

**경로 설명:**
- 기본값: `app/model/`(메인 앱) 또는 `plugin/<플러그인>/app/model/`(플러그인)
- `--path`는 프로젝트 루트 기준 상대 경로(예: `plugin/admin/app/model`)
- `--plugin`과 `--path`를 동시에 사용할 때는 둘 다 동일한 디렉터리를 가리켜야 합니다

**예시:**
```bash
# app/model에 User 모델 생성
php webman make:model User

# 테이블명과 ORM 지정
php webman make:model User -t wa_users -o laravel

# 플러그인에 생성
php webman make:model AdminUser -p admin

# 사용자 정의 경로
php webman make:model User -P plugin/admin/app/model
```

**대화형 모드:** 이름을 전달하지 않으면 대화형 흐름으로 진입: 테이블 선택 → 모델명 입력 → 경로 입력. 지원: Enter로 더 보기, `0`으로 빈 모델 생성, `/키워드`로 테이블 필터링.

**생성되는 파일 구조:**
```php
<?php
namespace app\model;

use support\Model;

/**
 * @property integer $id (기본 키)
 * @property string $name
 */
class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;
}
```

테이블 구조에 따라 `@property` 주석이 자동 생성됩니다. MySQL, PostgreSQL을 지원합니다.

<a name="make-crud"></a>
### make:crud

데이터베이스 테이블을 기반으로 모델, 컨트롤러, 검증기를 한 번에 생성하여 전체 CRUD 기능을 구성합니다.

**사용법:**
```bash
php webman make:crud
```

**옵션:**

| 옵션 | 단축 | 설명 |
|--------|----------|-------------|
| `--table` | `-t` | 테이블명 지정 |
| `--model` | `-m` | 모델 클래스명 |
| `--model-path` | `-M` | 모델 디렉터리(프로젝트 루트 기준 상대 경로) |
| `--controller` | `-c` | 컨트롤러 클래스명 |
| `--controller-path` | `-C` | 컨트롤러 디렉터리 |
| `--validator` | | 검증기 클래스명(`webman/validation` 의존) |
| `--validator-path` | | 검증기 디렉터리(`webman/validation` 의존) |
| `--plugin` | `-p` | 해당 플러그인 디렉터리에 관련 파일 생성 |
| `--orm` | `-o` | ORM: `laravel` 또는 `thinkorm` |
| `--database` | `-d` | 데이터베이스 연결명 |
| `--force` | `-f` | 기존 파일 덮어쓰기 |
| `--no-validator` | | 검증기 생성 안 함 |
| `--no-interaction` | `-n` | 비대화형 모드, 기본값 사용 |

**실행 흐름:** `--table`을 지정하지 않으면 대화형 테이블 선택으로 진입; 모델명은 기본적으로 테이블명에서 추론; 컨트롤러명은 기본적으로 모델명 + 컨트롤러 접미사; 검증기명은 기본적으로 컨트롤러명에서 접미사 제거 + `Validator`. 각 경로 기본값: 모델 `app/model/`, 컨트롤러 `app/controller/`, 검증기 `app/validation/`; 플러그인은 `plugin/<플러그인>/app/` 하위 디렉터리.

**예시:**
```bash
# 대화형 생성(테이블 선택 후 단계별 확인)
php webman make:crud

# 테이블명 지정
php webman make:crud --table=users

# 테이블명과 플러그인 지정
php webman make:crud --table=users --plugin=admin

# 각 경로 지정
php webman make:crud --table=users --model-path=app/model --controller-path=app/controller

# 검증기 생성 안 함
php webman make:crud --table=users --no-validator

# 비대화형 + 덮어쓰기
php webman make:crud --table=users --no-interaction --force
```

**생성되는 파일 구조:**

모델(`app/model/User.php`):
```php
<?php

namespace app\model;

use support\Model;

class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
}
```

컨트롤러(`app/controller/UserController.php`):
```php
<?php

namespace app\controller;

use support\Request;
use support\Response;
use app\model\User;
use app\validation\UserValidator;
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(validator: UserValidator::class, scene: 'create', in: ['body'])]
    public function create(Request $request): Response
    {
        $data = $request->post();
        $model = new User();
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'update', in: ['body'])]
    public function update(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $data = $request->post();
        unset($data['id']);
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'delete', in: ['body'])]
    public function delete(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $model->delete();
        return json(['code' => 0, 'msg' => 'ok']);
    }

    #[Validate(validator: UserValidator::class, scene: 'detail')]
    public function detail(Request $request): Response
    {
        if (!$model = User::find($request->input('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }
}
```

검증기(`app/validation/UserValidator.php`):
```php
<?php
declare(strict_types=1);

namespace app\validation;

use support\validation\Validator;

class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:0',
        'username' => 'required|string|max:32'
    ];

    protected array $messages = [];

    protected array $attributes = [
        'id' => '기본 키',
        'username' => '사용자 이름'
    ];

    protected array $scenes = [
        'create' => ['username', 'nickname'],
        'update' => ['id', 'username'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
```

**설명:**
- `webman/validation`이 설치되지 않았거나 활성화되지 않으면 검증기 생성이 자동으로 건너뜁니다(설치: `composer require webman/validation`).
- 검증기의 `attributes`는 데이터베이스 필드 주석에 따라 자동 생성되며, 주석이 없으면 `attributes`가 생성되지 않습니다.
- 검증기 오류 메시지는 다국어를 지원하며, 언어는 `config('translation.locale')`에 따라 자동 선택됩니다.

<a name="make-middleware"></a>
### make:middleware

미들웨어 클래스를 생성하고 `config/middleware.php`(플러그인은 `plugin/<플러그인>/config/middleware.php`)에 자동 등록합니다.

**사용법:**
```bash
php webman make:middleware <이름>
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 예 | 미들웨어 이름 |

**옵션:**

| 옵션 | 단축 | 설명 |
|--------|----------|-------------|
| `--plugin` | `-p` | 해당 플러그인 디렉터리에 미들웨어 생성 |
| `--path` | `-P` | 대상 디렉터리(프로젝트 루트 기준 상대 경로) |
| `--force` | `-f` | 기존 파일 덮어쓰기 |

**예시:**
```bash
# app/middleware에 Auth 미들웨어 생성
php webman make:middleware Auth

# 플러그인에 생성
php webman make:middleware Auth -p admin

# 사용자 정의 경로
php webman make:middleware Auth -P plugin/admin/app/middleware
```

**생성되는 파일 구조:**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Auth implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        return $handler($request);
    }
}
```

**설명:**
- 기본적으로 `app/middleware/` 디렉터리에 배치됩니다
- 생성 후 해당 middleware 설정 파일에 클래스명이 자동으로 추가되어 활성화됩니다

<a name="make-command"></a>
### make:command

콘솔 명령 클래스를 생성합니다.

**사용법:**
```bash
php webman make:command <명령명>
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `명령명` | 예 | 명령 이름, 형식: `group:action`(예: `user:list`) |

**옵션:**

| 옵션 | 단축 | 설명 |
|--------|----------|-------------|
| `--plugin` | `-p` | 해당 플러그인 디렉터리에 명령 생성 |
| `--path` | `-P` | 대상 디렉터리(프로젝트 루트 기준 상대 경로) |
| `--force` | `-f` | 기존 파일 덮어쓰기 |

**예시:**
```bash
# app/command에 user:list 명령 생성
php webman make:command user:list

# 플러그인에 생성
php webman make:command user:list -p admin

# 사용자 정의 경로
php webman make:command user:list -P plugin/admin/app/command

# 기존 파일 덮어쓰기
php webman make:command user:list -f
```

**생성되는 파일 구조:**
```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('user:list', 'user list')]
class UserList extends Command
{
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Hello</info> <comment>' . $this->getName() . '</comment>');
        return self::SUCCESS;
    }
}
```

**설명:**
- 기본적으로 `app/command/` 디렉터리에 배치됩니다

<a name="make-bootstrap"></a>
### make:bootstrap

부트스트랩 초기화 클래스(Bootstrap)를 생성합니다. 프로세스 시작 시 클래스의 start 메서드가 자동으로 호출되며, 일반적으로 프로세스 시작 시 전역 초기화 작업에 사용됩니다.

**사용법:**
```bash
php webman make:bootstrap <이름> 
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 예 | Bootstrap 클래스명 |

**옵션:**

| 옵션 | 단축 | 설명 |
|--------|----------|-------------|
| `--plugin` | `-p` | 해당 플러그인 디렉터리에 생성 |
| `--path` | `-P` | 대상 디렉터리(프로젝트 루트 기준 상대 경로) |
| `--force` | `-f` | 기존 파일 덮어쓰기 |

**예시:**
```bash
# app/bootstrap에 MyBootstrap 생성
php webman make:bootstrap MyBootstrap

# 생성만 하고 자동 활성화 안 함
php webman make:bootstrap MyBootstrap no

# 플러그인에 생성
php webman make:bootstrap MyBootstrap -p admin

# 사용자 정의 경로
php webman make:bootstrap MyBootstrap -P plugin/admin/app/bootstrap

# 기존 파일 덮어쓰기
php webman make:bootstrap MyBootstrap -f
```

**생성되는 파일 구조:**
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MyBootstrap implements Bootstrap
{
    public static function start($worker)
    {
        $is_console = !$worker;
        if ($is_console) {
            return;
        }
        // ...
    }
}
```

**설명:**
- 기본적으로 `app/bootstrap/` 디렉터리에 배치됩니다
- 활성화 시 클래스가 `config/bootstrap.php`(플러그인은 `plugin/<플러그인>/config/bootstrap.php`)에 추가됩니다

<a name="make-process"></a>
### make:process

사용자 정의 프로세스 클래스를 생성하고 `config/process.php` 설정에 기록하여 자동 시작합니다.

**사용법:**
```bash
php webman make:process <이름>
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 예 | 프로세스 클래스명(예: MyTcp, MyWebsocket) |

**옵션:**

| 옵션 | 단축 | 설명 |
|--------|----------|-------------|
| `--plugin` | `-p` | 해당 플러그인 디렉터리에 생성 |
| `--path` | `-P` | 대상 디렉터리(프로젝트 루트 기준 상대 경로) |
| `--force` | `-f` | 기존 파일 덮어쓰기 |

**예시:**
```bash
# app/process에 생성
php webman make:process MyTcp

# 플러그인에 생성
php webman make:process MyProcess -p admin

# 사용자 정의 경로
php webman make:process MyProcess -P plugin/admin/app/process

# 기존 파일 덮어쓰기
php webman make:process MyProcess -f
```

**대화형 흐름:** 실행 후 순서대로 질문: 포트 리스닝 여부 → 프로토콜 유형(websocket/http/tcp/udp/unixsocket) → 리스닝 주소(IP+포트 또는 unix socket 경로) → 프로세스 수. HTTP 프로토콜은 내장 모드 또는 사용자 정의 모드 사용 여부도 질문합니다.

**생성되는 파일 구조:**

리스닝하지 않는 프로세스(`onWorkerStart`만):
```php
<?php
namespace app\process;

use Workerman\Worker;

class MyProcess
{
    public function onWorkerStart(Worker $worker)
    {
        // TODO: Write your business logic here.
    }
}
```

TCP/WebSocket 등 리스닝 프로세스는 해당 `onConnect`, `onMessage`, `onClose` 등 콜백 템플릿이 생성됩니다.

**설명:**
- 기본적으로 `app/process/` 디렉터리에 배치되며, 프로세스 설정은 `config/process.php`에 기록됩니다
- 설정 키는 클래스명의 snake_case이며, 이미 존재하면 실패합니다
- HTTP 내장 모드는 `app\process\Http` 프로세스 파일을 재사용하며, 새 파일을 생성하지 않습니다
- 지원 프로토콜: websocket, http, tcp, udp, unixsocket

## 빌드 및 배포

<a name="build-phar"></a>
### build:phar

프로젝트를 PHAR 아카이브 파일로 패키징하여 분배 및 배포를 용이하게 합니다.

**사용법:**
```bash
php webman build:phar
```

**시작:**

build 디렉터리로 이동하여 실행

```bash
php webman.phar start
```

**주의사항**
* 패키징된 프로젝트는 reload를 지원하지 않으며, 코드 업데이트 시 restart로 재시작해야 합니다

* 패키징 파일 크기가 과도하게 커져 메모리를 많이 사용하는 것을 방지하려면 config/plugin/webman/console/app.php의 exclude_pattern exclude_files 옵션을 설정하여 불필요한 파일을 제외할 수 있습니다.

* webman.phar 실행 후 webman.phar가 있는 디렉터리에 runtime 디렉터리가 생성되어 로그 등 임시 파일을 저장합니다.

* 프로젝트에서 .env 파일을 사용하는 경우 .env 파일을 webman.phar가 있는 디렉터리에 두어야 합니다.

* webman.phar는 Windows에서 사용자 정의 프로세스 실행을 지원하지 않습니다

* 사용자 업로드 파일을 phar 패키지에 저장하지 마세요. phar:// 프로토콜로 사용자 업로드 파일을 조작하는 것은 매우 위험합니다(phar 역직렬화 취약점). 사용자 업로드 파일은 phar 패키지 외부의 디스크에 별도로 저장해야 합니다. 아래를 참조하세요.

* 업로드 파일을 public 디렉터리에 저장해야 하는 경우 public 디렉터리를 webman.phar가 있는 디렉터리에 별도로 두어야 하며, 이때 config/app.php를 설정해야 합니다.
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
비즈니스에서는 헬퍼 함수 public_path($파일상대위치)를 사용하여 실제 public 디렉터리 위치를 찾을 수 있습니다.


<a name="build-bin"></a>
### build:bin

프로젝트를 독립 실행형 바이너리 파일로 패키징하며, PHP 런타임이 포함되어 있어 대상 환경에 PHP 설치가 필요 없습니다.

**사용법:**
```bash
php webman build:bin [버전]
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `버전` | 아니오 | PHP 버전(예: 8.1, 8.2), 기본값은 현재 PHP 버전, 최소 8.1 |

**예시:**
```bash
# 현재 PHP 버전 사용
php webman build:bin

# PHP 8.2 지정
php webman build:bin 8.2
```

**시작:**

build 디렉터리로 이동하여 실행

```bash
./webman.bin start
```

**주의사항:**
* 로컬 PHP 버전과 패키징 버전을 일치시키는 것을 강력히 권장합니다. 예: 로컬이 php8.1이면 패키징도 php8.1 사용, 호환성 문제 방지
* 패키징 시 php8 소스 코드를 다운로드하지만 로컬에 설치하지 않으며 로컬 PHP 환경에 영향을 주지 않습니다
* webman.bin은 현재 x86_64 아키텍처 Linux 시스템에서만 실행을 지원하며 Mac 시스템에서는 지원하지 않습니다
* 패키징된 프로젝트는 reload를 지원하지 않으며, 코드 업데이트 시 restart로 재시작해야 합니다
* 기본적으로 env 파일을 패키징하지 않습니다(config/plugin/webman/console/app.php의 exclude_files로 제어). 따라서 시작 시 env 파일을 webman.bin과 동일한 디렉터리에 두어야 합니다
* 실행 중 webman.bin이 있는 디렉터리에 runtime 디렉터리가 생성되어 로그 파일을 저장합니다
* 현재 webman.bin은 외부 php.ini 파일을 읽지 않습니다. 사용자 정의 php.ini가 필요하면 /config/plugin/webman/console/app.php 파일의 custom_ini에 설정하세요
* 일부 파일은 패키징할 필요가 없으며 config/plugin/webman/console/app.php에서 제외하여 패키징된 파일이 과도하게 커지는 것을 방지할 수 있습니다
* 바이너리 패키징은 swoole 코루틴 사용을 지원하지 않습니다
* 사용자 업로드 파일을 바이너리 패키지에 저장하지 마세요. phar:// 프로토콜로 사용자 업로드 파일을 조작하는 것은 매우 위험합니다(phar 역직렬화 취약점). 사용자 업로드 파일은 패키지 외부의 디스크에 별도로 저장해야 합니다.
* 업로드 파일을 public 디렉터리에 저장해야 하는 경우 public 디렉터리를 webman.bin이 있는 디렉터리에 별도로 두어야 하며, 이때 config/app.php를 아래와 같이 설정하고 다시 패키징해야 합니다.
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

Webman 프레임워크의 설치 스크립트를 실행합니다(`\Webman\Install::install()` 호출). 프로젝트 초기화에 사용됩니다.

**사용법:**
```bash
php webman install
```

## 유틸리티 명령

<a name="version"></a>
### version

workerman/webman-framework 버전을 표시합니다.

**사용법:**
```bash
php webman version
```

**설명:** `vendor/composer/installed.php`에서 버전을 읽으며, 읽을 수 없으면 실패를 반환합니다.

<a name="fix-disable-functions"></a>
### fix-disable-functions

php.ini의 `disable_functions`를 수정하여 Webman 실행에 필요한 함수를 제거합니다.

**사용법:**
```bash
php webman fix-disable-functions
```

**설명:** `disable_functions`에서 다음 함수(및 접두사 일치)를 제거합니다: `stream_socket_server`, `stream_socket_accept`, `stream_socket_client`, `pcntl_*`, `posix_*`, `proc_*`, `shell_exec`, `exec`. php.ini를 찾을 수 없거나 `disable_functions`가 비어 있으면 건너뜁니다. **php.ini 파일을 직접 수정합니다**. 사전 백업을 권장합니다.

<a name="route-list"></a>
### route:list

등록된 모든 라우트를 테이블 형식으로 나열합니다.

**사용법:**
```bash
php webman route:list
```

**출력 예시:**
```
+-------+--------+-----------------------------------------------+------------+------+
| URI   | 메서드 | 콜백                                          | 미들웨어   | 이름 |
+-------+--------+-----------------------------------------------+------------+------+
| /user | GET    | ["app\\controller\\UserController","index"] | null       |      |
| /api  | POST   | 클로저                                        | ["Auth"]   | api  |
+-------+--------+-----------------------------------------------+------------+------+
```

**출력 열:** URI, 메서드, 콜백, 미들웨어, 이름. 클로저 콜백은 「클로저」로 표시됩니다.

## 앱 플러그인 관리 (app-plugin:*)

<a name="app-plugin-create"></a>
### app-plugin:create

새 앱 플러그인을 생성하며, `plugin/<이름>` 하위에 전체 디렉터리 구조와 기본 파일을 생성합니다.

**사용법:**
```bash
php webman app-plugin:create <이름>
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 예 | 플러그인 이름, `[a-zA-Z0-9][a-zA-Z0-9_-]*` 형식 준수, `/` 또는 `\` 포함 불가 |

**예시:**
```bash
# foo라는 앱 플러그인 생성
php webman app-plugin:create foo

# 하이픈이 포함된 플러그인 생성
php webman app-plugin:create my-app
```

**생성되는 디렉터리 구조:**
```
plugin/<이름>/
├── app/
│   ├── controller/IndexController.php
│   ├── model/
│   ├── middleware/
│   ├── view/index/index.html
│   └── functions.php
├── config/          # app.php, route.php, menu.php 등
├── api/Install.php  # 설치/제거/업데이트 훅
├── public/
└── install.sql
```

**설명:**
- 플러그인은 `plugin/<이름>/` 하위에 생성되며, 디렉터리가 이미 존재하면 실패합니다

<a name="app-plugin-install"></a>
### app-plugin:install

앱 플러그인을 설치하며, `plugin/<이름>/api/Install::install($version)`을 실행합니다.

**사용법:**
```bash
php webman app-plugin:install <이름>
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 예 | 플러그인 이름, `[a-zA-Z0-9][a-zA-Z0-9_-]*` 형식 준수 |

**예시:**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

앱 플러그인을 제거하며, `plugin/<이름>/api/Install::uninstall($version)`을 실행합니다.

**사용법:**
```bash
php webman app-plugin:uninstall <이름>
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 예 | 플러그인 이름 |

**옵션:**

| 옵션 | 단축 | 설명 |
|--------|----------|-------------|
| `--yes` | `-y` | 확인 건너뛰고 바로 실행 |

**예시:**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

앱 플러그인을 업데이트하며, `Install::beforeUpdate($from, $to)`와 `Install::update($from, $to, $context)`를 순서대로 실행합니다.

**사용법:**
```bash
php webman app-plugin:update <이름>
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 예 | 플러그인 이름 |

**옵션:**

| 옵션 | 단축 | 설명 |
|--------|----------|-------------|
| `--from` | `-f` | 시작 버전, 기본값은 현재 버전 |
| `--to` | `-t` | 대상 버전, 기본값은 현재 버전 |

**예시:**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

앱 플러그인을 ZIP 파일로 패키징하여 `plugin/<이름>.zip` 출력합니다.

**사용법:**
```bash
php webman app-plugin:zip <이름>
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 예 | 플러그인 이름 |

**예시:**
```bash
php webman app-plugin:zip foo
```

**설명:**
- `node_modules`, `.git`, `.idea`, `.vscode`, `__pycache__` 등 디렉터리를 자동으로 제외합니다

## 플러그인 관리 (plugin:*)

<a name="plugin-create"></a>
### plugin:create

새 Webman 플러그인(Composer 패키지 형식)을 생성하며, `config/plugin/<이름>` 설정 디렉터리와 `vendor/<이름>` 플러그인 소스 디렉터리를 생성합니다.

**사용법:**
```bash
php webman plugin:create <이름>
php webman plugin:create --name <이름>
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 예 | 플러그인 패키지명, 형식: `vendor/package`(예: `foo/my-admin`), Composer 패키지명 규칙 준수 |

**예시:**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**생성 구조:**
- `config/plugin/<이름>/app.php`: 플러그인 설정(`enable` 스위치 포함)
- `vendor/<이름>/composer.json`: 플러그인 패키지 정의
- `vendor/<이름>/src/`: 플러그인 소스 디렉터리
- 프로젝트 루트 `composer.json`에 PSR-4 매핑 자동 추가
- `composer dumpautoload` 실행하여 자동 로드 갱신

**설명:**
- 이름은 `vendor/package` 형식이어야 하며, 소문자, 숫자, `-`, `_`, `.`만 사용 가능하고 반드시 `/`를 하나 포함해야 합니다
- `config/plugin/<이름>` 또는 `vendor/<이름>`이 이미 존재하면 실패합니다
- 인수와 `--name`을 동시에 전달하고 값이 다르면 오류가 발생합니다

<a name="plugin-install"></a>
### plugin:install

플러그인의 설치 스크립트(`Install::install()`)를 실행하여 플러그인 리소스를 프로젝트 디렉터리로 복사합니다.

**사용법:**
```bash
php webman plugin:install <이름>
php webman plugin:install --name <이름>
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 예 | 플러그인 패키지명, 형식: `vendor/package`(예: `foo/my-admin`) |

**옵션:**

| 옵션 | 설명 |
|--------|-------------|
| `--name` | 옵션 형식으로 플러그인명 지정, 인수와 둘 중 하나만 사용 |

**예시:**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

플러그인의 제거 스크립트(`Install::uninstall()`)를 실행하여 플러그인이 프로젝트에 복사한 리소스를 제거합니다.

**사용법:**
```bash
php webman plugin:uninstall <이름>
php webman plugin:uninstall --name <이름>
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 예 | 플러그인 패키지명, 형식: `vendor/package` |

**옵션:**

| 옵션 | 설명 |
|--------|-------------|
| `--name` | 옵션 형식으로 플러그인명 지정, 인수와 둘 중 하나만 사용 |

**예시:**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

플러그인을 활성화하며, `config/plugin/<이름>/app.php`의 `enable`을 `true`로 설정합니다.

**사용법:**
```bash
php webman plugin:enable <이름>
php webman plugin:enable --name <이름>
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 예 | 플러그인 패키지명, 형식: `vendor/package` |

**옵션:**

| 옵션 | 설명 |
|--------|-------------|
| `--name` | 옵션 형식으로 플러그인명 지정, 인수와 둘 중 하나만 사용 |

**예시:**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

플러그인을 비활성화하며, `config/plugin/<이름>/app.php`의 `enable`을 `false`로 설정합니다.

**사용법:**
```bash
php webman plugin:disable <이름>
php webman plugin:disable --name <이름>
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 예 | 플러그인 패키지명, 형식: `vendor/package` |

**옵션:**

| 옵션 | 설명 |
|--------|-------------|
| `--name` | 옵션 형식으로 플러그인명 지정, 인수와 둘 중 하나만 사용 |

**예시:**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

프로젝트의 플러그인 설정 및 지정 디렉터리를 `vendor/<이름>/src/`로 내보내고 `Install.php`를 생성하여 패키징 및 배포에 사용합니다.

**사용법:**
```bash
php webman plugin:export <이름> [--source=경로]...
php webman plugin:export --name <이름> [--source=경로]...
```

**인수:**

| 인수 | 필수 | 설명 |
|----------|----------|-------------|
| `이름` | 예 | 플러그인 패키지명, 형식: `vendor/package` |

**옵션:**

| 옵션 | 단축 | 설명 |
|--------|----------|-------------|
| `--name` | | 옵션 형식으로 플러그인명 지정, 인수와 둘 중 하나만 사용 |
| `--source` | `-s` | 내보낼 경로(프로젝트 루트 기준 상대 경로), 여러 번 지정 가능 |

**예시:**
```bash
# 플러그인 내보내기, 기본적으로 config/plugin/<이름> 포함
php webman plugin:export foo/my-admin

# app, config 등 디렉터리 추가 내보내기
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**설명:**
- 플러그인명은 Composer 패키지명 규칙(`vendor/package`)을 준수해야 합니다
- `config/plugin/<이름>`이 존재하고 `--source`에 없으면 자동으로 내보내기 목록에 추가됩니다
- 내보낸 `Install.php`에는 `pathRelation`이 포함되어 `plugin:install` / `plugin:uninstall`에서 사용됩니다
- `plugin:install`, `plugin:uninstall`은 플러그인이 `vendor/<이름>`에 이미 존재하고 `Install` 클래스와 `WEBMAN_PLUGIN` 상수가 있어야 합니다

## 서비스 관리

<a name="start"></a>
### start

Webman 워커 프로세스를 시작합니다. 기본값은 DEBUG 모드(포그라운드 실행)입니다.

**사용법:**
```bash
php webman start
```

**옵션:**

| 옵션 | 단축 | 설명 |
|--------|----------|-------------|
| `--daemon` | `-d` | DAEMON 모드로 시작(백그라운드 실행) |

<a name="stop"></a>
### stop

Webman 워커 프로세스를 중지합니다.

**사용법:**
```bash
php webman stop
```

**옵션:**

| 옵션 | 단축 | 설명 |
|--------|----------|-------------|
| `--graceful` | `-g` | 그레이스풀 중지, 현재 요청 처리 완료 후 종료 |

<a name="restart"></a>
### restart

Webman 워커 프로세스를 재시작합니다.

**사용법:**
```bash
php webman restart
```

**옵션:**

| 옵션 | 단축 | 설명 |
|--------|----------|-------------|
| `--daemon` | `-d` | 재시작 후 DAEMON 모드로 실행 |
| `--graceful` | `-g` | 그레이스풀 중지 후 재시작 |

<a name="reload"></a>
### reload

무중단 코드 리로드. 코드 업데이트 후 핫 리로드에 적합합니다.

**사용법:**
```bash
php webman reload
```

**옵션:**

| 옵션 | 단축 | 설명 |
|--------|----------|-------------|
| `--graceful` | `-g` | 그레이스풀 리로드, 현재 요청 처리 완료 후 리로드 |

<a name="status"></a>
### status

워커 프로세스 실행 상태를 확인합니다.

**사용법:**
```bash
php webman status
```

**옵션:**

| 옵션 | 단축 | 설명 |
|--------|----------|-------------|
| `--live` | `-d` | 상세 정보 표시(실시간 상태) |

<a name="connections"></a>
### connections

워커 프로세스 연결 정보를 조회합니다.

**사용법:**
```bash
php webman connections
```

