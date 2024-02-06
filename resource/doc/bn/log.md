# লগ
webman লগ প্রসেস করার জন্য [monolog/monolog](https://github.com/Seldaek/monolog) ব্যবহার করে।

## ব্যবহার
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('লগ পরীক্ষা');
        return response('ওহে index');
    }
}
```

## উপলব্ধ পদক্ষেপ
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
সমতুল্য
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

## কনফিগারেশন
```php
return [
    // ডিফল্ট লগ চ্যানেল
    'default' => [
        // ডিফল্ট চ্যানেলের হ্যান্ডলার প্রসেসিং, এটি একাধিক সেট করা যাবে
        'handlers' => [
            [   
                // হ্যান্ডলার ক্লাসের নাম
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // হ্যান্ডলার ক্লাসের কন্সট্রাক্টর অ্যার্গুমেন্ট
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // ফরম্যাটার সম্পর্কে
                'formatter' => [
                    // ফরম্যাটার প্রসেসিং ক্লাসের নাম
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // ফরম্যাটার প্রসেসিং ক্লাসের কন্সট্রাক্টর অ্যার্গুমেন্ট
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## বহুল চ্যানেল
মনোলগ বহুল চ্যানেল সমর্থন করে, ডিফল্টভাবে `ডিফল্ট` চ্যানেল ব্যবহার হয়। যদি `লগ2` চ্যানেল যোগ করতে চান, তাহলে নিম্নলিখিতের মত কনফিগার করুন:
```php
return [
    // ডিফল্ট লগ চ্যানেল
    'default' => [
        // ডিফল্ট চ্যানেলের হ্যান্ডলার প্রসেসিং, এটি একাধিক সেট করা যাবে
        'handlers' => [
            [   
                // হ্যান্ডলার ক্লাসের নাম
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // হ্যান্ডলার ক্লাসের কন্সট্রাক্টর অ্যার্গুমেন্ট
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // ফরম্যাটার সম্পর্কে
                'formatter' => [
                    // ফরম্যাটার প্রসেসিং ক্লাসের নাম
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // ফরম্যাটার প্রসেসিং ক্লাসের কন্সট্রাক্টর অ্যার্গুমেন্ট
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // লগ2 চ্যানেল
    'log2' => [
        // ডিফল্ট চ্যানেলের হ্যান্ডলার প্রসেসিং, এটি একাধিক সেট করা যাবে
        'handlers' => [
            [   
                // হ্যান্ডলার ক্লাসের নাম
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // হ্যান্ডলার ক্লাসের কন্সট্রাক্টর অ্যার্গুমেন্ট
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // ফরম্যাটার সম্পর্কে
                'formatter' => [
                    // ফরম্যাটার প্রসেসিং ক্লাসের নাম
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // ফরম্যাটার প্রসেসিং ক্লাসের কন্সট্রাক্টর অ্যার্গুমেন্ট
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

লগ2 চ্যানেল ব্যবহারের জন্য ব্যবহার পদ্ধতি:
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
        $log->info('লগ2 পরীক্ষা');
        return response('ওহে index');
    }
}
```
