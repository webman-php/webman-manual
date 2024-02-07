# คู่มือของ webman

## คำอธิบาย
`vlucas/phpdotenv` เป็นคอมโพเนนต์ที่โหลดตัวแปรสภาพแวดล้อมเพื่อแยกการตั้งค่าของสภาพแวดล้อมต่าง ๆ เช่น สภาพแวดล้อมการพัฒนา สภาพแวดล้อมการทดสอบ เป็นต้น

## ที่ตั้งโปรเจกต์
https://github.com/vlucas/phpdotenv

## การติดตั้ง
 
```php
composer require vlucas/phpdotenv
 ```

## การใช้งาน

#### สร้างไฟล์ `.env` ในไดเร็กทอรีหลักของโปรเจกต์
**.env**
```plaintext
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### แก้ไขไฟล์การตั้งค่า
**config/database.php**
```php
return [
    // ฐานข้อมูลเริ่มต้น
    'default' => 'mysql',

    // การตั้งค่าการเชื่อมต่อต่าง ๆ
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
> แนะนำให้เพิ่มไฟล์ `.env` เข้าไปในรายการ `.gitignore` เพื่อหลีกเลี่ยงการส่งไฟล์ดังกล่าวไปยังคลังโค้ด โดยเพิ่มไฟล์ตัวอย่างการตั้งค่า `.env.example` เข้าไปในคลังโค้ด และเมื่อนำโปรเจกต์ไปใช้จริง ก็ควรทำการคัดลอกไฟล์ `.env.example` เป็น `.env` แล้วแก้ไขค่าในไฟล์ `.env` ตามสภาพแวดล้อมปัจจุบัน อย่างนี้ก็ทำให้โปรเจกต์สามารถโหลดค่าต่าง ๆ ในตั้งค่าได้ที่สภาพแวดล้อมที่แตกต่างกัน

> **โปรดทราบ**
> `vlucas/phpdotenv` อาจมีบั๊กใน PHP TS เวอร์ชัน (เวอร์ชันที่ปลอดภัยในการใช้งาน) ดังนั้นแนะนำให้ใช้เวอร์ชัน NTS (เวอร์ชันที่ไม่ปลอดภัยในการใช้งาน)
> สามารถตรวจสอบเวอร์ชัน PHP ปัจจุบันได้โดยการใช้คำสั่ง `php -v`

## ข้อมูลเพิ่มเติม

เข้าชม https://github.com/vlucas/phpdotenv
