# ログ
webmanは、ログ処理に [monolog/monolog](https://github.com/Seldaek/monolog) を使用しています。

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
以下は以下と同等です。
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
        // デフォルトチャンネルを処理するハンドラー、複数設定可能
        'handlers' => [
            [   
                // ハンドラークラス名
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // ハンドラークラスのコンストラクター引数
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // フォーマットに関連する設定
                'formatter' => [
                    // フォーマット処理クラス名
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // フォーマット処理クラスのコンストラクター引数
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## マルチチャンネル
monologは複数のチャンネルをサポートしており、デフォルトでは `default` チャンネルが使用されます。`log2` チャンネルを追加したい場合は、以下のように設定します。
```php
return [
    // デフォルトのログチャンネル
    'default' => [
        // デフォルトチャンネルを処理するハンドラー、複数設定可能
        'handlers' => [
            [   
                // ハンドラークラス名
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // ハンドラークラスのコンストラクター引数
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // フォーマットに関連する設定
                'formatter' => [
                    // フォーマット処理クラス名
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // フォーマット処理クラスのコンストラクター引数
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // log2 チャンネル
    'log2' => [
        // デフォルトチャンネルを処理するハンドラー、複数設定可能
        'handlers' => [
            [   
                // ハンドラークラス名
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // ハンドラークラスのコンストラクター引数
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // フォーマットに関連する設定
                'formatter' => [
                    // フォーマット処理クラス名
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // フォーマット処理クラスのコンストラクター引数
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```
`log2` チャンネルを使用する場合の方法は以下の通りです。
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
        $log->info('log2 テスト');
        return response('こんにちは、index');
    }
}
```
