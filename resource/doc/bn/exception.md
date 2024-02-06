# অসুবিধা নিপুণন

## কনফিগারেশন
`config/exception.php`
```php
return [
    // এখানে ব্যাপারগুলি এক্সেপশন হ্যান্ডলিং ক্লাস কনফিগার করুন
    '' => support\exception\Handler::class,
];
```
এমন কারণে, আপনি প্রতিটি অ্যাপ্লিকেশনের জন্য বিশেষ অসুবিধা নিপুণন ক্লাস কনফিগার করতে পারেন, [মাল্টি-অ্যাপ্লিকেশন](multiapp.md) দেখুন

## ডিফল্ট অসুবিধা হ্যান্ডলিং ক্লাস
ওয়েবম্যানের মধ্যে অসুবিধা স্বয়ংক্রিয়ভাবে `support\exception\Handler` ক্লাস দ্বারা হ্যান্ডল করা হয়। ডিফল্ট অসুবিধা নিপুণন ক্লাস পরিবর্তন করতে কনফিগারেশন ফাইল `config/exception.php` পরিবর্তন করুন। অসুবিধা নিপুণন ক্লাসটি `Webman\Exception\ExceptionHandlerInterface` ইন্টারফেস প্রয়োজন করে।
```php
interface ExceptionHandlerInterface
{
    /**
     * লগ করা
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * রিন্ডার রিটার্ন
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```


## রিন্ডার রিসপন্স
অসুবিধা হ্যান্ডলিং ক্লাসের `render` মেথডটি রিসপন্স রেন্ডার করার জন্য।

যদি `config/app.php` ফাইলে `debug` মানটি `true` হয় (পরিচ্ছন্নে সাধারণে বলুন `app.debug=true`), তাহলে বিস্তারিত অসুবিধা তথ্য ফিরিয়ে দেওয়া হবে, অন্যথায় সংক্ষেপণীয় অসুবিধা তথ্য ফিরিয়ে দেওয়া হবে।

যদি অনুরোধ JSON রিটার্ন করার প্রত্যাশা করে, তবে অসুবিধা তথ্যটি json ফর্ম্যাটে ফিরিয়ে দেওয়া হবে, যেমন
```json
{
    "code": "500",
    "msg": "অসুবিধা তথ্য"
}
```
যদি `app.debug=true`, তবে json ডেটা তে অতিরিক্তভাবে `trace` ক্ষেত্র থাকবে যা বিস্তারিত কল স্ট্যাক ফিরিয়ে দেওয়া হবে।

আপনি আপনার নিজের অসুবিধা হ্যান্ডলিং ক্লাস লিখতে পারেন যাতে ডিফল্ট অসুবিধা নিপুণন লজিক পরিবর্তন করতে পারেন।

# ব্যবসায়িক অসুবিধা বিজনেস ইক্সপেপশন
কখনও কখনও আমরা কোনও মনোব্রান্তিকর ফাংশনের মধ্যে অনুরোধ বন্ধ করে এবং ক্লায়েন্টের জন্য একটি ত্রুটি বার্তা ফিরিয়ে দিতে চাইতে পারি, এই সময়ে এটা প্রাপ্ত করার জন্য `BusinessException` ফেলা যাবে।
উদাহরণ:
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
            throw new BusinessException('প্যারামিটার ভুল', 3000);
        }
    }
}
```

উপরের উদাহরণে এমন একটি JSON উপর পাবেন
```json
{"code": 3000, "msg": "প্যারামিটার ভুল"}
```

> **দ্রষ্টব্য**: 
> ব্যবসায়িক অসুবিধা BusinessException ট্রাই ক্যাচ করার প্রয়োজন নেই, ফ্রেমওয়ার্ক স্বয়ংক্রিয়ভাবে ক্যাচ করবে এবং রিকোয়েস্ট টাইপ অনুযায়ী উপযোগী আউটপুট ফিরিয়ে দেবে।

## কাস্টম ব্যবসায়িক অসুবিধা

উপরের রিটার্ন আপনার প্রয়োজনের অনুরূপ না হলে, যেমন চাইতে পারেন `msg` নিয়ে পরিবর্তন করতে, তাহলে নিজের কাস্টম `MyBusinessException` তৈরি করতে পারেন।

`app/exception/MyBusinessException.php` তৈরি করুন নিচের মত
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
        // যদি json রিকোয়েস্ট পেয়ে থাকে, তাহলে json ডেটা ফিরিয়ে দিতে হবে
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // জেসন না থাকলে একটি পৃষ্ঠা রিটার্ন করতে হবে
        return new Response(200, [], $this->getMessage());
    }
}
```

এই মাধ্যমে যখন ব্যবসায়িকভাবে কল করতে হবে
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('প্যারামিটার ভুল', 3000);
```
json রিকোয়েস্ট এর ক্ষেত্রে, একটি প্রায় নিম্নলিখিত json রিটার্ন পাবেন
```json
{"code": 3000, "message": "প্যারামিটার ভুল"}
```

> **পরামর্শ**: 
> ব্যবসায
