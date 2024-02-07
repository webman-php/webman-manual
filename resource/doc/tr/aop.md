# AOP

> Hyperf yazarına çok teşekkürler

### Kurulum

- aop-integration kurulumu

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### AOP ile ilgili yapılandırma ekleme

`config` dizini altında `config.php` konfigürasyonunu eklememiz gerekiyor

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
        // Buraya ilgili Aspect yazın
        app\aspect\DebugAspect::class,
    ]
];

```

### Config.php giriş dosyasını yapılandırma

> Başlatma yöntemini zaman diliminin altına yerleştireceğiz, diğer kodlar burada atlanmıştır.

```php
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// İlklenme
ClassLoader::init();
```

### Test

Öncelikle kesilmesi gereken sınıfı yazalım

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

Daha sonra ilgili `DebugAspect` dosyasını ekleyin

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

Daha sonra kontrolcüyü düzenleyin `app/controller/IndexController.php`

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

Sonra rotayı yapılandırın

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

Son olarak sunucuyu başlatın ve test edin.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
