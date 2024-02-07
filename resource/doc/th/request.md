# คำอธิบาย

## รับอ็อบเจ็กต์ขอคำขอ
webman จะแทรขอคำขอโดยอัตโนมัติไปยังพารามิเตอร์แรกของเมธอดแอ็กชัน เช่น

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
        // รับพารามิเตอร์ name จากคำขอ get และถ้าไม่มีการระบุพารามิเตอร์ name จะคืนค่าเป็น $default_name
        $name = $request->get('name', $default_name);
        // ส่งข้อความกลับไปยังเบราว์เซอร์
        return response('hello ' . $name);
    }
}
```

ผ่านอ็อบเจ็กต์ `$request` เราสามารถรับข้อมูลขอคำขอที่เกี่ยวข้องได้

**บางครั้งเราอาจต้องการรับอ็อบเจ็กต์ `$request` ของคำขอปัจจุบันจากคลาสอื่น ๆ ในกรณีนี้เราสามารถใช้ฟังก์ชันตัวช่วย `request()` ได้**

## รับพารามิเตอร์ของคำขอ get

**รับอาร์เรย์ทั้งหมดของ get**
```php
$request->get();
```
หากคำขอไม่มีพารามิเตอร์ get จะคืนค่าเป็นอาร์เรย์ที่ว่างเปล่า

**รับค่าของอาร์เรย์ get**
```php
$request->get('name');
```
หากอาร์เรย์ get ไม่มีค่านี้จะคืนค่าเป็น null

คุณยังสามารถส่งพารามิเตอร์ตัวสองให้กับเมธอด get หากอาร์เรย์ get ไม่พบค่าที่สอดคล้องกันจะคืนค่าตัวที่เป็นค่าเริ่มต้น เช่น
```php
$request->get('name', 'tom');
```

## รับพารามิเตอร์ของคำขอ post

**รับอาร์เรย์ทั้งหมดของ post**
```php
$request->post();
```
หากคำขอไม่มีพารามิเตอร์ post จะคืนค่าเป็นอาร์เรย์ที่ว่างเปล่า

**รับค่าของอาร์เรย์ post**
```php
$request->post('name');
```
หากอาร์เรย์ post ไม่มีค่านี้จะคืนค่าเป็น null

เหมือนกับเมธอด get คุณยังสามารถส่งพารามิเตอร์ตัวสองให้กับเมธอด post หากอาร์เรย์ post ไม่พบค่าที่สอดคล้องกันจะคืนค่าตัวที่เป็นค่าเริ่มต้น เช่น
```php
$request->post('name', 'tom');
```
## รับพารามิเตอร์ post ต้นฉบับ
```php
$post = $request->rawBody();
```
ฟังก์ชันนี้เหมือนกับการกระทำ `file_get_contents("php://input");` ใน `php-fpm` สำหรับการรับข้อมูลคำขอ post รูปแบบอีกแบบที่ไม่ใช่ `application/x-www-form-urlencoded` มีประโยชน์มาก

## รับเฮดเดอร์
**รับอาร์เรย์ทั้งหมดของเฮดเดอร์**
```php
$request->header();
```
หากคำขอไม่มีพารามิเตอร์เฮดเดอร์ จะคืนค่าเป็นอาร์เรย์ที่ว่างเปล่า โปรดทราบว่าคีย์ทั้งหมดเป็นตัวพิมพ์เล็ก

**รับค่าของอาร์เรย์เฮดเดอร์**
```php
$request->header('host');
```
หากอาร์เรย์เฮดเดอร์ไม่มีค่านี้จะคืนค่าเป็น null โปรดทราบว่าคีย์ทั้งหมดเป็นตัวพิมพ์เล็ก

เหมือนกับเมธอด get คุณยังสามารถส่งพารามิเตอร์ตัวสองให้กับเมธอดเฮดเดอร์ หากอาร์เรย์เฮดเดอร์ไม่พบค่าที่สอดคล้องกันจะคืนค่าตัวที่เป็นค่าเริ่มต้น เช่น
```php
$request->header('host', 'localhost');
```

## รับคุกกี้
**รับอาร์เรย์ทั้งหมดของคุกกี้**
```php
$request->cookie();
```
หากคำขอไม่มีพารามิเตอร์คุกกี้ จะคืนค่าเป็นอาร์เรย์ที่ว่างเปล่า

**รับค่าของอาร์เรย์คุกกี้**
```php
$request->cookie('name');
```
หากอาร์เรย์คุกกี้ไม่มีค่านี้จะคืนค่าเป็น null

เหมือนกับเมธอด get คุณยังสามารถส่งพารามิเตอร์ตัวสองให้กับเมธอดคุกกี้ หากอาร์เรย์คุกกี้ไม่พบค่าที่สอดคล้องกันจะคืนค่าตัวที่เป็นค่าเริ่มต้น เช่น
```php
$request->cookie('name', 'tom');
```

## รับข้อมูลทั้งหมด
ประกอบด้วยข้อมูลจาก `post` `get` ทั้งหมด
```php
$request->all();
```

## รับค่าที่ระบุ
รับค่าจาก `post` `get` ทั้งหมด
```php
$request->input('name', $default_value);
```

## รับข้อมูลบางส่วน
รับข้อมูลบางส่วนจาก `post` `get`
```php
// รับอาร์เรย์ที่เป็นผลลัพธ์จากการระบุคีย์ username และ password ซึ่งจะถูกถ้าไม่มีคีย์ที่ถูกระบุ
$only = $request->only(['username', 'password']);
// รับข้อมูลทั้งหมดนอกเหนือจาก avatar และ age
$except = $request->except(['avatar', 'age']);
```

## รับไฟล์ที่อัพโหลด
**รับอาร์เรย์ทั้งหมดของไฟล์ที่อัพโหลด**
```php
$request->file();
```

พร้อมด้วยฟอร์ม:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

`$request->file()` จะคืนค่าในรูปแบบเดียวกับ:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
เป็นอาร์เรย์ของอินสแตนท์ `webman\Http\UploadFile` โดย `webman\Http\UploadFile` คือคลาสจาก PHP ที่มีอยู่อย่างใน [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) และให้เมทอดอินสแตนท์

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // ได้หรือไม่ได้รับไฟล์ ตัวอย่างเช่น true|false
            var_export($spl_file->getUploadExtension()); // ได้นามสกุลไฟล์ที่อัพโหลด เช่น 'jpg'
            var_export($spl_file->getUploadMimeType()); // ได้ชนิด mine ของไฟล์ที่อัพโหลด เช่น 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // ได้รหัสข้อผิดพลาดการอัพโหลด เช่น UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // ได้ชื่อไฟล์ที่อัพโหลด เช่น 'my-test.jpg'
            var_export($spl_file->getSize()); // ได้ขนาดของไฟล์ เช่น 13364 หน่วยเป็นไบต์
            var_export($spl_file->getPath()); // ได้ตำแหน่งที่อัพโหลด เช่น '/tmp'
            var_export($spl_file->getRealPath()); // ได้เส้นทางไฟล์ชั่วคราว เช่น `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**โปรดทราบ:**

- ไฟล์ที่อัพโหลดจะถูกตั้งชื่อเป็นไฟล์ชั่วคราว เช่น `/tmp/workerman.upload.SRliMu`
- ขนาดของไฟล์ที่ได้อัพโหลดได้จะถูก จำกัด โดยการกำหนดขนาดเริ่มต้นที่ [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html) เป็น 10 M โดยปริยาย สามารถเปลี่ยนแปลงค่าเริ่มต้นได้ในไฟล์ `config/server.php` โดยการเปลี่ยนแปลง `max_package_size`
- ไฟล์ชั่วคราวจะถูกลบโดยอัตโนมัติหลังจากคำขอสิ้นสุด
- หากคำขอไม่มีการอัพโหลดไฟล์ `$request->file()` จะคืนค่าเป็นอาร์เรย์ที่ว่างเปล่า
- ไม่รองรับการใช้งาน `move_uploaded_file()` สำหรับไฟล์ที่อัพโหลด โปรดใช้เมทอด `$file->move()` ตามตัวอย่างด้านล่าง

### รับไฟล์ที่อัพโหลด
```php
$request->file('avatar');
```
หากไฟล์มีค่าจะคืนค่าเป็นอินสแตนส์ `webman\Http\UploadFile` ที่สอดคล้อง มิเช่นนั้นจะคืนค่าเป็น null

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
## รับ host
รับข้อมูล host ของการร้องขอ
```php
$request->host();
```
หากที่ร้องขอมาไม่ใช่พอร์ตมาตรฐาน 80 หรือ 443 ข้อมูล host อาจจะมีพอร์ตเช่น `example.com:8080` ถ้าไม่ต้องการรวมพอร์ต สามารถใส่พารามิเตอร์ตัวแรกเป็น `true`
```php
$request->host(true);
```

## รับวิธีการร้องขอ
```php
 $request->method();
```
คืนค่าอาจเป็น `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, หรือ `HEAD` หนึ่งวิธีการ

## รับ URI ของการร้องขอ
```php
$request->uri();
```
คืนค่า URI ของการร้องขอ รวมถึง path และส่วน queryString

## รับเส้นทางของการร้องขอ
```php
$request->path();
```
คืนเส้นทาง (path) ของการร้องขอ

## รับ queryString ของการร้องขอ
```php
$request->queryString();
```
คืนค่า queryString ของการร้องขอ

## รับ URL ของการร้องขอ
เมธอด `url()` คืนค่า URL โดยไม่รวมพารามิเตอร์ `Query`
```php
$request->url();
```
คืนค่าเช่น `//www.workerman.net/workerman-chat`

เมธอด `fullUrl()` คืนค่า URL รวมพารามิเตอร์ `Query`
```php
$request->fullUrl();
```
คืนค่าเช่น `//www.workerman.net/workerman-chat?type=download`

> **โปรดทราบ**
>`url()` และ `fullUrl()` ไม่คืนค่าส่วนโปรโตคอล (protocol) (ไม่คืนค่า http หรือ https) เนื่องจากในเบราว์เซอร์เมื่อใช้ที่อยู่ที่ขึ้นต้นด้วย `//` จะตรวจจับโปรโตคอลของเว็บไซต์ปัจจุบันโดยอัตโนมัติ และทำการส่งคำร้องขอโดยใช้ http หรือ https

หากคุณใช้ nginx proxy โปรดเพิ่ม `proxy_set_header X-Forwarded-Proto $scheme;` ลงในการกำหนด nginx ดูเพิ่มเติมที่ [nginx proxy](others/nginx-proxy.md) นี้ ทำให้สามารถทำการใช้ `$request->header('x-forwarded-proto');` เพื่อตรวจสอบว่าเป็น http หรือ https เช่น
```php
echo $request->header('x-forwarded-proto'); // แสดงผล http หรือ https
```

## รับเวอร์ชัน HTTP ของการร้องขอ
```php
$request->protocolVersion();
```
คืนค่าสตริง `1.1` หรือ `1.0`

## รับ sessionId ของการร้องขอ
```php
$request->sessionId();
```
คืนค่าสตริงที่ประกอบด้วยตัวอักษรและตัวเลข

## รับ IP ของกลุ่มผู้ใช้งาน (Client)
```php
$request->getRemoteIp();
```

## รับพอร์ตของกลุ่มผู้ใช้งาน (Client)
```php
$request->getRemotePort();
```

## รับ IP ของกลุ่มผู้ใช้งาน (Client) จริง
```php
$request->getRealIp($safe_mode=true);
```
เมื่อโปรเจกต์ใช้พร็อกซี(เช่น nginx) การใช้ `$request->getRemoteIp()` มักจะได้รับ IP ของเซิร์ฟเวอร์พร็อกซี (เช่น `127.0.0.1` `192.168.x.x`) แทน IP จริงของกลุ่มผู้ใช้งาน (Client) ในกรณีนี้ คุณสามารถลองใช้ `$request->getRealIp()` เพื่อรับ IP จริงของกลุ่มผู้ใช้งาน (Client)

`$request->getRealIp()` จะพยายามจะรับ IP จริงจากเชดอู่การร้องขอ HTTP ของ `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, `via`

> เนื่องจากเฮดเดร์ HTTP สามารถปลอมแปลงได้ง่าย ดังนั้นวิธีนี้ในการรับ IP จริงของกลุ่มผู้ใช้งานจึงไม่สามารถเชื่อถือได้ โดยเฉพาะถ้า `$safe_mode` เป็นเท็จ วิธีที่ที่ที่เป็นการนำเข้า IP ผู้ใช้จริงโดยอาจหลือกคือทราย IP ของเซิร์ฟเวอร์พร็อกซีที่มีความปลอดภัยทราบอันดับและมุ่นนร้ายโดยรู้จัก [การใช้งาน $request->getRemoteIp()` และอาจารย์ความปลอดภัย) หาก IP ที่คืนใช้งาน $request->getRemoteIp()` ยืนยันว่าเป็นเซิร์ฟเวอร์พร็อกซีที่ทราบอันดับและมุ่นนร้ายหลังจากนั้นโดยใช้`$request->header('โครงข่ากราง IP ที่จะนำเข้า')` รับ IP จริง

## รับ IP ของเซิร์ฟเวอร์
```php
$request->getLocalIp();
```

## รับพอร์ตของเซิร์ฟเวอร์
```php
$request->getLocalPort();
```

## ตรวจสอบว่าเป็นการร้องขอแบบ AJAX หรือไม่
```php
$request->isAjax();
```

## ตรวจสอบว่าเป็นการร้องขอแบบ PJAX หรือไม่
```php
$request->isPjax();
```

## ตรวจสอบว่าการร้องขอคาดหวังการคืนค่าในรูปแบบ JSON หรือไม่
```php
$request->expectsJson();
```

## ตรวจสอบว่าไคลเอ็นต์ได้รับการคืนค่าเป็น JSON หรือไม่
```php
$request->acceptJson();
```

## รับชื่อปลั๊กอินของการร้องขอ
การร้องขอที่ไม่ใช่แบบปลั๊กอินจะคืนค่าสตริงว่าง
```php
$request->plugin;
```
>คุณสมบูรณ์จำเป็นต้องใช้ ว็บแมน>=1.4.0

## รับชื่อแอพพลิเคชันของการร้องขอ
เมื่อมีแอพพลิเคชันเดียวเสมอคืนค่าสตริงว่าง
เมื่อมี [แอพพลิเคชันเยอะ](multiapp.md) คืนกลับชื่อของแอพพลิเคชัน
```php
$request->app;
```
>เนื่องจากฟังก์ชันปิดไม่ได้อยู่ภายใต้แอพพลิเคชันใด ๆ การร้องขอจากเส้นทางปิดในฟังก์ชันปิดคืนค่าสตริงว่าง
> ดูเพิ่มเติมที่ [เส้นทาง](route.md)

## รับชื่อคลาสควบคิลของการร้องขอ
รับชื่อคลาสควบคิลที่สอดคลีกัน
```php
$request->controller;
```
คืนค่าเช่น `app\controller\IndexController`

> เนื่องจากฟังก์ชันปิดไม่ได้อยู่ภายใต้คลาสควบคิลใด ๆ การร้องขอจากเส้นทางปิดในคลาสควบคิลคืนค่าสตริงว่าง
> ดูเพิ่มเติมที่ [เส้นทาง](route.md)

## รับชื่อเมธอดที่าร้องขอ
รับชื่อเมธอดที่สอดคลีกันของควบคิล
```php
$request->action;
```
คืนค่าเช่น `index`

> เนื่องจากฟังก์ชันปิดไม่ได้อยู่ภายใต้คลาสควบคิลใด ๆ การร้องขอจากเส้นทางปิดในคลาสควบคิลคืนค่าสตริงว่าง
> ดูเพิ่มเติมที่ [เส้นทาง](route.md)
