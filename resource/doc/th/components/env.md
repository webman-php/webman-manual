# vlucas/phpdotenv

## คำอธิบาย
`vlucas/phpdotenv` เป็นคอมโพเนนต์ในการโหลดตัวแปรสภาพแวดล้อม ที่ใช้ในการแยกการกำหนดค่าของสภาพแวดล้อมต่าง ๆ (เช่น สภาพแวดล้อมการพัฒนา สภาพแวดล้อมการทดสอบ เป็นต้น)

## ที่อยู่โปรเจ็กต์

https://github.com/vlucas/phpdotenv

## การติดตั้ง
 
```php
composer require vlucas/phpdotenv
```
  
## การใช้

#### สร้างไฟล์ `.env` ที่รากโปรเจ็กต์
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### แก้ไขไฟล์กำหนดค่า
**config/database.php**
```php
return [
    // ฐานข้อมูลเริ่มต้น
    'default' => 'mysql',

    // การกำหนดค่าฐานข้อมูลต่าง ๆ
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DB_HOST'),
            'port'        => getenv('DB_PORT'),
            'database'    => getenv('DB_NAME'),
            'username'    => getenv('DB_USER'),
            'password'    => getenv('DB_PASSWORD'),
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
```

> **คำแนะนำ**
> แนะนำให้เพิ่มไฟล์ `.env` ลงในรายการ `.gitignore` เพื่อป้องกันการส่งไปยังที่เก็บรหัส ที่เพิ่มไฟล์ `.env.example` ขึ้นมาในรหัส และเมื่อนำโปรเจ็กต์ไปใช้งาน คัดลอกไฟล์ `.env.example` เป็น `.env`, และปรับเปลี่ยนค่าใน `.env` ตามสภาพแวดล้อมปัจจุบัน นี้จะทำให้โปรเจ็กต์สามารถโหลดค่าต่าง ๆ ตามสภาพแวดล้อมที่ต่างกัน

> **แจ้งเตือน**
> `vlucas/phpdotenv` อาจมีบั๊กในเวอร์ชัน PHP TS (เวอร์ชันแบบปลอดภัยทางเทรดส์) โปรดใช้เวอร์ชัน NTS (เวอร์ชันที่ไม่ปลอดภัยทางเทรดส์)
> เวอร์ชัน PHP ปัจจุบันสามารถตรวจสอบได้โดยใช้คำสั่ง `php -v` 

## ข้อมูลเพิ่มเติม

เข้าถึง https://github.com/vlucas/phpdotenv
