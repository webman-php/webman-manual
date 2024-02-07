# ত্রুটি কোড কম্পোনেন্ট স্বয়ংক্রিয়ভাবে তৈরি করুন

## বর্ণনা

পূর্ণ নিযুক্তি অনুসারে ত্রুটি কোড স্বয়ংক্রিয়ভাবে উৎপন্ন করতে।

> ফিরে আসা ডেটাতে কোড প্যারামিটার, সমস্ত ব্যক্তিগত কোড, ধনাত্মক সংখ্যা সেবা সাধারণত, নেতিবাচক সেবা সাধারণত নেতিবাচক অবস্থা দেখায়।

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
 * স্বয়ংক্রিয়ভাবে উৎপন্ন ফাইল, দয়া করে ম্যানুয়ালি সম্পাদনা না করুন।
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```
### কনফিগারেশন ফাইল

ত্রুটি কোড স্বয়ংক্রিয়ভাবে নিম্নলিখিত সেটিংস মাধ্যমে উত্পন্ন হবে, যেমন বর্তমান system_number = 201, start_min_number = 10000, এটির মাধ্যমে প্রথম ত্রুটি কোড উৎপন্ন হবে -20110001।

- ফাইল পথ ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // ErrorCode ক্লাস ফাইল
    "root_path" => app_path(), // বর্তমান কোড রুট পাথ
    "system_number" => 201, // সিস্টেম অথবা ইডেন্টিফায়ার
    "start_min_number" => 10000 // ত্রুটি কোড উত্পন্নের ব্যাপ্তি যেমন 10000-99999
];
```

### start.php ফাইলে স্বয়ংক্রিয়ভাবে ত্রুটি কোড জেনারেট করতে যোগ করুন

- ফাইল পথ ./start.php

```php
// Config::load(config_path(), ['route', 'container']); পরে রাখুন

// ত্রুটি কোড জেনারেট, শুধুমাত্র APP_DEBUG মোডে উৎপন্ন করুন
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### কোডে ব্যবহার

নিচের কোডে কোড **ErrorCode::ModelAddOptionsError** , **ModelAddOptionsError**  অংশ ব্যবহার করা হয়, এখানে **ModelAddOptionsError** হল ত্রুটি কোড, যা ব্যবহারকারীরা তাদের চাহিদার অনুযায়ী শব্দিকরণ করতে বা ব্যবহার করতে পারে।

> আপনি লিখে দেখতে পাবেন যে, ব্যবহার করা হয়নি, পুনরায় পুনরায় শুরু করা হবে, পুনরায় পুনরায় পুনরসন্ধান করার জন্য টেন্সন করতে হবে।

```php
<?php
/**
 * নেভিগেশনে সম্পর্কিত অপারেশন সার্ভিস ক্লাস
 */

namespace app\service;

use app\model\Demo as DemoModel;

// ErrorCode ক্লাস ফাইল আনুষাঙ্গিক করুন
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
            // ত্রুটি তথ্য বিস্তারিত দেখান
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### ধারণক্ষেত্রে তৈরি ./support/ErrorCode.php ফাইল

```php
<?php
/**
 * স্বয়ংক্রিয়ভাবে তৈরি ফাইল , দয়া করে ম্যানুয়ালি সম্পাদনা না করুন।
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
