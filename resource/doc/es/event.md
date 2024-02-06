# Biblioteca de eventos webman webman-event

[![licencia](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

La ventaja de los eventos en comparación con los middlewares es que los eventos son más precisos en su ubicación (o tienen una granularidad más fina) y son más adecuados para la expansión de ciertos escenarios comerciales. Por ejemplo, a menudo nos encontramos con que después de que un usuario se registra o inicia sesión, es necesario realizar una serie de operaciones. A través del sistema de eventos, se puede lograr la expansión de las operaciones de inicio de sesión sin invadir el código original, lo que reduce la relación de acoplamiento del sistema y, al mismo tiempo, reduce la posibilidad de errores.

## Dirección del proyecto

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Dependencias

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Instalación

```shell script
composer require tinywan/webman-event
```

## Configuración

El archivo de configuración de eventos `config/event.php` tiene el siguiente contenido:

```php
return [
    // Evento escucha
    'listener'    => [],

    // Suscriptor de eventos
    'subscriber' => [],
];
```

### Configuración de inicio de procesos

Abre `config/bootstrap.php` y agrega la siguiente configuración:

```php
return [
    // Otras configuraciones ...
    webman\event\EventManager::class,
];
```

## Empezar rápidamente

### Definir eventos

Clase de evento `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // Nombre del evento, identificador único del evento

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

### Escuchar eventos

```php
return [
    // Evento escucha
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Suscribirse a eventos

Clase de suscripción `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: Descripción del método
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: Disparar evento
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // Algunas lógicas empresariales específicas
        var_dump($event->handle());
    }
}
```

Suscripción a eventos
```php
return [
    // Suscripción de eventos
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Desencadenador de eventos

Desencadena el evento `LogErrorWriteEvent`.

```php
$error = [
    'errorMessage' => 'Mensaje de error',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error), LogErrorWriteEvent::NAME);
```

Resultado de la ejecución

![Print Result](./trigger.png)

## Licencia

Este proyecto tiene licencia bajo la [licencia Apache 2.0](LICENSE).
