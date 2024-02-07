# แคช

webman ใช้ [symfony/cache](https://github.com/symfony/cache) เป็นคอมโพเนนต์แคชตามค่าเริ่มต้น.

> ก่อนที่จะใช้ `symfony/cache` คุณต้องติดตั้งปลั๊กอิน redis สำหรับ `php-cli` ก่อน.

## การติดตั้ง
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

หลังจากติดตั้งเสร็จสิ้น คุณจำเป็นต้อง restart เพื่อให้เป็นผล

## การกำหนดค่า Redis
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
use support\Cache;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Cache::set($key, rand());
        return response(Cache::get($key));
    }
}
```

> **โปรดทราบ**
> ควรเพิ่ม prefix ใน key เพื่อป้องกันการชนกับธุรกิจอื่น ๆ ที่ใช้ redis

## การใช้คอมโพเนนต์แคชอื่น ๆ

คอมโพเนนต์ [ThinkCache](https://github.com/top-think/think-cache) ใช้เพื่ออ้างอิง [ฐานข้อมูลอื่น ๆ](others.md#ThinkCache)
