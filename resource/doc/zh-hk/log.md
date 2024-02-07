# 日誌
webman使用 [monolog/monolog](https://github.com/Seldaek/monolog) 處理日誌。

## 使用
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('log test');
        return response('hello index');
    }
}
```

## 提供的方法
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
等價於
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

## 配置
```php
return [
    // 默認日誌通道
    'default' => [
        // 處理默認通道的handler，可以設置多個
        'handlers' => [
            [   
                // handler類的名字
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // handler類的構造函數參數
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // 格式相關
                'formatter' => [
                    // 格式化處理類的名字
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // 格式化處理類的構造函數參數
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## 多通道
monolog支持多通道，默认使用`default`通道。如果想增加一個`log2`通道，配置類似如下：
```php
return [
    // 默認日誌通道
    'default' => [
        // 處理默認通道的handler，可以設置多個
        'handlers' => [
            [   
                // handler類的名字
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // handler類的構造函數參數
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // 格式相關
                'formatter' => [
                    // 格式化處理類的名字
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // 格式化處理類的構造函數參數
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // log2通道
    'log2' => [
        // 處理默認通道的handler，可以設置多個
        'handlers' => [
            [   
                // handler類的名字
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // handler類的構造函數參數
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // 格式相關
                'formatter' => [
                    // 格式化處理類的名字
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // 格式化處理類的構造函數參數
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

使用`log2`通道時用法如下：
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
        $log->info('log2 test');
        return response('hello index');
    }
}
```
