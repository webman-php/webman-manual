# ตัวอย่างง่าย

## การส่งคืนข้อความ
**สร้างคอนโทรลเลอร์**

สร้างไฟล์ `app/controller/UserController.php` ดังตัวอย่างนี้

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // รับพารามิเตอร์ name จากคำขอแบบ GET หากไม่มีการส่งพารามิเตอร์ name ไปจะคืนค่า $default_name
        $name = $request->get('name', $default_name);
        // ส่งคืนข้อความไปยังเบราว์เซอร์
        return response('hello ' . $name);
    }
}
```

**เข้าถึง**

เข้าถึงผ่านเบราว์เซอร์ที่ `http://127.0.0.1:8787/user/hello?name=tom`

เบราว์เซอร์จะคืนค่า `hello tom`

## การส่งคืน JSON
แก้ไขไฟล์ `app/controller/UserController.php` ดังตัวอย่างนี้

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return json([
            'code' => 0, 
            'msg' => 'ok', 
            'data' => $name
        ]);
    }
}
```

**เข้าถึง**

เข้าถึงผ่านเบราว์เซอร์ที่ `http://127.0.0.1:8787/user/hello?name=tom`

เบราว์เซอร์จะคืนค่า `{"code":0,"msg":"ok","data":"tom""}`

การใช้ฟังก์ชันช่วยสร้าง JSON ส่งค่าโดยอัตโนมัติจะเพิ่มหัวข้อ `Content-Type: application/json`

## การส่งคืน XML
เช่นกัน การใช้ฟังก์ชันช่วย `xml($xml)` จะส่งค่าในรูปแบบ XML พร้อมเสริมหัวข้อ `Content-Type: text/xml`

ที่พารามิเตอร์ `$xml` สามารถเป็นสตริง XML หรืออ็อบเจ็กต์ `SimpleXMLElement`

## การส่งคืน JSONP
เช่นกัน การใช้ฟังก์ชันช่วย `jsonp($data, $callback_name = 'callback')` จะส่งค่าในรูปแบบ JSONP

## การส่งคืนมุมมอง
แก้ไขไฟล์ `app/controller/UserController.php` ดังตัวอย่างนี้

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return view('user/hello', ['name' => $name]);
    }
}
```

สร้างไฟล์ `app/view/user/hello.html` ดังตัวอย่างนี้

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

เข้าถึงผ่านเบราว์เซอร์ที่ `http://127.0.0.1:8787/user/hello?name=tom`
จะคืนค่าหน้า HTML ที่มีเนื้อหาเป็น `hello tom`

โปรดทราบ: webman ใช้ไวยากรณ์ต้นฉบับของ PHP เป็นรูปแบบมาตรฐานของมอดูล หากต้องการใช้มุมมองอื่นๆ โปรดดูที่[มุมมอง](view.md)
