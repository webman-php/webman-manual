# AOP

> Hyperf ஆசிரியரை நன்றி

### நிறுவுக

- aop-integration ஐ நிறுவுக

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### AOP குழு மேலும் அமைப்புகளைச் சேர்க்கவும்

நாங்கள் `config` கோப்புறையில் `config.php` அமைப்பை சேர்க்க வேண்டும்

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
        // இங்கே பொருநர் அமைப்பை உள்ளிடவும்
        app\aspect\DebugAspect::class,
    ]
];

```

### துணைக்கோப்பு start.php ஐ அமைப்படுத்துக

> ஒரு ஸ்டார்ட் இனிஷலைஸேஷனை, மாநிலம் அடிக்கும் மாதிரி நாம் சேர்த்துக்கொண்டேவோம், அனுப்பித்து முடிவுச் செய்ப்பதை மீண்டும் உருவாக்கவும்

```
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// ப்ராரம்பிக்கவும்
ClassLoader::init();
```

### சோதனை

முதலில் நாங்கள் வெளிக்காட்டாக்க பொருள் எழுதுவதை எழுதுவோம்

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

பிறகு அதிகபட்சமாக "DebugAspect" ஐ சேர்த்துவைக்கின்றோம்

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

அடுத்து, கட்டுப்படுத்துள்ளபடி "app/controller/IndexController.php" ஐ திருத்தவோம

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

அப்பால் வழிச்சாரவரிசையை அமைக்கவும்

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

கடைசி அலுவலகத்தை துவக்கி, சோதனை செய்கின்றேன் மற்றும் சோதனை செய்கின்றது.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
