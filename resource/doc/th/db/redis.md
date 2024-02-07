# Redis

คอมโพเนนต์ redis ของ webman ใช้ [illuminate/redis](https://github.com/illuminate/redis) เป็นค่าเริ่มต้น ซึ่งเป็นไลบรารี redis ของ Laravel โดยการใช้งานมีความคล้ายคลึงกับ Laravel

ก่อนที่จะใช้ `illuminate/redis` คุณต้องติดตั้งเพิ่มเติม redis extension ให้กับ `php-cli`

> **โปรดทราบ** ใช้คำสั่ง `php -m | grep redis` เพื่อตรวจสอบว่า `php-cli` มีตัวขยายของ redis หรือไม่ โดยให้ทราบว่า แม้จะติดตั้งตัวขยายของ redis สำหรับ `php-fpm` แต่จะไม่สามารถใช้งานกับ `php-cli` เนื่องจาก `php-cli` และ `php-fpm` เป็นโปรแกรมที่แตกต่างกัน ซึ่งอาจใช้ค่าตัวกำหนดของ `php.ini` ที่แตกต่างกัน โปรดใช้คำสั่ง `php --ini` เพื่อตรวจสอบว่า `php-cli` ใช้ค่าตัวกำหนด `php.ini` ของตัวไหน

## การติดตั้ง

```php
composer require -W illuminate/redis illuminate/events
```

หลังจากติดตั้งจะต้องรีสตาร์ท (restart) (reload จะไม่ทำงาน)

## การกำหนดค่า

ไฟล์กำหนดค่า redis อยู่ที่ `config/redis.php`

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

## อินเตอร์เฟสของ Redis

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

> **โปรดทราบ** อย่าใช้ `Redis::select($db)` อินเทอร์เฟส เนื่องจาก webman เป็นเฟรมเวิร์กที่ถูกจำพวกข้อมูลไว้อยู่ในหน่วยความจำ ถ้าคำขอหนึ่งคำใช้ `Redis::select($db)` เพื่อสลับฐานข้อมูลหลังจากนั้นจะมีผลกระทบต่อคำขออื่น ๆ แนะนำให้กำหนด `$db` ที่แตกต่างไว้เป็นการตั้งค่าเชื่อมต่อ Redis ที่แตกต่างกัน

## การใช้งานการเชื่อมต่อ Redis หลายตัว

เช่น ไฟล์กำหนดค่า `config/redis.php`
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
ค่าเริ่มต้นที่ใช้งานคือการเชื่อมต่อที่กำหนดไว้ใน `default` คุณสามารถใช้เมธอด `Redis::connection()` เพื่อเลือกใช้การเชื่อมต่อ Redis ที่คุณต้องการ
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## การกำหนดค่าคลัสเตอร์ (Cluster Configuration)

หากโปรแกรมของคุณใช้เซิร์ฟเวอร์ Redis แบบคลัสเตอร์ คุณควรใช้คีย์ `clusters` ในไฟล์กำหนดค่าของ Redis เพื่อกำหนดค่าคลัสเตอร์นั้น
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

โดยค่าเริ่มต้น คลัสเตอร์สามารถรวมโปรแกรมในโหนด ที่อนุญาตให้คุณสร้างกลุ่มของอิชคิวนั่นไว้ และสร้างแม่เหมี่ยวที่ใช้ภาระข้อมูลที่มีอยู่ สำหรับการใช้งานที่ส่วนใหญ่ คุณควรพิจารณาให้เขียนค่าข้มูลเพื่อให้ได้ข้อมูลคัชข้อมูลที่อยู่ในอิชคิว็นแบบตัวเลือกใบไม่ได้ผลไวมากระแตะคิสดย الن من – أي – معلن تل – حكومة

หากต้องการใช้คลัสเตอร์รีดิสแบบไททีท้ คุณจำเป็นต้องจัดการค่าที่ `options` ในไฟล์กำหนดค่าแกมหนี่งตัวหนำ 

```php
return [
    'options' => [
        'cluster' => 'redis',
    ],

    'clusters' => [
        // ...
    ],
];
```

## คำสั่งท่่เซิงคชม์จ (Pipeline Command)

เมื่อคุณต้องการส่งคำสั่งหลาย ๆ ตัวให้กับเซิร์ฟเวอร์ในการดำเนินการคุณควรใช้คำสั่งเซิงคชม์จ (Pipeline Command) คำสั่ง pipeline รับการเส้นตรงของเซิร์ฟเวอร์ที่ใชฺ้นุณของรีดิสตัวอย่างจาก การนสุ่มเอาค่าที่มีเคีพจาการสุ่มหัวงับไว้ที่ค่าที่คุณอยาที่ท่างท่างเกบบับแมา้ใ้บคดหานน้าำไวำชั ด 
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
