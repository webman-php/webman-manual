# AOP

> Hyperf yazarına yapılan katkılar için teşekkürler

### Kurulum

- aop-integration kurulumu

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### AOP ile ilgili yapılandırmayı ekleyin

`config` dizini altında `config.php` yapılandırma eklememiz gerekiyor

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
        // Bu kısma ilgili Aspect yazılmalıdır
        app\aspect\DebugAspect::class,
    ]
];

```

### Giriş dosyası start.php yapılandırması

> Başlatma yöntemini `timezone`'un altına yerleştireceğiz, diğer kodları atlıyoruz.

```
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// Başlatma
ClassLoader::init();
```

### Test

İlk olarak kesilecek sınıfı yazalım

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

Sonra uygun `DebugAspect` ekleyin

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

Daha sonra `app/controller/IndexController.php` kontrolcüsünü düzenleyin

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

Ardından yönlendirmeyi yapılandırın

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

Son olarak servisi başlatın ve test edin.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
