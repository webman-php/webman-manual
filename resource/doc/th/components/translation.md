# ภาษาหลายภาษา

การใช้งานภาษาหลายภาษาต้องใช้คอมโพเนนต์ [symfony/translation](https://github.com/symfony/translation) โปรดติดตั้ง
```
composer require symfony/translation
```

## สร้างแพ็คภาษา (Language Pack)
webman จะมีการจัดทำแพ็คภาษาไว้ที่ไดเรกทอรี `resource/translations` (หากไม่มีโปรดสร้างใหม่) หากต้องการเปลี่ยนไดเรกทอรีโปรดตั้งค่าใน `config/translation.php`

แต่ละภาษาจะมีไดเรกทอรีในนั้น และการกำหนดภาษาจะถูกเก็บไว้ใน `message.php` เช่น
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

ไฟล์ภาษาทั้งหมดจะส่งค่ากลับเป็นอาร์เรย์ เช่น ไฟล์ `/translations/en/messages.php` จะส่งค่ากลับเป็น
```php
return [
    'hello' => 'สวัสดี webman',
];
```

## การตั้งค่า

`config/translation.php`
```php
return [
    // ภาษาเริ่มต้น
    'locale' => 'zh_CN',
    // ภาษาสำรอง ในกรณีที่ภาษาปัจจุบันไม่พร้อมใช้
    'fallback_locale' => ['zh_CN', 'en'],
    // ไดเรกทอรีที่เก็บไฟล์ภาษา
    'path' => base_path() . '/resource/translations',
];
```
การแปลทำได้ผ่าน `trans()` มีการสร้างไฟล์ภาษา `resource/translations/zh_CN/messages.php` เช่น
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

เข้าชม `http://127.0.0.1:8787/user/get` จะแสดง "สวัสดี โลก!"
การเปลี่ยนภาษาเริ่มต้นจะใช้ `locale()`

เพิ่มไฟล์ภาษา `resource/translations/en/messages.php` เช่น
```php
return [
    'hello' => 'สวัสดีชาวโลก!',
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
        // เปลี่ยนภาษาเริ่มต้น
        locale('en');
        $hello = trans('hello'); // สวัสดีชาวโลก!
        return response($hello);
    }
}
```
เข้าชม `http://127.0.0.1:8787/user/get` จะแสดง "สวัสดีชาวโลก!"

คุณสามารถใช้พารามิเตอร์ที่ 4 ของฟังก์ชัน `trans()` เพื่อเปลี่ยนภาษาชั่วคราว เช่นตัวอย่างด้านบนและตัวอย่างด้านล่างคือเทียบเท่ากัน
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // พารามิเตอร์ที่ 4 เปลี่ยนภาษา
        $hello = trans('hello', [], null, 'en'); // สวัสดีชาวโลก!
        return response($hello);
    }
}
```
## การตั้งค่าภาษาให้แน่ใจสำหรับคำขอแต่ละอัน

translation เป็น Singleton ซึ่งหมายความว่าคำขอทั้งหมดจะใช้ instances เดียวกัน หากคำขอไหนใช้ `locale()` ในการตั้งค่าภาษาเริ่มต้น มันจะส่งผลต่อคำขอทั้งหมดของ processes ที่เหลือ ดังนั้นคุณควรตั้งค่าภาษาอย่างแน่นอนสำหรับแต่ละคำขอ เช่น ใช้ middleware ต่อไปนี้

สร้างไฟล์ `app/middleware/Lang.php` (ถ้า directory อยู่ใหม่โปรดสร้างเอง) เช่น
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

ใน `config/middleware.php` เพิ่ม middleware ระดับ global ดังนี้
```php
return [
    // มิดเดิลแอร์ระดับ global
    '' => [
        // ... มิดเดิลแอร์อื่นๆ จะมีการเก็บเงื่อนไข
        app\middleware\Lang::class,
    ]
];
```
## การใช้ตราบส่วน

บางครั้งข้อความอาจมีตัวแปรที่ต้องการแปล เช่น
```php
trans('hello ' . $name);
```
เมื่อพบกรณีนี้ เราจะใช้ตราบส่วนในการจัดการ

เปลี่ยน `resource/translations/zh_CN/messages.php` เช่น
```php
return [
    'hello' => '你好 %name%! ',
];
```
แปลเมื่อมีการส่งตราบส่วนที่สอดคล้องผ่านพารามิเตอร์ที่ 2 เช่น
```php
trans('hello', ['%name%' => 'webman']); // 你好 webman! 
```
## การจัดการข้อความแบบพหุน

ภาษาบางภาษาอาจต้องการการเปลี่ยนแปลงขึ้นอยู่กับจำนวนของวัตถุ ตัวอย่างเช่น `There is %count% apple` เมื่อค่า `%count%` เท่ากับ 1 จะแสดงผลถูกต้อง แต่เมื่อมากกว่า 1 ค่าที่แสดงผิดพลาด

ในกรณีนี้เราสามารถใช้ **pipe** (`|`) เพื่อแสดงจำนวนของวัตถุเอง

ไฟล์ภาษา `/translations/en/messages.php` จึงมีการเพิ่ม `apple_count` เช่น
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

เรายังสามารถกำหนดช่วงของตัวเลขเพื่อเปลี่ยนแปลงข้อความของวัตถุที่มากขึ้น ในทางกลับกันจะทำให้มันเป็นการแสดงผลที่ถูกต้องขึ้นมากขึ้น
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```
## เลือกไฟล์ภาษา

ไฟล์ภาษาเริ่มต้นมีชื่อว่า `message.php` แต่คุณสามารถสร้างไฟล์ที่มีชื่ออื่นได้

สร้างไฟล์ภาษา `resource/translations/zh_CN/admin.php` เช่น
```php
return [
    'hello_admin' => 'สวัสดี ผู้ดูแลระบบ!',
];
```

ผ่าน `trans()` พารามิเตอร์ที่ 3 ใช้ในการระบุไฟล์ภาษา (เว้นชื่อไฟล์ `.php`)
```php
trans('hello', [], 'admin', 'zh_CN'); // สวัสดี ผู้ดูแลระบบ!
```

## ข้อมูลเพิ่มเติม
ดูเพิ่มเติมได้ที่ [คู่มือ symfony/translation](https://symfony.com/doc/current/translation.html)
