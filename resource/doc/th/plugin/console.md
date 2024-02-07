# ปลัั้กอินสำหรับคำสั่ง webman/console

`webman/console` ซึ่งขึ้นอยู่กับ `symfony/console`

>  ปลัั้กอินต้องการ webman>=1.2.2 webman-framework>=1.2.1

## การติดตั้ง
 
```sh
composer require webman/console
```

## คำสั่งที่รองรับ
**วิธีใช้**  
`php webman คำสั่ง` หรือ `php webman คำสั่ง` 
ตัวอย่างเช่น `php webman version` หรือ `php webman version`

## คำสั่งที่รองรับ
### version
**พิมพ์เวอร์ชันของ webman**

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
**สร้างปลัั้กอินพื้นฐาน**
เช่น `php webman plugin:create --name=foo/admin` จะสร้างไดเรกทอรี `config/plugin/foo/admin` และ `vendor/foo/admin`
ดูเพิ่มเติมที่ [สร้างปลัั้กอินพื้นฐาน](/doc/webman/plugin/create.html)

### plugin:export
**ส่งออกปลัั้กอินพื้นฐาน**
เช่น `php webman plugin:export --name=foo/admin` 
ดูเพิ่มเติมที่ [สร้างปลัั้กอินพื้นฐาน](/doc/webman/plugin/create.html)

### plugin:export
**ส่งออกปลัั้กอินแอปพลิเคชัน**
เช่น `php webman plugin:export shop`
ดูเพิ่มเติมที่ [ปลัั้กอินแอปพลิเคชัน](/doc/webman/plugin/app.html)

### phar:pack
**บีบอัดโปรเจค webman เป็นไฟล์ phar**
ดูเพิ่มเติมที่ [การบีบอัด phar](/doc/webman/others/phar.html)
> คุณลักษณะนี้ต้องการ webman>=1.2.4 webman-framework>=1.2.4 webman\console>=1.0.5

## คำสั่งที่กำหนดเอง
ผู้ใช้สามารถกำหนดเองได้ เช่น ต่อไปนี้คือคำสั่งแสดงการกำหนดค่าฐานข้อมูล

* ป้อน `php webman make:command config:mysql`
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
    protected static $defaultDescription = 'แสดงการกำหนดค่าเซิร์ฟเวอร์ MySQL ปัจจุบัน';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('ข้อมูลการกำหนดค่าเซิร์ฟเวอร์ MySQL คือ :');
        $config = config('database');
        $headers = ['name', 'default', 'driver', 'host', 'port', 'database', 'username', 'password', 'unix_socket', 'charset', 'collation', 'prefix', 'strict', 'engine', 'schema', 'sslmode'];
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

รันคำสั่งใน Command line `php webman config:mysql`

ผลลัพธ์จะเป็นดังนี้:
```shell
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## อ่านข้อมูลเพิ่มเติมได้ที่
http://www.symfonychina.com/doc/current/components/console.html
