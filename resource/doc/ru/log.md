# Журнал

webman использует [monolog/monolog](https://github.com/Seldaek/monolog) для обработки журналов.

## Использование
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('тестирование журнала');
        return response('привет, index');
    }
}
```

## Доступные методы
```php
Log::log($level, $message, array $context = [])
Log::debug($message, array $context = [])
Log::info($message, array $context = [])
Log::notice($message, array $context = [])
Log::warning($message, array $context = [])
Log::error($message, array $context = [])
Log::critical($message, array $context = [])
Log::alert($message, array $context = [])
Log::emergency($message, array $context = [])
```
Эквивалентно
```php
$log = Log::channel('default');
$log->log($level, $message, array $context = [])
$log->debug($message, array $context = [])
$log->info($message, array $context = [])
$log->notice($message, array $context = [])
$log->warning($message, array $context = [])
$log->error($message, array $context = [])
$log->critical($message, array $context = [])
$log->alert($message, array $context = [])
$log->emergency($message, array $context = [])
```

## Конфигурация
```php
return [
    // По умолчанию канал журнала
    'default' => [
        // Обработчики канала по умолчанию, можно установить несколько
        'handlers' => [
            [
                // Название класса обработчика
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Параметры конструктора класса обработчика
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Связано с форматом
                'formatter' => [
                    // Название класса форматирования
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Параметры конструктора класса форматирования
                    'constructor' => [null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## Множественные каналы
monolog поддерживает множественные каналы, по умолчанию используется канал `default`. Если вы хотите добавить канал `log2`, конфигурация будет следующей:

```php
return [
    // По умолчанию канал журнала
    'default' => [
        // Обработчики канала по умолчанию, можно установить несколько
        'handlers' => [
            [
                // Название класса обработчика
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Параметры конструктора класса обработчика
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Связано с форматом
                'formatter' => [
                    // Название класса форматирования
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Параметры конструктора класса форматирования
                    'constructor' => [null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // Канал log2
    'log2' => [
        // Обработчики канала log2, можно установить несколько
        'handlers' => [
            [
                // Название класса обработчика
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Параметры конструктора класса обработчика
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // Связано с форматом
                'formatter' => [
                    // Название класса форматирования
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Параметры конструктора класса форматирования
                    'constructor' => [null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

Использование канала `log2` выглядит следующим образом:
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        $log = Log::channel('log2');
        $log->info('тестирование log2');
        return response('привет, index');
    }
}
```
