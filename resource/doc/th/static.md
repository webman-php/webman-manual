webman รองรับการเข้าถึงไฟล์สถิติ โดยที่ไฟล์สถิติถูกวางไว้ที่ไดเรกทอรี `public` ตัวอย่างเช่นการเข้าถึง `http://127.0.0.8787/upload/avatar.png` จริง ๆ คือการเข้าถึง `{public directory}/upload/avatar.png` ของโปรเจคหลัก

> **โปรดทราบ**
> webman ตั้งแต่เวอร์ชัน 1.4 เป็นต้นมาสนับสนุนปลั๊กอินแอพพลิเคชัน การเข้าถึงไฟล์สถิติที่เริ่มต้นด้วย `/app/xx/ชื่อไฟล์` จริง ๆ คือการเข้าถึงไดเรกทอรี `public` ของปลั๊กอินแอพพลิเคชัน ก็คือ webman >=1.4.0 ไม่รองรับการเข้าถึงไดเรกทอรี `{public directory}/app/` 
> สำหรับข้อมูลเพิ่มเติมโปรดดูที่ [ปลั๊กอินแอพพลิเคชัน](./plugin/app.md)

### ปิดการรองรับไฟล์สถิติ
หากไม่ต้องการการรองรับไฟล์สถิติ ให้เปิดไฟล์ `config/static.php` และเปลี่ยน `enable` เป็น false หลังจากปิดการรองรับไฟล์สถิติการเข้าถึงไฟล์สถิติทั้งหมดจะได้รับการตอบกลับด้วย 404

### เปลี่ยนไดเรกทอรีไฟล์สถิติ
webman ใช้ไดเรกทอรี public เป็นไดเรกทอรีของไฟล์สถิติตามค่าเริ่มต้น หากต้องการเปลี่ยนให้แก้ไขฟังก์ชั่น​ `public_path()` ใน `support/helpers.php`

### มิดเดิลแวร์ไฟล์สถิติ
webman มีมิดเดิลแวร์ไฟล์สถิติที่ใช้งานอยู่ ที่อยู่ที่ไฟล์ `app/middleware/StaticFile.php`
บางครั้งเราอาจต้องการปรับแต่งไฟล์สถิติ เช่น เพิ่มส่วนหัว HTTP สำหรับความก้าวหรือห้ามการเข้าถึงไฟล์ที่ขึ้นต้นด้วยจุด (.) สามารถใช้มิดเดิลแวร์นี้

เนื้อหาของ `app/middleware/StaticFile.php` คล้ายกับตัวอย่างนี้:
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // ห่อห้ามการเข้าถึงไฟล์ที่ด้านซ้ายด้วยจุด
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // เพิ่มส่วนหัว HTTP สำหรับความก้าว
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```

หากระบุมิดเดิลแวร์ในไฟล์ `config/static.php` ให้เปิด ​​`middleware` ในตัวเลือก ได้
