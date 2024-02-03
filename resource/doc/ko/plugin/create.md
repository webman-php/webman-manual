# 기본 플러그인 생성 및 배포 프로세스

## 원리
1. 플러그인은 CORS(Cross-Origin Resource Sharing) 플러그인을 예로 들어, 세 가지 부분으로 나뉩니다. 하나는 CORS 미들웨어 프로그램 파일이고, 다른 하나는 미들웨어 구성 파일인 middleware.php이며, 또한 Install.php를 통해 자동 생성됩니다.
2. 우리는 명령어를 사용하여 세 개의 파일을 패키징하고 composer에 배포합니다.
3. 사용자가 composer를 사용하여 CORS 플러그인을 설치하면, Install.php에서 CORS 미들웨어 프로그램 파일과 구성 파일을 `{주 프로젝트}/config/plugin`에 복사하여 webman이 로드하도록 합니다. 따라서 CORS 미들웨어 파일의 자동 구성이 효과적으로 이루어집니다.
4. 사용자가 플러그인을 삭제할 때, Install.php는 해당 CORS 미들웨어 프로그램 파일과 구성 파일을 삭제하여 플러그인이 자동으로 언인스톨 되도록 합니다.

## 규정
1. 플러그인 이름은 `제조업체`와 `플러그인 이름` 두 부분으로 구성됩니다. 예를 들어 `webman/push`와 같이 구성되며, 이는 composer 패키지 이름과 일치합니다.
2. 플러그인 구성 파일은 일관된 방식으로 `config/plugin/업체/플러그인 이름/`에 위치해야 합니다(콘솔 명령어가 자동으로 구성 디렉토리를 생성합니다). 플러그인에 구성이 필요하지 않은 경우, 자동으로 생성된 구성 디렉토리를 삭제해야 합니다.
3. 플러그인 구성 디렉토리는 app.php(플러그인 주 구성), bootstrap.php 프로세스 시작 구성, route.php 라우트 구성, middleware.php 미들웨어 구성, process.php 사용자 정의 프로세스 구성, database.php 데이터베이스 구성, redis.php Redis 구성, thinkorm.php ThinkORM 구성만 지원합니다. 이러한 구성은 webman에서 자동으로 인식됩니다.
4. 플러그인은 다음을 사용하여 구성을 얻을 수 있습니다. `config('plugin.업체.플러그인 이름.구성 파일.구체적인 구성 항목');`예를 들어 `config('plugin.webman.push.app.app_key')`
5. 플러그인이 자체 데이터베이스 구성을 갖고 있는 경우, 다음과 같이 액세스할 수 있습니다. `illuminate/database`는 `Db::connection('plugin.업체.플러그인 이름.특정 연결')`이며, `thinkorm`은 `Db::connection('plugin.업체.플러그인 이름.특정 연결')`입니다.
6. 플러그인이 `app/` 디렉토리에 비즈니스 파일을 위치시키려는 경우, 충돌을 피하기 위해 주 프로젝트 및 다른 플러그인과 충돌하지 않도록해야 합니다.
7. 플러그인은 주 프로젝트에 파일이나 디렉토리를 복사하는 것을 피해야 합니다. CORS 플러그인은 구성 파일 외에도 주 프로젝트에 복사할 필요가 없으므로, 미들웨어 파일을 `vendor/webman/cros/src`에 위치시키고, 주 프로젝트에 복사할 필요가 없습니다.
8. 플러그인 네임스페이스는 대문자를 사용하는 것이 좋습니다. 예를 들어 Webman/Console를 권장합니다.

## 예

**'webman/console' 명령줄 설치**

`composer require webman/console`

#### 플러그인 만들기

가정으로 플러그인 이름이 'foo/admin'이라고 가정합니다(이름은 나중에 composer로 게시할 프로젝트 이름이기 때문에 소문자로 작성해야 합니다)
아래 명령어를 실행합니다.
`php webman plugin:create --name=foo/admin`

플러그인을 만든 후에는 'vendor/foo/admin' 디렉터리가 플러그인 관련 파일을 저장하는 데 사용되고, 'config/plugin/foo/admin' 디렉터리는 플러그인 관련 구성을 저장하는 데 사용됩니다.

> 참고
> 'config/plugin/foo/admin'은 다음을 지원합니다. app.php 플러그인 주 구성, bootstrap.php 프로세스 시작 구성, route.php 라우트 구성, middleware.php 미들웨어 구성, process.php 사용자 정의 프로세스 구성, database.php 데이터베이스 구성, redis.php Redis 구성, thinkorm.php ThinkORM 구성. 구성 형식은 'webman'과 동일하며, 이러한 구성은 webman에서 자동으로 인식되어 구성에 병합됩니다.
'plugin'을 접두사로 사용하여 액세스합니다. 예를 들어 config('plugin.foo.admin.app');

#### 플러그인 내보내기

플러그인 개발을 마친 후, 아래 명령어를 실행하여 플러그인을 내보냅니다.
`php webman plugin:export --name=foo/admin`
내보내기

> 설명
> 내보낸 후에는 config/plugin/foo/admin 디렉터리를 vendor/foo/admin/src로 복사하고, Install.php를 자동으로 생성합니다. Install.php는 자동 설치 및 자동 언인스톨 때 몇 가지 작업을 실행하는 데 사용됩니다.
기본 작업은 vendor/foo/admin/src에 있는 구성을 현재 프로젝트의 config/plugin에 복사하는 것입니다.
제거할 때의 기본 작업은 현재 프로젝트 config/plugin에 있는 구성 파일을 삭제하는 것입니다. 설치 및 제거시 사용자 정의 작업을 수행하도록 Install.php를 수정할 수 있습니다.

#### 플러그인 제출
* 여러분이 이미 [github](https://github.com) 및 [packagist](https://packagist.org) 계정을 가지고 있는 것으로 가정합니다.
* [github](https://github.com)에서 admin 프로젝트를 만들고 코드를 업로드한 후, 프로젝트 주소를 `https://github.com/your-username/admin`라고 가정합니다.
* `https://github.com/your-username/admin/releases/new`에 들어가서 `v1.0.0`과 같은 릴리즈를 게시합니다.
* [packagist](https://packagist.org)에 로그인하여 `Submit` 탭을 클릭한 후 귀하의 github 프로젝트 주소`https://github.com/your-username/admin`를 제출합니다. 이렇게 하면 플러그인의 배포가 완료됩니다.

> **팁**
> 'packagist'에서 플러그인을 제출할 때 충돌이 발생하는 경우, 제조업체 이름을 다시 선택하여 플러그인 이름을 바꿀 수 있습니다. 예를 들어 'foo/admin'을 'myfoo/admin'으로 변경합니다.

향후 플러그인 프로젝트 코드를 업데이트하려면 코드를 github에 동기화한 후에 `https://github.com/your-username/admin/releases/new`에 다시 릴리즈를 게시하고, 다시 `https://packagist.org/packages/foo/admin` 페이지로 이동하여 `Update` 버튼을 클릭하여 버전을 업데이트해야 합니다.

## 플러그인에 명령 추가
가끔히 플러그인에는 일부 사용자 정의 명령이 필요할 때가 있습니다. 예를 들어 'webman/redis-queue' 플러그인을 설치한 후에 프로젝트에 `redis-queue:consumer` 명령이 자동으로 추가되어 사용자가 `php webman redis-queue:consumer send-mail`을 실행하면 프로젝트에 SendMail.php 소비자 클래스가 생성됩니다. 이는 빠른 개발에 도움이 됩니다.

'foo/admin' 플러그인에 'foo-admin:add' 명령을 추가하려면 다음 단계를 참고하세요.

#### 명령 생성

**명령 파일 `vendor/foo/admin/src/FooAdminAddCommand.php` 생성**

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
    protected static $defaultDescription = '이 부분은 명령어 설명입니다';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Add name');
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
> 플러그인 간 명령어 충돌을 피하기 위해, 명령어 형식은 `업체-플러그인 이름:특정 명령어`를 권장합니다. 예를 들어 'foo/admin' 플러그인의 모든 명령어는 'foo-admin:'을 접두어로 사용해야 합니다. 예를 들어 'foo-admin:add'와 같이 말입니다.

#### 구성 추가
**구성 파일 `config/plugin/foo/admin/command.php` 생성**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....여러 개의 구성을 추가할 수 있습니다...
];
```

> **팁**
> `command.php`는 플러그인에 사용자 정의 명령을 설정하는 데 사용됩니다. 배열의 각 요소는 하나의 명령어 클래스 파일에 해당하며, 각 클래스 파일은 하나의 명령에 해당합니다. 사용자가 명령 줄을 실행할 때 `webman/console`은 각 플러그인의 `command.php`에 설정된 사용자 정의 명령을 자동으로 로드합니다. 명령어 관련 자세한 정보는 [Command Line(console.md)](console.md)을 참고하세요.

#### 내보내기 실행
명령어 `php webman plugin:export --name=foo/admin`을 실행하여 플러그인을 내보내고, `packagist`에 제출합니다. 그러면 사용자가 'foo/admin' 플러그인을 설치하면 'foo-admin:add' 명령이 추가됩니다. `php webman foo-admin:add jerry`를 실행하면 `Admin add jerry`가 출력됩니다.
