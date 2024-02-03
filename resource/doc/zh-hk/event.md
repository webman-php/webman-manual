# webman 事件庫 webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

與中間件相比，事件的優勢在於更加精確定位（或者說粒度更細），並且更適合於某些業務場景的擴展。例如，我們通常會遇到用戶註冊或登錄後需要進行一系列操作，通過事件系統可以在不侵入原有代碼的情況下擴展登錄操作，降低系統的耦合性，同時也降低了錯誤的可能性。

## 專案地址

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## 依賴

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## 安裝

```shell script
composer require tinywan/webman-event
```
## 配置 

事件配置文件 `config/event.php` 內容如下

```php
return [
    // 事件監聽
    'listener'    => [],

    // 事件訂閱器
    'subscriber' => [],
];
```
### 進程啟動配置

打開 `config/bootstrap.php`，加入如下配置：

```php
return [
    // 這裡省略了其他配置 ...
    webman\event\EventManager::class,
];
```
## 快速開始

### 定義事件

事件類別 `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // 事件名，事件的唯一標識

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

### 監聽事件
```php
return [
    // 事件監聽
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### 訂閱事件

訂閱類別 `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: 方法描述
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: 觸發事件
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // 一些具體的業務邏輯
        var_dump($event->handle());
    }
}
```

事件訂閱
```php
return [
    // 事件訂閱
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### 事件觸發器

觸發 `LogErrorWriteEvent` 事件。

```php
$error = [
    'errorMessage' => '錯誤消息',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

執行結果

![打印结果](./trigger.png)

## 授權許可

本專案使用 [Apache 2.0 授權許可](LICENSE)。