# กระบวนการสร้างและเผยแพร่ปลั๊กอินพื้นฐาน

## หลักการ
1. ในที่นี้จะใช้ปลั๊กอิน cross-domain เป็นตัวอย่าง ปลั๊กอินประกอบด้วยสามส่วน คือ ไฟล์โปรแกรม middleware, ไฟล์การกำหนด middleware.php และไฟล์ Install.php ที่สร้างโดยคำสั่ง
2. เราใช้คำสั่งเพื่อบีบอัดสามไฟล์และเผยแพร่ไปยัง composer
3. เมื่อผู้ใช้ติดตั้งปลั๊กอิน cross-domain ไฟล์ Install.php จะคัดลอกไฟล์โปรแกรม middleware และไฟล์กำหนดไปยัง `{โปรเจคหลัก}/config/plugin` เพื่อให้ webman โหลด การให้ผลให้ cross-domain middleware ทำงานโดยอัตโนมัติ
4. เมื่อผู้ใช้ลบปลั๊กอินนั้นผ่าน composer Install.php จะลบไฟล์โปรแกรม middleware และไฟล์กำหนดที่เกี่ยวข้อง เพื่อให้ปลั๊กอินถอดออกโดยอัตโนมัติ

## มาตรฐาน
1. ชื่อปลั๊กอินประกอบด้วยสองส่วน คือ "ผู้ผลิต" และ "ชื่อปลั๊กอิน" เช่น `webman/push` ที่สอดคล้องกับชื่อของ composer package
2. ไฟล์กำหนดปลั๊กอินถูกเก็บไว้ใน `config/plugin/ผู้ผลิต/ชื่อปลั๊กอิน/` (คำสั่งคอนโซลจะสร้างไดเรกทอรีกำหนดโดยอัตโนมัติ) ถ้าปลั๊กอินไม่ต้องการการกำหนด จะต้องลบไดเรกทอรีกำหนดโดยอัตโนมัติ
3. ไดเรกทอรีกำหนดปลั๊กอินเฉพาะสนับสนุน app.php การกำหนดหลักของปลั๊กอิน, bootstrap.php การกำหนดการเริ่มต้นกระบวนการ, route.php การกำหนดเส้นทาง, middleware.php การกำหนด middleware, process.php การกำหนดกระบวนการเอง, database.php การกำหนดฐานข้อมูล, redis.php การกำหนด redis, thinkorm.php การกำหนด thinkorm การกำหนด การกำหนดเหล่านี้จะถูก webman จดจำอัตโนมัติ
4. ปลั๊กอินใช้วิธีนี้เพื่อเข้าถึงการกำหนด `config('plugin.ผู้ผลิ.ชื่อปลั๊กอิน.ไฟล์กำหนด.รายการกำหนดเฉพาะ');` เช่น `config('plugin.webman.push.app.app_key')`
5. ถ้าปลั๊กอินมีการกำหนดฐานข้อมูลของตัวเอง จะถูกเข้าถึงโดยวิธีนี้ `illuminate/database` เป็น `Db::connection('plugin.ผู้ผลิ.ชื่อปลั๊กอิน.การเชื่อมต่อเฉพาะ')` และ `thinkrom` คือ `Db::connect('plugin.ผู้ผลิ.ชื่อปลั๊กอิน.การเชื่อมต่อเฉพาะ')`
6. ถ้าปลั๊กอินต้องการวางไฟล์ธุรกิจในไดเรกทอรี `app/` จะต้องมั่นใจว่าไม่มีการชนกับโปรเจคของผู้ใช้และปลั๊กอินอื่น
7. ปลั๊กอินควรหลีกเลี่ยงการคัดลอกไฟล์หรือไดเรกทอรีย์ไปยังโปรเจคหลักในทางไหน ตัวอย่างเช่น ปลั๊กอิน cross-domain ยกเว้นการกำหนดไฟล์จะต้องใส่ไว้ที่ `vendor/webman/cros/src` และไม่ควรคัดลอกไปยังโปรเจคหลัก
8. ชื่อชุดปลั๊กอินควรใช้ตัวพิมพ์ใหญ่ เช่น Webman/Console

## ตัวอย่าง

**ติดตั้งคำสั่งของปลั๊กอิน `webman/console`**

`composer require webman/console`
#### สร้างปลั๊กอิน
สมมติว่าต้องการสร้างปลั๊กอินที่ชื่อ `foo/admin` (ชื่อเล็กตอสามารถตามหลังต้องเป็นชื่อโครงการที่ต้องการจะเผยแพร่ ชื่อจะต้องเป็นตัวเล็ก)
รันคำสั่ง
`php webman plugin:create --name=foo/admin`
หลังจากสร้างปลั๊กอินจะทำให้เกิดไดเรกทอรี `vendor/foo/admin` เพื่อใช้ในการเก็บไฟล์ที่เกี่ยวข้องกับปลั๊กอิน และ `config/plugin/foo/admin` เพื่อใช้ในการเก็บค่ากำหนดของปลั๊กอิน

> หมายเหตุ
> `config/plugin/foo/admin` รองรับการกำหนดต่อไปนี้ app.php การกำหนดหลักของปลั๊กอิน, bootstrap.php การกำหนดการเริ่มต้นกระบวนการ, route.php การกำหนดเส้นทาง, middleware.php การกำหนด middleware, process.php การกำหนดกระบวนการเอง, database.php การกำหนดฐานข้อมูล, redis.php การกำหนด redis, thinkorm.php การกำหนด thinkorm การกำหนดรูปแบบเหล่านี้ถูก webman จดจำอัตโนมัติ การเข้าถึงไปด้วยคำหนุน `plugin` เช่น config('plugin.foo.admin.app');
#### ส่งออกปลั๊กอิน
เมื่อเราสร้างปลั๊กอินเสร็จหลังจากนั้นทำการส่งออกปลั๊กอินด้วยคำสั่งต่อไปนี้
`php webman plugin:export --name=foo/admin`
หลังจากที่ส่งออกแล้ว ไดเรกทอรี config/plugin/foo/admin จะถูกคัดลอกไปยัง src ใน vendor/foo/admin พร้อมกับการสร้าง Install.php ใหม่ ๆ Install.php ใช้ในการดำเนินการที่จะถูกดำเนินการโดยอัตโนมัติเมื่อติดตั้งและถอนปลั๊กอิน
การทำงานเริ่มต้นการติดตั้งคือคัดลอกการกำหนดใน vendor/foo/admin/src ไปยังโปรเจคปัจจุบันที่ตั้งไว้ที่ config/plugin
การถอนปลั๊กอิน การทำงานเริ่มต้นคือลบไฟล์กำหนดที่ตั้งไว้ที่ config/plugin ในโปรเจคปัจจุบัน
คุณสามารถแก้ไข Install.php เพื่อให้ทำการดำเนินการที่กำหนดเพื่อให้สอดคล้องกับตอนติดตั้งและถอนปลั๊กอิน

#### ส่งปลั๊กอิน
* สมมติว่าคุณมีบัญชีใน [github](https://github.com) และ [packagist](https://packagist.org)
* ใน [github](https://github.com) สร้างโครงการ admin และอัพโหลดโค้ด และที่อยู่โค้ดคือ `https://github.com/username/admin`
* เข้าไปในที่อยุ่`https://github.com/username/admin/releases/new` เพื่อเปิดการใช้งาน release เช่น `v1.0.0`
* เข้าไปที่[packagist](https://packagist.org) และคลิกที่ที่อยู่`Submit` นำที่อยุ่โค้ด github ของคุณ `https://github.com/username/admin` ส่งเข้าไป ที่นั้นคุณจะสามารถเผยแพร่ปลั๊กอินได้

> **เกริ่นหมาย**
> ถ้ามีการส่งปลั๊กอินใน `packagist` แล้วแสดงว่าข้อความขัดแย้ง คุณสามารถตั้งชื่อผู้ผลิใหม่เช่น `foo/admin` สลายเป็น `myfoo/admin`

เมื่อมีการปรับปรุงโค้ดโปรเจคปลั๊กอิน คุณก็จำเป็นต้องปรับปรุงโค้ดใน github แล้วตั้งเป็นที่อยู่`https://github.com/username/admin/releases/new` และไปที่ `https://packagist.org/packages/foo/admin` จากนั้นคลิกที่ปุ่ม `Update` เพื่อปรับปรุงเวอร์ชั่น
## เพิ่มคำสั่งให้แพล็กอิน

บางครั้งเราต้องการเพิ่มคำสั่งที่กำหนดเองให้แพล็กอินเพื่อให้บริการฟังก์ชันด้านการช่วยเสริม เช่น เมื่อติดตั้งแพล็กอิน `webman/redis-queue` ลงในโปรเจค โปรเจคจะเพิ่มคำสั่ง `redis-queue:consumer` โดยผู้ใช้สามารถรัน `php webman redis-queue:consumer send-mail` เพื่อสร้างคลาสผู้บริโภค SendMail.php ในโปรเจคเพื่อช่วยในการพัฒนาอย่างรวดเร็ว

สมมติว่าแพล็กอิน `foo/admin` ต้องการเพิ่มคำสั่ง `foo-admin:add` โปรดอ้างอิงขั้นตอนด้านล่าง

#### สร้างคำสั่งใหม่

**สร้างไฟล์คำสั่งใหม่ใน `vendor/foo/admin/src/FooAdminAddCommand.php`**

```php
<?php

namespace Foo\Admin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FooAdminAddCommand extends Command
{
    protected static $defaultName = 'foo-admin:add';
    protected static $defaultDescription = 'คำอธิบายคำสั่งนี้ที่นี่';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'เพิ่มชื่อ');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln("Admin add $name");
        return self::SUCCESS;
    }

}
```

> **หมายเหตุ**
> เพื่อหลีกเลี่ยงข้อขัดแย้งของคำสั่งระหว่างแพล็กอิน ควรใช้รูปแบบคำสั่งเป็น `ผู้ผลิต-ชื่อแพล็กอิน:คำสั่งที่ระบุ` เช่น คำสั่งทั้งหมดของแพล็กอิน `foo/admin` ควรมีคำนำหน้าเป็น `foo-admin:` เช่น เช่น `foo-admin:add`

#### เพิ่มการกำหนดค่า
**สร้างไฟล์การกำหนดค่า `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....สามารถเพิ่มการกำหนดค่าเพิ่มเติมได้ตามต้องการ...
];
```

> **เคล็ดลับ**
> `command.php` ใช้สำหรับกำหนดคำสั่งที่กำหนดเองให้แพล็กอิน ทุกชิ้นในอาร์เรย์แต่ละชิ้นจะสอดคล้องกับไฟล์คลาสคำสั่่งที่อ้างถึงหนึ่งคำสั่ง  เมื่อผู้ใช้รันคำสั่งในโปรเจ็ค `webman/console` จะโหลดคำสั่งที่กำหนดเองจากไฟล์ `command.php` ของแต่ละแพล็กอินอัตโนมัติ หากต้องการเรียนรู้เพิ่มเติมเกี่ยวกับคำสั่งโปรดอ่านเพิ่มเติมที่[คำสั่ง](console.md)

#### ดำเนินการส่งออก
ดำเนินการส่งออกโปรดรันคำสั่ง `php webman plugin:export --name=foo/admin` เพื่อส่งออกแพล็กอินและเผยแพร่บน `packagist` นอกจานนี้ผู้ใช้สามารถติดตั้งแพล็กอิน `foo/admin` จะได้รับคำสั่ง `foo-admin:add` เมื่อเรียกใช้ `php webman foo-admin:add jerry` จะพิมพ์ `Admin add jerry`
