# 비즈니스 초기화

가끔은 프로세스가 시작된 후에 비즈니스 초기화를 수행해야 하는 경우가 있습니다. 이 초기화는 프로세스의 수명주기 동안 한 번만 실행되며, 예를 들어 프로세스가 시작된 후 타이머를 설정하거나 데이터베이스 연결을 초기화하는 등 작업을 수행할 수 있습니다. 아래에서 이에 대해 설명하겠습니다.

## 원리
**[실행 흐름](process.md)**에 설명된대로, webman은 프로세스가 시작된 후 `config/bootstrap.php`(또는 `config/plugin/*/*/bootstrap.php`)에 설정된 클래스를 로드하고 클래스의 start 메서드를 실행합니다. start 메서드에 비즈니스 코드를 추가하여 프로세스 시작 후의 비즈니스 초기화 작업을 완료할 수 있습니다.

## 과정
예를 들어, 현재 프로세스의 메모리 사용을 주기적으로 보고하는 타이머를 만들어야 한다고 가정해 봅시다. 이를 위해 `MemReport`라는 클래스를 만들 것입니다.

#### 명령어 실행

명령어 `php webman make:bootstrap MemReport`를 실행하여 초기화 파일 `app/bootstrap/MemReport.php`를 생성합니다.

> **팁**
> 만약 웹맨이 `webman/console`를 설치하지 않았다면, `composer require webman/console` 명령어로 설치하십시오.

#### 초기화 파일 편집
`app/bootstrap/MemReport.php`를 편집하여 다음과 같이 작성합니다:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // CLI 환경인가요?
        $is_console = !$worker;
        if ($is_console) {
            // CLI 환경에서 이 초기화를 실행하고 싶지 않다면 여기에서 바로 반환하세요.
            return;
        }
        
        // 10초마다 실행
        \Workerman\Timer::add(10, function () {
            // 데모를 위해 여기서는 리포트 프로세스 대신 출력을 사용합니다.
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **팁**
> 명령어 행을 사용할 때에도, 프레임워크는 `config/bootstrap.php`에서 설정된 start 메서드를 실행합니다. 우리는 `$worker`가 null인지 여부를 확인하여 CLI 환경인지 여부를 판단하고, 이에 따라 비즈니스 초기화 코드를 실행할지 여부를 결정할 수 있습니다.

#### 프로세스 시작과 함께 설정
`config/bootstrap.php`를 열고 `MemReport` 클래스를 시작 항목에 추가합니다.
```php
return [
    // ...다른 설정들은 생략합니다...
    
    app\bootstrap\MemReport::class,
];
```

이렇게 하면 비즈니스 초기화 과정을 완료하게 됩니다.

## 추가 설명
[사용자 정의 프로세스](../process.md)가 시작될 때도 `config/bootstrap.php`에서 설정된 start 메서드가 실행됩니다. 우리는 `$worker->name`을 사용하여 현재 프로세스가 무엇인지 확인한 후, 해당 프로세스에서 비즈니스 초기화 코드를 실행할 지 여부를 결정할 수 있습니다. 예를 들어, `monitor` 프로세스를 모니터링할 필요가 없다면 `MemReport.php`의 내용은 다음과 같을 것입니다:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // CLI 환경인가요?
        $is_console = !$worker;
        if ($is_console) {
            // CLI 환경에서 이 초기화를 실행하고 싶지 않다면 여기에서 바로 반환하세요.
            return;
        }
        
        // 모니터 프로세스에서는 타이머를 실행하지 않음
        if ($worker->name == 'monitor') {
            return;
        }
        
        // 10초마다 실행
        \Workerman\Timer::add(10, function () {
            // 데모를 위해 여기서는 리포트 프로세스 대신 출력을 사용합니다.
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
