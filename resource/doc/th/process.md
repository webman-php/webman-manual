# การปรับแต่งกระบวนการ

ใน webman คุณสามารถปรับแต่งการฟังหรือกระบวนการเหมือนกับ workerman

> **หมายเหตุ**
> ผู้ใช้ Windows จำเป็นต้องใช้ `php windows.php` เพื่อเริ่ม webman ในการเริ่มกระบวนการที่กำหนดเอง

## การปรับแต่งการให้บริการ HTTP
บางครั้งคุณอาจมีความต้องการที่พิเศษบางอย่าง และต้องการการเปลี่ยนแปลงรหัสของการให้บริการ HTTP ใน webman เพื่อสนับสนุนความต้องการเหล่านั้น คุณสามารถใช้กระบวนการที่กำหนดเองเพื่อทำการปรับแต่ง

ตัวอย่างเช่นพิจารณาที่ตั้งไฟล์ใหม่ app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // ที่นี่นี้เราสามารถเขียนคำสั่งใหม่ๆเพื่อแก้ไขรหัส engineering ของ Webman\App
}
```

เพิ่มการกำหนดค่าใน 'config/process.php' ดังต่อไปนี้

```php
use Workerman\Worker;

return [
    // ... เราจะข้ามการกำหนดค่าอื่นๆ ...

    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // จำนวนกระบวนการ
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // กำหนดคลาส request
            'logger' => \support\Log::channel('default'), // ตัวอย่างของ instance ของการบันทึกประวัติ
            'app_path' => app_path(), // ตำแหน่งไดเรกทอรี app
            'public_path' => public_path() // ตำแหน่งไดเรกทอรี public
        ]
    ]
];
```

> **คำแนะนำ**
> หากต้องการปิดการทำงานของการให้บริการ HTTP ที่มากับ webman สามารถทำได้โดยการกำหนดค่าใน 'config/server.php' ให้เป็น `listen=>''`

## ตัวอย่างการฟัง WebSocket ที่กำหนดเอง

สร้างไฟล์ใหม่ `app/Pusher.php`
```php
<?php
namespace app;

use Workerman\Connection\TcpConnection;

class Pusher
{
    public function onConnect(TcpConnection $connection)
    {
        echo "เมื่อเชื่อมต่อ\n";
    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        echo "เมื่อเชื่อมต่อผ่าน WebSocket\n";
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        $connection->send($data);
    }

    public function onClose(TcpConnection $connection)
    {
        echo "เมื่อปิดการเชื่อมต่อ\n";
    }
}
```
> โปรดทราบ: คุณสามารถเขียนคำสั่ง onXXX ทั้งหมดเป็น public

เพิ่มการกำหนดค่าใน 'config/process.php' ดังต่อไปนี้
```php
return [
    // ... เราจะข้ามการกำหนดค่าอื่นๆ ...

    // websocket_test คือชื่อของกระบวนการ
    'websocket_test' => [
        // ที่นี่กำหนดคลาสของกระบวนการ คือ Pusher ที่กำหนดขึ้นด้านบน
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1
    ],
];
```

## ตัวอย่างของกระบวนการที่ไม่ใช่การฟัง
สร้างไฟล์ใหม่ `app/TaskTest.php`
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // ตรวจสอบฐานข้อมูลทุก ๆ 10 วินาทีว่ามีผู้ใช้ที่ลงทะเบียนเข้าใหม่หรือไม่
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
เพิ่มการกำหนดค่าใน 'config/process.php' ดังต่อไปนี้
```php
return [
    // ... เราจะข้ามการกำหนดค่าอื่นๆ ...
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> โปรดทราบ: หากการกำหนดค่า 'listen' ได้ยืมเหมื่นไม่ได้ที่เหนียคับ หากการกำหนดค่า 'count' ได้ยืมการกำหนดเองตั้งต้นเป็น1

## คำอธิบายไฟล์การกำหนดค่า
การกำหนดค่าสมบูรณ์ของกระบวนการคือดังนี้
```php
return [
    // ... 
    
    // websocket_test คือชื่อของกระบวนการ
    'websocket_test' => [
        // ที่นี่กำหนดคลาสของกระบวนการ
        'handler' => app\Pusher::class,
        // เป็นส่งของพร๊อโธคอลไอพี และพอร์ทที่กำหนดได้ (เลือกได้)
        'listen'  => 'websocket://0.0.0.0:8888',
        // จำนวนกระบวนการ (เลือกได้ ตั้งต้นเป็น 1)
        'count'   => 2,
        // ผู้ใช้ที่กระบวนการจะทำงานเป็น (เลือกได้  ใช้งานเป็นผู้ใช้ปัจจุบัน)
        'user'    => '',
        // กลุ่มของผู้ใช้ที่กระบวนการจะทำงานเป็น (เลือกได้  ใช้งานเป็นผู้ใช้ปัจจุบัน)
        'group'   => '',
        // ทำได้ให้กระบวนการทำงานต่อซ้ำหรือไม่ (เลือกได้ เริ่มต้นเป็นจริง)
        'reloadable' => true,
        // เปิดใช้ rusePort หรือไม่ (เลือกได้  ต้องใช้ php>=7.0 หรือมากกว่านั้น เริ่มต้นเป็นจริง)
        'reusePort'  => true,
        // transport (เลือก  สำหรับการเปิดใช้งาน ssl กำหนดเป็น ssl เริ่มต้นเป็น tcp)
        'transport'  => 'tcp',
        // context (เลือก  สำหรับการเปิดใช้งาน ssl ทำการส่งตำแหน่งของใบรับรองเมื่อ transport คือ ssl)
        'context'    => [], 
        // พารามิเตอร์ของผู้สร้างกระบวนการคลาส ที่นี่เป็นพารามิเตอร์ของคลาส Process\Pusher::class (เลือก)
        'constructor' => [],
    ],
];
```

## สรุป
การปรับแต่งกระบวนการใน webman ก็คือการห่อหุ้นง่ายของ workerman ซึ่งแยกการกำหนดค่าและธุรกิจ และทำการใช้เทคนิค onXXX ของ workerman ผ่านการอธิบายของคลาส หมายถึงการใช้งานประการอื่นๆเข้าร่วนด้วย workerman ที่มีโดยอย่างมีนั้นที่ๆเรียกบได้เทียบเท่ากับ workerman ทิ้งทั้งหมด
