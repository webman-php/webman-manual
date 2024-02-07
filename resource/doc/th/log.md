# บันทึก
Webman ใช้ [monolog/monolog](https://github.com/Seldaek/monolog) เพื่อจัดการกับบันทึกข้อมูล.

## การใช้งาน
```php
<?php
namespace app\controller;

use support\Request;
use support\Log;

class FooController
{
    public function index(Request $request)
    {
        Log::info('ทดสอบบันทึก');
        return response('สวัสดี ดัชนี');
    }
}
```

## เมธอดที่ให้บริการ
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
เทียบเท่ากับ
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

## การกำหนดค่า
```php
return [
    // ช่องเราที่จะใช้บันทึก
    'default' => [
        // แฮนเดอร์ที่ใช้จัดการช่องเราที่จะใช้ สามารถกำหนดหลายตัว
        'handlers' => [
            [   
                // ชื่อคลาสแฮนเดอร์
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // ตัวแปรของคอนสตรัคเตอร์ของคลาสแฮนเดอร์
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // แบบรูปแบบที่เกี่ยวข้อง
                'formatter' => [
                    // ชื่อคลาสตัวจัดการการจัดรูปแบบ
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // ตัวแปรของคอนสตรัคเตอร์ของคลาสตัวจัดการการจัดรูปแบบ
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## หลายช่อง
Monolog รองรับหลายช่อง โดยค่าเริ่มต้นคือการใช้ช่อง `default` หากต้องการเพิ่มช่อง `log2` สามารถกำหนดได้ดังนี้:
```php
return [
    // ช่องเราที่จะใช้บันทึก
    'default' => [
        // แฮนเดอร์ที่ใช้จัดการช่องเราที่จะใช้ สามารถกำหนดหลายตัว
        'handlers' => [
            [   
                // ชื่อคลาสแฮนเดอร์
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // ตัวแปรของคอนสตรัคเตอร์ของคลาสแฮนเดอร์
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // แบบรูปแบบที่เกี่ยวข้อง
                'formatter' => [
                    // ชื่อคลาสตัวจัดการการจัดรูปแบบ
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // ตัวแปรของคอนสตรัคเตอร์ของคลาสตัวจัดการการจัดรูปแบบ
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // ช่อง log2
    'log2' => [
        // แฮนเดอร์ที่ใช้จัดการช่อง log2 สามารถกำหนดหลายตัว
        'handlers' => [
            [   
                // ชื่อคลาสแฮนเดอร์
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // ตัวแปรของคอนสตรัคเตอร์ของคลาสแฮนเดอร์
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // แบบรูปแบบที่เกี่ยวข้อง
                'formatter' => [
                    // ชื่อคลาสตัวจัดการการจัดรูปแบบ
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // ตัวแปรของคอนสตรัคเตอร์ของคลาสตัวจัดการการจัดรูปแบบ
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```



การใช้งานช่อง `log2` สามารถทำได้โดยวิธีดังนี้:
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
        $log->info('ทดสอบ log2');
        return response('สวัสดี ดัชนี');
    }
}
```
