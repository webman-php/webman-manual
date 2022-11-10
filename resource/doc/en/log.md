# log
webmanUsage [monolog/monolog](https://github.com/Seldaek/monolog) Processlog。

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

## Methods provided
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

## Configure
```php
return [
    // Default logging channel
    'default' => [
        // Handler for handling the default channel, multiple can be set
        'handlers' => [
            [   
                // handlerName of class
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // handlerClass constructor parameters
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Format-related
                'formatter' => [
                    // Formatting the name of the processing class
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Formatting the constructor parameters of the processing class
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## Multichannel
monologMultiple channels are supported, and the default is to use the `default` channel. If you want to add a `log2` channel, the configuration is similar to the following：
```php
return [
    // Default logging channel
    'default' => [
        // Handler for handling the default channel, multiple can be set
        'handlers' => [
            [   
                // handlerName of class
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // handlerClass constructor parameters
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Format-related
                'formatter' => [
                    // Formatting the name of the processing class
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Formatting the constructor parameters of the processing class
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // log2Channel
    'log2' => [
        // Handler for handling the default channel, multiple can be set
        'handlers' => [
            [   
                // handlerName of class
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // handlerClass constructor parameters
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // Format-related
                'formatter' => [
                    // Formatting the name of the processing class
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Formatting the constructor parameters of the processing class
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

The usage when using the `log2` channel is as follows：
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