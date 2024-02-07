# webman 이벤트 라이브러리 webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

이벤트는 미들웨어보다 정확한 위치 지정(또는 보다 세분화된)과 일부 비즈니스 시나리오의 확장에 더 적합하다는 장점이 있습니다. 예를 들어, 일련의 작업을 수행해야 하는 사용자가 등록하거나 로그인하는 경우, 이벤트 시스템을 사용하여 기존 코드에 침범하지 않고 로그인 작업을 확장하여 시스템의 결합도를 낮추고 버그 가능성을 줄일 수 있습니다.

## 프로젝트 주소

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## 종속성

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## 설치

```shell script
composer require tinywan/webman-event
```
## 설정 

이벤트 구성 파일 `config/event.php`의 내용은 다음과 같습니다.

```php
return [
    // 이벤트 리스닝
    'listener'    => [],

    // 이벤트 구독자
    'subscriber' => [],
];
```
### 프로세스 시작 구성

`config/bootstrap.php`을 열고 다음 구성을 추가합니다.

```php
return [
    // 여기에 다른 설정이 생략되었습니다 ...
    webman\event\EventManager::class,
];
```
## 빠른 시작

### 이벤트 정의

이벤트 클래스 `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // 이벤트 이름, 이벤트의 고유 식별자

    /** @var array */
    public array $log;

    public function __construct(array $log)
    {
        $this->log = $log;
    }

    public function handle()
    {
        return $this->log;
    }
}
```

### 이벤트 리스닝
```php
return [
    // 이벤트 리스닝
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### 이벤트 구독

구독 클래스 `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: 메소드 설명
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: 이벤트 트리거
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // 일부 구체적인 비즈니스 로직
        var_dump($event->handle());
    }
}
```

이벤트를 구독합니다.
```php
return [
    // 이벤트 구독
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### 이벤트 트리거

`LogErrorWriteEvent` 이벤트를 트리거합니다.

```php
$error = [
    'errorMessage' => '에러 메시지',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

실행 결과

![Print result](./trigger.png)

## 라이센스

이 프로젝트는 [Apache 2.0 라이센스](LICENSE)에 따라 라이센스가 부여됩니다.
