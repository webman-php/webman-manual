# AOP

> Agradecimentos ao autor do Hyperf pela contribuição

### Instalação

- Instale a integração AOP

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### Adicionar configuração relacionada ao AOP

Precisamos adicionar a configuração `config.php` no diretório `config`.

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
        // Aqui você deve adicionar o Aspect correspondente
        app\aspect\DebugAspect::class,
    ]
];

```

### Arquivo de entrada de configuração start.php

> Colocaremos o método de inicialização abaixo de `timezone`, o restante do código será omitido a seguir

```php
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// Inicialização
ClassLoader::init();
```

### Teste

Primeiro, vamos escrever a classe de corte a ser testada

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

Em seguida, adicionaremos o `DebugAspect` correspondente

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

A seguir, edite o controlador `app/controller/IndexController.php`

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

Agora, configure a rota

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

Por fim, inicie o serviço e teste.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
