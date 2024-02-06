# เริ่มต้นอย่างรวดเร็ว

webman ใช้ฐานข้อมูลเริ่มต้นโดยใช้ [illuminate/database](https://github.com/illuminate/database) ซึ่งหมายถึง [ฐานข้อมูลของ laravel](https://learnku.com/docs/laravel/8.x/database/9400) มีวิธีการใช้เหมือนกับ laravel

แน่นอนว่าคุณสามารถดูตัวอย่างการใช้ [การใช้ฐานข้อมูลอื่น ๆ](others.md) ในจังหวะที่ต้องการใช้ ThinkPHP หรือฐานข้อมูลอื่น ๆ

## การติดตั้ง

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

หลังจากการติดตั้งจำเป็นต้องทำการ restart และ ไม่สามารถใช้ reload 

> **เคล็ดลับ**
> ถ้าคุณไม่ได้ต้องการใช้ pagination, อีเวนต์ฐานข้อมูล, หรือ SQL debug คุณสามารถใช้คำสั่งนี้ได้เลย
> `composer require -W illuminate/database`

## การกำหนดค่าฐานข้อมูล
`config/database.php`
```php

return [
    // ฐานข้อมูลเริ่มต้น
    'default' => 'mysql',

    // การกำหนดค่าของฐานข้อมูลต่าง ๆ
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'test',
            'username'    => 'root',
            'password'    => '',
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
            'options' => [
                \PDO::ATTR_TIMEOUT => 3
            ]
        ],
    ],
];
```


## การใช้งาน
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        $default_uid = 29;
        $uid = $request->get('uid', $default_uid);
        $name = Db::table('users')->where('uid', $uid)->value('username');
        return response("hello $name");
    }
}
```
