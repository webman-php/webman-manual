# ตัวอย่างง่าย

## การส่งค่าเป็นข้อความ
**สร้างคอนโทรลเลอร์ใหม่**

สร้างไฟล์ `app/controller/UserController.php` ดังนี้

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // รับพารามิเตอร์ name จากคำขอแบบ GET ถ้าไม่ได้รับพารามิเตอร์ name ก็คืนค่าเป็น $default_name
        $name = $request->get('name', $default_name);
        // ส่งข้อความกลับไปยังเบราว์เซอร์
        return response('hello ' . $name);
    }
}
```

**เข้าถึง**

เข้าถึงผ่านเบราว์เซอร์ที่ `http://127.0.0.1:8787/user/hello?name=tom`

เบราว์เซอร์จะคืนค่าเป็น `hello tom`

## การส่งค่าเป็น json
แก้ไขไฟล์ `app/controller/UserController.php` ดังนี้

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

เบราว์เซอร์จะคืนค่าเป็น `{"code":0,"msg":"ok","data":"tom"}`

การใช้ฟังก์ชันช่วยในการส่งค่า json จะถูกเพิ่มเฮดเดอร์ `Content-Type: application/json` โดยอัตโนมัติ

## การส่งค่าเป็น xml
เช่นเดียวกัน การใช้ฟังก์ชันช่วย `xml($xml)` จะคืนค่าเป็นการตอบกลับ `xml` พร้อมเฮดเดอร์ `Content-Type: text/xml`

ที่พารามิเตอร์ `$xml` สามารถเป็นสตริง `xml` หรืออ็อบเจกต์ `SimpleXMLElement` ก็ได้

## การส่งค่าเป็น jsonp
เช่นเดียวกัน การใช้ฟังก์ชันช่วย `jsonp($data, $callback_name = 'callback')` จะคืนค่าเป็นการตอบกลับ `jsonp`

## การส่งค่าเป็นหน้าแสดงผล
แก้ไขไฟล์ `app/controller/UserController.php` ดังนี้

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

สร้างไฟล์ `app/view/user/hello.html` ดังนี้

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
จะคืนค่าเป็นหน้า html ที่มีเนื้อหาเป็น `hello tom`

หมายเหตุ：webman ใช้ไวยากรต้นฉบับของ php เป็นภาษาต้นฉบับสำหรับมุมมอง. หากต้องการใช้มุมมองอื่นๆ ดูที่ [มุมมอง](view.md)
