# 비즈니스 초기화

가끔은 프로세스를 시작한 후에 비즈니스 초기화를 수행해야 하는 경우가 있습니다. 이 초기화는 프로세스 수명 주기에서 한 번만 실행되며, 예를 들어 프로세스 시작 후 타이머를 설정하거나 데이터베이스 연결을 초기화하는 것과 같은 작업을 수행할 수 있습니다. 아래에서는 이에 대해 설명하겠습니다.

## 원리
**[실행 흐름](process.md)**에 설명된대로, webman은 프로세스가 시작된 후 `config/bootstrap.php` (즉, `config/plugin/*/*/bootstrap.php` 포함)에 설정된 클래스를 로드하고 클래스의 start 메서드를 실행합니다. 우리는 여기에 비즈니스 코드를 추가하여 프로세스 시작 후 비즈니스 초기화 작업을 완료할 수 있습니다.

## 과정
우리가 메모리 사용량을 주기적으로 보고하는 타이머를 만들어야 한다고 가정해봅시다. 이 클래스는 `MemReport`라는 이름으로 만들어집니다.

#### 명령 실행

명령 `php webman make:bootstrap MemReport`를 실행하여 초기화 파일 `app/bootstrap/MemReport.php`를 생성합니다.

> **팁**
> 만약 webman에 'webman/console'가 설치되어 있지 않다면 `composer require webman/console` 명령을 실행하여 설치하십시오.

#### 초기화 파일 편집
`app/bootstrap/MemReport.php`를 편집하여 다음과 같이 내용을 작성합니다:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // 명령행 환경인가 ?
        $is_console = !$worker;
        if ($is_console) {
            // 명령행 환경에서 이 초기화를 실행하고 싶지 않다면 여기서 바로 반환합니다.
            return;
        }
        
        // 매 10초마다 실행
        \Workerman\Timer::add(10, function () {
            // 데모를 위해 여기에서는 리포팅 프로세스 대신 출력을 사용합니다
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **팁**
> 명령행을 사용할 때도 프레임워크는 `config/bootstrap.php`에 설정된 start 메서드를 실행하는데, 이때 우리는 `$worker`가 null인지 여부로 명령행 환경인지를 판단하여 비즈니스 초기화 코드를 실행할지를 결정할 수 있습니다.

#### 프로세스 시작과 함께 설정
`config/bootstrap.php`을 열고 `MemReport` 클래스를 시작 항목에 추가하십시오.
```php
return [
    // ...기타 설정은 생략...
    
    app\bootstrap\MemReport::class,
];
```

이렇게 하면 비즈니스 초기화 과정이 완료됩니다.

## 추가 설명
[사용자 정의 프로세스](../process.md)가 시작되면 `config/bootstrap.php`에 설정된 start 메서드도 실행됩니다. 우리는 `$worker->name`을 사용하여 현재 프로세스가 무엇인지 판단하고, 해당 프로세스에서 비즈니스 초기화 코드를 실행할지를 결정할 수 있습니다. 예를 들어 모니터 프로세스를 감시할 필요가 없는 경우, `MemReport.php`의 내용은 다음과 같습니다:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // 명령행 환경인가 ?
        $is_console = !$worker;
        if ($is_console) {
            // 명령행 환경에서 이 초기화를 실행하고 싶지 않다면 여기서 바로 반환합니다.
            return;
        }
        
        // 모니터 프로세스는 타이머를 실행하지 않음
        if ($worker->name == 'monitor') {
            return;
        }
        
        // 매 10초마다 실행
        \Workerman\Timer::add(10, function () {
            // 데모를 위해 여기에서는 리포팅 프로세스 대신 출력을 사용합니다
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
