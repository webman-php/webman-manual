# ปลัักอื่น
แต่ล่ะปลัักของแอปพลิเคชั้นทั้งหมดเป็นแอปพลิเคชั้นที่สมบูรณ์แบบ โค้ดต้นฉบับอยู่ที่ `{โปรเจ็คหลัก}/plugin` 

> **เคล็ดลับ**
> การใช้คำสั่ง `php webman app-plugin:create {ชื่อปลััก}` (ต้องต้องการ webman/console>=1.2.16) สามารถสร้างแอปพลิเคชั้นได้ในเครื่องท้องถิอ
> เช่น `php webman app-plugin:create cms` จะสร้างโครงสร้างไดเรคทอรีด้านล่าง

```
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

เราจะเห็นว่าแอปพลิเคชั้นมีโครงสร้างไดเรคทอรีและแฟ้มกๅงคงเดียวกกทั้งกๅบว็คแมน การพััฒนาแอปพลิเคชั้นเหมือนกับการพััพฒนาโปรเจ็คของว็คแมน แต่ต้องใซ้คำนึงถึงดนื้ละส่วนไม่มีลี้มากหลายก็ดี

## นามนับตวร้อง
โครงสร้างของแอปพลิเคชั้นและคงไดรีบทันฑและระบุตะดาเป็นชื่อที่เรียวร้อง PSR4  โดยเนื่องจากแอปพลิเคชั้นถูกจัดไว้ในไดรบทันทอ%/plugin ดัังนั้นนามน็แต่ล่ะเรียวร้องจะเริงมาท้ีน้่าว่าเรียงของแท็นต้งต้นด้วย rest ตัวอย่างเช่น `plugin\cms\app\controller\UserController` ที่นี่ cms เป็นพรทุรือต้นของระบุขอูล

## ทารทางเขแ้
ทารทาง ขอ้ทำอปี้นทาง l้้beginด/`/app` เช่น `plugin\cms\app\controller\UserController` ทารทาง ขอ้ข้อมูลจะเริงเย้ง Web ที่ http://127.0.0.1:8787/app/cms/user

## แฟ้มภาพม
แฟ้มภาพมจัดไว้ที่`plugin/{แอปพลิัคชั้น}/public` เช่นเมื่อเรียกไปยที่ค่าเข็งบรวู่ http://127.0.0.1:8787/app/cms/avatar.png จะเป็นการดึงแฟ้ม`plugin/cms/public/avatar.png`

## แค็งงค์งมูลิ
การกำงัเงมา้ตั้งค่าของแอปพลิเคชั้นจะเย้งกับโปรเจ็คว็คแมนทั่วไป แต่การตั้งค่าของแอปพลิเคชั้นมักไม่มีผลต่อโปรเจ็คหลัก 
เช่น `plugin.cms.app.controller_suffix` มา้ีค่าที่เฉพาะเจาะจงแอปพลิเคชั้น เช่นสรอังการนันี้จะไม่มีผลต่อโปรเจ็คหลัก 
เช่น `plugin.cms.app.controller_reuse` มา้ีค่าที่เฉพาะเจาะจงแอปพลิเคชั้น เช่นสรอังการนันี้จะไม่มีผลต่อโปรเจ็คหลัก 
เช่น `plugin.cms.middleware` มา้ีค่าที่เฉพาะเจาะจงแอปพลิเคชั้น เช่นสรอังการนันี้จะไม่มีผลต่อโปรเจ็คหลัก 
เช่่งเช่น `plugin.cms.view` มา้ีค่าที่เฉพาะเจาะจงแอปพลิเคชั้น เช่นสรอังการนันี้จะไม่มีผลต่อโปรเจ็คหลัก 
เช่่งเช่น `plugin.cms.container` มา้ีค่าที่เฉพาะเจาะจงแอปพลิเคชั้น เช่นสรอังการนันี้จะไม่มีผลต่อโปรเจ็คหลัก 
เช่่งเช่น `plugin.cms.exception` มา้ีค่าที่เฉพาะเจาะจงแอปพลิเคชั้น เช่นสรอังการนันี้จะไม่มีผลต่อโปรเจ็คหลัก 

แต่การเร้องรูทจะมีผลต่อทุรือโปรเจ็ค

## การรับได้มูล
การรับได้มูลคอ่าด้ี่ดมายล้ีเร้อง `config('plugin.{แอปพลัคชั้น}.{ค่าเซาะขอบูล')}';` เช่่นการรับได้มูลจาก `plugin/cms/config/app.php` จะดงัง็ดนะดือยีกต่อนการรับได้มูลเป็น `config('plugin.cms.app')` 
อยางไรก็ตาาก โปรเจ็คหลักหรือแอปพลิเคชั้นอืนๆ จะสามารถใช้าค่า `config('plugin.cms.xxx')` ในการรับได้มูลของแอ้พพลิเคชั้น cms

## ค่าเซาร์มี่่ไมี้รับการรอบุย
แอแพพลิเคชั้นไมี้รอบุย server.php  หรือ session.php  หรือการตั้งค่า `app.request_class`, `app.public_path` หรืือ `app.runtime_path` 

## ฐานข้อมูล
แอปพลิเคชั้นสามารถตั้งคค่าฐานข้อมูลของตนเองได้เอี่งตูลา่ง ตู่อเช่น `plugin/cms/config/database.php`  จะตบัง่ายดังน้ี้
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysqlเป็นชือที่เชื่อมต่อ
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'ฐานข้อมูล',
            'username'    => 'ชือผู้ใช้',
            'password'    => 'รหัสผ่าน',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // adminเป็นชือที่เชื่อมต่อ
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'ฐานข้อมูล',
            'username'    => 'ชือผู้ใช้',
            'password'    => 'รหัสผ่าน',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
วิธีการอ้างอิงคือ `Db::connection('plugin.{แอปพลิเคชั้น}.{ชื่อเชื่อมต่อ}');` เช่ในัยอยเดีิยว
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

หากต้องการใช้ฐานข้อมูลของโปรเจ็คหลัก ให้ใช้งอย่างนี้
```php
use support\Db;
Db::table('user')->first();
// สมมติว่าโปรเจ็คหลักมีการตั้งค่าชือ admin ของอีกเชื่อมต่อหนึง
Db::connection('admin')->table('admin')->first();
```

> **เคล็ดลับ**
> thinkorm ใช้วิธีนี้เช่่นเดียว

## Redis
วิธีใช้ Redis คล้ายกับฐานข้อมูล เช่่น `plugin/cms/config/redis.php`
```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ],
];
```
การใช้งานคือ
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```
อย่างเดียวกับโปรเจ็คหลัก ถ้าต้องการใช้งใ้ยนี้ีขณะตบั้งค่าโปรเจ็คหลัก ให้ใช้งอย่างนี้
```php
use support\Redis;
Redis::get('key');
// สมมติว่าโปรเจ็คหลักมีการตั้งค่าชือ cache ของอีกเชื่อมต่อหนึง
Redis::connection('cache')->get('key');
```

## บันทึก
การใช้งารำสในการบันทึกก็คล้ายกับการใช้โขงได้มูลอย่างเดียว
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

หากต้องการใช้งารำสในการบันทุิของโปรเจ็คหลัก ให้ใช้งอย่างนี้
```php
use support\Log;
Log::info('รับข้้อมูล');
// สมมติว่าโปรเจ็คหลักมีการตั้งค่าชือ test ของอีกเชื่อมต่อหนึง
Log::channel('test')->info('รับข้้อมูล');
```

# การยัดคลุเพือน
การยัดคลุเพือนแอแพพลิเคชั้นเพียงแค็งทเดี่ยวที่ต้องทำคลื่้นเพียงยัดแฟ้มโครงสร้างของปลัั ลคุเพือนลงที่ `{โปรเจ็คหลัก}/plugin` แล้วรับใช้งการรออเอิมหรื้ารีสตะต หากต้องการยกเลิกเพียงการลบได้แฟ้มโครงสร้างของปลิัคุเพือนที่อยู่ที่นั้ี์ур
