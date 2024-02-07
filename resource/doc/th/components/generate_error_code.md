# คอมโพเนนต์การสร้างรหัสข้อผิดพลาด

## คำอธิบาย

สามารถสร้างรหัสข้อผิดพลาดโดยอัตโนมัติตามกฎที่กำหนด

> ตกลงกับพารามิเตอร์ code ในข้อมูลที่คืนคืน โดยทุกรหัสที่กำหนดเอง ค่าบวกแทนการให้บริการปกติ และค่าลบแทนการเกิดข้อผิดพลาด

## ที่อยู่โปรเจค

https://github.com/teamones-open/response-code-msg

## การติดตั้ง

```php
composer require teamones/response-code-msg
```

## การใช้

### ไฟล์คลาส ErrorCode ที่ว่างเปล่า

- ที่อยู่ไฟล์ ./support/ErrorCode.php

```php
<?php
/**
 * ไฟล์ที่สร้างโดยอัตโนมัติ กรุณาอย่าแก้ไขด้วยตนเอง
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### แฟ้มการตั้งค่า

รหัสข้อผิดพลาดจะถูกสร้างขึ้นโดยอัตโนมัติตามพารามิเตอร์ที่กำหนดด้านล่าง ตัวอย่างเช่น ระบุ system_number = 201 และ start_min_number = 10000 ถ้าเป็นเช่นนั้นรหัสข้อผิดพลาดแรกที่สร้างจะเป็น -20110001

- ที่อยู่ไฟล์ ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // ไฟล์คลาส ErrorCode
    "root_path" => app_path(), // ไดเรกทอรีรหัสอาร์เอปป์ปัจจุบัน
    "system_number" => 201, // ตัวแทนระบบ
    "start_min_number" => 10000 // ขอบเขตการสร้างรหัสข้อผิดพลาด เช่น 10000-99999
];
```

### เพิ่มรหัสเริ่มต้นที่ start.php

- ที่อยู่ไฟล์ ./start.php

```php
// วางหลังจาก Config::load(config_path(), ['route', 'container']);

// สร้างรหัสข้อผิดพลาด สำหรับโหมด APP_DEBUG เท่านั้น
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### ใช้ในโค้ด

รหัสข้อผิดพลาด **ErrorCode::ModelAddOptionsError** ในโค้ดข้างล่าง โดยที่ **ModelAddOptionsError** เป็นรหัสซึ่งผู้ใช้ต้องเขียนให้หมายถึงความหมายตามความต้องการปัจจุบันเป็นตัวพิมพ์ใหญ่

> เมื่อทำการเขียนเสร็จจะพบว่าไม่สามารถใช้งานได้ หลังจากนั้นจะสร้างรหัสข้อผิดพลาดที่สอดคล้องเมื่อเริ่มต้นใหม่ โปรดทราบว่าบางครั้งอาจจำเป็นต้องรีสตาร์ทสองครั้ง

```php
<?php
/**
 * คลาสเซอวิสที่เกี่ยวกับการทำงานที่เกี่ยวข้องกับ navigation
 */

namespace app\service;

use app\model\Demo as DemoModel;

// นำเข้าไฟล์คลาส ErrorCode
use support\ErrorCode;

class Demo
{
    /**
     * เพิ่ม
     * @param $data
     * @return array|mixed
     * @throws \exception
     */
    public function add($data): array
    {
        try {
            $demo = new DemoModel();
            foreach ($data as $key => $value) {
                $demo->$key = $value;
            }

            $demo->save();

            return $demo->getData();
        } catch (\Throwable $e) {
            // แสดงข้อผิดพลาด
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### แฟ้ม ./support/ErrorCode.php ที่สร้างขึ้นหลังการเริ่มต้น

```php
<?php
/**
 * ไฟล์ที่สร้างโดยอัตโนมัติ กรุณาอย่าแก้ไขด้วยตนเอง.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
    const LoginNameOrPasswordError = -20110001;
    const UserNotExist = -20110002;
    const TokenNotExist = -20110003;
    const InvalidToken = -20110004;
    const ExpireToken = -20110005;
    const WrongToken = -20110006;
    const ClientIpNotEqual = -20110007;
    const TokenRecordNotFound = -20110008;
    const ModelAddUserError = -20110009;
    const NoInfoToModify = -20110010;
    const OnlyAdminPasswordCanBeModified = -20110011;
    const AdminAccountCannotBeDeleted = -20110012;
    const DbNotExist = -20110013;
    const ModelAddOptionsError = -20110014;
    const UnableToDeleteSystemConfig = -20110015;
    const ConfigParamKeyRequired = -20110016;
    const ExpiryCanNotGreaterThan7days = -20110017;
    const GetPresignedPutObjectUrlError = -20110018;
    const ObjectStorageConfigNotExist = -20110019;
    const UpdateNavIndexSortError = -20110020;
    const TagNameAttNotExist = -20110021;
    const ModelUpdateOptionsError = -20110022;
}
```
