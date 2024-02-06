# ภาษาหลายภาษา

การใช้งานหลายภาษาใช้ [symfony/translation](https://github.com/symfony/translation) component.

## การติดตั้ง
```
composer require symfony/translation
```

## สร้างแพ็คภาษา
ไว้ใน webman มีการสร้างแพ็คภาษา จะอยู่ในไดเรกทอรี `resource/translations` (ถ้าไม่มีโปรดสร้างด้วยตนเอง) ถ้าคุณต้องการเปลี่ยนไดเรกทอรี โปรดตั้งค่าในไฟล์ `config/translation.php` แต่ละภาษาจะถูกแบ่งออกเป็นโฟลเดอร์ย่อย โดยภาษาจะถูกกำหนดไว้ที่ไฟล์ `messages.php` ดังตัวอย่าง:
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

ไฟล์ภาษาทั้งหมดจะได้ array ส่งกลับมา เช่น
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## การตั้งค่า

`config/translation.php`

```php
return [
    // ภาษาเริ่มต้น
    'locale' => 'zh_CN',
    // ภาษาย่อๆสำหรับภาษาที่ถูกใช้อยู่หลัก
    'fallback_locale' => ['zh_CN', 'en'],
    // ไดเรกทอรีเก็บไฟล์ภาษา
    'path' => base_path() . '/resource/translations',
];
```

## แปล

ใช้ `trans()` method.

สร้างไฟล์ภาษา `resource/translations/zh_CN/messages.php` ดังนี้:
```php
return [
    'hello' => 'สวัสดี โลก!',
];
```

สร้างไฟล์ `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // สวัสดี โลก!
        return response($hello);
    }
}
```

เข้าถึง `http://127.0.0.1:8787/user/get` จะคืนค่า "สวัสดี โลก!"

## เปลี่ยนภาษาเริ่มต้น

แปลภาษาโดยใช้ method `locale()`.

สร้างไฟล์ภาษา `resource/translations/en/messages.php` ดังนี้:
```php
return [
    'hello' => 'hello world!',
];
```

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // เปลี่ยนภาษา
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
เข้าถึง `http://127.0.0.1:8787/user/get` จะคืนค่า "hello world!"

คุณสามารถใช้วิธีที่ 4 ของ `trans()` function ในการเปลี่ยนภาษาชั่วคราว ในตัวอย่างข้างบนและด้านล่างเป็นเท่ากัน:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // ควบคุมภาษาด้วยวิธีที่ 4
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## การตั้งภาษาที่เจอกับแต่ละการร้องขอที่ชัดเจน
การแปลเป็นหนึ่งเดียว หมายความว่าทุกๆการขอ เผยแพร่การสำรองไว้เหมือนกัน ถ้าคำร้องไหล่ใช้ `locale()` แล้วก็มีผลต่อการขออื่นๆ ต่อมาหม้อนั่นเราควาจะต้องตั้งภาษาที่ชัดเจนทุกการขอ จนถึงการใช้ชั่วคราว ยกตัวอย่างเช่นการใช้มิดเดิลเวียต

สร้างไฟล์ `app/middleware/Lang.php` (ถ้าไม่มีโปรดสร้างด้วยตนเอง) ดังนี้:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        locale(session('lang', 'zh_CN'));
        return $handler($request);
    }
}
```

ในไฟล์ `config/middleware.php` เพิ่ม middleware ไว้ทั่วไปดังนี้:
```php
return [
    // มิดเดิลเวียตไว้ทั่วไป
    '' => [
        // ... ขอให้ลอยเท่าสายตา
        app\middleware\Lang::class,
    ]
];
```


## ใช้ตัวยึดที่
บางครั้งข้อความประกอบด้วยตัวแปรที่ต้องการแปล เช่น
```php
trans('hello ' . $name);
```
เมื่อเจอปัญหาแบบนี้เราใช้ตัวยึดในการจัดการ

เปลี่ยนไฟล์ `resource/translations/zh_CN/messages.php` ดังนี้:
```php
return [
    'hello' => 'สวัสดี %name%!',
];
```
เข้าถึงการแปลเพราะค่าของตัวแปรผ่านผ่านอาร์กิวเมนต์ที่สอง
```php
trans('hello', ['%name%' => 'webman']); // สวัสดี webman!
```

## การจัดการพหูพจนา

ภาษาบางภาษาประกอบไปด้วยแบบอย่างที่ไม่เหมือนกันเพราะจำนวนของสิ่งของที่ต่างกัน เช่น`There is %count% apple` เมื่อ`%count%` เป็น 1 การใช้ลักษณะถูกต้อง แต่เมื่อมากกว่า 1 ก็ผิด

เมื่อเจอกรณีแบบนี้เราใช้ตัวในไฟล์ **pipe** (`|`) ในการแหลือออก

ไฟล์ภาษา `resource/translations/en/messages.php` เพิ่ม`apple_count`สองดังนี้:
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

เราสามารถแนะแนะเลขขอบเขต เพื่อทำล่าขอบเขตแบบจำนวน:
```php
return [
    // ...
    'apple_count' => '{0} ไม่มีแอปเปิ้ล|{1} มีแอปเปิ้ล 1 ลูก|]1,19] มีแอปเปิ้ล %count% ลูก|]20,Inf[ มีแอปเปิ้ลเป็นจำนวนมาก'
];
```

```php
trans('apple_count', ['%count%' => 20]); // มีแอปเปิ้ลเป็นจำนวนมาก
```

## กำหนดไฟล์ภาษา

ไฟล์ภาษามีชื่อมักเป็น `messages.php` แต่จริงๆแล้วคุณสามารถสร้างไฟล์ภาษาชื่ออื่น

สร้างไฟล์ภาษา `resource/translations/zh_CN/admin.php` ดังนี้:
```php
return [
    'hello_admin' => 'สวัสดี ผู้ดูแลระบบ!',
];
```

ผ่าน `trans()` ในอาร์กิวเมนต์ที่สามมากเพื่อกำหนดหลังไฟล์ (ละคงตัว `.php` หลังจากชื่อ)
```php
trans('hello', [], 'admin', 'zh_CN'); // สวัสดี ผู้ดูแลระบบ!
```

## ข้อมูลเพิ่มเติม
อ่าน [เอกสาร symfony/translation](https://symfony.com/doc/current/translation.html)
