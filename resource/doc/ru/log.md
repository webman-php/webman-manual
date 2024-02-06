# Журнал
Webman использует [monolog/monolog](https://github.com/Seldaek/monolog) для обработки журнала.

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
        Log::info('тест журнала');
        return response('привет, index');
    }
}
```

## Предоставляемые методы
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
эквивалентно
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
    // Журнал по умолчанию
    'default' => [
        // Обработчики для журнала по умолчанию, можно установить несколько
        'handlers' => [
            [   
                // имя класса обработчика
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // параметры конструктора класса обработчика
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Связанная информация о формате
                'formatter' => [
                    // имя класса форматирования
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // параметры конструктора класса форматирования
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## Многоканальность
Monolog поддерживает множественные каналы, по умолчанию используется канал `default`. Если вы хотите добавить канал `log2`, конфигурация будет выглядеть следующим образом:
```php
return [
    // Журнал по умолчанию
    'default' => [
        // Обработчики для журнала по умолчанию, можно установить несколько
        'handlers' => [
            [   
                // имя класса обработчика
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // параметры конструктора класса обработчика
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Связанная информация о формате
                'formatter' => [
                    // имя класса форматирования
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // параметры конструктора класса форматирования
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // Канал log2
    'log2' => [
        // Обработчики для канала log2, можно установить несколько
        'handlers' => [
            [   
                // имя класса обработчика
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // параметры конструктора класса обработчика
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // Связанная информация о формате
                'formatter' => [
                    // имя класса форматирования
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // параметры конструктора класса форматирования
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

Использование канала `log2` будет выглядеть следующим образом:
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
        $log->info('тест log2');
        return response('привет, index');
    }
}
```
