# பதிவு
webman புள்ளி பணியாளர், உள்ளிட்ட உதவியினால் monolog/monolog ஐ அனுப்புகின்றது.

## பயன்பாடு
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('பதிவு சோதனை');
        return response('வணக்கம் புள்ளி');
    }
}
```

## வழங்கும் முறைகள்
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
அதே மாதிரி
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

## உருவாக்கல்
```php
return [
    // இயல்புநிலை புள்ளி சாதனம்
    'default' => [
        // இயல்புநிலை புள்ளி சாதனத்தை செயல்படுத்தும் அனுபாத்திகள், பல கையெழுதலாம்
        'handlers' => [
            [   
                // அனுபாத்தித் தொகுப்பனின் பெயர்
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // அனுபாத்தித் தொகுப்பனின் உருவரலாம்
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // வடிவமைப்பு தொகுப்புப் பகுக்கத்தைப் பற்றி
                'formatter' => [
                    // வடிவமைப்பு செயல் பகுதியாக்கலின் பெயர்
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // வடிவமைப்பு செயல் பகுதியாக்கலின் உருவரலாம்
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## பல புள்ளி
monolog பல புள்ளி ஆதரிக்கின்றது, இயல்புநிலையாக `default` புள்ளியைப் பயன்படுத்தி உருமாறி `log2` ஒரு புள்ளியை சேர்த்து உள்ளிடுக. முன்பே ஒரு புள்ளியைச் சேர்த்து உள்ளிட்டால், பிரகாரம் உள்ளிட்டது முரண்டு புள்ளி ஆகும்:
```php
return [
    // இயல்புநிலை புள்ளி சாதனம்
    'default' => [
        // இயல்புநிலை புள்ளி சாதனத்தை செயல்படுத்தும் அனுபாத்திகள், பல கையெழுதலாம்
        'handlers' => [
            [   
                // அனுபாத்தித் தொகுப்பனின் பெயர்
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // அனுபாத்தித் தொகுப்பனின் உருவரலாம்
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // வடிவமைப்பு தொகுப்புப் பகுக்கத்தைப் பற்றி
                'formatter' => [
                    // வடிவமைப்பு செயல் பகுதியாக்கலின் பெயர்
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // வடிவமைப்பு செயல் பகுதியாக்கலின் உருவரலாம்
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // log2 புள்ளி
    'log2' => [
        // இயல்புநிலை புள்ளி சாதனத்தை செயல்படுத்தும் அனுபாத்திகள், பல கையெழுதலாம்
        'handlers' => [
            [   
                // அனுபாத்தித் தொகுப்பனின் பெயர்
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // அனுபாத்தித் தொகுப்பனின் உருவரலாம்
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // வடிவமைப்பு தொகுப்புப் பகுக்கத்தைப் பற்றி
                'formatter' => [
                    // வடிவமைப்பு செயல் பகுதியாக்கலின் பெயர்
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // வடிவமைப்பு செயல் பகுதியாக்கலின் உருவரலாம்
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

`log2` புள்ளி பயன்படும்போது பயன்படுத்துதல் வழி:
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
        $log->info('log2 சோதனை');
        return response('வணக்கம் புள்ளி');
    }
}
```
