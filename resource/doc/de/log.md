# Protokoll
Webman verwendet [monolog/monolog](https://github.com/Seldaek/monolog) zur Protokollierung.

## Verwendung
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('Protokolltest');
        return response('Hallo Index');
    }
}
```

## Bereitgestellte Methoden
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
Äquivalent zu
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

## Konfiguration
```php
return [
    // Standard-Protokollkanal
    'default' => [
        // Handler für den Standardkanal, können mehrere eingestellt werden
        'handlers' => [
            [   
                // Klassenname des Handlers
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Konstruktorparameter des Handlers
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formatbezogen
                'formatter' => [
                    // Klassenname des Formatierungshandlers
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Konstruktorparameter des Formatierungshandlers
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## Mehrere Kanäle
Monolog unterstützt mehrere Kanäle und verwendet standardmäßig den `default`-Kanal. Wenn Sie einen zusätzlichen Kanal `log2` hinzufügen möchten, erfolgt die Konfiguration wie folgt:
```php
return [
    // Standard-Protokollkanal
    'default' => [
        // Handler für den Standardkanal, können mehrere eingestellt werden
        'handlers' => [
            [   
                // Klassenname des Handlers
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Konstruktorparameter des Handlers
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formatbezogen
                'formatter' => [
                    // Klassenname des Formatierungshandlers
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Konstruktorparameter des Formatierungshandlers
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // Kanal log2
    'log2' => [
        // Handler für den Kanal log2, können mehrere eingestellt werden
        'handlers' => [
            [   
                // Klassenname des Handlers
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Konstruktorparameter des Handlers
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formatbezogen
                'formatter' => [
                    // Klassenname des Formatierungshandlers
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Konstruktorparameter des Formatierungshandlers
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

Verwendung des Kanals `log2`:
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
        $log->info('Log2-Test');
        return response('Hallo Index');
    }
}
```
