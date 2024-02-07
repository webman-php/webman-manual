# webman biblioteca de eventos webman-event

[![licença](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

A vantagem dos eventos em comparação com os middlewares é que os eventos são mais precisos (ou seja, têm granularidade mais fina) e são mais adequados para a expansão de alguns cenários de negócios. Por exemplo, geralmente nos deparamos com situações em que é necessário realizar uma série de operações após o registro ou login do usuário. Através do sistema de eventos, podemos realizar a extensão das operações de login sem invadir o código existente, reduzindo a complexidade do sistema e, ao mesmo tempo, reduzindo a possibilidade de bugs.

## Endereço do Projeto

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Dependências

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Instalação

```shell script
composer require tinywan/webman-event
```
## Configuração

O arquivo de configuração de eventos `config/event.php` contém o seguinte conteúdo

```php
return [
    // Ouvinte do evento
    'listener'    => [],

    // Assinante do Evento
    'subscriber' => [],
];
```
### Configuração de Inicialização do Processo

Abra `config/bootstrap.php` e adicione a seguinte configuração:

```php
return [
    // Outras configurações foram omitidas aqui ...
    webman\event\EventManager::class,
];
```
## Início Rápido

### Definir Eventos

Classe de eventos `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // Nome do evento, identificador exclusivo do evento

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

### Ouvir Eventos

```php
return [
    // Ouvinte do evento
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Assinar Eventos

Classe de inscrição `LoggerSubscriber.php`

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
        // Algo específico da lógica de negócios
        var_dump($event->handle());
    }
}
```

Evento de Assinatura
```php
return [
    // Assinatura de eventos
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Disparador de Eventos

Disparar o evento `LogErrorWriteEvent`.

```php
$error = [
    'errorMessage' => 'Mensagem de erro',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Resultado da execução

![Print result](./trigger.png)

## Licença

Este projeto está licenciado sob a [licença Apache 2.0](LICENSE).
