# ত্রুটি কোড কম্পোনেন্ট স্বয়ংক্রিয়ভাবে উৎপন্ন করুন

## বর্ণনা

নির্দিষ্ট নিয়ম অনুযায়ী ত্রুটি কোড স্বয়ংক্রিয়ভাবে উৎপন্ন করার ক্ষমতা।

> ফিরে দেওয়া ডেটা এর কোড প্যারামিটার, সমস্ত কাস্টম কোড, ধনাত্মক সংখ্যা পরিষেবা সাধ্য, নেতি সংখ্যা সেবা বাধিত করা।

## প্রকল্প ঠিকানা

https://github.com/teamones-open/response-code-msg

## ইনস্টল করুন

```php
composer require teamones/response-code-msg
```

## ব্যবহার

### খালি ErrorCode ক্লাস ফাইল

- ফাইল পথ ./support/ErrorCode.php

```php
<?php
/**
 * অটোমেটিক ফাইল, আপনি কাজীকরণ না করুন.
 * @লেখক: $Id$
 */
namespace support;

class ErrorCode
{
}
```

### কনফিগারেশন ফাইল

ত্রুটি কোডগুলি স্বয়ংক্রিয়ভাবে নির্ধারিত প্যারামিটার অনুযায়ী নিমিত হবে, উদাহরণস্বরূপ বর্তমান সিস্টেম নম্বর = 201, start_min_number = 10000, তবে তাদের মাঝের অগণিতের প্রথম ত্রুটি কোড হল -20110001।

- ফাইল পথ ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // ErrorCode ক্লাস ফাইল
    "root_path" => app_path(), // বর্তমান কোড রুট পাথ
    "system_number" => 201, // সিস্টেম শনাক্ত
    "start_min_number" => 10000 // ত্রুটি কোড উৎপন্ন ব্যাপ্তি, উদাহরণস্বরূপ 10000-99999
];
```

### start.php ফাইলে স্বয়ংক্রিয়ভাবে ত্রুটি কোড তৈরির কোড যোগ করুন

- ফাইল পথ ./start.php

```php
// কনফিগ::লোড (config_path(), [ 'পথ', 'আবডি']); পরে

// ত্রুটি কোড তৈরি করুন, শুধুমাত্র APP_DEBUG মোডেলে উৎপন্ন করুন
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### কোডে ব্যবহার

নিচের কোডগুলিতে **ErrorCode::ModelAddOptionsError** ত্রুটি কোড, যেখানে **ModelAddOptionsError** ব্যবহারকারী নিজের প্রয়োজনে শব্দানুসারিত হিসেবে প্রথম অক্ষরটি লেখা হয়।

> লিখে ফেলেন যে, আপনার সাথে ব্যবহার করা যাবে না, তা পুনরায় পুনরায় আরম্ভ করার পরে ত্রুটি কোড নিজস্বভাবে উৎপন্ন করা হবে। মনে রাখবেন, কিছুসময় পুনরায় পুনরায় আরম্ভ করার পর প্রয়োজন হতে পারে।

```php
<?php
/**
 * নেভিগেশন সম্পর্কিত অপারেশন সেবা ক্লাস
 */

namespace app\service;

use app\model\Demo as DemoModel;

// ErrorCode ক্লাস ফাইল আনুসরণ করুন
use support\ErrorCode;

class Demo
{
    /**
     * যোগ করুন
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
            // ত্রুটি বিবৃতি উৎপন্ন করুন
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### ./support/ErrorCode.php ফাইলে উৎপন্ন হওয়া

```php
<?php
/**
 * অটোমেটিক ফাইল, আপনি কাজীকরণ না করুন.
 * @লেখক: $Id$
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
