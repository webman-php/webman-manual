# 프로그래밍에 대한 이해

## 운영 체제
webman은 Linux 및 Windows 시스템에서 모두 실행할 수 있습니다. 그러나 workerman은 Windows에서 다중 프로세스 설정 및 데몬을 지원하지 않기 때문에 Windows 시스템은 개발 환경과 디버깅에만 사용하는 것을 권장하며, 본격적인 운영 환경에는 Linux 시스템을 사용해야 합니다.

## 시작 방법
**Linux 시스템**에서는 `php start.php start`(디버그 디버깅 모드) 또는 `php start.php start -d` (데몬 모드) 명령을 사용하여 시작합니다.
**Windows 시스템**에서는 `windows.bat`을 실행하거나 `php windows.php` 명령을 사용하여 시작하고, Ctrl+C를 눌러 중지합니다. Windows 시스템에서는 stop, reload, status 및 reload connections와 같은 명령을 지원하지 않습니다.

## 상주 메모리
webman은 상주 메모리 프레임워크이며, 일반적으로 PHP 파일이 메모리에로드되면 재사용되며 디스크에서 다시 읽지 않습니다(템플릿 파일은 제외). 따라서 본격적인 운영 환경에서는 비즈니스 코드나 구성 변경 후 `php start.php reload`를 실행해야 적용됩니다. 프로세스 관련 구성을 변경하거나 새로운 작성 패키지를 설치한 경우에는 `php start.php restart`를 실행해야 합니다.

> 개발 편의를 위해 webman에는 비즈니스 파일 업데이트를 모니터링하는 사용자 지정 프로세스가 있습니다. 비즈니스 파일이 업데이트되면 자동으로 다시로드됩니다. 이 기능은 workerman에서 디버그 모드로 실행할 경우에만 활성화됩니다(시작 시 `-d`를 사용하지 않습니다). Windows 사용자는 `windows.bat` 또는 `php windows.php`를 실행해야 활성화됩니다.

## 출력문에 대한 주의
전통적인 php-fpm 프로젝트에서는 `echo`나 `var_dump`와 같은 함수를 사용하여 데이터를 출력하면 페이지에 직접 표시되지만, webman에서는 이러한 출력물이 종종 터미널에 표시되어 웹 페이지에 표시되지 않습니다(템플릿 파일의 출력은 제외).

## `exit` 및 `die` 문 실행 금지
`die` 또는 `exit`를 실행하면 프로세스가 종료되고 다시 시작되므로 현재 요청이 올바르게 응답되지 않습니다.

## `pcntl_fork` 함수 실행 금지
`pcntl_fork` 함수를 사용하여 프로세스를 생성하는 것은 webman에서 허용되지 않습니다.
