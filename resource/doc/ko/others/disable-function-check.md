# 함수 확인 해제

이 스크립트를 사용하여 금지된 함수가 있는지 확인하십시오. 명령 줄에서 `curl -Ss https://www.workerman.net/webman/check | php`를 실행하십시오.

`Functions 함수명 has be disabled. Please check disable_functions in php.ini`라는 메시지가 나오면 webman이 의존하는 함수가 비활성화되었다는 것을 의미하며, webman을 정상적으로 사용하려면 php.ini에서 비활성화를 해제해야 합니다.
비활성화를 해제하려면 다음 방법 중 하나를 참고하십시오.

## 방법 1
`webman/console`을 설치하십시오.
```
composer require webman/console ^v1.2.35
```

다음 명령을 실행하십시오.
```
php webman fix-disable-functions
```

## 방법 2

`curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` 스크립트를 실행하여 비활성화를 해제하십시오.

## 방법 3

`php --ini`를 실행하여 php cli가 사용하는 php.ini 파일의 위치를 찾으십시오.

php.ini를 열어 `disable_functions`를 찾고 다음 함수의 호출을 해제하십시오.
```
stream_socket_server
stream_socket_client
pcntl_signal_dispatch
pcntl_signal
pcntl_alarm
pcntl_fork
posix_getuid
posix_getpwuid
posix_kill
posix_setsid
posix_getpid
posix_getpwnam
posix_getgrnam
posix_getgid
posix_setgid
posix_initgroups
posix_setuid
posix_isatty
proc_open
proc_get_status
proc_close
shell_exec
```
