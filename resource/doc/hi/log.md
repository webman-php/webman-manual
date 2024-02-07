# लॉग
webman [monolog/monolog](https://github.com/Seldaek/monolog)  का उपयोग करता है लॉग को संसाधित करने के लिए।

## उपयोग
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('लॉग परीक्षण');
        return response('नमस्ते इंडेक्स');
    }
}
```

## प्रदान की गई मेथड
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
के समान
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

## संरचना
```php
return [
    // डिफ़ॉल्ट लॉग चैनल
    'default' => [
        // डिफ़ॉल्ट चैनल के हैंडलर को संसाधित करें, एक से अधिक सेट किया जा सकता है
        'handlers' => [
            [   
                // हैंडलर की नाम
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // हैंडलर की कंस्ट्रक्टर पैरामीटर
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // स्वरूप संबंधित
                'formatter' => [
                    // स्वरूपीकरण हैंडलर की नाम
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // स्वरूपीकरण हैंडलर की कंस्ट्रक्टर पैरामीटर
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## बहु-चैनल
monolog बहु-चैनल का समर्थन करता है, डिफ़ॉल्ट रूप से `default` चैनल का उपयोग करता है। अगर आप `log2` चैनल जोड़ना चाहते हैं, तो निम्नलिखित के बराबर संरचित करें:
```php
return [
    // डिफ़ॉल्ट लॉग चैनल
    'default' => [
        // डिफ़ॉल्ट चैनल के हैंडलर को संसाधित करें, एक से अधिक सेट किया जा सकता है
        'handlers' => [
            [   
                // हैंडलर की नाम
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // हैंडलर की कंस्ट्रक्टर पैरामीटर
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // स्वरूप संबंधित
                'formatter' => [
                    // स्वरूपीकरण हैंडलर की नाम
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // स्वरूपीकरण हैंडलर की कंस्ट्रक्टर पैरामीटर
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // log2 चैनल
    'log2' => [
        // डिफ़ॉल्ट चैनल के हैंडलर को संसाधित करें, एक से अधिक सेट किया जा सकता है
        'handlers' => [
            [   
                // हैंडलर की नाम
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // हैंडलर की कंस्ट्रक्टर पैरामीटर
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // स्वरूप संबंधित
                'formatter' => [
                    // स्वरूपीकरण हैंडलर की नाम
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // स्वरूपीकरण हैंडलर की कंस्ट्रक्टर पैरामीटर
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

`log2` चैनल का उपयोग करते समय उपयोग का तरीका निम्नलिखित है:
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
        $log->info('log2 परीक्षण');
        return response('नमस्ते इंडेक्स');
    }
}
```
