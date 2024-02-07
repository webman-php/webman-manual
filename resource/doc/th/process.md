# การปรับแต่งกระบวนการ

ใน webman คุณสามารถปรับแต่งฟังก์ชันที่เชื่อมต่อหรือกระบวนการได้เช่นเดียวกับ workerman

> **หมายเหตุ**
> ผู้ใช้ Windows จำเป็นต้องใช้ `php windows.php` เพื่อเริ่มต้น webman ในการเริ่มต้นกระบวนการที่กำหนดเอง

## การปรับแต่งบริการ HTTP
บางครั้งคุณอาจมีความต้องการเฉพาะทางที่จะเปลี่ยนแปลงโค้ดของบริการ HTTP ใน webman ในกรณีนี้คุณสามารถใช้กระบวนการที่กำหนดเอง

ตัวอย่างเช่น สร้าง app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // ที่นี่คุณสามารถเขียนโค้ดทับของเมธอดใน Webman\App
}
```

เพิ่มการตั้งค่าใน `config/process.php` ดังนี้

```php
use Workerman\Worker;

return [
    // ... เนื้อหาอื่น ๆ ถูกข้ามทิ้ง...
    
    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // จำนวนกระบวนการ
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // การตั้งค่าคลาสของคำขอ
            'logger' => \support\Log::channel('default'), // Instance ของบันทึก
            'app_path' => app_path(), // ตำแหน่งไดเรกทอรี่แอป
            'public_path' => public_path() // ตำแหน่งไดเรกทอรี่สาธารณะ
        ]
    ]
];
```

> **เราแนะนำ**
> หากต้องการปิดบริการ http ที่มากับ webman เพียงแค่ตั้งค่า `listen=>''` ใใ config/server.php

## ตัวอย่างของการตั้งค่าการฟังก์ชัน WebSocket ที่กำหนดเอง

สร้าง `app/Pusher.php`

```php
<?php
namespace app;

use Workerman\Connection\TcpConnection;

class Pusher
{
    public function onConnect(TcpConnection $connection)
    {
        echo "onConnect\n";
    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        echo "onWebSocketConnect\n";
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        $connection->send($data);
    }

    public function onClose(TcpConnection $connection)
    {
        echo "onClose\n";
    }
}
```
> หมายเหตุ: คุณสามารถใช้ลักษณะทั่วไปทั้งหมดชนิด onXXX

เพิ่มการตั้งค่าใน `config/process.php` ดังนี้

```php
return [
    // ... การตั้งค่ากระบวนการอื่น ๆ ถูกข้ามทิ้ง...
    
    // websocket_test คือชื่อกระบวนการ
    'websocket_test' => [
        // ที่นี่ระบุคลาสกระบวนการ คือคลาส Pusher ที่กำหนดเองด้านบน
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## ตัวอย่างของกระบวนการที่ไม่ได้รับการตั้งค่าการฟังก์ชันการฟังก์ชันที่กำหนดเอง

สร้าง `app/TaskTest.php`

```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // ตรวจสอบฐานข้อมูลทุก ๆ 10 วินาทีว่ามีผู้ใช้ที่ลงทะเบียนใหม่หรือไม่
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```

เพิ่มการตั้งค่าใน `config/process.php` ดังนี้

```php
return [
    // ... การตั้งค่ากระบวนการอื่น ๆ ถูกข้ามทิ้ง
   
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> หมายเหตุ: ถ้าไม่ระบุ listen จะไม่ได้ฟังที่พอร์ตใด ๆ และไม่ระบุ count จะใช้ค่าเริ่มต้นเป็น 1

## คำอธิบายการตั้งค่า

การตั้งค่าที่เต็มรูปแบบของกระบวนการประกอบด้วย
```php
return [
    // ... การตั้งค่ากระบวนการอื่น ๆ ถูกข้ามทิ้ง...

    // websocket_test คือชื่อกระบวนการ
    'websocket_test' => [
        // ที่นี่ระบุคลาสกระบวนการ
        'handler' => app\Pusher::class,
        // จุดต่อสองทางของโปรโตคอล ไอพีและพอร์ต (ทางเลือก)
        'listen'  => 'websocket://0.0.0.0:8888',
        // จำนวนของกระบวนการ (ทางเลือก, เริ่มต้นเป็น 1)
        'count'   => 2,
        // ผู้ใช้ที่รันกระบวนการ (ทางเลือก, เริ่มต้นเป็นผู้ใช้ปัจจุบัน)
        'user'    => '',
        // กลุ่มผู้ใช้ที่รันกระบวนการ (ทางเลือก, เริ่มต้นเป็นกลุ่มผู้ใช้ปัจจุบัน)
        'group'   => '',
        // กระบวนการปัจจุบันรองรับการโหลดใหม่หรือไม่ (ทางเลือก, เริ่มต้นเป็น true)
        'reloadable' => true,
        // เปิดใช้งานโหลดใหม่ของพอร์ต (ทางเลือก, ต้องการ PHP >= 7.0, เริ่มต้นเป็น true)
        'reusePort'  => true,
        // โหลดขั้นส่ง (ทางเลือก, เมื่อต้องการเปิดใช้งาน SSL ให้ตั้งค่าเป็น ssl, เริ่มต้นเป็น tcp)
        'transport'  => 'tcp',
        // บริบท (ทางเลือก, เมื่อโหลดเป็น ssl ต้องมีการมอบค่าเป็นเส้นทางของใบรับรอง)
        'context'    => [], 
        // อาร์กิวเมนต์ของฟังก์ชันสร้างคลาส ที่นี่คืออาร์กิวเมนต์ของคลาส process\Pusher::class (ทางเลือก)
        'constructor' => [],
    ],
];
```

## สรุป
กระบวนการที่กำหนดเองใน webman หลายที่จริง ๆ คือการนึงซอฟต์แวร์ workerman อย่างง่าย ๆ มันสนับสนุนการแยกการตั้งค่าและธุรกิจ และการดัไต่ callback ของ workerman `onXXX` ด้วยวิธีการของคลาส และวิธีการใช้อื่น ๆ เหมือนกับ workerman อย่างสมบูรณ์แบบ
