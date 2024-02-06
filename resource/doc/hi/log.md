# लॉग
webman [monolog/monolog](https://github.com/Seldaek/monolog) का उपयोग करता है ताकी लॉग को निर्धारित किया जा सके।

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

## प्रदान की गई विधियाँ
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
समान
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

## समाकृति
```php
return [
    // डिफ़ॉल्ट लॉग पाइप
    'default' => [
        // डिफ़ॉल्ट पाइप का हैंडलर, एक से अधिक सेट किया जा सकता है
        'handlers' => [
            [   
                // हैंडलर की क्लास का नाम
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // हैंडलर की कंस्ट्रक्टर पैरामीटर
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // स्वरूप संबंधित
                'formatter' => [
                    // स्वरूपण हैंडलर की क्लास का नाम
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // स्वरूपण हैंडलर की कंस्ट्रक्टर पैरामीटर
                    'constructor' => [null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## बहु-पाइप
monolog बहु-पाइप का समर्थन करता है, डिफ़ॉल्ट रूप से `default` पाइप का उपयोग करता है। यदि एक और `log2` पाइप जोड़ना चाहते हैं, तो निम्नलिखित तरह से कॉन्फ़िगर करें:
```php
return [
    // डिफ़ॉल्ट लॉग पाइप
    'default' => [
        // डिफ़ॉल्ट पाइप का हैंडलर, एक से अधिक सेट किया जा सकता है
        'handlers' => [
            [   
                // हैंडलर की क्लास का नाम
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // हैंडलर की कंस्ट्रक्टर पैरामीटर
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // स्वरूप संबंधित
                'formatter' => [
                    // स्वरूपण हैंडलर की क्लास का नाम
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // स्वरूपण हैंडलर की कंस्ट्रक्टर पैरामीटर
                    'constructor' => [null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // log2 पाइप
    'log2' => [
        // डिफ़ॉल्ट पाइप का हैंडलर, एक से अधिक सेट किया जा सकता है
        'handlers' => [
            [   
                // हैंडलर की क्लास का नाम
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // हैंडलर की कंस्ट्रक्टर पैरामीटर
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // स्वरूप संबंधित
                'formatter' => [
                    // स्वरूपण हैंडलर की क्लास का नाम
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // स्वरूपण हैंडलर की कंस्ट्रक्टर पैरामीटर
                    'constructor' => [null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

`log2` पाइप का उपयोग करते समय उपयोग की जाने वाली विधि का उपयोग का द्योतक निम्नलिखित है:
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
