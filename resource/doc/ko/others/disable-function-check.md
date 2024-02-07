# 함수 비활성화 확인

이 스크립트를 사용하여 비활성화된 함수가 있는지 확인하십시오. 명령 라인에서 다음을 실행하십시오: ```curl -Ss https://www.workerman.net/webman/check | php```

함수가 비활성화되었다는 메시지가 나오면 ```php.ini``` 파일에서 기능을 활성화해야만 webman을 정상적으로 사용할 수 있습니다. 비활성화 해제를 위해 다음 방법 중 하나를 선택하십시오.

## 방법 1
`webman/console` 을 설치합니다.
``` 
composer require webman/console ^v1.2.35
```

다음 명령을 실행합니다.
```
php webman fix-disable-functions
```

## 방법 2
비활성화된 함수를 해제하기 위해 다음 스크립트를 실행하십시오:
```
curl -Ss https://www.workerman.net/webman/fix-disable-functions | php
```

## 방법 3
`php --ini` 명령을 실행하여 php cli가 사용하는 ```php.ini``` 파일 위치를 찾으십시오.

그리고 ```php.ini``` 파일을 열고, `disable_functions`를 찾아 다음 함수의 호출을 해제하십시오.
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
