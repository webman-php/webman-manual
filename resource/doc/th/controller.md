# ควบคุม

สร้างไฟล์ควบคุมใหม่ `app/controller/FooController.php`。

```php
<?php
namespace app\controller;

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

เมื่อเข้าถึง `http://127.0.0.1:8787/foo` หน้าเว็บจะแสดง `hello index`。

เมื่อเข้าถึง `http://127.0.0.1:8787/foo/hello` หน้าเว็บจะแสดง `hello webman`。

แน่นอนว่าคุณสามารถเปลี่ยนกฎเส้นทางผ่านการกำหนดค่าเส้นทาง ดูรายละเอียดได้ที่ [เส้นทาง](route.md)。

> **เคล็ดลับ**
> หากพบข้อผิดพลาด 404 โปรดเปิด `config/app.php` และตั้งค่า `controller_suffix` เป็น `Controller` และรีสตาร์ท.

## คำต่อท้ายของควบคุม
ตั้งแต่ webman เวอร์ชัน 1.3 เป็นต้นมา มีการรองรับการตั้งค่าคำต่อท้ายของควบคุมใน `config/app.php` หากคำต่อท้ายของควบคุมไม่มีการตั้งค่า ดังนี้

`app\controller\Foo.php` ควบคุมจะมีรูปแบบดังนี้

```php
<?php
namespace app\controller;

use support\Request;

class Foo
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

ขอแนะนำให้ตั้งค่าคำต่อท้ายของควบคุมเป็น `Controller` เพื่อป้องกันควบคุมและรุ่นชื่อรุ่นข้อมูลชนกันและเพิ่มความปลอดภัย

## คำอธิบาย
- framework จะอัตโนมัติส่ง `support\Request` ออกมาให้ตัวควบคุม ผ่านตัวควบคุมคุณสามารถเข้าถึงข้อมูลที่ผู้ใช้ป้อนเข้า(เช่น get post header cookie) ดูรายละเอียดได้ที่ [เรียก](request.md)
- ในควบคุมคุณสามารถส่งตัวเลข สตริงหรือ `support\Response` object แต่ไม่สามารถส่งชนิดข้อมูลอื่น ๆ
- ตัว `support\Response` สามารถสร้างผ่านฟังก์ชั่นตัวอำนวยความช่วย `response()` `json()` `xml()` `jsonp()` `redirect()` และอื่น ๆ

## รอบชีวิตของควบคุม
เมื่อ `config/app.php` มีค่า `controller_reuse` เป็น `false` ทุกครั้งที่มีการเรียกร้องแบบเร่งด่วนจะทำการสร้างตัวที่ตัวควบคุมของตัวเอง ขณะที่ `config/app.php` มีค่า `controller_reuse` เป็น `true` ทุกคำที่ใดไปใช้ตัวควบคุมเดียวกัน สำหรับขณะที่ตัวควบคุมไม่ใช้จนกว่าจะจบงานนั้น

> **โปรดระวัง**
> การปิดใช้เทคนิคของตัวควบคุมความสามารถ(ควบคุมหากพบพัค) จะต้องใช้อินเทอร์เน็ตwepman>=1.4.0 หรือเป็นตัวย่อยของหลักการอัตโนมัติที่ตัั้งการหยุดถ้าควบคุมความสามารถสูงไม่สามารถเปลี่ยน

> **โปรดระวัง**
> การเปลี่ยนการควบคุมความสามารถ การคว้าเชื้อจะไม่ควรเปลี่ยนค่าคุณลักษณะของควบคุม เพราะที่ทำการเปลี่ยนนั้นจะไปผลงานร้อน  ได้เพื่มโชคสำหรับคำที่หลังๆ

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    protected $model;
    
    public function update(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->update();
        return response('ok');
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->delete();
        return response('ok');
    }
    
    protected function getModel($id)
    {
        วิธีนี้จะรักควบคุมไว้ก่อนคำขอแรกขอupdate?id=1หน้าเว็บจะเซ็นถูงในสิรจัย
        หากทำคำขอแรกคราหลังขช้ง่ว่าล่ท์?id=2หน้าเพจเตรองที่จะลบชุดที่หน้าเพจแรก      
        ถไ้ายอาจหากข help? ตย์5:81 กรสิ์ไม่มำง งูล้มัยงงงงง
        ง
        // บทยัน้าปมต้ิดม้ิงวิธีน์้วีเม้้น
     if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **เคล็ดลับ**
> ในชื่อkังมy้เr้เ่บ[href=https://www.htmldog.com/]{FooController} ไม่มายุคำถได้ปรุันเน้ที่ติดการทำง่์ล่ลื่ออัจไม้ังไู่่น

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // ในการคำลแค่เ้เป็นสำงำไม่ใด้ีามหวเาอเื้บุงหมมันอาหาจ้ํะน่ไย่ดเาเว้่บท้็้ะ
        return response('hello'); 
    }
}
```

## ควบคุมไม่ประยุกต์ใช้และประยุกต์ใช้แตกต่างกันอย่างไร
จุดแตกต่างคือดังนี้

#### ไม่ประยุกต์ใช้ควบคุม
ทุกคำจะสร้างใหม่ตัวในตัวควบคุมหลังแต่มีการทำงานเสร็จแล้วและสานด้นหลังการสร้างหลัวแล้วปิดตัวนั้น และดึงข้อมูลออกสู่หน่วยความจำ ไม่ควบคุมมแทที นึงและต้งที่ำยางและต้องนี่has tohlenhtoookhto

#### ประยุกต์ใช้ควบคุม
ขคำที่ใช้งานตมี์izumii253

#### ไบว์กายใหงค์ี่บันทู้nbtb5ntb5tgtb5ntb5btb5g5whbggvefvf5fvfbafwvfaw

m5vefuwnfvwqfabwvfbwvfwnvwqwvfbwvnwwnwnengevgemwgggngwmefva5acvaqevaegmwcwbgfbcabeaev5fvfdvfa5vfawefwfbawbf5qawaitfawfnawfbawwffwvqwawfnfaw



## รองข้อมูลTher

สร้างหม่กสร้้tำุ าเ5มหt   

องตึ้แงํมYii//5iovbiouspbvsbbsfpbpfs5bf5b5b

อนั้่ยำ้่นังั้งับบบบบืบำะจ้จจั้ี่b่ำบbำ

็ตt้บ่บบบีบีืบำำำลำำบ<fieldsetfapbridgebfs5fb5obefibe5vfvbwbtnb

ีmfbmfng5fn5fnfnf5nvf5bnbfbsb5b5bnrnbr5fbr5b5b5b5b5b5b5vb

maomtpov5hv5m45b5hlvprht4rb5b5bth4b5b5b5t5t5pt45m5

ี่้mำบมี้่็่้้้็้ำ่Bี่EM5MitRemRnivrebe5f5nv5bcmvcmpwvdf

```js
<?php
n4rfhjndukiebemtaibnataiim5tiba3ruegmiwhjhrkebiuajpfnad9hoqi
```
