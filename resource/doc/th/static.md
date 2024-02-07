webman รองรับการเข้าถึงไฟล์สถิติ โดยไฟล์สถิติจะถูกวางไว้ในไดเร็กทอรี 'public' เช่น การเข้าถึง `http://127.0.0.8787/upload/avatar.png` จริงๆแล้วก็คือการเข้าถึง `{public directory}/upload/avatar.png`

> **โปรดทราบ**
> เริ่มตั้งแต่ webman 1.4 เป็นต้นมา รองรับการใช้งานปลั๊กอินแอปพลิเคชัน การเข้าถึงไฟล์สถิติที่เริ่มต้นด้วย `/app/xx/ชื่อไฟล์` จริงๆแล้วเหมือนกับการเข้าถึงไดเร็กทอรี 'public' ของปลั๊กอินโดย webman >=1.4.0 จะไม่รองรับการเข้าถึงไดเร็กทอรี `{public directory}/app/` โปรดดูเพิ่มเติมที่[ปลั๊กอินแอปพลิเคชัน](./plugin/app.md)

### ปิดการรองรับไฟล์สถิติ
หากไม่ต้องการรองรับไฟล์สถิติ โปรดเปิดไฟล์ `config/static.php` และเปลี่ยนตัวเลือก `enable` เป็น false หลังจากปิดการรองรับทุกการเข้าถึงไฟล์สถิติจะคืนค่า404

### เปลี่ยนไดเร็กทอรีของไฟล์สถิติ
webman ใช้ไดเร็กทอรี 'public' เป็นไดเร็กทอรีไฟล์สถิติเริ่มต้น หากต้องการเปลี่ยนโปรดเปลี่ยนฟังก์ชัน `public_path()` ภายใน `support/helpers.php`

### มิดเดิลแวร์ไฟล์สถิติ
webman มาพร้อมกับมิดเดิลแวร์ไฟล์สถิติหนึ่งตัว ที่ตั้งอยู่ที่ `app/middleware/StaticFile.php`
บางครั้งเราอาจต้องการปรับแต่งไฟล์สถิติ เช่น การเพิ่มหัว http สำหรับไฟล์สถิติ หรือ ห้ามการเข้าถึงไฟล์ที่ขึ้นต้นด้วยจุด (`.`)

เนื้อหาภายใน `app/middleware/StaticFile.php` คล้ายกับด้านล่าง:
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
        // ห้ามการเข้าถึงไฟล์ที่ขึ้นต้นด้วย .
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 ห้ามเข้าถึง</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // เพิ่มหัว http สำหรับการข้าถึง
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
หากต้องการใช้มิดเดิลแวร์นี้ โปรดเปิดในตัวเลือก `middleware` ใน`config/static.php`
