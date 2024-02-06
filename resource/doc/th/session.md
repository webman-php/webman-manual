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

การใช้ `$request->session();` เพื่อรับอินสแตนซ์ของ `Workerman\Protocols\Http\Session` และใช้เมธอดของอินสแตนซ์เพื่อเพิ่ม แก้ไข หรือลบข้อมูลเซสชัน

> **หมายเหตุ:** เมื่อวัตถุเซสชันถูกทำลาย ข้อมูลเซสชันจะถูกบันทึกโดยอัตโนมัติ ดังนั้น อย่าเก็บวัตถุที่ได้จาก `$request->session()` ไว้ในอาร์เรย์โกบัลหรือเป็นสมาชิกคลาส หรือจะทำให้เซสชันไม่อาจบันทึก


## รับข้อมูลเซสชันทั้งหมด
```php
$session = $request->session();
$all = $session->all();
```
ค่าที่คืนคืออาร์เรย์ ถ้าไม่มีข้อมูลเซสชันเลย จะคืนค่าอาร์เรย์ว่าง


## รับค่าข้อมูลจากเซสชัน
```php
$session = $request->session();
$name = $session->get('name');
```
ถ้าข้อมูลไม่มีอยู่ จะคืนค่า null


คุณยังสามารถส่งพารามิเตอร์ที่สองให้กับเมธอด get หากไม่พบค่าในอาร์เรย์เซสชัน จะคืนค่าเริ่มต้น ตัวอย่างเช่น:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```


## บันทึกเซสชัน
เมื่อต้องการบันทึกข้อมูลแต่ละรายการให้ใช้เมธอด set
```php
$session = $request->session();
$session->set('name', 'tom');
```
การใช้ set ไม่คืนค่า ข้อมูลเซสชันจะถูกบันทึกโดยอัตโนมัติเมื่อวัตถุเซสชันถูกทำลาย

เมื่อต้องการบันทึกข้อมูลมากกว่าหนึ่งรายการให้ใช้เมธอด put
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
อย่างเช่น put ไม่คืนค่าเช่นกัน


## ลบข้อมูลเซสชัน
เมื่อต้องการลบข้อมูลเซสชันรายการหนึ่งหรือมากกว่า ให้ใช้เมธอด `forget`
```php
$session = $request->session();
// ลบรายการหนึ่ง
$session->forget('name');
// ลบหลายรายการ
$session->forget(['name', 'age']);
```

อีกวิธีที่สามารถใช้ได้คือการใช้เมธอด delete ความแตกต่างคือ delete สามารถลบเพียงหนึ่งรายการ เช่น
```php
$session = $request->session();
// เหมือนกับ $session->forget('name');
$session->delete('name');
```

## รับและลบค่าข้อมูลจากเซสชัน
```php
$session = $request->session();
$name = $session->pull('name');
```
ผลลัพธ์ของข้อความนี้เทียบเท่ากับโค้ดข้างล่าง
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
หากไม่มีเซสชันที่สอดคล้าย จะคืนค่า null


## ลบข้อมูลเซสชันทั้งหมด
```php
$request->session()->flush();
```
ไม่คืนค่า เมื่อวัตถุเซสชันถูกทำลาย ข้อมูลเซสชันจะถูกลบออกจากการจัดเก็บโดยอัตโนมัติ

## ตรวจสอบว่าข้อมูลเซสชันที่สอดคล้ายอยู่หรือไม่
```php
$session = $request->session();
$has = $session->has('name');
```
เมื่อไม่มีข้อมูลเซสชันที่สอดคล้ายอยู่หรือหรือข้อมูลเซสชันที่สอดคล้ายมีค่าเป็น null จะคืนค่าเป็นเท็จ ไม่งั้นจะคืนค่าเป็นจริง

```
$session = $request->session();
$has = $session->exists('name');
```
โค้ดข้างล่างนี้ใช้เพื่อตรวจสอบว่าข้อมูลเซสชันที่สอดคล้ายอยู่หรือไม่ แต่แต่้ผลลัพธ์จะเป็นจริงเมื่อรายการข้อมูลเป็นค่า null๑٨เครื่องจักรอื่น ๆ แทนที่จะเป็นเท็จ

## ฟังก์ชันช่วย session()
> 2020-12-09 เพิ่มข้อความใหม่

webman มีการให้บริการฟังก์ชันช่วย `session()` ทำหน้าที่เดียวกัน
```php
// รับอินสแสตนซ์เซสชัน
$session = session();
// เทียบเท่ากับ
$session = $request->session();

// รับค่า
$value = session('key', 'default');
// เทียบเท่ากับ
$value = session()->get('key', 'default');
// เทียบเท่ากับ
$value = $request->session()->get('key', 'default');

// กำหนดค่าให้กับเซสชัน
session(['key1'=>'value1', 'key2' => 'value2']);
// เทียบเท่ากับ
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// เทียบเท่ากับ
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
```

## ไฟล์การตั้งค่า
ไฟล์การตั้งค่าเซสชันอยู่ที่ `config/session.php` มีเนื้อหาที่คล้ายกันกับนี้
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class หรือ RedisSessionHandler::class หรือ RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // หากมีการใช้ FileSessionHandler::class ให้ระบุค่าเป็น file
    // หากมีการใช้ RedisSessionHandler::class ให้ระบุค่าเป็น redis
    // หากมีการใช้ RedisClusterSessionHandler::class ให้ระบุค่าเป็น redis_cluster ทีเฉพาะที่เป็นคลัสเตอร์ของ redis
    'type'    => 'file',

    // ให้ใช้ค่าการตั้งค่าที่แตกต่างกัน
    'config' => [
        // ค่าข้อมูลเซสชันที่เป็น file
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // ค่าข้อมูลเซสชันที่เป็น redis
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

    'session_name' => 'PHPSID', // ชื่อคุ๊กกี้ที่มีข้อมูล session_id
    
    // === การตั้งค่าต่อไปนี้ต้อการ webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // เปิดการสครับเป็นอัตโนมัติหรือไม่ เริ่มต้นคือปิด
    'lifetime' => 7*24*60*60,          // เวลาที่เลยรายการเซสชัน
    'cookie_lifetime' => 365*24*60*60, // เวลาที่คุ๊กกี้ session_id เลย
    'cookie_path' => '/',              // เสมที่ของคุ๊กกี้ session_id
    'domain' => '',                    // โดเมนของคุ๊กกี้ session_id
    'http_only' => true,               // เปิด httpOnly หรือไม่ เริ่มต้นคือเปิด
    'secure' => false,                 // เฉพาะ https เท่านั้นที่ระบุเซสชัน หรือไม่ เริ่มต้นคือปิด
    'same_site' => '',                 // ใช้ในการป้องกันการโจมตี CSRF และการติดตามของผู้ใช้ ค่าที่ประเภทstrict/lax/none
    'gc_probability' => [1, 1000],     // โอกาสในการกำจัดข้อมูล session
];
```


> **หมายเหตุ** 
> ตั้งแต่ webman-framework 1.4.0 เป็นต้นมาไฮเลีย SessionHandler ดัปเปลื่อนรูปแบบการอ้างอิงจากเดิม
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> เป็น  
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  



## การตั้งค่าการมีผลอย่างยั่ง
เมื่อ webman-framework < 1.3.14 เริ่มต้นให้ตั้งค่าเวลาหมดอายุของเซสชันทำในไฟล์ `php.ini`

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

สมมุติว่าตั้งเวลาให้มีผลอย่างย่รง 1440 วินาทีตามโคดด้านล่าง
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **ใบเสร็จ**
> สามารถใช้คอมมาร์ด `php --ini` เพื่อหาที่อยู่ของไฟล์ `php.ini` ได้
