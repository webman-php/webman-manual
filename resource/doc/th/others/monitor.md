webmanมาพร้อมกับกระบวนการตรวจสอบที่เรียกว่า monitor ซึ่งรองรับฟังก์ชั่นสองอย่าง ดังนี้
1. ตรวจสอบการอัปเดตไฟล์และโหลดโค้ดธุรกิจใหม่ๆโดยอัตโนมัติ (ปกติสำหรับการพัฒนา)
2. ตรวจสอบการใช้หน่วยความจำของกระบวนการทั้งหมด หากกระบวนการใดใช้หน่วยความจำเกินขีดจำกัดที่กำหนดใน `php.ini` จะมีการรีสตาร์ทกระบวนการนั้นๆโดยอัตโนมัติ (โดยไม่มีผลต่อธุรกิจ)

### การตั้งค่าการตรวจสอบ 
ไฟล์กำหนดค่า `config/process.php` ในการกำหนดค่า `monitor`
```php

global $argv;

return [
    // File update detection and automatic reload
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Monitor these directories
            'monitorDir' => array_merge([    // โฟลเดอร์ไหนที่ต้องการตรวจสอบไฟล์
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // Files with these suffixes will be monitored
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // เปิดการตรวจสอบไฟล์หรือไม่
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // เปิดการตรวจสอบการใช้หน่วยความจำหรือไม่
            ]
        ]
    ]
];
```
`monitorDir` ใช้ในการกำหนดโฟลเดอร์ที่ต้องการตรวจสอบการอัปเดต (โฟลเดอร์ที่ต้องการตรวจสอบไม่ควรมีไฟล์มากเกินไป)
`monitorExtensions` ใช้ในการกำหนดไฟล์ที่จะต้องการตรวจสอบในโฟลเดอร์ `monitorDir`
`options.enable_file_monitor` ถ้าเป็น `true` จะเปิดการตรวจสอบการอัปเดตไฟล์ (การทำงานในลีนุกซ์จะเปิดตรวจสอบไฟล์โดยค่าเริ่มต้นตอนทำงานแบบ debug)
`options.enable_memory_monitor` ถ้าเป็น `true` จะเปิดการตรวจสอบการใช้งานหน่วยความจำ (การตรวจสอบการใช้งานหน่วยความจำไม่รองรับระบบปฎิบัติการวินโดว์)

> **เคล็ดลับ**
> ในระบบปฎิบัติการวินโดว์จะต้องเปิดการตรวจสอบการอัปเดตไฟล์เมื่อต้องการทำงานในระบบวินโดว์โดยการรัน `windows.bat` หรือ `php windows.php` 
