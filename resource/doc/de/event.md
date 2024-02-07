# webman Event Library webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

Der Vorteil von Events gegenüber Mittlerwaren besteht darin, dass Events im Vergleich zu Mittlerwaren präziser positioniert sind (oder genauer gesagt, die Granularität ist feiner) und besser für die Erweiterung einiger Geschäftsszenarien geeignet sind. Beispielsweise müssen wir häufig eine Reihe von Operationen durchführen, nachdem sich ein Benutzer registriert oder angemeldet hat. Durch das Event-System können wir die Anmeldung ohne Eingriffe in den vorhandenen Code erweitern, die Kopplung des Systems reduzieren und gleichzeitig die Möglichkeit von Fehlern verringern.

## Projektadresse

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Abhängigkeiten

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Installation

```shell script
composer require tinywan/webman-event
```

## Konfiguration

Der Event-Konfigurationsdatei `config/event.php` hat folgenden Inhalt:

```php
return [
    // Event-Listener
    'listener'    => [],

    // Event-Abonnenten
    'subscriber' => [],
];
```

### Prozessstart-Konfiguration

Öffnen Sie `config/bootstrap.php` und fügen Sie die folgende Konfiguration hinzu:

```php
return [
    // andere Konfigurationen werden hier ausgelassen ...
    webman\event\EventManager::class,
];
```

## Schnellstart

### Eventdefinition

Event-Klasse `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // Eventname, die eindeutige Kennung des Events

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

### Event-Listener

```php
return [
    // Event-Listener
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Event-Abonnent

Abonnentenklasse `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: Beschreibung der Methode
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: Event auslösen
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // Einige spezifische Geschäftslogik
        var_dump($event->handle());
    }
}
```

Event-Abonnement
```php
return [
    // Event-Abonnements
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Event-Auslöser

`LogErrorWriteEvent` Ereignis auslösen.

```php
$error = [
    'errorMessage' => 'Fehlermeldung',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Ausführungsergebnis

![Print result](./trigger.png)

## Lizenz

Dieses Projekt ist lizenziert unter der [Apache 2.0-Lizenz](LICENSE).
