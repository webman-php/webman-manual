# AOP

> হাইপারফ লেখকের প্রদানের জন্য ধন্যবাদ

### ইনস্টলেশন

- aop-integration ইনস্টল করুন

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### AOP সম্পর্কিত কনফিগারেশন যোগ করুন

আমাদের প্রয়োজন আছে `config` ডিরেক্টরিতে, `config.php` কনফিগুরেশন যোগ করতে

```php
<?php

use Hyperf\Di\Annotation\AspectCollector;

return [
    'annotations' => [
        'scan' => [
            'paths' => [
                BASE_PATH . '/app',
            ],
            'ignore_annotations' => [
                'mixin',
            ],
            'class_map' => [
            ],
            'collectors' => [
                AspectCollector::class
            ],
        ],
    ],
    'aspects' => [
        // এখানে মানসনুযোগী অ্যাসপেক্ট লিখুন
        app\aspect\DebugAspect::class,
    ]
];

```

### শুরু ফাইল start.php কনফিগার

> আমরা আপনাকে সময় অনুপ্রবেশে প্রাথমিক করা দরকার, নিম্নলিখিত অন্য কোডগুলি বাদ দিয়ে

```
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// ইনিশিয়ালাইজ
ClassLoader::init();
```

### পরীক্ষা

প্রথমে আসুন আমরা প্রতীক্ষিত করা ক্লাস লিখে ফেলি

```php
<?php
namespace app\service;

class UserService
{
    public function first(): array
    {
        return ['id' => 1];
    }
}
```

পরে সাথে তারায় নতুন `DebugAspect`

```php
<?php
namespace app\aspect;

use app\service\UserService;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;

class DebugAspect extends AbstractAspect
{
    public $classes = [
        UserService::class . '::first',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        var_dump(11);
        return $proceedingJoinPoint->process();
    }
}
```

পরবর্তীতে নির্দিষ্ট করুন নিয়ন্ত্রক 'app/controller/IndexController.php'

```php
<?php
namespace app\controller;

use app\service\UserService;
use support\Request;

class IndexController
{
    public function json(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok', 'data' => (new UserService())->first()]);
    }
}
```

তারপরে রাউট কনফিগার করুন

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

শেষে সার্ভার চালু করুন এবং পরীক্ষা করুন।

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
