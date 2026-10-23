# 이진 파일 패키징

webman은 프로젝트를 하나의 이진 파일로 패키징할 수 있어, PHP 환경 없이도 Linux에서 실행할 수 있습니다.

> **주의**
> 패키징된 파일은 현재 x86_64 아키텍처 Linux에서만 실행되며, Windows와 Mac을 지원하지 않습니다.
> `php.ini`에서 phar 설정을 비활성화해야 합니다. `phar.readonly = 0`으로 설정하세요.

## 명령줄 도구 설치
`composer require webman/console`

## 패키징
다음 명령을 실행합니다
```
php webman build:bin
```
특정 PHP 버전으로 패키징할 수도 있습니다. 예:
```
php webman build:bin 8.3
```

패키징 후 build 디렉터리에 `webman.bin` 파일이 생성됩니다.

## 시작
webman.bin을 Linux 서버에 업로드한 뒤 `./webman.bin start` 또는 `./webman.bin start -d`로 시작합니다.

## 원리
* 먼저 로컬 webman 프로젝트를 phar 파일로 패키징합니다
* 그다음 php8.x.micro.sfx를 원격에서 다운로드합니다
* php8.x.micro.sfx와 phar 파일을 이어서 하나의 이진 파일로 만듭니다

## 주의사항
* 호환성 문제를 피하려면 로컬과 패키징에 같은 PHP 버전(예: 둘 다 PHP 8.3) 사용을 권장합니다
* 패키징 시 PHP 8 소스가 다운로드되지만 로컬에 설치되지 않아, 로컬 PHP 환경에 영향을 주지 않습니다
* webman.bin은 현재 x86_64 Linux에서만 동작하며 Mac을 지원하지 않습니다
* 패키징된 프로젝트는 reload를 지원하지 않으며, 코드 수정 시 재시작이 필요합니다
* 기본적으로 env 파일은 패키징되지 않습니다(`config/plugin/webman/console/app.php`의 exclude_files로 제어). 시작 시 env 파일은 webman.bin과 같은 디렉터리에 두어야 합니다
* 실행 중 webman.bin 디렉터리에 runtime 디렉터리가 생성되어 로그를 저장합니다
* 현재 webman.bin은 외부 php.ini를 읽지 않습니다. php.ini를 수정하려면 `config/plugin/webman/console/app.php`의 custom_ini에 설정하세요
* 패키징할 필요가 없는 파일은 `config/plugin/webman/console/app.php`에서 제외해 패키지 크기를 줄일 수 있습니다
* 이진 패키징은 Swoole 코루틴을 지원하지 않습니다
* 사용자가 업로드한 파일을 이진 패키지 안에 저장하지 마세요. `phar://` 프로토콜로 업로드 파일을 다루면 phar 역직렬화 취약점 때문에 위험합니다. 업로드 파일은 패키지 밖 디스크에 따로 저장해야 합니다
* public 디렉터리에 업로드가 필요하다면, public 디렉터리를 webman.bin과 같은 위치에 두고, `config/app.php`에 아래처럼 설정한 뒤 다시 패키징하세요:
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## 정적 PHP 개별 다운로드
전체 PHP 환경 없이 PHP 실행 파일만 필요한 경우, [정적 PHP 다운로드](https://www.workerman.net/download)를 사용하세요.

> **팁**
> 정적 PHP에 php.ini를 지정하려면: `php -c /your/path/php.ini start.php start -d`

## 지원 확장 모듈
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## 프로젝트 출처

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
