# บันทึก
webman ใช้ [monolog/monolog](https://github.com/Seldaek/monolog) เพื่อจัดการบันทึกข้อมูล

## การใช้
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
        return response('สวัสดี index');
    }
}
```

## เมธอดที่ใช้งาน
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
    // ช่องบันทึกริยาสำหรับยูสตัวเดียว
    'default' => [
        // ตัวจัดการช่องค่าเริ่มต้น สามารถกำหนดได้หลายตัว
        'handlers' => [
            [   
                // ชื่อคลาสของตัวจัดการ
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // พารามิเตอร์ที่ใช้ในการสร้างคลาสของตัวจัดการ
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // รูปแบบที่เกี่ยวข้อง
                'formatter' => [
                    // ชื่อคลาสของตัวจัดการรูปแบบ
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // พารามิเตอร์ที่ใช้ในการสร้างคลาสตัวจัดการรูปแบบ
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

## ช่องหลายช่อง
monolog รองรับช่องหลายช่อง โดยค่าเริ่มต้นคือ `default` ถ้าต้องการเพิ่มช่อง `log2` สามารถกำหนดได้ดังนี้:
```php
return [
    // ช่องบันทึกริยาสำหรับยูสตัวเดียว
    'default' => [
        // ตัวจัดการช่องค่าเริ่มต้น สามารถกำหนดได้หลายตัว
        'handlers' => [
            [   
                // ชื่อคลาสของตัวจัดการ
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // พารามิเตอร์ที่ใช้ในการสร้างคลาสของตัวจัดการ
                'constructor' => [
                    runtime_path() . '/logs/webman.log',
                    Monolog\Logger::DEBUG,
                ],
                // รูปแบบที่เกี่ยวข้อง
                'formatter' => [
                    // ชื่อคลาสของตัวจัดการรูปแบบ
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // พารามิเตอร์ที่ใช้ในการสร้างคลาสตัวจัดการรูปแบบ
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
    // ช่อง log2
    'log2' => [
        // ตัวจัดการช่องค่าเริ่มต้น สามารถกำหนดได้หลายตัว
        'handlers' => [
            [   
                // ชื่อคลาสของตัวจัดการ
                'class' => Monolog\Handler\RotatingFileHandler::class,
                // พารามิเตอร์ที่ใช้ในการสร้างคลาสของตัวจัดการ
                'constructor' => [
                    runtime_path() . '/logs/log2.log',
                    Monolog\Logger::DEBUG,
                ],
                // รูปแบบที่เกี่ยวข้อง
                'formatter' => [
                    // ชื่อคลาสของตัวจัดการรูปแบบ
                    'class' => Monolog\Formatter\LineFormatter::class,
                    // พารามิเตอร์ที่ใช้ในการสร้างคลาสตัวจัดการรูปแบบ
                    'constructor' => [ null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];
```

การใช้งานช่อง `log2` จะเป็นดังนี้:
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
        return response('สวัสดี index');
    }
}
```
