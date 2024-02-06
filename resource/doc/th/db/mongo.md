เอกสารของ webman เน้นการใช้งาน MongoDB ซึ่งใช้ [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) เป็นคอมโพเนนต์ MongoDB ซึ่งถูกแยกออกมาจากโปรเจค Laravel และใช้ได้เหมือนกับ Laravel

ก่อนที่จะใช้ `jenssegers/mongodb` จะต้องติดตั้ง extionsion MongoDB ให้กับ `php-cli` ก่อน

> ใช้คำสั่ง `php -m | grep mongodb` เพื่อตรวจสอบว่า `php-cli` ได้ติดตั้ง extionsion MongoDB หรือไม่ โปรดทราบว่า หากคุณได้ติดตั้ง extionsion MongoDB ให้กับ `php-fpm` แล้ว ก็ไม่ได้หมายความว่าคุณสามารถใช้งานได้ใน `php-cli` เนื่องจาก `php-cli` และ `php-fpm` เป็นแอปพลิเคชันที่แตกต่างกัน และอาจจะใช้ `php.ini` ที่แตกต่างกัน โปรดใช้คำสั่ง `php --ini` เพื่อดูว่า `php-cli` ของคุณใช้ `php.ini` ไฟล์เวอร์ชันไหน

## การติดตั้ง

PHP>7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

หลังจากที่ติดตั้งแล้ว จำเป็นต้อง restart (reload ไม่ทำงาน)

## การกำหนดค่า
ใน `config/database.php` เพิ่ม `mongodb` connection ที่เป็นเช่นนี้:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ... ตรงนี้ข้ามการกำหนดค่าต่างๆ ...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // ที่นี่คุณสามารถส่งการตั้งค่าเพิ่มเติมไปที่ Mongo Driver Manager
                // ดูได้ที่ https://www.php.net/manual/en/mongodb-driver-manager.construct.php ที่ "Uri Options" เพื่อดูรายการของพารามิเตอร์ที่คุณสามารถใช้

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## ตัวอย่าง
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        Db::connection('mongodb')->collection('test')->insert([1,2,3]);
        return json(Db::connection('mongodb')->collection('test')->get());
    }
}
```

## สำหรับข้อมูลเพิ่มเติม
https://github.com/jenssegers/laravel-mongodb
