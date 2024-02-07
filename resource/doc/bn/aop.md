# AOP

> হাইপারফ রাইটার এর সাবমিট এর জন্য ধন্যবাদ।

### ইনস্টলেশন

- aop-integration ইনস্টল করুন

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### AOP সংক্রান্ত কনফিগারেশন যোগ করুন

আমাদের একটি `config.php` কনফিগারেশন ফাইল যোগ করতে হবে কনফিগ ফোল্ডারে

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
        // এখানে ম্যাচিং এসপেক্ট এড করুন
        app\aspect\DebugAspect::class,
    ]
];

```

### এন্ট্রি ফাইল start.php কনফিগার করুন

> আমরা তারিখ সেট করার পরিবেশনা কোডটি timezone নীচে রেখেছি, অন্য কোডগুলি জাতীয় থাকলে এগুলিকে বাদ দেয়া হবে 

```php
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// আইনিটিয়ালাইজেশন
ClassLoader::init();
```

### পরীক্ষা

প্রথমে আমাদের কাটা ক্লাস লিখি

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

পরবর্তীতে সাথে যুক্ত করুন `DebugAspect`

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

তারপরে নিয়ে অনুপাত আপডেট করুন `app/controller/IndexController.php`

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

এরপর রাউট কনফিগার করুন

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

শেষ অবস্থিতি এবং পরীক্ষা করুন।

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
