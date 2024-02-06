# AOP

> धन्यवाद Hyperf लेखक की सबमिट करने के लिए

### स्थापना

- aop-इंटीग्रेशन स्थापित करें

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### AOP संबंधित कॉन्फ़िगरेशन जोड़ें

हमें `config` निर्देशिका के अंदर, `config.php` कॉन्फ़िगरेशन जोड़ने की जरूरत है

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
        // यहां अनुरुप Aspect लिखें
        app\aspect\DebugAspect::class,
    ]
];

```

### कॉन्फ़िग प्रवेश फ़ाइल start.php

> हम वक्तक्षेत्र के नीचे आप्रकार की शुरुआती विधि डालेंगे, नीचे बाकी कोड नहीं दिखाया गया है

```
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// प्रारंभ करें
ClassLoader::init();
```

### परीक्षण

सबसे पहले हमें काटने योग्य वर्ग लिखने के लिए प्रोत्साहित करें

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

उसके बाद संबंधित `DebugAspect` जोड़ें

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

फिर कंट्रोलर `app/controller/IndexController.php` को संपादित करें

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

फिर मार्ग समर्थन को कॉन्फ़िगर करें

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

अंत में सेवा शुरू करें और परीक्षण करें।

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
