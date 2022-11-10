# webman EventBase webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

The advantage of events over middleware is that events are more precisely targeted (or finer grained) than middleware, and are better suited to the extension of some business scenarios. For example, we usually encounter a user registration or login need to do a series of operations, through the event system can be done without invading the original code to complete the login operation extensions, reducing the coupling of the system at the same time, but also reduce the possibility of bugs 。

## Project address

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Dependency

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Install

```shell script
composer require tinywan/webman-event
```
## Configure 

The event configuration file `config/event.php` reads as follows

```php
return [
    // Event Listening
    'listener'    => [],

    // Event Subscriber
    'subscriber' => [],
];
```
### Process Startup Configuration

Open `config/bootstrap.php` and add the following configuration：

```php
return [
    // Other configuration omitted here ...
    webman\event\EventManager::class,
];
```
## Quick Start

### Define events

Event Class `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // Event name, unique identifier of the event

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

### Listening to events
```php
return [
    // Event Listening
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Subscribe to events

SubscriptionClass `LoggerSubscriber.php`

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
     * @desc: Trigger events
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
    // Event Subscription
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Event Triggers

Triggers the `LogErrorWriteEvent` event。

```php
$error = [
    'errorMessage' => 'Error Message',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Execute results

![Print Results](./trigger.png)

## License

This project is licensed under the [Apache 2.0 license](LICENSE).

