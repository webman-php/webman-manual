# webman/console 명령행 플러그인

`webman/console` 은 `symfony/console`에 기반합니다.

> 플러그인은 webman>=1.2.2 webman-framework>=1.2.1 이 필요합니다.

## 설치

```sh
composer require webman/console
```

## 지원되는 명령어
**사용 방법**  
`php webman 명령어` 또는 `php webman 명령어`를 사용합니다.
예를 들어 `php webman version` 또는 `php webman version`

## 지원되는 명령어
### version
**webman 버전을 인쇄합니다.**

### route:list
**현재 라우트 구성을 인쇄합니다.**

### make:controller
**컨트롤러 파일을 생성합니다.**
예를 들어 `php webman make:controller admin`은 `app/controller/AdminController.php`을 만듭니다.
예를 들어 `php webman make:controller api/user`는 `app/api/controller/UserController.php`을 만듭니다.

### make:model
**모델 파일을 생성합니다.**
예를 들어 `php webman make:model admin`은 `app/model/Admin.php`을 만듭니다.
예를 들어 `php webman make:model api/user`는 `app/api/model/User.php`을 만듭니다.

### make:middleware
**미들웨어 파일을 생성합니다.**
예를 들어 `php webman make:middleware Auth`는 `app/middleware/Auth.php`을 만듭니다.

### make:command
**사용자 정의 명령어 파일을 생성합니다.**
예를 들어 `php webman make:command db:config`는 `app\command\DbConfigCommand.php`을 만듭니다.

### plugin:create
**기본 플러그인을 생성합니다.**
예를 들어 `php webman plugin:create --name=foo/admin`은 `config/plugin/foo/admin`과 `vendor/foo/admin` 두 개의 디렉토리를 생성합니다.
[기본 플러그인 생성](/doc/webman/plugin/create.html)을 참조하세요.

### plugin:export
**기본 플러그인을 내보냅니다.**
예를 들어 `php webman plugin:export --name=foo/admin`을 실행하세요.
[기본 플러그인 생성](/doc/webman/plugin/create.html)을 참조하세요.

### plugin:export
**애플리케이션 플러그인을 내보냅니다.**
예를 들어 `php webman plugin:export shop`을 실행하세요.
[애플리케이션 플러그인](/doc/webman/plugin/app.html)을 참조하세요.

### phar:pack
**webman 프로젝트를 phar 파일로 패키징합니다.**
[phar 패키징](/doc/webman/others/phar.html)을 참조하세요.
> 이 기능은 webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5 가 필요합니다.

## 사용자 정의 명령어
사용자가 직접 명령어를 정의할 수 있습니다. 아래는 데이터베이스 구성을 출력하는 명령어 예시입니다.

* `php webman make:command config:mysql`를 실행합니다.
* `app/command/ConfigMySQLCommand.php` 파일을 열고 아래와 같이 수정합니다.

```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigMySQLCommand extends Command
{
    protected static $defaultName = 'config:mysql';
    protected static $defaultDescription = '현재 MySQL 서버 구성 표시';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('MySQL 구성 정보는 다음과 같습니다:');
        $config = config('database');
        $headers = ['name', 'default', 'driver', 'host', 'port', 'database', 'username', 'password', 'unix_socket', 'charset', 'collation', 'prefix', 'strict', 'engine', 'schema', 'sslmode'];
        $rows = [];
        foreach ($config['connections'] as $name => $db_config) {
            $row = [];
            foreach ($headers as $key) {
                switch ($key) {
                    case 'name':
                        $row[] = $name;
                        break;
                    case 'default':
                        $row[] = $config['default'] == $name ? 'true' : 'false';
                        break;
                    default:
                        $row[] = $db_config[$key] ?? '';
                }
            }
            if ($config['default'] == $name) {
                array_unshift($rows, $row);
            } else {
                $rows[] = $row;
            }
        }
        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();
        return self::SUCCESS;
    }
}
```

## 테스트

커맨드 라인에서 `php webman config:mysql`를 실행합니다.

다음과 같은 결과가 나타납니다:
```txt
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## 자세한 자료
http://www.symfonychina.com/doc/current/components/console.html
