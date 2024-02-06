# السجلات
يستخدم webman [monolog/monolog](https://github.com/Seldaek/monolog) لمعالجة السجلات.

## الاستخدام
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('اختبار السجل');
        return response('مرحبًا بكم في الفهرس');
    }
}
```

## الطرق المتاحة
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
ما يعادل
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

## التكوين
```php
return [
    // القناة الافتراضية للسجلات
    'default' => [
        // المعالج الذي يعالج القناة الافتراضية، يمكن تعيين عدة معالجين
        'handlers' => [
            [   
                // اسم فئة المعالج
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // معاملات بناء فئة المعالج
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // صيغ ذات الصلة
                'formatter' => [
                    // اسم فئة معالج التنسيق
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // معاملات بناء فئة معالج التنسيق
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## عدة قنوات
يدعم monolog قنوات متعددة، وهو يستخدم القناة `default` افتراضيًا. إذا كنت ترغب في إضافة قناة `log2`، يكون التكوين كما يلي:
```php
return [
    // القناة الافتراضية للسجلات
    'default' => [
        // المعالج الذي يعالج القناة الافتراضية، يمكن تعيين عدة معالجين
        'handlers' => [
            [   
                // اسم فئة المعالج
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // معاملات بناء فئة المعالج
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // صيغ ذات الصلة
                'formatter' => [
                    // اسم فئة معالج التنسيق
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // معاملات بناء فئة معالج التنسيق
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // قناة log2
    'log2' => [
        // المعالج الذي يعالج القناة الافتراضية، يمكن تعيين عدة معالجين
        'handlers' => [
            [   
                // اسم فئة المعالج
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // معاملات بناء فئة المعالج
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // صيغ ذات الصلة
                'formatter' => [
                    // اسم فئة معالج التنسيق
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // معاملات بناء فئة معالج التنسيق
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

عند استخدام القناة `log2`، يمكن استخدام الطريقة التالية:
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
        $log->info('اختبار log2');
        return response('مرحبًا بكم في الفهرس');
    }
}
```
