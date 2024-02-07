# AOP

> ขอบคุณคุณ Hyperf ที่ส่งมา

### การติดตั้ง

- ติดตั้ง aop-integration

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### เพิ่มการกำหนดค่า AOP ที่เกี่ยวข้อง

เราต้องเพิ่มการกำหนดค่า `config.php` ในไดเรกทอรี `config`

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
        // ที่นี่เขียน Aspect ที่เกี่ยวข้อง
        app\aspect\DebugAspect::class,
    ]
];

```

### การกำหนดค่าในไฟล์ start.php

> เราจะแทนที่วิธีเริ่มต้นไว้ที่ timezone 以下นี้ข้ามการเขียนโค้ดอื่น ๆ

```php
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// การเริ่มต้น
ClassLoader::init();
```

### การทดสอบ

ก่อนอื่น ให้เราเขียนคลาสที่ต้องการตัดเข้า

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

จากนั้นเพิ่ม `DebugAspect` ที่เกี่ยวข้อง

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

แล้วแก้ไขคอนโทรลเลอร์ `app/controller/IndexController.php`

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

จากรูที่นั้นก็กำหนดเส้นทาง

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

สุดท้าย ทำการเริ่มบริการและทดสอบ

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
