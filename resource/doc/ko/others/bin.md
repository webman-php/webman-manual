# 이진 파일 패키징

webman은 프로젝트를 바이너리 파일로 패키징하여 php 환경없이도 리눅스 시스템에서 실행할 수 있게 지원합니다.

> **주의**
> 패키징된 파일은 현재 x86_64 아키텍처 리눅스 시스템에서만 실행되며 맥 시스템은 지원하지 않습니다.
> `php.ini`의 phar 설정 옵션을 비활성화해야 합니다. 즉, `phar.readonly = 0`으로 설정해야 합니다.

## 명령 줄 도구 설치
`composer require webman/console ^1.2.24`

## 설정 구성
`config/plugin/webman/console/app.php` 파일을 열고 다음과 같이 설정합니다.
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
불필요한 디렉터리 및 파일을 배제하여 패키징된 크기가 너무 커지는 것을 방지합니다.

## 패키징
다음 명령을 실행합니다.
```sh
php webman build:bin
```
동시에 특정 php 버전으로 패키징할 수 있습니다. 예를 들어,
```sh
php webman build:bin 8.1
```

패키징 후에는 "build" 디렉터리에 "webman.bin" 파일이 생성됩니다.

## 시작
webman.bin을 리눅스 서버에 업로드하고, `./webman.bin start` 또는 `./webman.bin start -d`를 실행하여 시작할 수 있습니다.

## 작동 원리
* 먼저 로컬 webman 프로젝트를 phar 파일로 패키징합니다.
* 그런 다음 원격으로 php8.x.micro.sfx를 로컬에 다운로드합니다.
* 그후 php8.x.micro.sfx와 phar 파일을 연결하여 하나의 이진 파일을 생성합니다.

## 주의 사항
* 로컬 php 버전이 7.2 이상이면 패키징 명령을 실행할 수 있지만, 이진 파일은 php8만 지원합니다.
* 로컬 php 버전과 패키징된 버전이 일치하는 것이 좋으며, 호환성 문제를 피하기 위함입니다.
* 패키징은 php8의 소스 코드를 다운로드하지만 로컬에 설치하지는 않으며, 로컬 php 환경에 영향을 미치지 않습니다.
* webman.bin은 현재 x86_64 아키텍처 리눅스 시스템에서만 실행되며 맥 시스템에서는 지원하지 않습니다.
* 기본적으로 env 파일은 패키징되지 않습니다(`config/plugin/webman/console/app.php`의 exclude_files에서 제어) 따라서 시작할 때 env 파일은 webman.bin과 동일한 디렉터리에 있어야 합니다.
* 실행 중에 webman.bin이 있는 디렉터리에는 로그 파일을 저장하기 위한 runtime 디렉터리가 생성됩니다.
* 현재 webman.bin은 외부 php.ini 파일을 읽지 않으며, 사용자 정의 php.ini가 필요한 경우 `/config/plugin/webman/console/app.php` 파일에서 custom_ini를 설정해야 합니다.

## 독립된 PHP 다운로드
가끔 PHP 환경을 배포하지 않고 PHP 실행 파일만 필요한 경우가 있습니다. [여기를 클릭하여 정적 PHP 다운로드](https://www.workerman.net/download)를 받습니다.

> **팁**
> 정적 PHP에 특정 php.ini 파일을 지정하려면 다음 명령을 사용하세요. `php -c /your/path/php.ini start.php start -d`

## 지원되는 확장 기능
bcmath
calendar
Core
ctype
curl
date
dom
event
exif
FFI
fileinfo
filter
gd
hash
iconv
json
libxml
mbstring
mongodb
mysqlnd
openssl
pcntl
pcre
PDO
pdo_mysql
pdo_sqlite
Phar
posix
readline
redis
Reflection
session
shmop
SimpleXML
soap
sockets
SPL
sqlite3
standard
tokenizer
xml
xmlreader
xmlwriter
zip
zlib

## 프로젝트 출처

https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
