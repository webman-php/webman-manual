# AOP

> نشكر مؤلف Hyperf لتقديمه

### التثبيت

- تثبيت دمج aop

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### إضافة تكوين AOP ذات الصلة

نحتاج إلى إضافة تكوين `config.php` في الدليل `config`

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
        // يتم كتابة الناحية المناسبة هنا
        app\aspect\DebugAspect::class,
    ]
];

```

### ملف الإدخال start.php

> سنضع الطريقة المبدئية تحت timezone ، وسنحافظ على الشيفرة الأخرى

```
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// تهيئة
ClassLoader::init();
```

### اختبار

أولاً دعونا نكتب الفئة المستهدفة

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

ثم أضف الناحية DebugAspect المقابلة

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

ثم قم بتحرير تحكم المدخل `app/controller/IndexController.php`

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

ثم قم بتكوين الطرق

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

أخيرًا، قم بتشغيل الخدمة واختبرها.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
