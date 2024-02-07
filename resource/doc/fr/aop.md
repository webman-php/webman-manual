# AOP

> Merci pour la soumission de l'auteur de Hyperf

### Installation

- Installer aop-integration

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### Ajouter une configuration concernant AOP

Nous devons ajouter une configuration "config.php" dans le répertoire `config`

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
        // Écrire ici l'Aspect correspondant
        app\aspect\DebugAspect::class,
    ]
];

```

### Fichier d'entrée de configuration start.php

> Nous allons placer la méthode d'initialisation sous le fuseau horaire, le code suivant est omis

```php
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// Initialiser
ClassLoader::init();
```

### Test

Tout d'abord, écrivons la classe à intercepter

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

Ensuite, ajoutez l'aspect `DebugAspect` correspondant

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

Ensuite, modifiez le contrôleur `app/controller/IndexController.php`

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

Ensuite, configurez la route

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

Enfin, démarrez le service et testez.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
