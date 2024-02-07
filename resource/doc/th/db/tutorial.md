# การเริ่มต้นอย่างรวดเร็ว

webman มาพร้อมกับฐานข้อมูลที่ใช้ด้วย [illuminate/database](https://github.com/illuminate/database) ซึ่งคือ [ฐานข้อมูลของ laravel](https://learnku.com/docs/laravel/8.x/database/9400) การใช้งานเหมือนกับ laravel

แน่นอนคุณสามารถอ้างอิงถึง [การใช้งาน component ฐานข้อมูลอื่น ๆ](others.md) ในบทที่มีชื่อว่าอื่น ๆ เช่น การใช้งานกับ ThinkPHP หรือฐานข้อมูลอื่น ๆ

## การติดตั้ง

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

หลังจากที่ติดตั้งเสร็จแล้วจะต้องทำการ restart และไม่สามารถใช้ reload

> **คำแนะนำ**
> หากคุณไม่ต้องการใช้งาน pagination ฐานข้อมูล, ส่ิง์กับเหตุการณ์ในฐานข้อมูล, หรือพิมพ์คำสั่ง SQL, คุณสามารถทำการ ใช้คำสั่ง
> `composer require -W illuminate/database`

## การตั้งค่าฐานข้อมูล
`config/database.php`
```php
return [
    // ค่าเริ่มต้นของฐานข้อมูล
    'default' => 'mysql',

    // การตั้งค่าฐานข้อมูลต่าง ๆ
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
