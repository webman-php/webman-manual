# AOP

> Hyperfの作者に感謝します

### インストール

- aop-integrationをインストールします

```shell
composer require "hyperf/aop-integration: ^1.1"
```

### AOP関連の設定を追加

`config`ディレクトリに`config.php`設定を追加する必要があります

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
        // ここに対応するAspectを記述します
        app\aspect\DebugAspect::class,
    ]
];

```

### エントリーポイントファイルstart.phpの構成

> 初期化メソッドを`timezone`の下に配置し、以下は他のコードを省略します

```
use Hyperf\AopIntegration\ClassLoader;

if ($timezone = config('app.default_timezone')) {
    date_default_timezone_set($timezone);
}

// 初期化
ClassLoader::init();
```

### テスト

まず、インターセプト対象のクラスを作成します

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

次に、対応する`DebugAspect`を追加します

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

次に、コントローラー`app/controller/IndexController.php`を編集します

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

次に、ルートを構成します

```php
<?php
use Webman\Route;

Route::any('/json', [app\controller\IndexController::class, 'json']);
```

最後にサーバーを起動してテストします。

```shell
php start.php start
curl http://127.0.0.1:8787/json
```
