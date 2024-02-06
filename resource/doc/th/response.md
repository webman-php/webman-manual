# การตอบกลับ
การตอบกลับนั้นเป็นอินสแตนซ์ของ `support\Response` จริง ๆ ซึ่งเพื่อความสะดวกในการสร้างอินสแตนซ์นี้ เว็บแมนมีฟังก์ชั่นตัวช่วยบางตัว ดังนี้

## การตอบกลับใด ๆ
**ตัวอย่าง**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

ฟังก์ชั่น response ทำงานดังนี้:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

คุณยังสามารถสร้างอินสแตนซ์ response ว่าง ๆ และจากนั้นใช้เมทอด `$response->cookie()` `$response->header()` `$response->withHeaders()` `$response->withBody()` ที่เหมาะสมในที่พอดีเพื่อกำหนดเนื้อหาที่จะส่งคืนได้

## การตอบกลับเป็น JSON
**ตัวอย่าง**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```
ฟังก์ชั่น json ทำงานดังนี้
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```

## การตอบกลับเป็น XML
**ตัวอย่าง**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        $xml = <<<XML
               <?xml version='1.0' standalone='yes'?>
               <values>
                   <truevalue>1</truevalue>
                   <falsevalue>0</falsevalue>
               </values>
               XML;
        return xml($xml);
    }
}
```
ฟังก์ชั่น xml ทำงานดังนี้:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## การตอบกลับเป็นมุมมอง
สร้างไฟล์ `app/controller/FooController.php` ดังนี้

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return view('foo/hello', ['name' => 'webman']);
    }
}
```

สร้างไฟล์ `app/view/foo/hello.html` ดังนี้

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

## การเปลี่ยนเส้นทาง
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return redirect('/user');
    }
}
```

ฟังก์ชั่น redirect ทำงานดังนี้:
```php
function redirect($location, $status = 302, $headers = [])
{
    $response = new Response($status, ['Location' => $location]);
    if (!empty($headers)) {
        $response->withHeaders($headers);
    }
    return $response;
}
```

## การตั้งค่า header
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Header Value' 
        ]);
    }
}
```
คุณยังสามารถใช้เมทอด `header` และ `withHeaders` เพื่อการตั้งค่า header โดยเดี่ยว หรือเป็นจำนวนมาก
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'Header Value 1',
            'X-Header-Tow' => 'Header Value 2',
        ]);
    }
}
```
คุณยังสามารถตั้งค่า header ล่วงหน้าและตั้งค่าเนื้อหาที่ต้องการส่งคืนท้ายที่สุด

## การตั้งค่า cookie
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->cookie('foo', 'value');
    }
}
```
คุณยังสามารถตั้งค่า cookie ล่วงหน้าและตั้งค่าเนื้อหาที่ต้องการส่งคืนท้ายที่สุด

## ส่งไฟล์ที่ได้จากการเรียกดู
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->file(public_path() . '/favicon.ico');
    }
}
```
- webman รับส่งไฟล์ขนาดใหญ่ได้
- สำหรับไฟล์ขนาดใหญ่ (มากกว่า 2M) webman จะไม่อ่านไฟล์ทั้งหมดเข้าไปในหน่วยความจำทีเดียว แต่จะอ่านไฟล์เป็นส่วนๆ ส่วนที่เหมาะสมและส่ง
- webman จะปรับปรุงการอ่านและการส่งไฟล์ตามความเร็วในการรับของทางลูกค้าเพื่อส่งไฟล์ไว้ที่ใจ และลดการใช้หน่วยความจำให้น้อยที่สุด
- การส่งข้อมูลไม่ขัดข้อง ไม่มีผลต่อการทำงานของร้องขออื่น ๆ
- โฟลเดอร์เก็บไฟล์ที่จะส่งสามารถส่งส่งรหัสสถานะ `if-modified-since` และเมื่อมีการร้องขอครั้งต่อไป เมื่อไฟล์ไม่ได้เปลี่ยนแปลงจะส่งกลับ 304 เพื่อประหยัดแบนด์วิดท์
- ตั้งค่า header อัตโนมัติสำหรับการส่งไฟล์ที่ดีที่สุด

## ดาวน์โหลดไฟล์
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'ชื่อไฟล์.ico');
    }
}
```
เมทอด download แทบไม่ต่างจากเมทอด file
1、ที่ต่างคือหลังจากตั้งชื่อไฟล์โดยปกติไฟล์จะโหลดลงมา และไม่แสดงที่เบราว์เซอร์
2、download ไม่ตรวจสอบ `if-modified-since` หัว

## รับผลลัพธ์
บางครั้งไลบรารีบางตัวนั้นจะพิมพ์เนื้อหาไปที่อินพุตมาตลอด นั่นคือข้อมูลจะถูกรับไปที่หน่วยความจำและไม่ได้ส่งไปที่เบราวเซอร์ ในกรณีนี้เราต้องใช้ `ob_start();` `ob_get_clean();` เพื่อจับข้อมูลไปที่ตัวแปรหนึ่งแล้วจึงส่งข้อมูลไปที่เบราวเซอร์ เช่น

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // สร้างภาพ
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

        // เริ่มจับผลลัพธ์
        ob_start();
        // ส่งอินเมจจัพ
        imagejpeg($im);
        // รับเนื้อหาของอินเมจ
        $image = ob_get_clean();
        
        // ส่งอินเมจ
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
