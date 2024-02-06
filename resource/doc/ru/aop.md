# AOP

> Благодарим автора Hyperf за представленное обновление

### Установка

- Установите aop-integration

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### Добавление AOP-связанной конфигурации

Нам необходимо добавить конфигурацию `config.php` в каталог `config`.

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
        // Здесь указываем соответствующий Aspect
        app\aspect\DebugAspect::class,
    ]
];

```

### Настройка стартового файла start.php

> Мы поместим метод инициализации под раздел timezone, далее пропустим другой код

```php
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// Инициализация
ClassLoader::init();
```

### Тестирование

Сначала давайте напишем класс для среза

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

Затем добавим соответствующий `DebugAspect`

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

Затем отредактируем контроллер `app/controller/IndexController.php`

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

Затем настроим маршрут

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

И, наконец, запустим сервер и проведем тестирование.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
