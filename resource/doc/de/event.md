# webman Event-Bibliothek webman-event

[![Lizenz](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

Der Vorteil von Events gegenüber Middleware besteht darin, dass Events eine präzisere Positionierung (oder eine feinere Granularität) gegenüber Middleware aufweisen und besser für die Erweiterung einiger Geschäftsszenarien geeignet sind. Zum Beispiel müssen wir häufig eine Reihe von Operationen ausführen, nachdem sich ein Benutzer registriert oder angemeldet hat. Durch das Ereignissystem können wir die Anmeldung ohne Beeinträchtigung des vorhandenen Codes erweitern. Dies reduziert die Kopplung des Systems und verringert gleichzeitig die Möglichkeit von Fehlern.

## Projektadresse

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Abhängigkeiten

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Installation

```shell script
composer require tinywan/webman-event
```

## Konfiguration 

Der Ereignissystem-Konfigurationsdatei `config/event.php` enthält den folgenden Inhalt:

```php
return [
    // Ereignisüberwachung
    'listener'    => [],

    // Ereignisabonnent
    'subscriber' => [],
];
```

### Prozessstartkonfiguration

Öffnen Sie `config/bootstrap.php` und fügen Sie die folgende Konfiguration hinzu:

```php
return [
    // Hier sind andere Konfigurationen ausgelassen ...
    webman\event\EventManager::class,
];
```

## Schnellstart

### Definition des Ereignisses

Ereignisklasse `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // Ereignisname, die eindeutige Kennung des Ereignisses

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

### Ereignisüberwachung
```php
return [
    // Ereignisüberwachung
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Ereignisabonnent

Abonnementklasse `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: Methode Beschreibung
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: Ereignis auslösen
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // Einige spezifische Geschäftslogik
        var_dump($event->handle());
    }
}
```

Ereignisabonnement
```php
return [
    // Ereignisabonnement
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Ereignisauslöser

Das `LogErrorWriteEvent` Ereignis auslösen.

```php
$error = [
    'errorMessage' => 'Fehlermeldung',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Ergebnis anzeigen

![Druckergebnis] (./trigger.png)

## Lizenz

Dieses Projekt ist unter der [Apache 2.0-Lizenz](LICENSE) lizenziert.
