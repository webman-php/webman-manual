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
        Log::info('kiểm tra nhật ký');
        return response('xin chào index');
    }
}
```

## Phương pháp cung cấp
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
$log->warning($message, array $context = [])
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
        // Xử lý của kênh mặc định, có thể thiết lập nhiều
        'handlers' => [
            [   
                // Tên lớp xử lý
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Tham số hàm tạo của lớp xử lý
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Định dạng liên quan
                'formatter' => [
                    // Tên lớp định dạng
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Tham số hàm tạo của lớp định dạng
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## Nhiều kênh
monolog hỗ trợ nhiều kênh, mặc định sử dụng kênh `default`. Nếu muốn thêm một kênh `log2`, cấu hình sẽ tương tự như sau:
```php
return [
    // Kênh nhật ký mặc định
    'default' => [
        // Xử lý của kênh mặc định, có thể thiết lập nhiều
        'handlers' => [
            [   
                // Tên lớp xử lý
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Tham số hàm tạo của lớp xử lý
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // Định dạng liên quan
                'formatter' => [
                    // Tên lớp định dạng
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Tham số hàm tạo của lớp định dạng
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // Kênh log2
    'log2' => [
        // Xử lý của kênh mặc định, có thể thiết lập nhiều
        'handlers' => [
            [   
                // Tên lớp xử lý
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // Tham số hàm tạo của lớp xử lý
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // Định dạng liên quan
                'formatter' => [
                    // Tên lớp định dạng
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // Tham số hàm tạo của lớp định dạng
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
        $log->info('kiểm tra log2');
        return response('xin chào index');
    }
}
```
