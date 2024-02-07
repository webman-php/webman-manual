# Nhật ký
webman sử dụng [monolog/monolog](https://github.com/Seldaek/monolog) để xử lý nhật ký.

## Sử dụng
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

## Các phương thức được cung cấp
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
Tương đương với
```php
$log = Log::channel('default');
$log->log($level, $message, array $context = [])
$log->debug($message, array $context = [])
$log->info($message, array $context = [])
$log->notice($message, array $context = [])
$log->warning($message, array $conte
xt = [])
$log->error($message, array $context = [])
$log->critical($message, array $context = [])
$log->alert($message, array $context = [])
$log->emergency($message, array $context = [])
```

## Cấu hình
```php
return [
    // Kênh nhật ký mặc định
    'default' => [
        // Bộ xử lý kênh mặc định, có thể thiết lập nhiều
        'handlers' => [
            [   
                // Tên lớp xử lý
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Tham số constructor của lớp xử lý
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Liên quan đến định dạng
                'formatter' => [
                    // Tên lớp xử lý định dạng
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Tham số constructor của lớp xử lý định dạng
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
               ],
            ]
        ],
    ],
];
```

## Đa kênh
monolog hỗ trợ nhiều kênh, mặc định sử dụng kênh `default`. Nếu bạn muốn thêm một kênh `log2`, cấu hình tương tự như sau:
```php
return [
    // Kênh nhật ký mặc định
    'default' => [
        // Bộ xử lý kênh mặc định, có thể thiết lập nhiều
        'handlers' => [
            [   
                // Tên lớp xử lý
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Tham số constructor của lớp xử lý
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Liên quan đến định dạng
                'formatter' => [
                    // Tên lớp xử lý định dạng
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Tham số constructor của lớp xử lý định dạng
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // Kênh log2
    'log2' => [
        // Bộ xử lý kênh mặc định, có thể thiết lập nhiều
        'handlers' => [
            [   
                // Tên lớp xử lý
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Tham số constructor của lớp xử lý
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // Liên quan đến định dạng
                'formatter' => [
                    // Tên lớp xử lý định dạng
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Tham số constructor của lớp xử lý định dạng
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

Sử dụng kênh `log2` như sau:
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
