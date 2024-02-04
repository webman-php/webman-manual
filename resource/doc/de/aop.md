# AOP

> Vielen Dank an den Autor von Hyperf

### Installation

- Installieren Sie die AOP-Integration

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### Hinzufügen von AOP-spezifischen Konfigurationen

Es ist erforderlich, dass wir im Verzeichnis "config" die Datei "config.php" mit den folgenden Konfigurationen hinzufügen:

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
        // Fügen Sie hier die entsprechenden Aspekte hinzu
        app\aspect\DebugAspect::class,
    ]
];

```

### Konfiguration der Einstiegsdatei start.php

> Den Initialisierungsschritt platzieren wir unter "timezone". Der folgende Code wird ausgelassen.

```
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// Initialisierung
ClassLoader::init();
```

### Test

Legen wir zuerst die zu bearbeitende Klasse an:

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

Als nächstes fügen wir den entsprechenden `DebugAspect` hinzu:

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

Dann bearbeiten wir den Controller `app/controller/IndexController.php`:

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

Als nächstes konfigurieren wir die Route:

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

Zuletzt starten wir den Service und führen einen Test durch.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
