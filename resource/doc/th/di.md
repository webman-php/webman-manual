# การสร้างความสามารถในการส่งเข้า
ใน webman การสร้างความสามารถในการส่งเข้าเป็นคุณสมบัติที่เลือกได้ คุณสมบัตินี้ถูกปิดไว้โดยค่าเริ่มต้น หากคุณต้องการสร้างความสามารถในการส่งเข้า แนะนำให้ใช้[php-di](https://php-di.org/doc/getting-started.html) ต่อไปคือวิธีการใช้ webman ร่วมกับ `php-di`

## การติดตั้ง
```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

แก้ไขการตั้งค่า `config/container.php` ให้เป็นดังนี้
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```
> `config/container.php` ต้องคืนการสร้างตัวอย่างที่เป็นไปตามมาตรฐาน `PSR-11` ถ้าคุณไม่ต้องการใช้ `php-di` คุณสามารถสร้างและคืนตัวอย่างอื่นที่เป็นไปตามมาตรฐาน `PSR-11` ที่นี่

## การส่งตัวให้นำมาใช้
สร้าง `app/service/Mailer.php`(ถ้าโฟลเดอร์ไม่มีให้สร้างขึ้นมาเอง) โดยมีเนื้อหาดังนี้
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // โค้ดส่งอีเมลล์ของคุณ
    }
}
```

เนื้อหาของ `app/controller/UserController.php` ดังนี้
```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```

ตามปกติต้องมีการใช้โค้ดนี้เพื่อทำให้  `app\controller\UserController` ได้ยิ่งไปกับการสร้างโดยตั้งชื่อตัวแปรประเภท Mailer ไว้ดังนี้
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
เมื่อใช้ `php-di` นักพัฒนาไม่จำเป็นต้องสร้างตัวแปรของ `Mailer` ในคอนโทลเลอร์ เพราะ webman จะทำให้ด้วยเอง ถ้าต้องการใช้คลาสอื่นในการสร้าง `Mailer` ก็จะทำการสร้างและส่งเข้าโดยอัตโนมัติ นักพัฒนาไม่จำเป็นต้องทำงานเริ่มต้นใด ๆ

> **หมายเหตุ**
> ต้องมีการสร้างตัวอย่างที่ถูกสร้างด้วย webman หรือ `php-di` เท่านั้นที่จะสามารถส่งเข้าอัตโนมัติได้ การสร้างตัวเองด้วยคำสั่ง `new` ไม่สามารถส่งเข้าอัตโนมัติ หากต้องการส่งเข้าจะต้องใช้ `support\Container` อินเทอร์เฟซแทนคำสั่ง `new` เช่น

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// สร้างตัวตามคำสั่ง new ไม่สามารถทำให้ส่งเข้าได้
$user_service = new UserService;
// สร้างตัวตามคำสั่ง new ไม่สามารถทำให้ส่งเข้าได้
$log_service = new LogService($path, $name);

// สร้างตัวโดยใช้ Container เพื่อทำให้สามารถส่งเข้าได้
$user_service = Container::get(UserService::class);
// สร้างตัวโดยใช้ Container เพื่อทำให้สามารถส่งเข้าได้
$log_service = Container::make(LogService::class, [$path, $name]);
```

## ส่งเข้าโดยใช้คำอธิบาย
นอกจากการส่งเข้าการสร้างตัวด้วยสร้อยการู้ ยังสามารถใช้การส่งเข้าโดยใช้คำอธิบายได้อีกด้วย ตัดเช่นตัวอย่างที่กำหนดไว้ข้างต้น  `app\controller\UserController` การเปลี่ยนแปลงเป็นดังนี้
```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;
use DI\Annotation\Inject;

class UserController
{
    /**
     * @Inject
     * @var Mailer
     */
    private $mailer;

    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```
โดยตัวอย่างนี้ผ่านการใช้ `@Inject` การส่งเข้าภูมิการ์ และได้ทำการประกาศประเภทวัตถุด้วย `@var` โดยตัวอย่างนี้และการส่งเข้าตามคำอธิบายได้ทำซ้ำเอฟเฟกต์กับ การส่งเข้าตามสร้างตัว แต่โค้ดสั้นกว่า

> **หมายเหตุ**
> เวอร์ชันก่อนหน้าที่เขียนโค้ดนี้ webman ไม่รองรับการส่งพารามิเตอร์ของคอนโทลเลอร์ เช่น โค้ดนี้เมื่อ webman<=1.4.6 จะไม่รองรับ

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // มีการส่งพารามิเตอร์ของคอนโทลเลอร์ไม่รองรับก่อนเวอร์ชัน 1.4.6
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```

## การสร้างตัวอย่างการส่งเข้าเองเอง
เวลาในบางครั้งพารามิเตอร์ที่ผ่านการสร้างตัวอย่างอาจจะไม่ใช่ตัวอย่างของคลาส แต่อาจจะเป็นสตริง, เลข, อาเรย์ และอื่น ๆ ตัวอย่างเช่น Mailer จำเป็นต้องผ่านการส่ง smpt server ip และ port:

```php
<?php
namespace app\service;

class Mailer
{
    private $smtpHost;

    private $smtpPort;

    public function __construct($smtp_host, $smtp_port)
    {
        $this->smtpHost = $smtp_host;
        $this->smtpPort = $smtp_port;
    }

    public function mail($email, $content)
    {
        // โค้ดส่งอีเมลล์ของคุณ
    }
}
```

การส่งเข้าด้วยตนเองจะไม่สามารถใช้สร้างตัวอย่างที่ผ่านการสร้างตัวอย่างได้เนื่องจาก `php-di` ไม่สามารถระบุค่าของ `$smtp_host` `$smtp_port` ได้ ในกรณีนี้ลองใช้การส่งเข้าโดยกำหนดเอง

ใน `config/dependence.php`(หากไม่มีไฟล์นี้ให้สร้างขึ้นมาเอง) อย่างนี้:
```php
return [
    // ... การตั้งค่าส่วนต่อ
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
นี่คือการสร้างเมื่อต้องการผ่านการสร้างตัวอย่างคอลสการีเป็นตัวอย่างให้ `app\service\Mailer` โดยระบบจะทำการส่งโดยอัตโนมัติ `app\service\Mailer` ระหับตัวอย่างที่สร้างไว้ในคอสนี้

เราสังเกตได้ว่า `config/dependence.php` ใช้คำสั่ง `new` ในการสร้าง `Mailer` นั้นไม่มีปัญหาในตัวอย่างนี้ แต่มองการว่าถ้า `Mailer` อีกรูปแบบจะต้องผ่านการสร้างด้วย `new` สร้างตัวเองข้อมูลผ่านการสสร้างตัวอย่างของคอลสหไม่สามารถส่งเข้าได้ วิธีการแก้ไขคือใช้การส่งเข้าระหว่างฟฟฟ ย้ำ `Container::get(ชื่อคลาส)` หรือ `Container::make(ชื่อคลาส, [พารามิเตอร์ของคอนโทลเลอร์])` เพื่อสร้างคลาส
## การฝังอินเทอร์เฟซที่กำหนดเอง
ในโครงการจริงเราต้องการโปรแกรมตามหลักการของอินเทอร์เฟซ แทนที่จะเป็นคลาสที่เฉพาะเจาะจง เช่น `app\controller\UserController` ควรจะ include `app\service\MailerInterface` แทนที่จะ include `app\service\Mailer`  
กำหนดอินเทอร์เฟซ `MailerInterface` ดังนี้
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```
กำหนดการปฏิบัติตามอินเทอร์เฟซ `MailerInterface`
```php
<?php
namespace app\service;

class Mailer implements MailerInterface
{
    private $smtpHost;

    private $smtpPort;

    public function __construct($smtp_host, $smtp_port)
    {
        $this->smtpHost = $smtp_host;
        $this->smtpPort = $smtp_port;
    }

    public function mail($email, $content)
    {
        // ข้อความส่งออกจะถูกละเลย
    }
}
```
Include อินเทอร์เฟซ `MailerInterface` แทนที่จะมีการปฏิบัติตามตัวเอง
```php
<?php
namespace app\controller;

use support\Request;
use app\service\MailerInterface;
use DI\Annotation\Inject;

class UserController
{
    /**
     * @Inject
     * @var MailerInterface
     */
    private $mailer;
    
    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'สวัสดีและยินดีต้อนรับ!');
        return response('ok');
    }
}
```
`config/dependence.php` ถูกกำหนดตามอินเทอร์เฟซ `MailerInterface` ดังนี้
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```
นั่นคือเมื่อธุรกิจต้องการใช้ `MailerInterface` อินเทอร์เฟซ จะถูกใช้ `Mailer` สิ่งที่ดำเนินการ  
>  ประโยชน์ของการใช้อินเทอร์เฟซคือ เมื่อเราต้องการเปลี่ยนคอมโพเนนต์ใด ๆ เราไม่จำเป็นต้องเปลี่ยนโค้ดทำธุรกิจ แค่ต้องการเปลี่ยนการปฏิบัติตามใน `config/dependence.php` เท่านั้น สิ่งนี้ก็เป็นประโยชน์มากในการทดสอบของหน่วย

## การฝังอินเจคอื่นๆ
ใน `config/dependence.php` สามารถกำหนดค่าต่างๆของคลาสอื่นคลาส เช่น สตริง ตัวเลข อาเรย์ และอื่นๆ  
เช่น `config/dependence.php` ถูกกำหนดดังนี้
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```
นี้เวลาเราสามารถใช้ `@Inject` เพื่อฝัง `smtp_host` `smtp_port` เข้าไปในแอตทริบิวต์
```php
<?php
namespace app\service;

use DI\Annotation\Inject;

class Mailer
{
    /**
     * @Inject("smtp_host")
     */
    private $smtpHost;

    /**
     * @Inject("smtp_port")
     */
    private $smtpPort;

    public function mail($email, $content)
    {
        // ข้อความส่งออกจะถูกละเลย
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // ผลลัพธ์จะเอา 192.168.1.11:25
    }
}
```
>  โปรดทราบ: `@Inject("key")` ในนั้นมีเครื่องหมายคำพูดคู่

## เนื้อหาเพิ่มเติม
โปรดอ้างถึง[คู่มือ php-di](https://php-di.org/doc/getting-started.html)
