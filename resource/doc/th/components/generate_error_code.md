# สร้างคอมโพเนนต์รหัสข้อผิดพลาด

## อธิบาย

สามารถสร้างรหัสข้อผิดพลาดโดยอัตโนมัติตามกฎที่กำหนดไว้

> ตกลงในพารามิเตอร์ของการส่งคืนรหัสข้อมูลทั้งหมด code รหัสทุกคนที่กำหนดเป็นบวกแทนการบริการที่เรียบร้อยและติดลบแทนการบริการผิดปกติ

## ที่อยู่โปรเจกต์

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
 * ไฟล์ที่สร้างโดยอัตโนมัติ กรุณาอย่าแก้ไขด้วยตนเอง.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### ไฟล์การกำหนดค่า

รหัสข้อผิดพลาดจะถูกสร้างขึ้นโดยอัตโนมัติตามพารามิเตอร์ที่กำหนดด้านล่าง เช่นรหัสของระบบที่ผ่านมา = 201, start_min_number = 10000 ดังนั้นข้อผิดพลาดแรกที่ถูกสร้างคือ -20110001

- ที่อยู่ไฟล์ ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // ไฟล์คลาส ErrorCode
    "root_path" => app_path(), // ตำแหน่งของรากของโค้ดปัจจุบัน
    "system_number" => 201, // ระบบ
    "start_min_number" => 10000 // ช่วงของรหัสข้อผิดพลาด เช่น 10000-99999
];
```

### เพิ่มการเริ่มต้นสร้างรหัสข้อผิดพลาดโดยอัตโนมัติใน start.php

- ที่อยู่ไฟล์ ./start.php

```php
// วางหลังจาก Config::load(config_path(), ['route', 'container']);

// สร้างรหัสข้อผิดพลาด เฉพาะในโหมด APP_DEBUG เท่านั้น
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### การใช้ในโค้ด

ในโค้ดด้านล่าง **ErrorCode::ModelAddOptionsError** เป็นรหัสข้อผิดพลาดซึ่ง **ModelAddOptionsError** ต้องถูกเขียนโดยผู้ใช้ตามความต้องการปัจจุบันเพื่อให้มีความหมายตามภาพรวมและให้ตัวพิมพ์ใหญ่ตัวแรก

> เมื่อคุณเขียนเสร็จสิ้น คุณจะพบว่าคุณไม่สามารถใช้งานได้ หลังจากนั้นนำไปรีสตาร์ทอีกครั้งจะสร้างรหัสข้อผิดพลาดที่สอดคล้องกัน โปรดทราบว่าบางครั้งอาจต้องรีสตาร์ทสองครั้ง

```php
<?php
/**
 * คลาสบริการที่เกี่ยวข้องกับการนำทาง
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
            // แสดงข้อความข้อผิดพลาด
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### ไฟล์ ./support/ErrorCode.php หลังจากการสร้าง

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
