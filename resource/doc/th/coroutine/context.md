`support\Context` คลาสถูกใช้สำหรับจัดเก็บข้อมูลของบริบทการร้องขอ โดยข้อมูลของบริบทที่เกี่ยวข้องจะถูกลบอัตโนมัติเมื่อการร้องขอเสร็จสิ้น นั่นคืออายุการใช้งานของข้อมูลของบริบทจะเกี่ยวข้องกับอายุการใช้งานของการร้องขอ เราสามารถใช้ `support\Context` ในการรองรับ Fiber, Swoole และ Swow ด้วย

ดูเพิ่มเติมได้ที่ [webman โครูทีน](./fiber.md)

# อินเตอร์เฟซ
บริบทมีอินเตอร์เฟซต่อไปนี้

## ตั้งค่าข้อมูลบริบท
```php
Context::set(string $name, $mixed $value);
```

## รับข้อมูลบริบท
```php
Context::get(string $name = null);
```

## ลบข้อมูลบริบท
```php
Context::delete(string $name);
```

> **โปรดทราบ**
> โครงสร้างจะเรียกใช้ Context::destroy() โดยอัตโนมัติหลังจากร้องขอลงท้ายเพื่อทำลายข้อมูลบริบท ธุรกิจไม่ควรเรียกใช้ Context::destroy() ด้วยตนเอง

# ตัวอย่าง
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        return Context::get('name');
    }
}
```

# ข้อควรระวัง
**เมื่อใช้กับโครงสรรถุudiฐะ** ไม่ควรเก็บ**ข้อมูลสถานะที่เกี่ยวข้องกับการร้องขอ** ไว้ในตัวแปร global หรือตัวแปร static นั้นอาจเสี่ยงต่อการสกประกอบข้อมูลตัวแปรสถานะโดยรวม การใช้ Context เป็นวิธีที่ถูกต้องในการบันทึกและเข้าถึงข้อมูลที่เกี่ยวข้องกับการร้องขอนั้น
