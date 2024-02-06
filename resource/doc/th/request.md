# คำอธิบาย

## รับอ็อบเจ็กต์ของคำขอ
webman จะฉีดอ็อบเจ็กต์ขอคำขอโดยอัตโนมัติเข้าไปยังพารามิเตอร์แรกของเมธอดการกระทำ เช่น

**ตัวอย่าง**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // รับพารามิเตอร์ name จากคำขอแบบ get ถ้าไม่รับพารามิเตอร์ name ก็จะคืนค่าเป็น $default_name
        $name = $request->get('name', $default_name);
        // ส่งข้อความกลับไปยังเบราว์เซอร์
        return response('hello ' . $name);
    }
}
```

ผ่าน `อ๋อเจ็กต์ $request` เราสามารถรับข้อมูลที่เกี่ยวข้องกับคำขอได้ทุกอย่าง

**บางครั้งเราต้องการรับ `อ็อบเจ็กต์ $request` ปัจจุบันจากคลาสอื่น ๆ ในกรณีนี้เราเพียงแค่ใช้ฟังก์ชั่นตัวช่วย `request()` ได้ทันที**

## รับพารามิเตอร์ของคำขอแบบ get

**รับอาร์เรย์ get ทั้งหมด**
```php
$request->get();
```
ถ้าคำขอไม่มีพารามิเตอร์ get ก็จะคืนเป็นอาร์เรย์ว่าง

**รับค่าของอาร์เรย์ get หนึ่งค่า**
```php
$request->get('name');
```
ถ้าอาร์เรย์ get ไม่รวมค่านี้ก็จะคืนค่าเป็น null

คุณยังสามารถส่งค่าเริ่มต้นไปยังอาร์เรย์ get ในพารามิเตอร์ที่สองได้ ถ้าไม่พบค่าที่เกี่ยวข้องในอาร์เรย์ get ก็จะคืนค่าเริ่มต้น ตัวอย่างเช่น:
```php
$request->get('name', 'tom');
```

## รับพารามิเตอร์ของคำขอแบบ post
**รับอาร์เรย์ post ทั้งหมด**
```php
$request->post();
```
ถ้าคำขอไม่มีพารามิเตอร์ post ก็จะคืนเป็นอาร์เรย์ว่าง

**รับค่าของอาร์เรย์ post หนึ่งค่า**
```php
$request->post('name');
```
ถ้าอาร์เรย์ post ไม่รวมค่านี้ก็จะคืนค่าเป็น null

เหมือนกับเมธอด get คุณยังสามารถส่งค่าเริ่มต้นไปยังอาร์เรย์ post ในพารามิเตอร์ที่สองได้ ถ้าไม่พบค่าที่เกี่ยวข้องในอาร์เรย์ post ก็จะคืนค่าเริ่มต้น ตัวอย่างเช่น:
```php
$request->post('name', 'tom');
```

## รับโค้ดพักเก็บคำขอ post ต้นฉบับ
```php
$post = $request->rawBody();
```
คุณสามารถใช้เมธอดนี้เหมือนกับการดำเนินการ `file_get_contents("php://input");` ใน `php-fpm` เพื่อรับบรรทัดคำขอพักเก็บต้นฉบับ ซึ่งมีประโยชน์เมื่อต้องการรับข้อมูลคำขอ post รูปแบบที่ไม่ใช่ `application/x-www-form-urlencoded`

## รับเฮดเดอร์
**รับอาร์เรย์เฮดเดอร์ทั้งหมด**
```php
$request->header();
```
ถ้าคำขอไม่มีพารามิเตอร์เฮดเดอร์ ก็จะคืนเป็นอาร์เรย์ว่าง ๆ โปรดทราบว่าทุกคีย์จะเป็นตัวพิมพ์เล็ก

**รับค่าของอาร์เรย์เฮดเดอร์หนึ่งค่า**
```php
$request->header('host');
```
ถ้าอาร์เรย์เฮดเดอร์ไม่รวมค่านี้ก็จะคืนค่าเป็น null โปรดทราบว่าทุกคีย์จะเป็นตัวพิมพ์เล็ก

เหมือนกับเมธอด get คุณยังสามารถส่งค่าเริ่มต้นไปยังอาร์เรย์เฮดเดอร์ในพารามิเตอร์ที่สองได้ ถ้าไม่พบค่าที่เกี่ยวข้องในอาร์เรย์เฮดเดอร์ ก็จะคืนค่าเริ่มต้น ตัวอย่างเช่น:
```php
$request->header('host', 'localhost');
```

## รับคุ๊กกี้
**รับอาร์เรย์คุ๊กกี้ทั้งหมด**
```php
$request->cookie();
```
ถ้าคำขอไม่มีคุ๊กกี้ ก็จะคืนเป็นอาร์เรย์ว่าง

**รับค่าของอาร์เรย์คุ๊กกี้หนึ่งค่า**
```php
$request->cookie('name');
```
ถ้าคำขอไม่มีค่านี้ในอาร์เรย์คุ๊กกี้ ก็จะคืนค่าเป็น null

เหมือนกับเมธอด get คุณยังสามารถส่งค่าเริ่มต้นไปยังอาร์เรย์คุ๊กกี้ในพารามิเตอร์ที่สองได้ ถ้าไม่พบค่าที่เกี่ยวข้องในอาร์เรย์คุ๊กกี้ ก็จะคืนค่าเริ่มต้น ตัวอย่างเช่น:
```php
$request->cookie('name', 'tom');
```

## รับอิท แอนด์ คาล์
ประกอบด้วยคอลเลกชันของ `post` `get`

```php
$request->all();
```

## รับค่าอินพุตที่ระบุ
จาก `post` `get` คอลเลกชันเราสามารถรับค่าที่เฉพาะเอาได้

```php
$request->input('name', $default_value);
```

## รับข้อมูลอินพุตบางส่วน
จาก `post` `get` คอลเลกชันเราสามารถรับข้อมูลบางส่วนได้

```php
// รับอาเรย์ที่ประกอบด้วย username และ password หากไม่มี key ก็จะถูกข้าม
$only = $request->only(['username', 'password']);
// รับข้อมูลทั้งหมดที่ไม่รวม avatar และ age
$except = $request->except(['avatar', 'age']);
```

## รับไฟล์ที่อัพโหลด
**รับอาร์เรย์ไฟล์ทั้งหมด**
```php
$request->file();
```

แบบฟอร์มที่คล้าย:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

`$request->file()` จะคืนค่าในรูปแบบเช่น:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
มันเป็นอาร์เรย์ของอ็อบเจ็กต์ `webman\Http\UploadFile` โดย `webman\Http\UploadFile` คลาสสืบทอดมาจากคลาส [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) ภายใน PHP และมีเมธอดสำหรับการใช้งานที่สามารถใช้งานได้

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // ไฟล์ที่ถูกต้องหรือไม่ เช่น จริง|เท็จ
            var_export($spl_file->getUploadExtension()); // นามสกุลไฟล์ที่อัพโหลด เช่น 'jpg'
            var_export($spl_file->getUploadMimeType()); // mine ของไฟล์ที่อัพโหลด เช่น 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // ได้รับการรหัสข้อผิดพลาดการอัพโหลด เช่น UPLOAD_ERR_NO_TMP_DIR
            var_export($spl_file->getUploadName()); // ชื่อของไฟล์ที่อัพโหลด เช่น 'my-test.jpg'
            var_export($spl_file->getSize()); // รับขนาดของไฟล์ เช่น 13364 หน่วยเป็นไบต์
            var_export($spl_file->getPath()); // รับที่อยู่ของการอัพโหลด เช่น '/tmp'
            var_export($spl_file->getRealPath()); // รับที่อยู่ของไฟล์ชั่วคราว เช่น `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**โปรดทราบ**

- ไฟล์ที่ถูกอัพโหลดจะถูกตั้งชื่อใหม่เป็นไฟล์ชั่วคราว เช่น `/tmp/workerman.upload.SRliMu`
- ขนาดของไฟล์ที่อัพโหลดจะถูก จำกัด โดย [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html) ค่าเริ่มต้นคือ 10M สามารถแก้ไข ในไฟล์ `config/server.php` โดยที่ `max_package_size` สามารถเปลี่ยนค่าเริ่มต้น
- ไฟล์ชั่วคราวจะถูกลบโดยอัตโนมัติหลังจากคำขอจบลง
- หากคำขอไม่ได้ทำการอัพโหลดไฟล์ `$request->file()` จะคืนค่าเป็นอาร์เรย์ว่าง
- การอัพโหลดไฟล์ไม่สนับสนุน `move_uploaded_file()` กรุณาใช้ เมธอด `$file->move()` แทน ดูตัวอย่างต่อไป

### รับไฟล์อัพโหลดที่กำหนด
```php
$request->file('avatar');
```
หากมีไฟล์ ก็จะคืนค่าเป็นอ็อบเจ็กต์ `webman\Http\UploadFile` ที่เก็บไฟล์ให้ แต่ถ้าไม่มีก็จะคืนค่าเป็น null

**ตัวอย่าง**
```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function file(Request $request)
    {
        $file = $request->file('avatar');
        if ($file && $file->isValid()) {
            $file->move(public_path().'/files/myfile.'.$file->getUploadExtension());
            return json(['code' => 0, 'msg' => 'upload success']);
        }
        return json(['code' => 1, 'msg' => 'file not found']);
    }
}
```

## รับโฮสต์
รับข้อมูลโฮสต์ของคำขอ

```php
$request->host();
```
หาก URL ของคำขอไม่ใช่พอร์ทนิมทศ 80 หรือ 443 ข้อมูลโฮสต์อาจมีพอร์ทส่งมาด้วย เช่น `example.com:8080` ถ้าไม่ต้องการพอร์ท กรุณาใส่อาร์กิวเมนต์ที่สองเป็น `true`

```php
$request->host(true);
```

## รับเมธอดของคำขอ
```php
 $request->method();
```
คืนค่าอาจจะเป็น `GET` `POST` `PUT` `DELETE` `OPTIONS` `HEAD`

## รับuriของคำขอ
```php
$request->uri();
```
คืนค่าuriของคำขอ รวมทั้ง path และ queryString

## รับเส้นทางของคำขอ
```php
$request->path();
```
คืนค่าเป็นเส้นทางของคำขอ

## รับqueryStringของคำขอ
```php
$request->queryString();
```
คืนค่าเป็น queryString ของคำขอ

## รับ url ของคำขอ
## รับที่ประวัติศาสตร์ IP จริงของไคลเอนต์
```php
$request->getRealIp($safe_mode=true);
```

เมื่อโปรเจ็กต์ใช้พร็อกซี (เช่น nginx) การใช้ `$request->getRemoteIp()` มักจะได้ IP ของพร็อกซีเซิร์ฟเวอร์ (เช่น `127.0.0.1` `192.168.x.x`) แทนที่จะได้ IP จริงของไคลเอนต์ ในกรณีนี้ สามารถลองใช้ `$request->getRealIp()` เพื่อรับที่ประวัติศาสตร์ IP จริงของไคลเอนต์ ได้

`$request->getRealIp()` จะพยายามรับข้อมูลที่ประวัติศาสตร์ IP จริงจากฟิลด์ `x-real-ip` 、`x-forwarded-for` 、`client-ip` 、`x-client-ip` และ `via` ในหัวข้อ HTTP

> เนื่อจากหัว HTTP มีความจำโอน ในที่นี้จึกรับได้จริง IP ของไคลเอนต์ไม่ใช่ 100% และโดยเฉพาะ `$safe_mode` เป็นเท็จ การฉ้อโกงเจ้าของ IP จริงของไคลเอนต์ที่ได้รับจากพร็อกซี วิธีที่มั่นคงสะท้อนผล คือ การที่ทราบ IP ของพร็อกซีเซิร์ฟเวอร์ที่มั่นคงและมั่นใจด้วยว่าจะรับ IP จริง การทำ 1.์ `$request->getRemoteIp()` รับ IP มีขอทราบ แล้ว พร้อมทั้งทำ 2. การรับ IP จริงสะท้อนผลแล้วที่ `.header('การนำไปใช้ IP จริง HTTP')`


## รับไอพีเซิร์ฟเวอร์
```php
$request->getLocalIp();
```

## รับพอร์ตเซิร์ฟเวอร์
```php
$request->getLocalPort();
```

## ตรวจสอบว่าเป็นการขอข้อมูลแบบ ajax หรือไม่
```php
$request->isAjax();
```

## ตรวจสอบว่าเป็นการขอข้อมูลแบบ pjax หรือไม่
```php
$request->isPjax();
```

## ตรวจสอบว่าคาดหวังข้อมูลที่ส่งกลับเป็น json หรือไม่
```php
$request->expectsJson();
```

## ตรวจสอบว่าไคลเอนต์ได้รับข้อมูลที่ส่งกลับเป็น json หรือไม่
```php
$request->acceptJson();
```

## รับชื่อของปลั๊กอินที่ขอ
หากไม่ใช่การขอข้อมูลจากปลั๊กอิน ฟังก์ชันนี้จะส่งคืนสตริงว่าง  `''` แทนที่
```php
$request->plugin;
```
> คุณลักษณะนี้ต้องการ webman>=1.4.0

## รับชื่อของแอปพลิเคชั่นที่ขอ
ในกรณีของแอปพลิเคชั่นเดียว ฟังก์ชันนี้จะส่งคืนสตริงว่างเสมอ  `''`, ในกรณีของ [multiapp](multiapp.md) จะส่งคืนชื่อของแอปพลิเคชั่น
```php
$request->app;
```

> เนื่อที่ฟังก์ชันปิดการใช้งานไม่เรียงถึง แอปพลิเคชั่นคำขอ `$request->app` จะมาว่างเสมอ `''`  
> การใช้งานหลักใน [หน้าที่BerryRoute](route.md)

## รับชื่อคลาสควบคุมที่ขอ
รับคลาสควบคุมที่สองกับชื่อของคลาสนั้น
```php
$request->controller;
```
ส่งสตริงเช่น `app\controller\IndexController`

> เนื่อเป็นการช่วยเปิดที่ฟังก์ชันวางจากคลาสควบคุม ดังที่ข่าวเป็น คำขอ `$request->controller` รับไปว่าวางเป็นสตริงว่าง `''`  
> การใช้งานหลักใน [หน้าที่BerryRoute](route.md)

## รับชื่อของเมธอดที่ขอ
รับเมธอดคลาสควบคุม ที่ถูกขอ
```php
$request->action;
```
ส่งสตริงเช่น`index`

> เนื่อเป็นการช่วยเปิดที่ฟังก์ชันวางจากคลาสควบคุม ดังที่ข่าวเป็น คำขอ `$request->action` รับไปว่าวางเป็นสตริงว่าง `''`  
> การใช้งานหลักใน [หน้าที่BerryRoute](route.md)
