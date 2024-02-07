# แอปพลิเคชันมากตัว
บางครั้งโปรเจกต์หนึ่งอาจถูกแบ่งเป็นโปรเจกต์ย่อยหลายๆ โปรเจกต์ เช่น ร้านค้าออนไลน์อาจถูกแบ่งเป็นโปรเจกต์ย่อย ๆ 3 ประการคือ โปรเจกต์หลักของร้านค้า อินเทอร์เฟซ API ของร้านค้า และภายในร้านค้า เขาใช้ออกแบบดาต้าเบสเดียวกัน

webman ช่วยให้คุณสามารถวางแผน app directory ในแบบนี้:
```plaintext
app
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
เมื่อเข้าถึงที่อยู่ `http://127.0.0.1:8787/shop/{คอนโทรลเลอร์}/{เมธอด}` เข้าถึงคอนโทรเลอร์และเมธอดใน`app/shop/controller` 

เมื่อเข้าถึงที่อยู่ `http://127.0.0.1:8787/api/{คอนโทรลเลอร์}/{เมธอด}` เข้าถึงคอนโทรเลอร์และเมธอดใน `app/api/controller`

เมื่อเข้าถึงที่อยู่ `http://127.0.0.1:8787/admin/{คอนโทรลเลอร์}/{เมธอด}` เข้าถึงคอนโทรเลอร์และเมธอดใน`app/admin/controller`

ใน webman อาจตั้งกำหนด app directory แบบนี้ ด้วย:
```plaintext
app
├── controller
├── model
├── view

├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
นั่นหมายความว่าเมื่อเข้าถึงที่อยู่ `http://127.0.0.1:8787/{คอนโทรลเลอร์}/{เมธอด}` จะเข้าถึงคอนโทรเลอร์และเมธอดใน`app/controller` เมื่อมี directory path ที่เริ่มต้นด้วย `api` หรือ `admin` จะเข้าถึงคอนโทรเลอร์และเมธอดใน directory ที่สอดคล้องกับ 

เมื่อมีหลายๆแอปพลิเคชัน ชื่อ domain คลาสต้องปรับตาม `psr4` เช่น `app/api/controller/FooController.php` จะมีรูปแบบต่อไปนี้:
```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## การกำหนด middleware ของแอปพลิเคชันหลายๆ
บางครั้งคุณอาจต้องการกำหนด middleware ที่แตกต่างกันสำหรับแอปพลิเคชันต่าง ๆ, เช่น `api` อาจต้องการmiddlware ที่เปิดใช้งาน cross-origin, `admin` อาจต้องการmiddlware ที่ตรวจสอบการเข้าสู่ระบบของผู้ดูแลระบบ, การกำหนด middleware ต่างๆ สำหรับแอปพลิเคชันอาจมีรูปแบบต่อไปนี้ใน `config/midlleware.php`:

```php
return [
    // บนทั่วไป middleware
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // middleware ของแอปพลิเคชัน api
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // middleware ของแอปพลิเคชัน admin
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> Middleware ที่กล่าวถึงอาจไม่มีอยู่จริง ข้อมูลในที่นี้เพียงแค่ตัวอย่างการอธิบายวิธีการกำหนด middleware สำหรับแต่ละแอปพลิเคชันเท่านั้น

ลำดับการทำงานของ middleware จะเป็น `global middleware` -> `middleware ของแอปพลิเคชัน`

ท่านสามารถศึกษาการพัธนา middleware ได้[middleware บทที่](middleware.md)

## การกำหนดการจัดการข้อยกเว้นของแอปพลิเคชันหลายๆ
เช่นเดียวกับกับ middleware ท่านอาจต้องการกำหนดการจัดการข้อยกเว้นทั้งของแต่ละอะพลิเคชัน คุณอาจต้องการให้`shop` โปรแกรมเชิงอุปกรณ์แสดงข้อผิดพลาดที่เป็นมิตร; ขณะที่เมื่อโปรเจ็ค `api` มีข้อผิดพลาดที่ต้องการรับค่ากลับไม่ใช่หน้าเพจ แต่เป็นสตริง json. การกำหนดคลาสการจัดการข้อยกเว้นที่ต่างกันสำหรับแต่ละแอปพลิเคชันประมาณนี้`config/exception.php`:
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> ไม่เหมือนกับ middleware, แต่ละแอปพลิเคชันสามารถกำหนดคลาสการจัดการข้อยกเว้นได้เพียงหนึ่งโดยเท่านั้น

> คลาสการจัดการข้อผิดพลาดที่กล่าวถึงอาจไม่มีอยู่จริง, ข้อมูลในที่นี้เพียงแค่ตัวอย่างการอธิบายวิธีการกำหนดการจัดการข้อผิดพลาดสำหรับแต่ละแอปพลิเคชันเท่านั้น

สำหรับการพัฒนาการจัดการข้อผิดพลาด คุณสามารถศึกษาเพิ่มเติมได้จาก [บทเรื่องการจัดการข้อผิดพลาด](exception.md)
