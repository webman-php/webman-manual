# 이진파일 패키징

웹맨은 프로젝트를 이진 파일로 패키징하여 PHP 환경 없이도 리눅스 시스템에서 실행할 수 있게 지원합니다.

> **주의**
> 패키징된 파일은 현재 x86_64 아키텍처 리눅스 시스템에서만 실행되며 맥 시스템은 지원하지 않습니다.
> `php.ini`의 phar 구성 옵션을 닫아야 합니다. 즉, `phar.readonly = 0`으로 설정해야 합니다.

## 명령 줄 도구 설치
`composer require webman/console ^1.2.24`

## 설정 구성
`config/plugin/webman/console/app.php` 파일을 열고 다음을 설정합니다.
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
이는 불필요한 디렉터리 및 파일을 제외하고 패키징되어 크기가 너무 크지 않도록 하는 데 사용됩니다.

## 패키징
다음 명령을 실행합니다.
```
php webman build:bin
```
동시에 특정 PHP 버전으로 패키징을 지정할 수도 있습니다. 예를 들어,
```
php webman build:bin 8.1
```

패키징 후 `webman.bin` 파일이 build 디렉토리에 생성됩니다.

## 시작
webman.bin을 리눅스 서버에 업로드한 후 `./webman.bin start` 또는 `./webman.bin start -d`를 실행하여 시작할 수 있습니다.

## 동작 원리
* 먼저 로컬 webman 프로젝트를 phar 파일로 패키징합니다.
* 그 다음으로 원격으로 php8.x.micro.sfx를 다운로드합니다.
* 마지막으로 php8.x.micro.sfx와 phar 파일을 연결하여 하나의 이진 파일로 만듭니다.

## 유의사항
* 로컬 PHP 버전이 7.2 이상이면 패키징 명령을 실행할 수 있습니다.
* 그러나 PHP8의 이진 파일만 패키징할 수 있습니다.
* 로컬 PHP 버전과 패키징 버전이 일치하는 것이 좋으며, 즉 로컬이 PHP8.0이면 PHP8.0으로 패키징하여 호환성 문제를 피할 수 있습니다.
* 패키징하는 동안 PHP8의 소스 코드를 다운로드하지만 로컬에 설치하지는 않으므로 로컬 PHP 환경에 영향을 주지 않습니다.
* 현재 webman.bin은 x86_64 아키텍처 리눅스 시스템에서만 실행되며 맥 시스템에서는 지원되지 않습니다.
* 기본적으로 env 파일을 패키징하지 않습니다. 따라서 시작할 때 env 파일은 webman.bin과 같은 디렉토리에 배치되어야 합니다.
* 실행 중에 로그 파일을 저장하는 runtime 디렉토리가 webman.bin이 있는 디렉토리에 생성됩니다.
* 현재 webman.bin은 외부 php.ini 파일을 읽지 않으며 사용자 지정 php.ini이 필요한 경우 `/config/plugin/webman/console/app.php` 파일에서 custom_ini를 설정해야 합니다.

## 별도로 PHP 다운로드
PHP 환경을 배포할 필요없이 PHP 실행 파일만 필요한 경우가 있습니다. [여기를 클릭하여 정적 PHP 다운로드](https://www.workerman.net/download)를 받으십시오.

> **팁**
> 정적 PHP에 php.ini 파일을 지정하려면 다음 명령을 사용하십시오. `php -c /your/path/php.ini start.php start -d`

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
