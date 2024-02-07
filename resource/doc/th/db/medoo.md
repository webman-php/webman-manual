## เมดู

เมดูเป็นปลัักอินการดำเนินการฐานข้อมูลที่เบาเบา  [เว็บไซต์เมดู](https://medoo.in/)。

## การติดตั้ง
`composer require webman/medoo`

## การกำหนดค่าฐานข้อมูล
ตำแหน่งไฟล์การกำหนดค่าอยู่ที่ `config/plugin/webman/medoo/database.php`

## การใช้งาน
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Medoo\Medoo;

class Index
{
    public function index(Request $request)
    {
        $user = Medoo::get('user', '*', ['uid' => 1]);
        return json($user);
    }
}
```

> **คำแนะนำ**
> `Medoo::get('user', '*', ['uid' => 1]);`
> เทียบเท่ากับ
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## การกำหนดค่าฐานข้อมูลหลายฐานข้อมูล

**การกำหนดค่า**  
ใน `config/plugin/webman/medoo/database.php` เพิ่มการกำหนดค่าหนึ่งโดยใช้ key อะไรก็ได้ ในที่นี้ใช้ 'other' เป็นตัวอย่าง

```php
<?php
return [
    'default' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
    // เพิ่มการกำหนดค่า 'other'
    'other' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
];
```

**การใช้งาน**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## เอกสารอย่างละเอียด
ดูที่ [เอกสารเมดู](https://medoo.in/api/select) อย่างละเอียด
