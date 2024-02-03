# ログ
webmanでは、[monolog/monolog](https://github.com/Seldaek/monolog) を使用してログを処理します。

## 使用法
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('ログのテスト');
        return response('こんにちは、index');
    }
}
```

## 提供されるメソッド
```php
Log::log($level, $message, array $context = [])
Log::debug($message, array $context = [])
Log::info($message, array $context = [])
Log::notice($message, array $context = [])
Log::warning($message, array $context = [])
Log::error($message, array $context = [])
Log::critical($message, array $context = [])
Log::alert($message, array $context = [])
Log::emergency($message, array $context = [])
```
同等のもの
```php
$log = Log::channel('default');
$log->log($level, $message, array $context = [])
$log->debug($message, array $context = [])
$log->info($message, array $context = [])
$log->notice($message, array $context = [])
$log->warning($message, array $context = [])
$log->error($message, array $context = [])
$log->critical($message, array $context = [])
$log->alert($message, array $context = [])
$log->emergency($message, array $context = [])
```

## 設定
```php
return [
    // デフォルトのログチャネル
    'default' => [
        // デフォルトのチャネルを処理するハンドラー、複数を設定できる
        'handlers' => [
            [   
                // ハンドラークラス名
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // ハンドラークラスのコンストラクタ引数
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // フォーマットに関するもの
                'formatter' => [
                    // フォーマット処理クラス名
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // フォーマット処理クラスのコンストラクタ引数
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## 複数のチャネル
monologは複数のチャネルをサポートしており、デフォルトでは`default`チャネルが使用されます。`log2`チャネルを追加したい場合、以下のように設定します：
```php
return [
    // デフォルトのログチャネル
    'default' => [
        // デフォルトのチャネルを処理するハンドラー、複数を設定できる
        'handlers' => [
            [   
                // ハンドラークラス名
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // ハンドラークラスのコンストラクタ引数
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // フォーマットに関するもの
                'formatter' => [
                    // フォーマット処理クラス名
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // フォーマット処理クラスのコンストラクタ引数
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // log2チャネル
    'log2' => [
        // log2チャネルを処理するハンドラー、複数を設定できる
        'handlers' => [
            [   
                // ハンドラークラス名
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // ハンドラークラスのコンストラクタ引数
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // フォーマットに関するもの
                'formatter' => [
                    // フォーマット処理クラス名
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // フォーマット処理クラスのコンストラクタ引数
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

`log2`チャネルを使用する場合の方法は以下のとおりです：
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        $log = Log::channel('log2');
        $log->info('log2のテスト');
        return response('こんにちは、index');
    }
}
```
