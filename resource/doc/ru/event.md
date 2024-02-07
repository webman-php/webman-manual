# webman событийная библиотека webman-event

[![лицензия](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

Преимущество событий по сравнению с посредниками заключается в более точной локализации событий (или более тонкой гранулярности) и более подходящем расширении для некоторых бизнес-сценариев. Например, мы часто сталкиваемся с ситуацией, когда после регистрации или входа пользователя необходимо выполнить ряд операций. Через систему событий можно выполнить расширение операции входа без нарушения исходного кода, уменьшив при этом зависимость системы и вероятность ошибок.

## Адрес проекта

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Зависимости

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Установка

```shell script
composer require tinywan/webman-event
```
## Конфигурация 

Содержание файла конфигурации событий `config/event.php`:

```php
return [
    // Слушатели событий
    'listener'    => [],

    // Подписчики событий
    'subscriber' => [],
];
```
### Конфигурация запуска процесса

Откройте `config/bootstrap.php` и добавьте следующую конфигурацию:

```php
return [
    // Здесь опущены другие настройки...
    webman\event\EventManager::class,
];
```
## Быстрый старт

### Определение события

Класс события `LogErrorWriteEvent.php`:

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

### Прослушивание события
```php
return [
    // Слушатели событий
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Подписка на событие

Класс подписчика `LoggerSubscriber.php`:

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

Подписка на событие:
```php
return [
    // Подписчики событий
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Инициатор события

Инициировать событие `LogErrorWriteEvent`:

```php
$error = [
    'errorMessage' => 'Ошибка',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Результат выполнения

![Результат вывода](./trigger.png)

## Лицензия

Этот проект лицензируется в соответствии с лицензией [Apache 2.0 license](LICENSE).
