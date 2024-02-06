# ฐานข้อมูล
ปลั๊กอินสามารถกำหนดฐานข้อมูลของตนเองได้ เช่น `plugin/foo/config/database.php` โดยเนื้อหาจะมีดังนี้
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
การอ้างถึงจะเป็นแบบ `Db::connection('plugin.{ชื่อปลั๊ก}.{ชื่อการเชื่อมต่อ}');` ตัวอย่างเช่น
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

หากต้องการใช้งานฐานข้อมูลของโปรเจคหลัก สามารถใช้งานได้โดยตรงเช่น
```php
use support\Db;
Db::table('user')->first();
// สมมติว่าโปรเจคหลักมีการกำหนดการเชื่อมต่อ admin
Db::connection('admin')->table('admin')->first();
```

## กำหนดฐานข้อมูลสำหรับ Model
เราสามารถสร้างคลาส Base สำหรับ Model โดยใช้ `$connection` เพื่อระบุการเชื่อมต่อฐานข้อมูลของปลั๊กอินเอง เช่น

```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.foo.mysql';

}
```

ด้วยการนี้ Model ทั้งหมดในปลั๊กอินจะสืบทอดจาก Base และจะใช้งานฐานข้อมูลของปลั๊กอินเองโดยอัตโนมัติ

## การใช้งานการกำหนดฐานข้อมูลใหม่
แน่นอนเราสามารถใช้กำหนดฐานข้อมูลจากโปรเจคหลัก หรือถ้ามีการทำการเชื่อมต่อ [webman-admin](https://www.workerman.net/plugin/82) ก็สามารถใช้กำหนดฐานข้อมูลจาก [webman-admin](https://www.workerman.net/plugin/82) ได้ เช่น
```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.admin.mysql';

}
```
