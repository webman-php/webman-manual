# webman biblioteca de eventos webman-event

[![licença](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

Em comparação com os Middleware, a vantagem dos eventos é que eles são mais precisos do que os middlewares (ou seja, têm uma granularidade mais fina) e mais adequados para a extensão de cenários de negócios. Por exemplo, frequentemente nos deparamos com a necessidade de realizar uma série de operações após o registro ou login do usuário. Através do sistema de eventos, é possível realizar a extensão das operações de login sem afetar o código original, reduzindo assim o acoplamento do sistema e diminuindo a possibilidade de bugs.

## Endereço do Projeto

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Dependências

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Instalação

```shell script
composer require tinywan/webman-event
```

## Configuração

O arquivo de configuração de eventos `config/event.php` tem o seguinte conteúdo:

```php
return [
    // Ouvintes de evento
    'listener'    => [],

    // Assinantes de evento
    'subscriber' => [],
];
```

### Configuração de inicialização de processo

Abra `config/bootstrap.php` e adicione a seguinte configuração:

```php
return [
    // Aqui estão omitidas outras configurações ...
    webman\event\EventManager::class,
];
```

## Começo rápido

### Definir evento

Classe de evento `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // O nome do evento, identificador único do evento

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

### Ouvir evento

```php
return [
    // Ouvintes de evento
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Subscrever eventos

Classe de assinatura `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: Descrição do método
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
        // Alguma lógica de negócios específica
        var_dump($event->handle());
    }
}
```

Subscrição de eventos
```php
return [
    // Assinantes de evento
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Disparador de eventos

Disparar evento `LogErrorWriteEvent`.

```php
$error = [
    'errorMessage' => 'Mensagem de erro',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Resultado da execução

![Imprimir resultado](./trigger.png)

## Licença

Este projeto está licenciado sob a [licença Apache 2.0](LICENSE).
