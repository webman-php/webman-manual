# مكون توليد رموز الخطأ تلقائيًا

## الوصف

يمكنه توليد رموز الخطأ تلقائيًا وفقًا للقواعد المعطاة.

> في البيانات المُرجَعَة يوجد معلمة code ، كل الرموز المخصصة ، الأرقام الموجبة تعني خدمة طبيعية، والأرقام السالبة تعني خدمة غير طبيعية.

## عنوان المشروع

https://github.com/teamones-open/response-code-msg

## التثبيت

```php
composer require teamones/response-code-msg
```

## الاستخدام

### ملف فئة ErrorCode فارغ

- مسار الملف ./support/ErrorCode.php

```php
<?php
/**
 * الملف المولد، يرجى عدم التعديل يدوياً.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### ملف التكوين

سيتم توليد رموز الخطأ تلقائيًا وفقًا للمعلمات المُحدّدة أدناه، على سبيل المثال عندما يكون system_number = 201، start_min_number = 10000، سيكون الرمز الأول الذي يتم إنشاؤه -20110001.

- مسار الملف ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // ملف فئة ErrorCode
    "root_path" => app_path(), // المسار الجذري لكودك الحالي
    "system_number" => 201, // الهوية النظام
    "start_min_number" => 10000 // نطاق إنشاء رموز الخطأ مثل 10000-99999
];
```

### إضافة رمز الخطأ التلقائي في start.php

- مسار الملف ./start.php

```php
// ضعه بعد Config::load(config_path(), ['route', 'container']);

// توليد رموز الخطأ، يتم توليدها فقط في وضع APP_DEBUG
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### الاستخدام في الكود

رمز الخطأ التالي **ErrorCode::ModelAddOptionsError** في الكود أدناه، حيث **ModelAddOptionsError** يجب على المستخدم كتابته بحسب احتياجاته الحالية بحيث يتم توحيدها بحرف كبير.

> ستجد أنه غير قابل للاستخدام بعد كتابته، سيتم توليده تلقائياً عند إعادة التشغيل في المرة التالية. تنبيه: قد يحتاج في بعض الأحيان إلى إعادة التشغيل مرتين.

```php
<?php
/**
 * فئة خدمة العمليات ذات الصلة بالتنقل
 */

namespace app\service;

use app\model\Demo as DemoModel;

// استيراد ملف فئة ErrorCode
use support\ErrorCode;

class Demo
{
    /**
     * إضافة
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
            // إخراج رسالة الخطأ
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### ملف ./support/ErrorCode.php بعد التوليد

```php
<?php
/**
 * الملف المولد، يرجى عدم التعديل يدوياً.
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
