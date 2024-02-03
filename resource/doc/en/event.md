# webman Event Library webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

Compared with middleware, events have the advantage of more precise positioning (or finer granularity) and are more suitable for some business scenarios to expand. For example, we often encounter situations where a series of operations need to be performed after a user registers or logs in. The event system can achieve the extension of the login operation without invading the original code, reducing the coupling of the system and the possibility of bugs.

## Project Address

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Dependencies

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Installation

```shell script
composer require tinywan/webman-event
```

## Configuration

Contents of the event configuration file `config/event.php` are as follows:

```php
return [
    // Event Listeners
    'listener'    => [],

    // Event Subscribers
    'subscriber' => [],
];
```

### Process Start Configuration

Open `config/bootstrap.php` and add the following configuration:

```php
return [
    // Other configurations are omitted here ...
    webman\event\EventManager::class,
];
```

## Quick Start

### Define Event

Event class `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // Event Name, unique identifier for the event

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

### Listen to Event

```php
return [
    // Event Listeners
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Subscribe to Event

Subscription class `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: Method Description
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: Trigger Event
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // Some specific business logic
        var_dump($event->handle());
    }
}
```

Event Subscription

```php
return [
    // Event Subscribers
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Event Trigger

Trigger the `LogErrorWriteEvent` event.

```php
$error = [
    'errorMessage' => 'Error message',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Execution result

![Print result](./trigger.png)

## License

This project is licensed under the [Apache 2.0 license](LICENSE).