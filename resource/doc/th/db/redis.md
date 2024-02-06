# Redis

คอมโพเนนต์ Redis ของ webman ใช้ [illuminate/redis](https://github.com/illuminate/redis) โดยค่าเริ่มต้น นั่นคือไลบรารี่ redis ของ laravel และมีวิธีใช้เหมือนกับ laravel

ก่อนใช้ `illuminate/redis` คุณต้องติดตั้งส่วนเสริมของ redis สำหรับ `php-cli`

> **โปรดทราบ**
> ใช้คำสั่ง `php -m | grep redis` เพื่อตรวจสอบว่า `php-cli` มีส่วนเสริมของ redis หรือไม่โปรดทราบว่า แม้ว่าคุณติดตั้งส่วนเสริมของ redis สำหรับ `php-fpm`  ก็ไม่จำเป็นต้องบอกว่าคุณสามารถใช้งานใน `php-cli` เพราะ `php-cli` และ `php-fpm` เป็นโปรแกรมที่แตกต่างกัน และสามารถใช้กำหนดค่าของ `php.ini` ที่แตกต่างกัน ใช้คำสั่ง `php --ini` เพื่อเช็คว่าไฟล์กำหนดค่า `php.ini` ที่ใช้ของคุณเป็นไฟล์ชนิดใด

## การติดตั้ง

```php
composer require -W illuminate/redis illuminate/events
```

หลังจากติดตั้งจะต้องทำการรีสตาร์ท (การรีโหลดจะไม่ทำให้เกิดผล)

## การกำหนดค่า
ไฟล์กำหนดค่า redis จะอยู่ที่ `config/redis.php`
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ]
];
```

## ตัวอย่าง
```php
<?php
namespace app\controller;

use support\Request;
use support\Redis;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Redis::set($key, rand());
        return response(Redis::get($key));
    }
}
```

## อินเทอร์เฟซ Redis
```php
Redis::append($key, $value)
Redis::bitCount($key)
Redis::decr($key, $value)
Redis::decrBy($key, $value)
Redis::get($key)
Redis::getBit($key, $offset)
Redis::getRange($key, $start, $end)
Redis::getSet($key, $value)
Redis::incr($key, $value)
Redis::incrBy($key, $value)
Redis::incrByFloat($key, $value)
Redis::mGet(array $keys)
Redis::getMultiple(array $keys)
Redis::mSet($pairs)
Redis::mSetNx($pairs)
Redis::set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
Redis::setBit($key, $offset, $value)
Redis::setEx($key, $ttl, $value)
Redis::pSetEx($key, $ttl, $value)
Redis::setNx($key, $value)
Redis::setRange($key, $offset, $value)
Redis::strLen($key)
Redis::del(...$keys)
Redis::exists(...$keys)
Redis::expire($key, $ttl)
Redis::expireAt($key, $timestamp)
Redis::select($dbIndex)
```
เทียบเท่ากับ
```php
$redis = Redis::connection('default');
$redis->append($key, $value)
$redis->bitCount($key)
$redis->decr($key, $value)
$redis->decrBy($key, $value)
$redis->get($key)
$redis->getBit($key, $offset)
...
```

> **โปรดทราบ**
> การใช้ `Redis::select($db)` อย่างมีอิทธิพล เนื่องจาก webman เป็นเฟรมเวิร์คที่ถาวรและหากคำขอหนึ่งใช้ `Redis::select($db)` เพื่อเปลี่ยนฐานข้อมูลแล้วจะส่งผลกระทบต่อคำขอที่เกิดขึ้นในภายหลัง ดังนั้น คำแนะนำคือ หากมีการใช้งานหลายฐานข้อมูลควรกำหนดค่า `db` ที่แตกต่างกัน เป็นการกำหนดด้วยการเชื่อมต่อกับ redis ที่แตกต่างกัน
## การใช้งานหลายการเชื่อมต่อ Redis
ตัวอย่างไฟล์กำหนดค่า `config/redis.php`
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ],

    'cache' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 1,
    ],

]
```
ค่าเริ่มต้นที่ใช้งานคือการเชื่อมต่อที่กำหนดไว้ในส่วน `default` คุณสามารถใช้ `Redis::connection()` เพื่อเลือกใช้การเชื่อมต่อ redis ได้
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## การกำหนดค่าของคลัสเตอร์
ถ้าแอปพลิเคชันของคุณใช้เซิร์ฟเวอร์ redis แบบคลัสเตอร์ คุณควรกำหนดในไฟล์กำหนดค่าของ redis ด้วยคีย์ clusters เพื่อกำหนดคลัสเตอร์เหล่านั้น:
```php
return [
    'clusters' => [
        'default' => [
            [
                'host'     => 'localhost',
                'password' => null,
                'port'     => 6379,
                'database' => 0,
            ],
        ],
    ],

];
```
โดยค่าเริ่มต้น คลัสเตอร์สามารถทำการแบ่งชิ้นข้อมูลลูกบนโหนดไคลเอนต์ เพื่อเข้าถึงพูลไคลเอนต์และสร้างพื้นที่ความจำที่มีจำนวนมากได้ โดยไคลเอนต์ที่แชร์ไคลเอนต์ไม่จะการจัดการข้อมูลที่ล้มเหลวเป็นหลักหลังใช้งานแคชข้อมูลที่ได้รับจากฐานข้อมูลหลักของอีกฝ่ายหนึ่ง หากต้องการใช้คลัสเตอร์ redis ต้นฉบับคุณต้องระบุดังต่อไปนี้ในคีย์ options ภายใต้ไฟล์กำหนดค่า:
```php
return[
    'options' => [
        'cluster' => 'redis',
    ],

    'clusters' => [
        // ...
    ],
];
```

## การส่งคำสั่งท่อ
เมื่อคุณต้องการส่งคำสั่งไปยังเซิร์ฟเวอร์เพียงหลายคำสั่งคุณควรใช้การส่งคำสั่งท่อ pipeline method ที่รับ lambda ของ redis คุณสามารถสร้างคำสั่งทั้งหมดและส่งไปยังตัวอินแสตนซีของ redis ทั้งหมดในตัวดำเนินการเดียว:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
