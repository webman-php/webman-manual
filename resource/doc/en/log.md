# Logging
webman uses [monolog/monolog](https://github.com/Seldaek/monolog) to handle logging.

## Usage
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('log test');
        return response('hello index');
    }
}
```

## Provided Methods
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
Equivalent to
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

## Configuration
```php
return [
    // Default log channel
    'default' => [
        // Handlers for the default channel, can set multiple
        'handlers' => [
            [   
                // Class name of the handler
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Constructor parameters of the handler class
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formatter related
                'formatter' => [
                    // Class name of the formatter
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Constructor parameters of the formatter class
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## Multiple Channels
monolog supports multiple channels, with `default` channel being used by default. If you want to add a `log2` channel, the configuration is similar to the following:
```php
return [
    // Default log channel
    'default' => [
        // Handlers for the default channel, can set multiple
        'handlers' => [
            [   
                // Class name of the handler
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Constructor parameters of the handler class
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formatter related
                'formatter' => [
                    // Class name of the formatter
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Constructor parameters of the formatter class
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // log2 channel
    'log2' => [
        // Handlers for the log2 channel, can set multiple
        'handlers' => [
            [   
                // Class name of the handler
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Constructor parameters of the handler class
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // Formatter related
                'formatter' => [
                    // Class name of the formatter
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Constructor parameters of the formatter class
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

To use the `log2` channel, you can do as follows:
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
        $log->info('log2 test');
        return response('hello index');
    }
}
```
