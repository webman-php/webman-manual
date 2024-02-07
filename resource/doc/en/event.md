# webman Event Library

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

The advantage of events over middleware is that events are more precise in positioning (or finer-grained) than middleware, and they are more suitable for extending certain business scenarios. For example, we often encounter a series of operations that need to be performed after a user registers or logs in. With the event system, we can complete the login operation extension without modifying the original code, reducing the coupling of the system and reducing the possibility of bugs.

## Project URL

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Dependencies

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Installation

```shell script
composer require tinywan/webman-event
```

## Configuration

Event configuration file `config/event.php` is as follows:

```php
return [
    // Event listeners
    'listener'    => [],

    // Event subscribers
    'subscriber' => [],
];
```

### Process startup configuration

Open `config/bootstrap.php` and add the following configuration:

```php
return [
    // Other configurations are omitted here ...
    webman\event\EventManager::class,
];
```

## Quick Start

### Define an Event

Event class `LogErrorWriteEvent.php`:

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // Event name, the unique identifier of the event

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

### Listen for Events

```php
return [
    // Event listeners
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Subscribe to Events

Subscription class `LoggerSubscriber.php`:

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: Method description
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: Trigger event
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // Some specific business logic
        var_dump($event->handle());
    }
}
```

Event subscription:

```php
return [
    // Event subscribers
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

Execution result:

![Print result](./trigger.png)

## License

This project is licensed under the [Apache 2.0 license](LICENSE).
