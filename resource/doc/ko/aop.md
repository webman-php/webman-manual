# AOP

> 하이퍼프(Hyperf) 작성자에게 감사의 인사

### 설치

- aop-integration 설치

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### AOP 관련 구성 추가

`config` 디렉토리 아래 `config.php` 구성을 추가해야 합니다.

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
        // 여기에 해당하는 Aspect를 입력합니다.
        app\aspect\DebugAspect::class,
    ]
];

```

### 진입 파일 start.php 설정

> 초기화 메서드를 'timezone' 아래에 넣겠습니다. 다음은 다른 코드를 생략합니다.

```
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// 초기화
ClassLoader::init();
```

### 테스트

먼저 적용 대상 클래스를 작성해 봅시다.

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

그 다음에 해당하는 `DebugAspect`를 추가합니다.

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

이후에 컨트롤러 `app/controller/IndexController.php`를 편집합니다.

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

그런 다음 라우트를 설정합니다.

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

마지막으로 서버를 시작하고 테스트합니다.

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
