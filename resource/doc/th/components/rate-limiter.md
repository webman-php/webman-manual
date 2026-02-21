# ตัวจำกัดอัตรา

ตัวจำกัดอัตรา webman รองรับการจำกัดด้วย annotation
รองรับไดรเวอร์ apcu, redis และ memory

## ที่อยู่ซอร์สโค้ด

https://github.com/webman-php/limiter

## การติดตั้ง

```
composer require webman/limiter
```

## การใช้งาน

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\annotation\Limit;

class UserController
{

    #[Limit(limit: 10)]
    public function index(): string
    {
        // ค่าเริ่มต้นคือจำกัดตาม IP ช่วงเวลาเริ่มต้น 1 วินาที
        return 'สูงสุด 10 คำขอต่อ IP ต่อวินาที';
    }

    #[Limit(limit: 100, ttl: 60, key: Limit::UID)]
    public function search(): string
    {
        // key: Limit::UID จำกัดตาม ID ผู้ใช้ ต้องมี session('user.id') ไม่ว่าง
        return 'สูงสุด 100 การค้นหาต่อผู้ใช้ต่อ 60 วินาที';
    }

    #[Limit(limit: 1, ttl: 60, key: Limit::SID, message: '1 อีเมลต่อคนต่อนาทีเท่านั้น')]
    public function sendMail(): string
    {
        // key: Limit::SID จำกัดตาม session_id
        return 'ส่งอีเมลสำเร็จ';
    }

    #[Limit(limit: 100, ttl: 24*60*60, key: 'coupon', message: 'คูปองวันนี้หมดแล้ว กรุณาลองใหม่พรุ่งนี้')]
    #[Limit(limit: 1, ttl: 24*60*60, key: Limit::UID, message: 'ผู้ใช้แต่ละคนรับคูปองได้วันละ 1 ครั้งเท่านั้น')]
    public function coupon(): string
    {
        // key: 'coupon' คีย์กำหนดเองสำหรับจำกัดทั่วโลก สูงสุด 100 คูปองต่อวัน
        // จำกัดตาม ID ผู้ใช้ด้วย ผู้ใช้แต่ละคนรับคูปองได้วันละ 1 ครั้ง
        return 'ส่งคูปองสำเร็จ';
    }

    #[Limit(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: 'สูงสุด 5 SMS ต่อหมายเลขต่อวัน')]
    public function sendSms2(): string
    {
        // เมื่อ key เป็นตัวแปร ใช้ [คลาส, เมธอด_static] เช่น [UserController::class, 'getMobile'] ใช้ค่าที่คืนจาก UserController::getMobile() เป็นคีย์
        return 'ส่ง SMS สำเร็จ';
    }

    /**
     * คีย์กำหนดเอง ดึงหมายเลขโทรศัพท์ ต้องเป็นเมธอด static
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[Limit(limit: 1, ttl: 10, key: Limit::IP, message: 'จำกัดอัตรา', exception: RuntimeException::class)]
    public function testException(): string
    {
        // ข้อยกเว้นเริ่มต้นเมื่อเกิน: support\limiter\RateLimitException เปลี่ยนได้ผ่านพารามิเตอร์ exception
        return 'ok';
    }

}
```

**หมายเหตุ**

* ใช้อัลกอริทึมหน้าต่างคงที่
* ช่วงเวลา ttl เริ่มต้น: 1 วินาที
* ตั้งค่าหน้าต่างผ่าน ttl เช่น `ttl:60` สำหรับ 60 วินาที
* มิติจำกัดเริ่มต้น: IP (เริ่มต้น `127.0.0.1` ไม่จำกัด ดูการกำหนดค่าด้านล่าง)
* ในตัว: จำกัด IP, UID (ต้องมี `session('user.id')` ไม่ว่าง), SID (ตาม `session_id`)
* เมื่อใช้ nginx proxy ส่ง header `X-Forwarded-For` สำหรับจำกัด IP ดู [nginx proxy](../others/nginx-proxy.md)
* เรียก `support\limiter\RateLimitException` เมื่อเกิน กำหนดคลาสข้อยกเว้นผ่าน `exception:xx`
* ข้อความข้อผิดพลาดเริ่มต้นเมื่อเกิน: `Too Many Requests` กำหนดข้อความผ่าน `message:xx`
* ข้อความข้อผิดพลาดเริ่มต้นแก้ไขได้ผ่าน [การแปล](translation.md) อ้างอิง Linux:

```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Too Many Requests' => '请求频率受限'
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

## API

บางครั้งนักพัฒนาต้องการเรียกตัวจำกัดอัตราโดยตรงในโค้ด ดูตัวอย่างต่อไปนี้:

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // ใช้ mobile เป็นคีย์ที่นี่
        Limiter::check($mobile, 5, 24*60*60, 'สูงสุด 5 SMS ต่อหมายเลขต่อวัน');
        return 'ส่ง SMS สำเร็จ';
    }
}
```

## การกำหนดค่า

**config/plugin/webman/limiter/app.php**

```
<?php

use support\limiter\RateLimitException;

return [
    'enable' => true,
    'driver' => 'auto', // auto, apcu, memory, redis
    'stores' => [
        'redis' => [
            'connection' => 'default',
        ]
    ],
    // IP เหล่านี้ไม่ถูกจำกัด (มีผลเฉพาะเมื่อ key เป็น Limit::IP)
    'ip_whitelist' => [
        '127.0.0.1',
    ],
    'exception' => RateLimitException::class
];
```

* **enable**: เปิดใช้งานการจำกัดอัตรา
* **driver**: หนึ่งใน `auto`, `apcu`, `memory`, `redis`; `auto` เลือกอัตโนมัติระหว่าง `apcu` (ลำดับความสำคัญ) และ `memory`
* **stores**: การกำหนดค่า Redis `connection` ตรงกับคีย์ใน `config/redis.php`
* **ip_whitelist**: IP ใน whitelist ไม่ถูกจำกัด (มีผลเฉพาะเมื่อ key เป็น `Limit::IP`)

## การเลือก driver

**memory**

* คำอธิบาย
  ไม่ต้องติดตั้งส่วนขยาย ประสิทธิภาพดีที่สุด

* ข้อจำกัด
  จำกัดเฉพาะกระบวนการปัจจุบัน ไม่แชร์ข้อมูลระหว่างกระบวนการ ไม่รองรับการจำกัดคลัสเตอร์

* สถานการณ์การใช้งาน
  สภาพแวดล้อมพัฒนา Windows; ธุรกิจที่ไม่ต้องจำกัดเข้มงวด; ป้องกันการโจมตี CC

**apcu**

* การติดตั้งส่วนขยาย
  ต้องติดตั้งส่วนขยาย apcu และตั้งค่า php.ini:

```
apc.enabled=1
apc.enable_cli=1
```

หาตำแหน่ง php.ini ด้วยคำสั่ง `php --ini`

* คำอธิบาย
  ประสิทธิภาพดีมาก รองรับการแชร์ข้อมูลหลายกระบวนการ

* ข้อจำกัด
  ไม่รองรับคลัสเตอร์

* สถานการณ์การใช้งาน
  สภาพแวดล้อมพัฒนาใดๆ; จำกัดเซิร์ฟเวอร์เดี่ยวในโปรดักชัน; คลัสเตอร์ที่ไม่ต้องจำกัดเข้มงวด; ป้องกันการโจมตี CC

**redis**

* การพึ่งพา
  ต้องติดตั้งส่วนขยาย redis และคอมโพเนนต์ Redis คำสั่งติดตั้ง:

```
composer require -W webman/redis illuminate/events
```

* คำอธิบาย
  ประสิทธิภาพต่ำกว่า apcu รองรับการจำกัดแม่นยำเซิร์ฟเวอร์เดี่ยวและคลัสเตอร์

* สถานการณ์การใช้งาน
  สภาพแวดล้อมพัฒนา; เซิร์ฟเวอร์เดี่ยวในโปรดักชัน; สภาพแวดล้อมคลัสเตอร์
