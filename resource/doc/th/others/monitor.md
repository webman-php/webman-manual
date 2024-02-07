# การตรวจสอบกระบวนการ
webman มาพร้อมกับกระบวนการตรวจสอบที่ชื่อว่า monitor ซึ่งรองรับฟังก์ชันสองตัว
1. ตรวจสอบการอัปเดตไฟล์และรีโหลดโค้ดธุรกิจใหม่ๆโดยอัตโนมัติ (ทำงานมักในขณะที่กำลังพัฒนา)
2. ตรวจสอบการใช้หน่วยความจำของกระบวนการทั้งหมด หากมีกระบวนการใดที่ใช้หน่วยความจำเกือบจะเกินขีดจำกัดที่กำหนดใน `php.ini` จะทำการรีสตาร์ทกระบวนการนั้นอัตโนมัติ (โดยไม่มีผลต่อธุรกิจ)

## การตั้งค่าการตรวจสอบ
ไฟล์การตั้งค่า `config/process.php` ในส่วนของค่า `monitor`
```php

global $argv;

return [
    // ตรวจสอบการอัปเดตไฟล์และรีโหลดโค้ด
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // ตรวจสอบไดเรกทอรีเหล่านี้
            'monitorDir' => array_merge([   
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // ไฟล์ที่มีนามสกุลเหล่านี้จะถูกตรวจสอบ
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/',
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',
            ]
        ]
    ]
];
```
`monitorDir` ใช้สำหรับการกำหนดไดเรกทอรีที่จะตรวจสอบอัปเดต (ไม่ควรมีไฟล์มากเกินไปในไดเรกทอรีที่ตรวจสอบ)
`monitorExtensions` ใช้สำหรับกำหนดนามสกุลของไฟล์ที่ควรตรวจสอบในไดเรกทอรี `monitorDir`
ค่า `options.enable_file_monitor` เมื่อเท่ากับ `true` จะเปิดการตรวจสอบอัปเดตไฟล์ (ในระบบ Linux จะเปิดการตรวจสอบไฟล์โดยค่าเริ่มต้นเมื่อทำงานในโหมด debug)
ค่า `options.enable_memory_monitor` เมื่อเท่ากับ `true` จะเปิดการตรวจสอบการใช้หน่วยความจำ (การตรวจสอบการใช้หน่วยความจำไม่รองรับระบบปฏิบัติการ Windows)

> **เราแนะนำ**
> ในระบบปฏิบัติการ Windows จำเป็นต้องใช้ `windows.bat` หรือ `php windows.php` เพื่อเปิดการตรวจสอบอัปเดตไฟล์
