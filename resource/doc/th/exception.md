# การจัดการข้อยกเว้น

## การกำหนดค่า
`config/exception.php`
```php
return [
    // ที่นี่กำหนดคลาสการจัดการข้อยกเว้น
    '' => support\exception\Handler::class,
];
```
เมื่อใช้โหมดแอปพลิเคชันหลายตัว คุณสามารถกำหนดคลาสการจัดการข้อยกเว้นแยกตามแอปพลิเคชันที่แต่ละตัว ดูเพิ่มเติมที่ [multiapp.md](multiapp.md)

## คลาสการจัดการข้อยกเว้นเริ่มต้น
webman ใช้คลาส `support\exception\Handler` เป็นคลาสเริ่มต้นในการจัดการข้อยกเว้น คุณสามารถแก้ไขไฟล์กำหนดค่า `config/exception.php` เพื่อเปลี่ยนคลาสการจัดการข้อยกเว้นเริ่มต้น คลาสการจัดการข้อยกเว้นจำเป็นต้องใช้ `Webman\Exception\ExceptionHandlerInterface` ไอเท็มเพื่อดำเนินการ
```php
interface ExceptionHandlerInterface
{
    /**
     * บันทึกบันทึก
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * แสดงผลการตอบรับ
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```

## แสดงผลการตอบรับ
เมธอร์การจัดการข้อยกเว้นภายในคลาส `render` ถูกใช้เพื่อแสดงผลการตอบรับ

หากค่า `debug` ในไฟล์กำหนดค่า `config/app.php` คือ `true` (เราจะเก็บตัวย่อข้อมูลข้อยกเว้น) ระบบจะส่งข้อมูลข้อยกเว้นที่ละเอียดมาก ไม่เช่นนั้นระบบจะส่งข้อมูลข้อยกเว้นที่ละเอียดน้อย
หากคำขอต้องการค่าคืนที่เป็น json ข้อมูลข้อยกเว้นที่ส่งกลับจะอยู่ในรูปแบบ json ดังนี้
```json
{
    "code": "500",
    "msg": "ข้อผิดพลาด"
}
```
หาก `app.debug=true` ข้อมูล json จะมีฟิลด์เพิ่มข้อความ `trace` สำหรับเรียกฟังก์ชันที่ละเอียดมาก

คุณสามารถเขียนคลาสการจัดการข้อยกเว้นเพื่อเปลี่ยนตรรกงานการจัดการข้อยกเว้นเริ่มต้น

# ข้อยกเว้นภายการจัดธุรกิจ BusinessException
บางครั้ง เราต้องการหยุดคำขอในฟังก์ชันที่ซ้อนข้างในและส่งค่าผิดพลาดกลับไปที่ไคลเอนต์ นอกจากนี้ เราสามารถทำได้โดยการโยน `BusinessException`
ตัวอย่างเช่น:

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->chackInpout($request->post());
        return response('hello index');
    }
    
    protected function chackInpout($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('ข้อผิดพลาดพารามิเตอร์', 3000);
        }
    }
}
```

ตัวอย่างข้างต้นจะส่งค่ากลับเป็น
```json
{"code": 3000, "msg": "ข้อผิดพลาดพารามิเตอร์"}
```

> **โปรดทราบ**
> BusinessException ไม่จำเป็นต้อง try-catch การจัดการข้อยกเว้น, โครงสร้างจะทำการจัดการข้อยกเว้นและจะส่งค่าตรงตามรูปแบบที่ต้องการ

## การจัดการข้อยกเว้นภายในเริ่มต้น

หากการส่งค่าข้อมูลด้านบนไม่เหมาะสมตามความต้องการ เช่น ต้องการเปลี่ยนคำขอ `msg` เป็น `message` คุณสามารถสร้างไฟล์คลาสใหม่ชื่อ `MyBusinessException`

สร้าง `app/exception/MyBusinessException.php` โดยใส่เนื้อหาดังนี้
```php
<?php

namespace app\exception;

use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class MyBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        // การขอ json ส่งค่ากลับเป็นข้อมูล json
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // แทนขอแบบไม่เป็น json ส่งค่ากลับเป็นหน้าเว็บ
        return new Response(200, [], $this->getMessage());
    }
}
```

ด้วยวิธีนี้ เมื่อทำการโยนคำขอดังต่อไปนี้
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('ข้อผิดพลาดพารามิเตอร์', 3000);
```
คำขอ json จะได้รับค่าคล้ายกับนี้
```json
{"code": 3000, "message": "ข้อผิดพลาดพารามิเตอร์"}
```

> **เกริ่นแนะ**
> เนื่องจากข้อยกเว้นของ BusinessException เป็นข้อยกเว้นที่เกิดขึ้นเนื่องจากการดิพุทของผู้ใช้งานไม่ถูกต้อง ดัังนั้น ระบบไม่ได้คิดว่ามันเป็นข้อผิดพลาดนับถือว่ามันเป็นคำขอที่คาดการณ์ได้ ปลดสารข้อมูลการตอบรับมันไม่ได้บันทึกหลักฐาน

## สรุป
ในกรณีที่ต้องการให้คำขอปัจจุบันหยุดและส่งค่ากลับไปที่ไคลเอนต์ คุณสามารถพิจารณาการใช้ข้อยกเว้น `BusinessException`
