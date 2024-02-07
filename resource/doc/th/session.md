# การจัดการเซสชัน

## ตัวอย่าง
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('hello ' . $session->get('name'));
    }
}
```

ผ่าน `$request->session();` สามารถรับชุดคำสั่ง `Workerman\Protocols\Http\Session` instance และใช้เมทอดของ instance เพื่อเพิ่ม แก้ไข หรือลบข้อมูลจากเซสชัน

> หมายเหตุ: เมื่อวัตถุเซสชันถูกทำลาย ข้อมูลเซสชันจะถูกบันทึกโดยอัตโนมัติ ดังนั้น อย่าเก็บวัตถุที่ได้รับจาก `$request->session()` ไว้ในอาร์เรย์ที่เป็นตัวแปร global หรือเป็นตัวแปรของคลาสที่ต้องการ เพื่อป้องกันไม่ให้เซสชันบันทึกข้อมูลไม่ได้


## รับข้อมูลเซสชันทั้งหมด
```php
$session = $request->session();
$all = $session->all();
```
คืนค่าเป็นอาร์เรย์ หากไม่มีข้อมูลเซสชันใด ๆ คืนค่าเป็นอาร์เรย์ว่าง


## รับค่าของเซสชันที่ระบุ
```php
$session = $request->session();
$name = $session->get('name');
```
กรณีข้อมูลไม่มี จะคืนค่าเป็น null	คุณสามารถกำหนดค่าเริ่มต้นให้กับเมทอด get ด้วยอาร์กิวเม้นที่สอง หากอาร์กิวเม้นตัวแรกไม่พบค่าในอาร์เรย์เซสชันก็จะคืนค่าเริ่มต้น ตัวอย่างเช่น:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```


## บันทึกข้อมูลเซสชัน
ใช้เมทอด set เพื่อบันทึกข้อมูลที่ต้องการ
```php
$session = $request->session();
$session->set('name', 'tom');
```
เมทอด set ไม่คืนค่า ข้อมูลเซสชันจะถูกบันทึกโดยอัตโนมัติเมื่อวัตถุเซสชันถูกทำลาย

เมื่อต้องการบันทึกข้อมูลหลายอย่าง ให้ใช้เมทอด put
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
เช่นกัน เมทอด put ไม่คืนค่า

## ลบข้อมูลของเซสชัน
เมื่อต้องการลบข้อมูลหรือข้อมูลหลายข้อมูล ให้ใช้เมทอด `forget`
```php
$session = $request->session();
// ลบหนึ่งรายการ
$session->forget('name');
// ลบหลายรายการ
$session->forget(['name', 'age']);
```
อีกทางเลือกคือเมทอด delete ต่างจาก `forget` ที่สามารถลบได้แค่หนึ่งรายการเท่านั้น
```php
$session = $request->session();
// เหมือนกับ $session->forget('name');
$session->delete('name');
```


## รับและลบค่าของเซสชัน
```php
$session = $request->session();
$name = $session->pull('name');
```
ผลลัพธ์จะเหมือนกับโค้ดด้านล่าง
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
กรณีไม่พบเซสชันที่แสดง จะคืนค่าเป็น null

## ลบข้อมูลในเซสชันทั้งหมด
```php
$request->session()->flush();
```
ไม่คืนค่า เมื่อวัตถุเซสชันถูกทำลาย ข้อมูลของเซสชันจะถูกลบออกจากพื้นที่จัดเก็บโดยอัตโนมัติ


## ตรวจสอบว่ามีข้อมูลของเซสชันแล้วหรือยัง
```php
$session = $request->session();
$has = $session->has('name');
```
ถ้าไม่มีข้อมูลเซสชันที่สอดคล้องหรือค่าของเซสชันที่สอดคล้องเป็น null จะคืนค่าเป็นเท็จ มิฉะนั้นจะคืนค่าเป็นจริง

```php
$session = $request->session();
$has = $session->exists('name');
```
โค้ดด้านล่างใช้สำหรับตรวจสอบว่ามีข้อมูลของเซสชันที่ระบุหรือไม่ คำตอบคือ เมื่อค่าของเซสชันที่ระบุเป็น null การปฎิบัติตามคำสั่งนี้จะคืนค่าเป็นจริง

## ฟังก์ชันช่วย session()
> 2020-12-09 เพิ่มเติม

webman มีฟังก์ชันช่วย `session()` ทำงานเช่นเดียวกันกับฟังก์ชัน session() ดังตัวอย่างด้านล่าง
```php
// รับ instance เซสชัน
$session = session();
// เหมือนกับ
$session = $request->session();

// รับค่าหนึ่งรายการ
$value = session('key', 'default');
// เหมือนกับ
$value = session()->get('key', 'default');
// เหมือนกับ
$value = $request->session()->get('key', 'default');

// กำหนดค่าให้กับเซสชัน
session(['key1'=>'value1', 'key2' => 'value2']);
// เหมือนกับ
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// เหมือนกับ
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);

```

## ไฟล์การตั้งค่า
ไฟล์การตั้งค่าเซสชันอยู่ที่ `config/session.php` และมีรูปแบบดังตัวอย่างนี้:

```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class หรือ RedisSessionHandler::class หรือ RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // type เมื่อ handler เป็น FileSessionHandler::class จะมีค่าเป็น file
    // type เมื่อ handler เป็น RedisSessionHandler::class จะมีค่าเป็น redis
    // type เมื่อ handler เป็น RedisClusterSessionHandler::class จะมีค่าเป็น redis_cluster หรือกลุ่มredis
    'type'    => 'file',

    // handler ที่ต่างกันจะใช้การตั้งค่าที่ต่างกัน
    'config' => [
        // การตั้งค่าเมื่อ type เป็น file
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // การตั้งค่าเมื่อ type เป็น redis
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
    ],

    'session_name' => 'PHPSID', // ชื่อคุ๊กกี้ที่เก็บ session_id
    
    // === การตั้งค่าเพิ่มเติมที่จำเป็นต้องใช้ webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // การอัพเดท session โดยอัตโนมัติ ปิดการใช้งานตามค่าเริ่มต้น
    'lifetime' => 7*24*60*60,          // ระยะเวลาที่ session หมดอายุ
    'cookie_lifetime' => 365*24*60*60, // ระยะเวลาที่คุ๊กกี้ session_id หมดอายุ
    'cookie_path' => '/',              // เส้นทางของคุ๊กกี้ session_id
    'domain' => '',                    // โดเมนของคุ๊กกี้ session_id
    'http_only' => true,               // เปิดใช้งาน httpOnly ตามค่าเริ่มต้น
    'secure' => false,                 // เปิดใช้งาน session เฉพาะใน https ตามค่าเริ่มต้น
    'same_site' => '',                 // ใช้ป้องกันการโจมตี CSRF และการติดตามผู้ใช้งาน เลือกค่าได้แก่strict/lax/none
    'gc_probability' => [1, 1000],     // ความน่าจะเป็นในการล้างข้อมูลของเซสชัน
];
```


> **หมายเหตุ** 
> เริ่มตั้งแต่ webman 1.4.0 เปลี่ยนการเรียกใช้งาน SessionHandler namespace จากเดิมคือ
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> เปลี่ยนเป็น  
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  



## การตั้งค่าระยะที่เป็นประจำ
เมื่อ webman-framework < 1.3.14  ระยะเวลาของ session จะต้องระบุใน `php.ini`

```ini
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

เช่นเดียวกันถ้าตั้งระยะเวลาเป็น 1440 วินาที ความเร็วในการตั้งระยะของ session จะเป็นดังต่อไปนี้
```ini
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **คำแนะนำ**
> สามารถใช้คำสั่ง `php --ini` เพื่อค้นหาตำแหน่งของ `php.ini`
