# ฐานข้อมูล
ปลั๊กอินสามารถกำหนดฐานข้อมูลของตนเองได้ เช่น `plugin/foo/config/database.php` ด้วยเนื้อหาดังนี้
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // ชื่อการเชื่อมต่อ mysql
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'ชื่อฐานข้อมูล',
            'username'    => 'ชื่อผู้ใช้',
            'password'    => 'รหัสผ่าน',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // ชื่อการเชื่อมต่อ admin
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'ชื่อฐานข้อมูล',
            'username'    => 'ชื่อผู้ใช้',
            'password'    => 'รหัสผ่าน',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
การอ้างถึงโดยใช้ `Db::connection('plugin.{ชื่อปลั๊ก}.{ชื่อการเชื่อมต่อ}');` เช่น
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

หากต้องการใช้ฐานข้อมูลของโปรเจคหลัก สามารถใช้ได้โดยตรง เช่น
```php
use support\Db;
Db::table('user')->first();
// สมมติว่าโปรเจคหลักกำหนดการเชื่อมต่อ admin 
Db::connection('admin')->table('admin')->first();
```

## กำหนดฐานข้อมูลสำหรับ Model

เราสามารถสร้างคลาส Base สำหรับ Model โดยใช้ `$connection` ระบุการเชื่อมต่อฐานข้อมูลของปลั๊กเอง เช่น
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
ด้วยวิธีนี้ Model ทั้งหมดในปลั๊กจะสืบทอดจาก Base และจะใช้ฐานข้อมูลของปลั๊กเองโดยอัตโนมัติ

## การใช้งานกำหนดค่าฐานข้อมูลอย่างมีประสิทธิภาพ
แน่นอนเราสามารถนำค่ากำหนดของฐานข้อมูลของโปรเจคหลักมาใช้ซ้ำได้ หรือหากใช้ [webman-admin](https://www.workerman.net/plugin/82) ยังสามารถนำค่ากำหนดของ [webman-admin](https://www.workerman.net/plugin/82) มาใช้ซ้ำได้ด้วย เช่น
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
