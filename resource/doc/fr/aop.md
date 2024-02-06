# AOP

> Merci à l'auteur de Hyperf pour sa contribution

### Installation

- Installer l'intégration aop

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### Ajouter la configuration AOP

Nous devons ajouter la configuration `config.php` dans le répertoire `config`

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

### Configuration du fichier d'entrée start.php

> Nous placerons la méthode d'initialisation en dessous de `timezone`, le reste du code est omis ci-dessous.

```
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// Initialisation
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

Ensuite, ajoutons l'`DebugAspect` correspondant

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

Ensuite, nous éditons le contrôleur `app/controller/IndexController.php`

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

Ensuite, configurons la route

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

Enfin, démarrons le service et testons.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
