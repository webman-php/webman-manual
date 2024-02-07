# การจัดการข้อยกเว้น

## การกำหนดค่า
`config/exception.php`
```php
return [
    // ที่นี่กำหนดคลาสการจัดการข้อยกเว้น
    '' => support\exception\Handler::class,
];
```
ในโหมดแอปพลิเคชันสูงสุด คุณสามารถกำหนดคลาสการจัดการข้อยกเว้นเป็นแต่ละแอปพลิเคชัน โปรดดูที่ [multiapp.md](multiapp.md)


## คลาสการจัดการข้อยกเว้นเริ่มต้น
ใน webman ข้อยกเว้นถูกจัดการโดยคลาส `support\exception\Handler` เราสามารถแก้ไขค่าเริ่มต้นของการจัดการข้อยกเว้นโดยแก้ไขไฟล์คอนฟิก `config/exception.php` คลาสการจัดการข้อยกเว้นจำเป็นต้องดำเนินการโดยการปฏิบัติตาม `Webman\Exception\ExceptionHandlerInterface` อินเตอร์เฟซ
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
     * แสดงผลคืน
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```
## เอาพล์พลากข้อคิด
เมทอด `render` ในคลาสการจัดการข้อผิดพลาดถูกใช้ในการเอาพล์พลากข้อคิด

หากค่า `debug` ในไฟล์การตั้งค่า `config/app.php` เป็น `true` (นี้จะถูกเรียกว่า `app.debug=true`) จะคืนข้อมูลข้อผิดพลาดที่ละเอียด มิเช่นนั้นจะคืนข้อมูลข้อผิดพลาดที่สั้นหรือกระชับ

หากคำขอต้องการคืนค่าเป็น json ข้อมูลข้อผิดพลาดที่คืนกลับจะเป็นในรูปแบบ json เช่น
```json
{
    "code": "500",
    "msg": "ข้อผิดพลาด"
}
```
ถ้า `app.debug=true` ข้อมูล json จะมีฟิลด์ `trace` เพิ่มเติมที่คืนข้อมูลการเรียกหาที่ละเอียด

คุณสามารถเขียนคลาสการจัดการข้อผิดพลาดของคุณเพื่อเปลี่ยนแปลงตรรกะการจัดการข้อผิดพลาดเริ่มต้น

# ข้อผิดพลาดธุรกิจ BusinessException
บางครั้งเราอาจต้องการหยุดคำขอในฟังก์ชันที่ซ้อนกันและคืนข้อความผิดพลาดกลับไปยังไคลเอนต์ สามารถทำได้โดยการโยน `BusinessException` ออกไป
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
            throw new BusinessException('ผิดพลาดในพารามิเตอร์', 3000);
        }
    }
}
```

ตัวอย่างข้างต้นจะคืนข้อความ
```json
{"code": 3000, "msg": "ผิดพลาดในพารามิเตอร์"}
```

> **หมายเหตุ**
> ข้อผิดพลาดธุรกิจ BusinessException ไม่จำเป็นต้องจัดการด้วย try catch เพราะ framework จะจัดการจับข้อผิดพลาดโดยอัตโนมัติและคืนข้อมูลเอาพล์พลากที่เหมาะสมตามประเภทคำขอ
## การระบุข้อยกเว้นธุรกิจที่กำหนดเอง

หากการตอบสนองข้างต้นไม่ตรงตามความต้องการของคุณ เช่น ต้องการเปลี่ยน `msg` เป็น `message` คุณสามารถกำหนดข้อยกเว้น `MyBusinessException` ของตัวเองได้

สร้าง `app/exception/MyBusinessException.php` ดังต่อไปนี้
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
        // สำหรับคำขอ json ให้ส่งค่าข้อมูลในรูปแบบ json
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // สำหรับคำขอที่ไม่ใช่ json ให้ส่งกลับหน้าเพจ
        return new Response(200, [], $this->getMessage());
    }
}
```

ด้วยการนี้เมื่อเกิดข้อยกเว้นทางธุรกิจ
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('ข้อผิดพลาดในพารามิเตอร์', 3000);
```
คำขอ json จะได้รับการตอบกลับในรูปแบบ json เช่นนี้
```json
{"code": 3000, "message": "ข้อผิดพลาดในพารามิเตอร์"}
```

> **เกร็ดความรู้**
> เนื่องจากข้อยกเว้น BusinessException เป็นข้อยกเว้นทางธุรกิจ (เช่น ข้อผิดพลาดของผู้ใช้ในการป้อนพารามิเตอร์) ซึ่งเป็นประการที่พยากรณ์ได้ เพราะฉะนั้นเฟรมเวิร์คจึงไม่พิจารณาว่าเป็นข้อผิดพลาดร้ายแรง และจะไม่บันทึกบันทึกการทำงาน

## สรุป
เมื่อต้องการหยุดคำขอปัจจุบันและตอบกลับข้อมูลให้กับไคเอ็นไทม์ คุณควรพิจารณาใช้ข้อยกเว้น `BusinessException` ในทุกกรณี
