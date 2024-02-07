# AOP

> Hyperf ஆசிரியருக்கு நன்றி சொல்லுகிறேன்

### நிறுவுதல்

- aop-integration நிறுவது

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### AOP சர்வதேச கட்டுப்படுத்தல்

நாம் `config` கோப்பின் கீழ் `config.php` கட்டமைப்போட வேண்டும்

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
        // இங்கு பொருநர் Aspect ஐ உள்ளிடவும்
        app\aspect\DebugAspect::class,
    ]
];

```

### எண்.பிக்குப் கோப்பு start.php

> நாம்ககாக பொருத்தல் மெதாடுகளை, timezone கீழ் வைத்துக்கொண்டு, பிற குறியீடு செய்யும்

```php
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// துவக்கம்
ClassLoader::init();
```

### சோதனை

முதலில் நாம் விரும்பும் குளைகளை எழுத வேண்டும்

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

அடுத்து அதிகார DebugAspect ஐ சேர்க்க

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

பின்னர் கட்டுப்படுத்தாளர் app/controller/IndexController.php ஐ தெரிவுசெய்க

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

அப்பால் வழிகளை கட்ட்டுப் பதிவைசெய்யவும்

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

கடைசியாக சேவையை இயக்கவும், மற்றும் சோதனை செய்யவும்.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
