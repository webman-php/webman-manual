# বর্ণনা

## অনুরোধ অবজেক্ট পেতে
ওয়াবম্যান স্বয়ংক্রিয়ভাবে অ্যাকশন পদক্ষেপে অনুরোধ অবজেক্টকে নিয়ে আসে এর মধ্যে, উদাহরণ সীমাংকিত হলো

**উদাহরণ** 
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // গেট অনুরোধ থেকে নাম প্যারামিটারটি পাওয়া যায়, যদি নাম প্যারামিটারটি পাঠানো না হয় তবে প্রতিষ্ঠানীকৃত_নাম ফিরিয়ে দেওয়া হবে
        $name = $request->get('name', $default_name);
        // প্রয়োগকর্তাকে স্ট্রিং ফিরিয়ে দিতে
        return response('hello ' . $name);
    }
}
```

`$request` অবজেক্ট ব্যবহার করে আমরা যেকোনো অনুরোধের সংব-related তথ্য পেতে পারি।

**পরিধিবিবৃতি**
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

## অনুরোধ প্রাপ্তি করুন

**পূর্ণ গেট অ্যারে পেতে**
```php
$request->get();
```
যদি অনুরোধ কোনও গেট প্যারামিটার না থাকে তবে এটি একটি ফাঁকা অ্যারে ফিরে যায়।


**কোনও একটি মানের জন্য গেট অ্যারে প্রাপ্তি করুন**
```php
$request->get('name');
```
যদি get অ্যারেতে এই মানটি থাকে না তবে null ফিরে যায়।

আপনি চাইলে দ্বিতীয় প্যারামিটারে একটি ডিফল্ট মান পাঠাতে পারেন, যদি get অ্যারেতে প্রাপ্ত মান না পাওয়া গিয়ে তা ফিরতে হয়। উদাহরণস্বরূপ:
```php
$request->get('name', 'tom');
```

## পোস্ট অনুরোধ প্রাপ্তি করুন
**পূর্ণ পোস্ট অ্যারে প্রাপ্তি করুন**
```php
$request->post();
```
যদি অনুরোধে কোনও পোস্ট প্যারামিটার না থাকে তবে এটি একটি ফাঁকা অ্যারে ফিরে যায়।


**কোনও একটি মানের পোস্ট অ্যারে প্রাপ্তি করুন**
```php
$request->post('name');
```
যদি post অ্যারেতে এই মানটি থাকে না তবে null ফিরে যায়।

get পদক্ষেপে একেবারে, আপনি কিনা ডিফল্টার মান দিতে পারেন দ্বিতীয় প্যারামিটার দিয়ে, যদি পোস্ট অ্যারেতে পাওয়া না যায়। উদাহরণস্বরূপ:
```php
$request->post('name', 'tom');
```

## অনুরোধের মৌলিক পোস্ট বডি
```php
$post = $request->rawBody();
```
এটি `php-fpm` একটি `file_get_contents("php://input");` অপেরেশনের মত। হ্যাট্টপয়ের মৌলিক রিকুইস্ট বডি প্রাপ্ত করার জন্য। এটি বিশেষভাবে প্রয়োজন যখন `application/x-www-form-urlencoded` স্তরের পোস্ট অনুরোধ তথ্য প্রাপ্তি। 


## হেডার প্রাপ্তি
**পূর্ণ হেডার অ্যারে প্রাপ্তি**
```php
$request->header();
```
যদি অনুরোধে হেডার প্যারামিটার না থাকে তবে এটি একটি ফাঁকা অ্যারে ফিরে যায়। সমস্ত কুই কুলম ছোট হবে।


**কোনও একটি মানের হেডার অ্যারে প্রাপ্ত করুন**
```php
$request->header('host');
```
যদি হেডার অ্যারেতে এই মানটি থাকে না তবে null ফিরে যায়। সমস্ত কুই কুলম ছোট হবে।

get যেমনভাবে, উ চিলে দ্বিতীয়ট প্যারামিটারে একটি ডিফল্ট মান দিতে পারেন, যদি হেডার অ্যারেতে পাওয়া যায় না। উদাহরণস্বরূপ:
```php
$request->header('host', 'localhost');
```

## কুকি প্রাপ্তি
**পূর্ণ কুকি অ্যারে প্রাপ্তি**
```php
$request->cookie();
```
যদি অনুরোধে কুকি প্যারামিটার না থাকে তবে এটি একটি ফাঁকা অ্যারে ফিরে যায়।


**কোনও একটি মানের কুকি অ্যারে প্রাপ্ত করুন**
```php
$request->cookie('name');
```
যদি কুকি অ্যারেতে এই মানটি থাকে না তবে null ফিরে যায়।

get যেমনভাবে, উ চিলে দ্বিতীয়ট প্যারামিটারে একটি ডিফল্ট মান দিতে পারেন, যদি কুকি অ্যারেতে পাওয়া যায় না। উদাহরণস্বরূপ:
```php
$request->cookie('name', 'tom');
```

## সমস্ত ইনপুট প্রাপ্তি করুন
`post` এবং `get` এর সংমিশ্রিত সংগ্রহ।
```php
$request->all();
```

## নির্দিষ্ট ইনপুট সংগ্রহ প্রাপ্তি
`post` এবং `get` এর সংমিশ্রিত সংগ্রহ থেকে নির্দিষ্ট মান প্রাপ্ত করুন।
```php
$request->input('name', $default_value);
```

## অংশ ইনপুট তথ্য প্রাপ্তি

প্রতিষ্ঠিত ফলাফল দেখার জন্য অনুরোধ দলগুলির অ্যারে প্রাপ্ত করুন, যদি সংস্থাপন কী থাকে না।
```php
$only = $request->only(['username', 'password']);
// avatar এবং age ছাড়া সমস্ত ইনপুট প্রাপ্ত করুন
$except = $request->except(['avatar', 'age']);
```

## আপলোড ফাইল প্রাপ্তি
**পূর্ণ আপলোড ফাইল অ্যারে প্রাপ্তি**
```php
$request->file();
```

ফর্মের ধরন:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

`$request->file()` ফরের ফর্ম্যাট অনুযায়ী:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
এটি একটি উপলভ্য `webman\Http\UploadFile` প্রচারে . `webman\Http\UploadFile` ক্লাসটি পিএইচপি নিত্য সাথে আসা এবং কিছু প্রয়োজনীয় পদক্ষেপ সরবরাহ নিয়েছে।

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // ফাইল কিনা বৈধ, উদাহরণস্বরূপ sসাত্য| মিথ্যা
            var_export($spl_file->getUploadExtension()); // আপলোড ফাইলের পরের অঙ্ক, উদাহরণস্বরূপ 'jpg'
            var_export($spl_file->getUploadMimeType()); // আপলোড ফাইল mine ধরণ, উদাহরণস্বরূপ 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // আপলোড ত্রুটি কোড প্রাপ্ত করুন, উদাহরণস্বরূপ আপলোড ত্রুটি কোড প্রাপ্ত করুন UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // আপলোড ফাইলের নাম, উদাহরণস্বরূপ 'my-test.jpg'
            var_export($spl_file->getSize()); // ফাইল সাইজ প্রাপ্ত করুন, উদাহরণস্বরূপ 13364, একক বাইট
            var_export($spl_file->getPath()); // আপলোডের পথ প্রাপ্ত করুন, উদাহরণস্বরূপ '/tmp'
            var_export($spl_file->getRealPath()); // অস্থিত
```php
$request->getRealIp($safe_mode=true);
```
প্রকল্প কোন ধরণের প্রক্সি (উদাহরণস্বরূপ nginx) ব্যবহার করতে যখন, তবে ` $request->getRemoteIp()` ব্যবহার করে অফিসিয়ালি প্রক্সি সার্ভার IP (যেমন `127.0.0.1` `192.168.x.x`) পেতে হয়, পরিবর্তনে গ্রাহকের অসলি IP পাওয়ার জন্য `$request->getRealIp()` চেষ্টা করা যেতে পারে।

`$request->getRealIp()` প্রয়োগে পড়তে ছুটে HTTP শিরোনাম `x-real-ip`ব্যবহার করতে চেষ্টা করেঃ `x-forwarded-for`,`client-ip` , `x-client-ip`,`via` ক্ষেত্র হইতে অসলো আই পি পেতে গোবর  করা।

> হিসোবে এই পদ্ধতিতে পাওয়া গ্রাহক আই পি শতসাহস্য বিশ্বস্ত নয়, বিশেষত যখন `$safe_mode` ফ্লাগটি মূল ছোড়া না। সফ্‌টওয়্‌যার এর মাধ্যমে প্রক্সি থেকে গ্রাহক সত্যায়িত আই পি পেতে একটি কার্যকর পদ্ধতি হচ্ছে, যেমন সুরক্ষিত প্রমাণিত প্রক্সি সার্ভার আইপি জানা আছে এবং প্রক্সিটি কোন এইচটিটি শিরোনাম বহন করে জানা যেতে পারে যদি `$request->getRemoteIp()` ফাংশনটি ফোাার্ড বর্তমান পরিবেশটির জন্য প্রযোজ্য ব্যাপারে সঠিক বিশ্বাস করি।


```php
$request->getLocalIp();
```
জানতে পাওয়া যায় সার্ভার এর অভ্যন্তরীন আই পি।


```php
$request->getLocalPort();
```
জানতে পাওয়া যায় সার্ভারের পোর্ট।


```php
$request->isAjax();
```
আজাক্স অনুরোধ কি না তা বিচার করা হয়।


```php
$request->isPjax();
```
পিজ্যাক্স অনুরোধ কি না তা বিচার করা হয়।


```php
$request->expectsJson();
```
অনুরোধ করা হয়েছে জোন রিটার্ন কোন রকমের জেসন।


```php
$request->acceptJson();
```
গ্রাহক কি জেসন রিটার্ন করা হয়েছে কোন রকমে।


```php
$request->plugin;
```
অন-প্লাগিন অনুরোধ করা হয়েছে।


```php
$request->app;
```
অ্যাপলিকেশন নাম পেয়ে যায়, যদিও এটি সিংগেল অ্যাপলিকেশনের জন্য হলেও, [মাল্টি এপ](multiapp.md) হচ্ছে application নাম।


```php
$request->controller;
```
যার সাুধ আইন্ডেক্স কন্ট্রোলারের ক্লাস নেই।


```php
$request->action;
```
যা তাদের নিঃশিশ্চ, কন্ট্রোলার একশনের নাম।

