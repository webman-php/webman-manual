# การตอบกลับ

การตอบกลับจริง ๆ แล้วคืออ็อบเจ็กต์ `support\Response` เพื่อความสะดวกในการสร้างอ็อบเจ็กต์นี้ webman จึงมีฟังก์ชันช่วยบางอย่าง

## การส่งกลับข้อมูลใดก็ได้

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

ฟังก์ชัน response จะทำงานตามที่ต่อไปนี้:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

คุณยังสามารถสร้างอ็อบเจ็กต์ `response` ว่าง ๆ ก่อน จากนั้นอาจใช้ `$response->cookie()` `$response->header()` `$response->withHeaders()` `$response->withBody()` เพื่อตั้งค่าเนื้อหาที่จะส่งกลับ ดังต่อไปนี้
```php
public function hello(Request $request)
{
    // สร้างอ็อบเจ็กต์
    $response = response();
    
    // .... ลอจิกในธุรกิจที่ขาดหาย
    
    // ตั้งค่าคุกกี้
    $response->cookie('foo', 'value');
    
    // .... ลอจิกในธุรกิจที่ขาดหาย
   
    // ตั้งค่าหัวข้อ http
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
    ]);

    // .... ลอจิกในธุรกิจที่ขาดหาย

    // ตั้งค่าข้อมูลที่ต้องการส่งกลับ
    $response->withBody('ข้อมูลที่ต้องการส่งกลับ');
    return $response;
}
```

## ส่งกลับแบบ json
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
ฟังก์ชัน json ทำงานตามที่ต่อไปนี้:
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```

## ส่งกลับแบบ xml
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
ฟังก์ชัน xml ทำงานตามที่ต่อไปนี้:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## ส่งกลับแบบมุมมอง
สร้างไฟล์ `app/controller/FooController.php` ดังนี้:

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

สร้างไฟล์ `app/view/foo/hello.html` ดังนี้:

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

ฟังก์ชัน redirect ทำงานตามที่ต่อไปนี้:
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

## การตั้งค่าหัวข้อ
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
คุณสามารถใช้เมธอด `header` และ `withHeaders` เพื่อตั้งค่าหัวข้อแบบเดียวหรือเป็นท็อปด้วย 
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

คุณสามารถตั้งค่าหัวข้อล่วงหน้าและตั้งค่าข้อมูลที่จะส่งกลับท้ายที่สุด. 
```php
public function hello(Request $request)
{
    // สร้างอ็อบเจ็กต์
    $response = response();
    
    // .... ลอจิกในธุรกิจที่ขาดหาย

    // ตั้งค่าหัวข้อ http
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... ลอจิกในธุรกิจที่ขาดหาย

    // ตั้งค่าข้อมูลที่ต้องการส่งกลับ
    $response->withBody('ข้อมูลที่ต้องการส่งกลับ');
    return $response;
}
```

## ตั้งค่าคุกกี้

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

คุณสามารถตั้งค่าคุกกี้ล่วงหน้าและตั้งค่าข้อมูลที่จะส่งกลับท้ายที่สุด. 
```php
public function hello(Request $request)
{
    // สร้างอ็อบเจ็กต์
    $response = response();
    
    // .... ลอจิกในธุรกิจที่ขาดหาย

    // ตั้งค่าคุกกี้
    $response->cookie('foo', 'value');
    
    // .... ลอจิกในธุรกิจที่ขาดหาย

    // ตั้งค่าข้อมูลที่ต้องการส่งกลับ
    $response->withBody('ข้อมูลที่ต้องการส่งกลับ');
    return $response;
}
```

พารามิเตอร์เต็มของเมธอดคุกกี้คือ ดังนี้:

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## ส่งกลับไฟล์สตรีม
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

- webman รองรับการส่งไฟล์ขนาดใหญ่ที่สุด
- สำหรับไฟล์ขนาดใหญ่ (มากกว่า 2MB) webman จะไม่อ่านไฟล์ทั้งหมดในหน่วยความจำทันที แต่จะอ่านไฟล์เป็นส่วนสำหรับการส่งข้อมูลในเวลาที่เหมาะสม
- webman จะปรับปรุงการอ่านและการส่งไฟล์ตามความเร็วในการรับของไคลเอ็นต์เพื่อให้มีประสิทธิภาพในการส่งไฟล์ที่รวดเร็วที่สุดซึ่งจะลดการใช้หน่วยความจำลงที่สุด
- การส่งข้อมูลเป็นแบบไม่บล็อค จะไม่มีผลต่อการดำเนินการร้องขออื่น ๆ
- เมธอดไฟล์จะเพิ่มหัวร้องขอ `if-modified-since` และจะตรวจสอบ `if-modified-since` ในการร้องขอถัดไป หากไฟล์ไม่มีการเปลี่ยนแปลงจะส่งกลับ 304 เพื่อประหยัดแบนด์วิดธ์
- ไฟล์ที่ส่งกลับจะใช้หัวข้อ `Content-Type` ที่เหมาะสมสำหรับการส่งไปสู่เบราว์เซอร์
- หากไม่พบไฟล์ จะเปลี่ยนเป็นการตอบกลับ 404 โดยอัตโนมัติ
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

downloadเมธอดแทบเหมือนกับfileเมธอด แต่ความแตกต่างคือ
1. เมื่อตั้งชื่อไฟล์ที่ต้องการดาวน์โหลด ไฟล์จะถูกดาวน์โหลดลงมา แทนที่จะแสดงในเบราว์เซอร์
2. เมธอดdownloadจะไม่ตรวจสอบหัวข้อ `if-modified-since`

## การรับข้อมูลออก
บางครั้งไลบรารีบางรายการจะพิมพ์ข้อมูลไปทางการอย่างตรง หมายความว่าข้อมูลจะถูกพิมพ์ในท่านสายคำสั่งและจะไม่ถูกส่งให้เบราว์เซอร์ ในกรณีนี้เราจำเป็นต้องใช้ `ob_start();` `ob_get_clean();` เพื่อจัดการข้อมูลไว้อย่าพิมพ์ในท่านสายคำสั่งและจากนั้นส่งข้อมูลให้เบราวเซอร์ เช่น:

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

        // เริ่มต้นการจัดการการรับข้อมูลออก
        ob_start();
        // พิมพ์ภาพออกมา
        imagejpeg($im);
        // รับเนื้อหาของภาพ
        $image = ob_get_clean();
        
        // ส่งภาพ
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
