# ปลัักอินแอพลิเคชั่น
แต่ละปลัักอินแอพลิเคชั่นคือแอพลิเคชั่นที่สมบูรณ์แบบ โดยโค้ดซอร์สจะอยู่ที่ `{โปรเจคหลัก}/plugin` 

> **เคล็ดลับ**
> ใช้คำสั่ง `php webman app-plugin:create {ชื่อปลัักอิน}` (ต้องใช้ webman/console>=1.2.16) เพื่อสร้างแอพลิเคชั่นปลัักอินในเครื่องในโหมด local ตัวอย่างเช่น `php webman app-plugin:create cms` จะสร้างโครงสร้างไดเรกทอรีดังนี้

```plaintext
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

เราสามารถเห็นได้ว่าแอพลิเคชั่นปลัักอินมีโครงสร้างไดเรกทอรีและแฟ้มตั้งแต่ webman เดียวกัน จริงๆแล้วการพัฒนาปลัักอินแอพลิเคชั่นคล้ายกับการพัฒนาโครงการ webman ธรรมดา โดยที่คุณต้องใส่ความสำคัญในด้านนี้

## ชื่อโฟลเดอร์
โปรเจคและชื่อโฟลเดอร์ปลัักอินปฏิบัติตามข้อกำหนด PSR4 โดยเนื่องจากปลัักอินถูกวางไว้ในโฟลเดอร์ plugin ดังนั้นชื่อโฟลเดอร์จะเริ่มต้นด้วย plugin ตัวอย่างเช่น `plugin\cms\app\controller\UserController` ที่นี่ cms คือไดเรกทอรีหลักของปลัักอิน

## URL การเข้าถึง
เส้นทาง URL ของแอพลิเคชั่นปลัักอินเริ่มต้นด้วย `/app` เช่น `plugin\cms\app\controller\UserController` ที่ URL คือ `http://127.0.0.1:8787/app/cms/user`

## แฟ้มแบบสถานะ
แฟ้มแบบสถานะจัดเก็บที่ `plugin/{ชื่อปลัักอิน}/public` ตัวอย่างเมื่อเข้าถึง `http://127.0.0.1:8787/app/cms/avatar.png` จริงๆแล้วเป็นการเข้าถึงแฟ้ม `plugin/cms/public/avatar.png`

## การกำหนดค่า
การกำหนดค่าของปลัักอินเหมือนกับโปรเจกของ webman ทั้งนี้การกำหนดค่าของปลัักอินโดยทั่วไปจะมีผลกับปลัักอินเท่านั้น และไม่มีผลกับโปรเจกหลัก
เช่นค่า `plugin.cms.app.controller_suffix` มีผลกับคำยของควบคุมของปลัักอินเท่านั้น และไม่มีผลกับโปรเจคหลัก
ตัวอย่างเช่นค่า `plugin.cms.app.middleware` มีผลกับ middleware ของปลัักอินเท่านั้น และไม่มีผลกับโปรเจคหลัก
แต่เนื่องจากเส้นทางรู้เท่านั้นที่เป็นทั่้งโลก ค่ากำหนดของปลัักอินย่อมมีผลกับทั้งโลก

## การเข้าถึงค่าสถานะ
การเข้าถึงค่าของปลัักอินทำได้โดยใช้วิธี `config('plugin.{ชื่อปลัักอิน}.{การกำหนดค่า}')` ตัวอย่างการเข้าถึง `plugin/cms/config/app.php` ทั้งหมดสามารถทำได้โดยใช้ `config('plugin.cms.app')` 
อย่างเดียวกันโปรเจคหลักหรือปลัักอินอื่นๆ สามารถใช้ `config('plugin.cms.xxx')` เพื่อเข้าถึงค่าของ cms ได้

## ค่าที่ไม่รองรับ
ปลัักอินแอพลิเคชั่นไม่รองรับการกำหนดค่า server.php, session.php ไม่รองรับ `app.request_class`, `app.public_path`, `app.runtime_path`

## ฐานข้อมูล
ปลัักอินสามารถกำหนดการตั้งค่าของฐานข้อมูลของตัวเองได้ ตัวอย่างเช่น `plugin/cms/config/database.php` มีรายละเอียดดังนี้
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql เป็นชื่อการเชื่อมต่อ
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'ฐานข้อมูล',
            'username'    => 'ชื่อผู้ใช้',
            'password'    => 'รหัสผ่าน',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin เป็นชื่อการเชื่อมต่อ
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'ฐานข้อมูล',
            'username'    => 'ชื่อผู้ใช้',
            'password'    => 'รหัสผ่าน',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
วิธีการเรียกใช้คือ `Db::connection('plugin.{ชื่อปลัักอิน}.{ชื่อการเชื่อมต่อ}');` เช่น
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```
หากต้องการใช้ฐานข้อมูลของโปรเจคหลัก สามารถใช้โดยตรงได้ เช่น
```php
use support\Db;
Db::table('user')->first();
// สมมติว่าโปรเจคหลักยังกำหนดการเชื่อมต่อ admin
Db::connection('admin')->table('admin')->first();
```

> **เคล็ดลับ**
> thinkorm ก็ใช้เป็นอย่างการเดียวกัน

## Redis
การใช้งาน Redis คล้ายกับฐานข้อมูล ตัวอย่างเช่น `plugin/cms/config/redis.php` มีรายละเอียดดังนี้
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
เช่นกันหากต้องการใช้งานกำหนดค่า Redis ของโปรเจคหลัก
```php
use support\Redis;
Redis::get('key');
// สมมติว่าโปรเจคหลักยังกำหนดการเชื่อมต่อ cache
Redis::connection('cache')->get('key');
```

## บันทึก
การใช้งานคล้ายกับการใช้งานฐานข้อมูล
```php
use support\Log;
Log::channel('plugin.admin.default')->info('ทดสอบ');
```
หากต้องการใช้งานกำหนดค่าบันทึกของโปรเจคหลัก ให้ใช้เดียว
```php
use support\Log;
Log::info('เนื้อหาบันทึก');
// สมมติว่าโปรเจคหลักมีการตั้งค่าบันทึก test
Log::channel('test')->info('เนื้อหาบันทึก');
```

# การติดตั้งและถอดถอนปลัักอินแอพลิเคชั่น
เมื่อติดตั้งปลัักอินแอพลิเคชั่น คุณเพียงแค่คัดลอกโฟลเดอร์ปลัักอินไปยังโโปรเจคหลักที่อยู่ในโฟลเดอร์ `/{โปรเจคหลัก}/plugin` และพิมพ์คำสั่ง reload หรือ restart เพื่อให้มีผล
เมื่อถอดถอนเพียงแค่ลบโดยตรงโฟลเดอร์ปลัักอินที่อยู่ในโฟลเดอร์ `/{โปรเจคหลัก}/plugin` 
