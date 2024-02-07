# AOP

> 感謝 Hyerpf 作者的提交

### 安裝

- 安裝 aop-integration

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### 增加 AOP 相關配置

我們需要在 `config` 目錄下，增加 `config.php` 配置

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
        // 這裡寫入對應的 Aspect
        app\aspect\DebugAspect::class,
    ]
];

```

### 配置入口文件 start.php

> 我們將初始化方法，放到 timezone 下方，以下省略其他代碼

```php
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// 初始化
ClassLoader::init();
```

### 測試

首先讓我們編寫待切入類

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

其次新增對應的 `DebugAspect`

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

接下來編輯控制器 `app/controller/IndexController.php`

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

然後配置路由

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

最後啟動服務，並測試。

```shell
php start.php start
curl  http://127.0.0.1:8787/json
```
