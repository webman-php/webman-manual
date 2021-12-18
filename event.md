# webman 事件库 webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

事件相比较中间件的优势是事件比中间件更加精准定位（或者说粒度更细），并且更适合一些业务场景的扩展。例如，我们通常会遇到用户注册或者登录后需要做一系列操作，通过事件系统可以做到不侵入原有代码完成登录的操作扩展，降低系统的耦合性的同时，也降低了BUG的可能性。

## 项目地址

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## 依赖

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## 安装

```shell script
composer require tinywan/webman-event
```
## 配置 

事件配置文件 `config/event.php` 内容如下

```php
return [
    // 事件监听
    'listener'    => [],

    // 事件订阅器
    'subscriber' => [],
];
```
### 进程启动配置

打开 `config/bootstrap.php`，加入如下配置：

```php
return [
    // 这里省略了其它配置 ...
    webman\event\EventManager::class,
];
```
## 快速开始

### 定义事件

事件类 `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // 事件名，事件的唯一标识

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

### 监听事件
```php
return [
    // 事件监听
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### 订阅事件

订阅类 `LoggerSubscriber.php`

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
     * @desc: 触发事件
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // 一些具体的业务逻辑
        var_dump($event->handle());
    }
}
```

事件订阅
```php
return [
    // 事件订阅
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### 事件触发器

触发 `LogErrorWriteEvent` 事件。

```php
$error = [
    'errorMessage' => '错误消息',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

执行结果

![打印结果](./trigger.png)

## License

This project is licensed under the [Apache 2.0 license](LICENSE).

