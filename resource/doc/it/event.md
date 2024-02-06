# Libreria degli eventi webman - webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

Il vantaggio degli eventi rispetto ai middleware è che gli eventi offrono una localizzazione più precisa (o una granularità più fine) rispetto ai middleware e sono più adatti all'espansione in alcuni scenari aziendali. Ad esempio, è comune incontrare la necessità di eseguire una serie di operazioni dopo che un utente si è registrato o ha effettuato l'accesso; con il sistema degli eventi è possibile estendere l'operazione di accesso senza impattare il codice esistente, riducendo l'accoppiamento del sistema e diminuendo la possibilità di errori.

## Indirizzo del progetto

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Dipendenze

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Installazione

```shell script
composer require tinywan/webman-event
```
## Configurazione 

Il file di configurazione degli eventi `config/event.php` ha il seguente contenuto

```php
return [
    // Event listener
    'listener'    => [],

    // Event subscriber
    'subscriber' => [],
];
```
### Configurazione dell'avvio del processo

Aprire `config/bootstrap.php` e aggiungere la seguente configurazione:

```php
return [
    // Altre configurazioni sono state omesse ...
    webman\event\EventManager::class,
];
```
## Inizio veloce

### Definizione di un evento

Classe evento `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // Nome dell'evento, identificativo unico dell'evento

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

### Ascolto degli eventi
```php
return [
    // Event listener
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Sottoscrizione degli eventi

Classe di sottoscrizione `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: Metodo di descrizione
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: Scatena l'evento
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // Alcuna logica di business specifica
        var_dump($event->handle());
    }
}
```

Sottoscrizione degli eventi
```php
return [
    // Sottoscrizione degli eventi
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Trigger degli eventi

Attiva l'evento `LogErrorWriteEvent`.

```php
$error = [
    'errorMessage' => 'Messaggio di errore',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Risultato dell'esecuzione

![Print output](./trigger.png)

## Licenza

Questo progetto è sottoposto alla licenza [Apache 2.0](LICENSE).
