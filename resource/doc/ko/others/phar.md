# phar 패키지

phar은 PHP에서 JAR와 유사한 패키지 파일로, webman 프로젝트를 한 개의 phar 파일로 패키징하여 쉽게 배포할 수 있습니다.

**[fuzqing](https://github.com/fuzqing)** 의 PR에 대해 매우 감사합니다.**

> **주의**
> `php.ini` 파일의 phar 구성 옵션을 비활성화해야 합니다. 즉, `phar.readonly = 0`으로 설정해야 합니다.

## 명령줄 도구 설치
`composer require webman/console`

## 구성 설정
`config/plugin/webman/console/app.php` 파일을 열고, `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`을 설정하여 불필요한 디렉토리 및 파일을 배제하여 패키지 크기가 너무 커지지 않게 합니다.

## 패키징
webman 프로젝트의 루트 디렉토리에서 `php webman phar:pack` 명령을 실행하면 `webman.phar` 파일이 `build` 디렉토리에 생성됩니다.

> 패키지 관련 구성은 `config/plugin/webman/console/app.php` 파일에 있습니다.

## 시작 및 중지 관련 명령
**시작**
`php webman.phar start` 또는 `php webman.phar start -d`

**중지**
`php webman.phar stop`

**상태 확인**
`php webman.phar status`

**연결 상태 확인**
`php webman.phar connections`

**재시작**
`php webman.phar restart` 또는 `php webman.phar restart -d`

## 설명
* webman.phar를 실행하면 webman.phar가 있는 디렉토리에 일시적 파일과 로그를 저장하는 런타임 디렉토리가 생성됩니다.

* 프로젝트에 .env 파일을 사용하는 경우 .env 파일을 webman.phar가 있는 디렉토리에 두어야 합니다.

* 귀하의 업무가 public 디렉터리에 파일을 업로드해야 하는 경우 public 디렉터리를 webman.phar가있는 디렉토리 밖으로 독립적으로 이동해야 하며, 그때 `config/app.php`를 구성해야 합니다.
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
업무는 `public_path()` 헬퍼 함수를 사용하여 실제 public 디렉토리 위치를 찾을 수 있습니다.

* webman.phar는 Windows에서 사용자 정의 프로세스 실행을 지원하지 않습니다.
