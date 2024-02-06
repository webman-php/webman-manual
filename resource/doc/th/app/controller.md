# ควบคุม

ตามหลักการ PSR4 คลาสควบคุมจะมีเนมสเปซที่เริ่มต้นด้วย `plugin\{รหัสปลั๊กอิน}` เช่น

สร้างไฟล์ควบคุมใหม่ `plugin/foo/app/controller/FooController.php`。

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

เมื่อเข้าถึง `http://127.0.0.1:8787/app/foo/foo` จะแสดงหน้า `hello index`

เมื่อเข้าถึง `http://127.0.0.1:8787/app/foo/foo/hello` จะแสดงหน้า `hello webman`


## การเข้าถึง URL
เส้นทาง URL ของปลั๊กอินแอปพลิเคชันจะขึ้นต้นด้วย `/app` ตามด้วยรหัสปลั๊กอิน และตามด้วยควบคุมและเมทอดที่เฉพาะเจาะจง	
เช่น `plugin\foo\app\controller\UserController` URL คือ `http://127.0.0.1:8787/app/foo/user`
