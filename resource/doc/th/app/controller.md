# ควบคุม

ตามข้อกำหนดของ PSR4 โดย namespace ของคลาสควบคุมจะขึ้นต้นด้วย `plugin\{รหัสปลั๊กอิน}` เช่น

สร้างไฟล์ควบคุม `plugin/foo/app/controller/FooController.php` ดังตัวอย่างต่อไปนี้

```php
<?php
namespace plugin\foo\app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

เมื่อเข้าถึง `http://127.0.0.1:8787/app/foo/foo` จะแสดงผลเป็น `hello index`

เมื่อเข้าถึง `http://127.0.0.1:8787/app/foo/foo/hello` จะแสดงผลเป็น `hello webman`

## URL ที่เข้าถึง
ที่อยู่ URL ของปลั๊กอินแอปพลิเคชันเริ่มต้นด้วย `/app` ตามด้วยรหัสปลั๊กอิน และตามด้วยควบคุมและวิธีการที่เฉพาะเจาะจง
เช่น URL ของควบคุม `plugin\foo\app\controller\UserController` คือ `http://127.0.0.1:8787/app/foo/user`
