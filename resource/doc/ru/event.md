# Библиотека событий Webman webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

Преимущество событий по сравнению с промежуточным программным обеспечением заключается в том, что события более точно позиционируются (или имеют более мелкую гранулярность) по сравнению с промежуточным программным обеспечением, и они более подходят для расширения некоторых бизнес-сценариев. Например, мы часто сталкиваемся с тем, что после регистрации или входа пользователя требуется выполнить ряд операций. С помощью системы событий можно выполнить операцию входа без нарушения исходного кода, снизить связность системы, а также уменьшить возможность появления ошибок.

## Ссылка на проект

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Зависимость

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Установка

```shell script
composer require tinywan/webman-event
```
## Конфигурация

Содержимое файла конфигурации событий `config/event.php`:

```php
return [
    // Слушатель событий
    'listener'    => [],

    // Подписчик событий
    'subscriber' => [],
];
```
### Настройка запуска процесса

Откройте `config/bootstrap.php` и добавьте следующую конфигурацию:

```php
return [
    // Другие настройки здесь ...
    webman\event\EventManager::class,
];
```
## Быстрый старт

### Определение события

Класс событий `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // Имя события, уникальный идентификатор события

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

### Слушать событие

```php
return [
    // Слушатель событий
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Подписка на события

Класс подписчика `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: Описание метода
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: Запуск события
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // Некоторая конкретная бизнес-логика
        var_dump($event->handle());
    }
}
```

Подписка на события
```php
return [
    // Подписчик событий
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Инициатор событий

Запуск события `LogErrorWriteEvent`.

```php
$error = [
    'errorMessage' => 'Ошибка',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Результат выполнения

![Результат выполнения](./trigger.png)

## Лицензия

Этот проект лицензирован в соответствии с [лицензией Apache 2.0](LICENSE).
