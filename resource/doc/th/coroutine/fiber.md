# Webman

> **ความต้องการของ Webman**
> PHP>=8.1 workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> คำสั่งอัปเกรด webman `composer require workerman/webman-framework ^1.5.0`
> คำสั่งอัปเกรด workerman `composer require workerman/workerman ^5.0.0`
> ต้องติดตั้ง Fiber โครูทีน `composer require revolt/event-loop ^1.0.0`

# ตัวอย่าง
### การตอบกลับให้ช้า

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // หลับ 1.5 วินาที
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` คล้ายกับฟังก์ชัน `sleep()` ของ PHP แต่ `Timer::sleep()` จะไม่บล็อกกระบวนการ

### การส่งคำขอ HTTP

> **โปรดทราบ**
> ต้องติดตั้ง composer require workerman/http-client ^2.0.0

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // ส่งคำขอแบบไม่บล็อกโดยใช้เมธอดซิงโครนัส
        return $response->getBody()->getContents();
    }
}
```
การส่งคำขอ `$client->get('http://example.com')` เช่นเดียวกันเป็นการส่งคำขอแบบไม่บล็อก นี่สามารถใช้ในการส่งคำขอ HTTP แบบไม่บล็อกใน webman เพื่อเพิ่มประสิทธิภาพของแอปพลิเคชัน

ดูเพิ่มเติมที่ [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### เพิ่มคลาส support\Context

คลาส `support\Context` ใช้สำหรับเก็บข้อมูลของบริบทคำขอ ซึ่งเมื่อคำขอเสร็จสิ้นแล้วข้อมูลบริบทที่เกี่ยวข้องจะถูกลบโดยอัตโนมัติ กล่าวคืออายุของข้อมูลบริบทคำขอเทียบเท่ากับอายุของการเรียกคำขอ  `support\Context` รองรับ Fiber、Swoole และ Swow โครูทีน

### โครูทีนของ Swoole
หลังจากติดตั้งส่วนขยาย swoole (ต้องการ swoole>=5.0) สามารถเปิดใช้งานโครูทีนของ swoole ผ่านการตั้งค่า config/server.php
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

ดูเพิ่มเติมที่ [การขับเคลื่อนอีเว้นท์ workerman](https://www.workerman.net/doc/workerman/appendices/event.html)

### การปนเป็นตัวแปรโลก
ๅ ในโครงสรรค์โครูทีนห้ามเก็บข้อมูลสถานะที่เกี่ยวข้องกับคำขอในตัวแปรโลกหรือตัวแปรแบบคงที่ เนื่องจากนี้อาจส่งผลให้เกิดการปนเป็นข้อมูลสถานะโลกเช่น

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```

เมื่อตั้งจำนวนกระบวนการเป็น 1 เมื่อเราส่งคำขอสองคำขอต่อเนื่อง  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
เราคาดหวังว่าผลลัพธ์ของคำขอสองคำขอจะได้เป็น `lilei` และ `hanmeimei` ตามลำดับ แต่ในความเป็นจริงได้ผลลัพธ์เป็น `hanmeimei` เท่านั้น
นี่เป็นเพราะคำขอที่สองได้ทับซ้อน ตัวแปรแบบคงที่ `$name` ในขณะที่คำขอแรกกำลังหลับเมื่อคำขอที่หนึ่งเสร็จสิ้น ตัวแปรแบบคงที่ `$name` ได้กลายเป็น `hanmeimei`

**วิธีที่ถูกต้องควรใช้ context เพื่อเก็บข้อมูลสถาะของคำขอ**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        Timer::sleep(5);
        return Context::get('name');
    }
}
```

**ตัวแปรท้องถิ่นไม่ทำให้เกิดการปนเป็นข้อมูล**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        Timer::sleep(5);
        return $name;
    }
}
```
เนื่องจาก `$name` เป็นตัวแปรท้องถิ่น โครทีนไม่สามารถเข้าถึงตัวมันเองจากการปนเป็นข้อมูล ดังนั้นใช้ตัวแปรท้องถิ่นเป็นที่ปลอดภัยในโครทีน

# เกี่ยวกับโครูทีน
โครทีนไม่ใช่ตัวป้องกันที่ดี เมื่อนำเข้าโครทีนแสดงว่าต้องใส่ใจกับปัญหาข้อมูลสถานะโลก/ตัวแปรคงที่ จำเป็นต้องตั้ง context โดยเฉพาะ เวลาในการดัดแปลงไบก์ในโครทีนกับการแก้บักด์ทำให้เป็นไปที่จะยากขึ้นต่อการทำงานจากการเขียนปกติ

การเขียนโครทีนใน webman ซักระยะมาแล้วก็เป็นที่พอใช้ โดยตัวเองทดสอบการดันขอมูลฐานข้อมูลผ่าน [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) ในรอบสามปีล่าสุด ได้ถิมว่าการใช้ webman เพื่อดันข้อมูลฐานข้อมูลมีประสิทธิภาพสูงกว่าเฟรมเวิร์คของ Go เช่น gin และ echo ในเฟรมเวิร์คการทดสอบการล้อม้ฐานข้อมูลและมีประสิทธิภาพสูงยาวเป็น 1 เท่า และสูงกว่าเฟรมเวิร์ค传统อย่าง laravel ถึง 40 เท่า
![](../../assets/img/benchemarks-go-sw.png?)

เมื่อฐานข้อมูลและ redis อยู่ในเครือข่ายภายใน การสร้างตัวจัดการข้อมูลโครทีนได้มีประสิทธิภาพที่สูงกว่าโดยทั่วไป นี้เนื่องจากการสร้างตัวจัดการข้อมูลโครทีน การประชาสัมพันธ์ และค่าใช้จ่ายที่เสื่อมไปจากการเปลี่ยร์สังหารได้สูงกว่าค่าใช้จ่ายที่ระบบยักยบมี  เพราะท่านี่ที่พอให้ก็อยาับมูล่ว่าท่านี่แนบเร็วขึ้นเมื่อเทียบกับการเปลี่ยนระบบ

# เมื่อไหร่ควรใช้โครทีน
เมื่อการทำธุรกิจมีการเข้าถึงช้า อย่างเช่นเราต้องการเข้าถึงข้อมูลจากอินเตอร์เน็ตที่บุบเฉี่ยว สามารถใช้ [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) โดยใช้วิธีโครทีนเพื่อส่งคำขอ HTTP แบบเจาะจงเพื่อเพิ่มความสามารถของแอปพลิเคชัน
