# ปลั๊กอิน webman/console สำหรับคำสั่งในโหมดคอนโซล

`webman/console` มีพื้นฐานบน `symfony/console`

> พื้นฐานต้องการ webman>=1.2.2 และ webman-framework>=1.2.1

## การติดตั้ง
 
```sh
composer require webman/console
```

## คำสั่งที่รองรับ
**วิธีใช้**  
`php webman คำสั่ง` หรือ `php webman คำสั่ง`  
เช่น `php webman version` หรือ `php webman version`

## คำสั่งที่รองรับ
### version
**พิมพ์เลขเวอร์ชั่นของ webman**

### route:list
**พิมพ์การกำหนดเส้นทางปัจจุบัน**

### make:controller
**สร้างไฟล์ควบคุม** 
เช่น `php webman make:controller admin` จะสร้าง `app/controller/AdminController.php`
เช่น `php webman make:controller api/user` จะสร้าง `app/api/controller/UserController.php`

### make:model
**สร้างไฟล์โมเดล**
เช่น `php webman make:model admin` จะสร้าง `app/model/Admin.php`
เช่น `php webman make:model api/user` จะสร้าง `app/api/model/User.php`

### make:middleware
**สร้างไฟล์ middleware**
เช่น `php webman make:middleware Auth` จะสร้าง `app/middleware/Auth.php`

### make:command
**สร้างไฟล์คำสั่งที่กำหนดเอง**
เช่น `php webman make:command db:config` จะสร้าง `app\command\DbConfigCommand.php`

### plugin:create
**สร้างปลั๊กอินพื้นฐาน**
เช่น `php webman plugin:create --name=foo/admin` จะสร้างไดเรกทอรี `config/plugin/foo/admin` และ `vendor/foo/admin` 
ดูเพิ่มเติมที่ [สร้างปลั๊กอินพื้นฐาน](/doc/webman/plugin/create.html)

### plugin:export
**ส่งออกปลั๊กอินพื้นฐาน**
เช่น `php webman plugin:export --name=foo/admin` 
ดูเพิ่มเติมที่ [สร้างปลั๊กอินพื้นฐาน](/doc/webman/plugin/create.html)

### plugin:export
**ส่งออกปลั๊กอินแอปพลิเคชัน**
เช่น `php webman plugin:export shop`
ดูเพิ่มเติมที่ [ปลั๊กอินแอปพลิเคชัน](/doc/webman/plugin/app.html)

### phar:pack
**แพ็คเอาท์โปรเจค webman เป็นไฟล์ phar**
ดูเพิ่มเติมที่ [การแพ็คเอาท์โปรเจคเป็นไฟล์ phar](/doc/webman/others/phar.html)
> คุณลักษณะนี้ต้องการ webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5

## คำสั่งที่กำหนดเอง
ผู้ใช้สามารถกำหนดคำสั่งเอง เช่น ด้านล่างนี้เป็นตัวอย่างของคำสั่งสำหรับพิมพ์การกำหนดของฐานข้อมูล

* ทำ `php webman make:command config:mysql`
* เปิด `app/command/ConfigMySQLCommand.php` และแก้ไขเป็นดังนี้

```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigMySQLCommand extends Command
{
    protected static $defaultName = 'config:mysql';
    protected static $defaultDescription = 'แสดงการกำหนดของเซิร์ฟเวอร์ MySQL ปัจจุบัน';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('ข้อมูลการกำหนดของ MySQL มีดังนี้：');
        $config = config('database');
        $headers = ['ชื่อ', 'ค่าเริ่มต้น', 'ไดรเวอร์', 'โฮสต์', 'พอร์ต', 'ฐานข้อมูล', 'ชื่อผู้ใช้', 'รหัสผ่าน', 'ซ็อกเก็ตข้อมูล', 'เซ็ตข้อมูล', 'คอลเลชัน', 'คำนำหน้า', 'เข้ม', 'เหมาะสำหรับ', 'ตารางรายการ', 'โหมด SSL'];
        $rows = [];
        foreach ($config['connections'] as $name => $db_config) {
            $row = [];
            foreach ($headers as $key) {
                switch ($key) {
                    case 'name':
                        $row[] = $name;
                        break;
                    case 'default':
                        $row[] = $config['default'] == $name ? 'true' : 'false';
                        break;
                    default:
                        $row[] = $db_config[$key] ?? '';
                }
            }
            if ($config['default'] == $name) {
                array_unshift($rows, $row);
            } else {
                $rows[] = $row;
            }
        }
        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();
        return self::SUCCESS;
    }
}
```
  
## การทดสอบ

ในโหมดคอนโซลให้รัน `php webman config:mysql`

ผลลัพธ์จะเป็นดังนี้：
```
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| ชื่อ  | ค่าเริ่มต้น | ไดรเวอร์ | โฮสต์      | พอร์ต | ฐานข้อมูล | ชื่อผู้ใช้ | รหัสผ่าน | ซ็อกเก็ตข้อมูล | เซ็ตข้อมูล | คอลเลชัน       | คำนำหน้า | เข้ม | เหมาะสำหรับ | ตารางรายการ | โหมด SSL |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## ข้อมูลอื่น ๆ อ่านเพิ่มเติม
http://www.symfonychina.com/doc/current/components/console.html
