# แคช

ใน webman มีการใช้ [symfony/cache](https://github.com/symfony/cache)  เป็นคอมโพเนนต์แคชเริ่มต้น

> ก่อนที่จะใช้ `symfony/cache` จำเป็นต้องติดตั้งเพิ่มเติม redis extension สำหรับ `php-cli`

## การติดตั้ง
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

หลังจากติดตั้งจำเป็นต้องทำการ restart (reload จะไม่มีผล)

## การตั้งค่า Redis
ไฟล์ตั้งค่า Redis จะอยู่ที่ `config/redis.php`
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
> ควรเพิ่ม prefix ใน key เพื่อหลีกเลี่ยงการชนกับธุรกิจอื่นๆที่ใช้ redis

## การใช้คอมโพเนนต์แคชอื่นๆ
การใช้งาน [ThinkCache](https://github.com/top-think/think-cache) สามารถดูได้ที่ [ฐานข้อมูลอื่นๆ](others.md#ThinkCache)
