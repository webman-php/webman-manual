# webman libreria degli eventi webman-event

[![licenza](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

Il vantaggio degli eventi rispetto ai middleware è che gli eventi sono più precisi nel posizionamento (o hanno una granularità più fine) rispetto ai middleware e sono più adatti all'estensione in alcuni scenari aziendali. Ad esempio, spesso ci troviamo nella situazione in cui dopo la registrazione o il login dell'utente è necessario eseguire una serie di operazioni. Con il sistema degli eventi, è possibile estendere le operazioni di accesso senza intaccare il codice esistente, riducendo l'accoppiamento del sistema e riducendo al contempo la possibilità di errori.

## Indirizzo del progetto

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Dipendenze

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Installazione

```shell script
composer require tinywan/webman-event
```
## Configurazioni 

Il file di configurazione degli eventi `config/event.php` ha il seguente contenuto:

```php
return [
    // Ascoltatori di eventi
    'listener'    => [],

    // Abbonati agli eventi
    'subscriber' => [],
];
```
### Configurazione avvio processo

Aprire `config/bootstrap.php` e aggiungere la seguente configurazione:

```php
return [
    // Altre configurazioni omesse ...
    webman\event\EventManager::class,
];
```
## Inizio rapido

### Definire gli eventi

Classe evento `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // Nome evento, identificativo univoco dell'evento

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

### Ascoltare gli eventi
```php
return [
    // Ascoltatori di eventi
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Abbonarsi agli eventi

Classe di abbonamento `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: descrizione del metodo
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: Scaturire un evento
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // Qualche logica aziendale specifica
        var_dump($event->handle());
    }
}
```

Sottoscrizione agli eventi
```php
return [
    // Abbonati agli eventi
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Scaturitore di eventi

Scaturire l'evento `LogErrorWriteEvent`.

```php
$error = [
    'errorMessage' => 'Messaggio di errore',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Risultato dell'esecuzione

![Risultato della stampa](./trigger.png)

## Licenza

Questo progetto è distribuito sotto licenza [Apache 2.0](LICENSE).
