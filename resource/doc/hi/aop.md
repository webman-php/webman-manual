# AOP

> धन्यवाद Hyperf लेखक की सबमिट्टिंग के लिए

### स्थापना

- aop-integration इंस्टॉल करें

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### AOP संबंधित कॉन्फ़िगरेशन जोड़ें

हमें `config` फ़ोल्डर के अंदर, `config.php` कॉन्फ़िगरेशन जोड़ने की आवश्यकता है

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
        // यहां सम्बंधित Aspect लिखें
        app\aspect\DebugAspect::class,
    ]
];

```

### कॉन्फ़िगरेशन एंट्री फ़ाइल start.php

> हम इनिशियलाइज़ेशन मेथड को टाइमज़ोन के नीचे रखेंगे, निम्नलिखित कोड को छोड़ते हैं।

```php
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// इनिशियलाइज़ेशन
ClassLoader::init();
```

### टेस्ट

सबसे पहले हमें इंजेक्शन क्लास लिखने के लिए लिखना होगा

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

उसके बाद विशेष DebugAspect जोड़ें

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

और फिर रूटर कॉन्फ़िगरेशन करें

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

अंत में सेवा शुरू करें और टेस्ट करें।

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
