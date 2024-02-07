# 기본 플러그인 생성 및 배포 프로세스

## 원리
1. CORS 플러그인을 예로 들면, 플러그인은 CORS 미들웨어 프로그램 파일, 미들웨어 구성 파일 (middleware.php) 및 Install.php로 구성됩니다.
2. 우리는 명령어를 사용하여 세 개의 파일을 패키지화하고 Composer에 배포합니다.
3. 사용자가 Composer를 사용하여 CORS 플러그인을 설치하면 Install.php는 CORS 미들웨어 프로그램 파일 및 구성 파일을 `{주 프로젝트}/config/plugin`에 복사하여 webman이 로드하도록합니다. CORS 미들웨어 파일의 자동 구성 적용이 구현됩니다.
4. 사용자가 Composer를 사용하여 해당 플러그인을 삭제하면 Install.php는 해당 CORS 미들웨어 프로그램 파일과 구성 파일을 삭제하여 플러그인을 자동 제거합니다.

## 규범
1. 플러그인 이름은 '제조사'와 '플러그인 이름' 두 부분으로 구성되며 예를 들면, `webman/push`와 같이 구성됩니다. 이는 Composer 패키지 이름과 일치합니다.
2. 플러그인 구성 파일은 일관되게 `config/plugin/제조사/플러그인 이름/`에 위치해야 합니다 (콘솔 명령은 자동으로 구성 디렉토리를 생성합니다). 플러그인이 구성이 필요하지 않은 경우에는 자동으로 생성 된 구성 디렉토리를 삭제해야 합니다.
3. 플러그인 구성 디렉토리는 app.php 플러그인 기본 구성, bootstrap.php 프로세스 시작 구성, route.php 라우팅 구성, middleware.php 미들웨어 구성, process.php 사용자 정의 프로세스 구성, database.php 데이터베이스 구성, redis.php 레디스 구성, thinkorm.php thinkorm 구성을 지원합니다. 이러한 구성은 webman이 자동으로 인식합니다.
4. 플러그인은 다음 방법을 사용하여 구성을 가져옵니다. `config('plugin.제조사.플러그인 이름.구성 파일.구체적인 구성 요소');` 예를 들어, `config('plugin.webman.push.app.app_key')`
5. 플러그인에 별도의 데이터베이스 구성이 있는 경우 다음과 같이 액세스합니다. `illuminate/database`의 경우 `Db::connection('plugin.제조사.플러그인 이름.특정 연결')`, `thinkrom`의 경우 `Db::connct('plugin.제조사.플러그인 이름.특정 연결')`
6. 플러그인이 `app/` 디렉토리에 비즈니스 파일을 배치해야하는 경우 사용자 프로젝트 및 다른 플러그인과 충돌하지 않도록하여야 합니다.
7. 플러그인은 주 프로젝트에 파일 또는 디렉토리를 복사하지 않도록해야합니다. 예를 들어, CORS 플러그인의 경우 설정 파일을 주 프로젝트에 복사할 필요가 없으므로 미들웨어 파일은 `vendor/webman/cros/src`에 배치되어야합니다.
8. 플러그인 네임스페이스는 대문자로 사용하는 것이 좋으며, 예를 들어 Webman/Console과 같이 사용하는 것이 좋습니다.

## 예시

**`webman/console` 명령행 설치**

`composer require webman/console`

#### 플러그인 생성

가정컨데 플러그인 이름이 `foo/admin`인 경우 (프로젝트 이름은 나중에 배포할 때의 프로젝트 이름이므로 모두 소문자로 지정해야함)
다음 명령어를 실행합니다.
`php webman plugin:create --name=foo/admin`

플러그인 생성 후 `vendor/foo/admin` 디렉토리와 플러그인 관련 파일을 저장하는 `config/plugin/foo/admin` 디렉터리가 생성됩니다.

> 유의
> `config/plugin/foo/admin`은 app.php 플러그인 기본 구성, bootstrap.php 프로세스 시작 구성, route.php 라우팅 구성, middleware.php 미들웨어 구성, process.php 사용자 정의 프로세스 구성, database.php 데이터베이스 구성, redis.php 레디스 구성, thinkorm.php thinkorm 구성을 지원합니다. 구성 형식은 webman과 동일하며, 이러한 구성은 webman이 자동으로 인식하여 구성에 병합됩니다.
`plugin`를 접두어로 사용하여 액세스하십시오. 예를 들어, `config('plugin.foo.admin.app')`

#### 플러그인 내보내기

플러그인을 개발 한 후 다음 명령어를 실행하여 플러그인을 내보냅니다.
`php webman plugin:export --name=foo/admin`

> 설명
> 내보낸 후 config/plugin/foo/admin 디렉토리를 vendor/foo/admin/src로 복사하고 Install.php를 자동으로 생성합니다. Install.php는 플러그인을 자동으로 설치하고 제거 할 때 일부 작업을 수행합니다.
기본 설치는 vendor/foo/admin/src에있는 구성을 현재 프로젝트config/plugin에 복사하는 것입니다.
기본 제거 작업은 현재 프로젝트config/plugin에있는 구성 파일을 제거하는 것입니다.
설치 및 제거 플러그인에 사용자 정의 작업을 수행하도록 Install.php를 수정 할 수 있습니다.

#### 플러그인 제출
* 이미 [github](https://github.com) 및 [packagist](https://packagist.org) 계정이있는 경우
* [github](https://github.com)에 admin 프로젝트를 만들고 코드를 업로드하십시오. 프로젝트 주소는 `https://github.com/your-username/admin`으로 가정합시다.
* `https://github.com/your-username/admin/releases/new`로 이동하여 `v1.0.0`과 같은 릴리스를 발표하십시오.
* [packagist](https://packagist.org)로 이동하여 `Submit` 내비게이션을 클릭하고 귀하의 github 프로젝트 주소 `https://github.com/your-username/admin`를 제출합니다. 이렇게하면 플러그인이 게시됩니다.

> 팁
> `packagist`에서 플러그인을 제출하면 충돌이 발생하면 공급 업체 이름을 다시 지정하여야 합니다. 예를 들어 `foo/admin`을 `myfoo/admin`으로 변경합니다.

향후 플러그인 프로젝트 코드를 업데이트해야하는 경우 코드를 github에 동기화하고 다시 `https://github.com/your-username/admin/releases/new`에 발표를 다시 해야하며, 그런 다음 `https://packagist.org/packages/foo/admin`페이지에서 `Update` 버튼을 클릭하여 버전을 업데이트해야합니다.
## 플러그인에 명령어 추가하기
가끔씩 우리의 플러그인은 보조 기능을 제공하기 위해 몇 가지 사용자 정의 명령어가 필요할 수 있습니다. 예를 들어, `webman/redis-queue` 플러그인을 설치하면 프로젝트에 자동으로 `redis-queue:consumer` 명령어가 추가되어 사용자는 `php webman redis-queue:consumer send-mail` 명령을 실행하여 프로젝트에 SendMail.php 소비자 클래스를 생성할 수 있습니다. 이는 빠른 개발을 도와줍니다.

만약 `foo/admin` 플러그인이 `foo-admin:add` 명령어를 추가해야 한다면 다음 단계를 참고하세요.

#### 명령어 생성
**명령어 파일 생성 `vendor/foo/admin/src/FooAdminAddCommand.php`**
```php
<?php

namespace Foo\Admin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FooAdminAddCommand extends Command
{
    protected static $defaultName = 'foo-admin:add';
    protected static $defaultDescription = '이 곳에 명령어 설명을 입력합니다.';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, '추가할 이름');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln("Admin add $name");
        return self::SUCCESS;
    }

}
```

> **참고**
> 플러그인 간의 명령어 충돌을 피하기 위해 명령어 형식은 `제조사-플러그인이름:구체적인명령어`로 설정하는 것이 좋습니다. 예를 들어, `foo/admin` 플러그인의 모든 명령어는 `foo-admin:`으로 시작하여야 합니다. 예를 들어, `foo-admin:add`와 같이요.

#### 설정 추가
**설정 파일 생성 `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....여러 개의 설정 추가 가능...
];
```

> **팁**
> `command.php` 파일은 플러그인에 사용자 정의 명령어를 설정하는 데 사용되며, 배열의 각 요소는 각각 하나의 명령어 클래스 파일에 해당하며, 각 클래스 파일은 하나의 명령어에 해당합니다. 사용자가 명령어를 실행할 때 `webman/console`은 자동으로 각 플러그인의 `command.php`에 설정된 사용자 정의 명령어를 로드합니다. 더 많은 명령어 관련 정보는 [커맨드 라인](console.md)을 참조하세요.

#### 내보내기 실행
명령어 `php webman plugin:export --name=foo/admin`을 실행하여 플러그인을 내보내고 `packagist`에 제출합니다. 이렇게 하면 사용자가 `foo/admin` 플러그인을 설치한 후 `foo-admin:add` 명령어가 추가될 것입니다. `php webman foo-admin:add jerry`를 실행하면 `Admin add jerry`가 출력됩니다.
