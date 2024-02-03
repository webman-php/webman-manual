# phar 패키징

phar은 PHP에서 JAR과 유사한 패키지 파일로, webman 프로젝트를 하나의 phar 파일로 패키징하여 쉽게 배포할 수 있습니다.

**여기서 [fuzqing](https://github.com/fuzqing) 님의 PR에 대해 매우 감사드립니다.**

> **주의**
> `php.ini`의 phar 구성 옵션을 비활성화해야 합니다. `phar.readonly = 0`으로 설정합니다.

## 명령행 도구 설치
`composer require webman/console`

## 설정 구성
`config/plugin/webman/console/app.php` 파일을 열고, `'exclude_pattern' => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`를 설정하여 불필요한 디렉토리 및 파일을 패키지에서 제외하여 패키지 크기가 너무 커지는 것을 피합니다.

## 패키징
webman 프로젝트 루트 디렉토리에서 `php webman phar:pack` 명령을 실행하면 bulid 디렉토리에 `webman.phar` 파일이 생성됩니다.

> 패키징 관련 설정은 `config/plugin/webman/console/app.php`에서 확인할 수 있습니다.

## 시작 및 중지 관련 명령어
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
* webman.phar을 실행하면 webman.phar이 있는 디렉토리에 runtime 디렉토리가 생성되어 로그 및 임시 파일을 저장합니다.

* 프로젝트에 .env 파일을 사용하는 경우 .env 파일을 webman.phar이 있는 디렉토리에 위치시켜야 합니다.

* 파일을 public 디렉토리에 업로드해야 하는 경우 public 디렉토리를 webman.phar이 있는 디렉토리에서 분리하여 설정해야 합니다. 이때 `config/app.php`를 설정해야 합니다.
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
업무에서 헬퍼 함수 `public_path()`를 사용하여 실제 public 디렉토리 위치를 찾을 수 있습니다.

* webman.phar은 Windows에서 사용자 정의 프로세스를 활성화할 수 없습니다.
