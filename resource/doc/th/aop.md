# AOP

> ขอบคุณสำหรับการส่งข้อมูลจากผู้เขียน Hyperf

### การติดตั้ง

- ติดตั้ง aop-integration

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### เพิ่มการตั้งค่า AOP

เราจำเป็นต้องเพิ่มการตั้งค่า `config.php` ในไดเร็กทอรี `config`

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
        // ที่นี่เราเขียน Aspect ที่เกี่ยวข้อง
        app\aspect\DebugAspect::class,
    ]
];

```

### การตั้งค่าไฟล์อันเข้าสู่การทำงาน start.php

> เราจะให้วิธีการเปิดเริ่มต้นไว้ในส่วนของ timezone ดังต่อไปนี้จะข้ามโค้ดอื่น ๆ

```
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// การเริ่มต้น
ClassLoader::init();
```

### การทดสอบ

ก่อนอื่นเรามาเขียนคลาสที่รอประมวลผล

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

ต่อมาให้เพิ่ม `DebugAspect` ที่สอดคล้อง

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

ต่อมาแก้ไขคอนโทรลเลอร์ `app/controller/IndexController.php`

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

จากนั้นก็กำหนดเส้นทาง

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

สุดท้ายก็เริ่มบริการและทดสอบ

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
