# Biblioteca de eventos de webman webman-event

[![licencia](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

La ventaja de los eventos en comparación con los middleware es que los eventos son más precisos (o tienen una granularidad más fina) que los middleware, y son más adecuados para la expansión en ciertos escenarios comerciales. Por ejemplo, a menudo nos encontramos con que después de que un usuario se registra o inicia sesión, se necesitan realizar una serie de operaciones. A través del sistema de eventos, podemos realizar la extensión de la operación de inicio de sesión sin entrar en el código existente, lo que reduce la acoplamiento del sistema y, al mismo tiempo, reduce la posibilidad de errores.

## Dirección del proyecto

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Dependencias

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Instalación

```shell script
composer require tinywan/webman-event
```

## Configuración

El archivo de configuración de eventos `config/event.php` tiene el siguiente contenido

```php
return [
    // Oyentes de eventos
    'listener'    => [],

    // Suscriptor de eventos
    'subscriber' => [],
];
```

### Configuración de inicio de proceso

Abra `config/bootstrap.php` e incluya la siguiente configuración:

```php
return [
    // Otras configuraciones se han omitido aquí ...
    webman\event\EventManager::class,
];
```

## Comenzar rápidamente

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
    // Oyentes de eventos
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Suscribir eventos

Clase de suscriptor `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: Método de descripción
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
        // Algún lógica de negocio concreta
        var_dump($event->handle());
    }
}
```

Suscripción de eventos
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
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Resultado de ejecución

![trigger](./trigger.png)

## Licencia

Este proyecto está licenciado bajo la [licencia Apache 2.0](LICENSE).
