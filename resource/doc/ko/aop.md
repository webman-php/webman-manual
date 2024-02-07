# AOP

> Hyperf 작성자에게 감사드립니다.

### 설치

- aop-integration 설치

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### AOP 관련 구성 추가

`config` 디렉토리에 `config.php` 구성을 추가해야 합니다.

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
        // 여기에 해당하는 Aspect를 작성합니다.
        app\aspect\DebugAspect::class,
    ]
];

```

### 진입 파일 start.php 구성

> 우리는 초기화 방법을 timezone 아래에 놓을 것입니다. 아래는 다른 코드를 생략합니다.

```php
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// 초기화
ClassLoader::init();
```

### 테스트

먼저 적용할 클래스를 작성해 봅시다.

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

그런 다음 해당하는 `DebugAspect`를 추가합니다.

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

이제 컨트롤러인 `app/controller/IndexController.php`를 편집합니다.

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

그러고 나서 라우팅을 구성합니다.

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

마지막으로 서비스를 시작하고 테스트합니다.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
