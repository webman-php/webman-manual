# ব্যাপ্তিতা হ্যান্ডলিং

## কনফিগারেশন
`config/exception.php`
```php
return [
    // এখানে অদ্ভুত হ্যান্ডেলিং ক্লাস কনফিগার করুন
    '' => support\exception\Handler::class,
];
```
বহু অ্যাপ্লিকেশন মোডেলে এপ্লিকেশনের প্রতিটি জন্য ব্যাপ্তি হ্যান্ডলিং ক্লাস কনফিগার করতে পারেন, [মাল্টি-অ্যাপ](multiapp.md) দেখুন।


## ডিফল্ট ব্যাপ্তি হ্যান্ডলিং ক্লাস
webman এর ব্যতিত শীঘ্রই `support\exception\Handler` ক্লাস দ্বারা ব্যবস্থা করা হয়। ডিফল্ট ব্যাপ্তি প্রসেসিং ক্লাস পরিবর্তন করতে কনফিগারেশন ফাইল `config/exception.php` পরিবর্তন করা যেতে পারে। ব্যাপ্তি প্রসেসিং ক্লাসটি `Webman\Exception\ExceptionHandlerInterface` ইন্টারফেস ইমপ্লিমেন্ট করতে হবে।
```php
interface ExceptionHandlerInterface
{
    /**
     * লগ অটো
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * রেন্ডার রিটার্ন
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```



## রেন্ডার রিসপন্স
ব্যাপ্তি প্রসেসিং ক্লাসের `render` মেথড রেন্ডার রিসপন্স এর জন্য ব্যবহৃত।

`config/app.php` ফাইলে `debug` মানটি যদি `true` হয় (তাহলে `app.debug=true` ছোট হবে)। তবে তথ্যবহুল ব্যাপ্তি মেরের ক্ষেত্রে বিস্তারিত জনক ব্যাপ্তি বিস্তারিত তথ্য ঢুকাবে, অন্যথায় সংক্ষেপে ব্যাপ্তি মেরার ক্ষেত্রে। অনুরোধিত ভাবে রিটার্ন হবে যদি JSON রিটার্ অপারেশন হয়, সারণি

```json
{
    "code": "500",
    "msg": "ব্যাপ্তি তথ্য"
}
```

যদি `app.debug=true` হয়, তাহলে জনক ডাটা অতিরিক্তভাবে `trace` ফিল্ড আসবে ব্যাপ্তি নেটওয়ার্কটা অতিরিক্তভাবে।

আপনি নিজের ব্যাপ্তি প্রসেসিং ক্লাস লিখে আপনার ডিফল্ট ব্যাপ্তি প্রসেসিং লজিক পরিবর্তন করতে পারেন।

# ব্যবসা ব্যাপ্তি প্রসেসিং ক্লাস BusinessException
কখনও কখনও আমরা কোনও নেস্টেড ফাংশনে রিকুয়েস্ট বিরতি করতে এবং ক্লায়েন্ট দিকে একটি ত্রুটি তথ্য ফিরিয়ে দেতে চাই। এই সময়ে আমরা এটা অতিক্রম করার জন্য `BusinessException` নিয়ে ত্রুটি ফেলে। উদাহরণঃ

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

উপরের উদাহরণ একটি মতিতে ফিরে দেবে
```json
{"code": 3000, "msg": "প্যারামিটার ভুল"}
```

> **দ্য নোট**
> ব্যাপ্তি প্রসেসিং BusinessException এর প্রয়োজন নেই, চেষ্টা করার মাধ্যমে ক্যাচ রাখা হবে না, ফ্রেমওয়ার্কটি নিজেই ক্যাচ করবে এবং অনুসারে প্রস্তুত রিটার্ন করবে।

## স্বনিয়ত ব্যবসা ব্যাপ্তি

যদি উপরোক্ত অব্যবসায়িক উত্তর আপনার চাহিদায় সাঙ্গের না হয়, উদাহরণঃ আপনি `msg` হ্রাষ্টভাবে পরিবর্তন করতে চান, তাহলে আপনি নিজের `MyBusinessException` একটি নিজের উদাহরণ লিখতে পারেন

`app/exception/MyBusinessException.php` নতুন তৈরি করুন নয়।
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
        // যদি JSON অনুরোধ দেওয়া হয় তাহলে JSON ডাটা রিটার্ন করুন
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // JSON অনুরোধের উল্লেখ না থাকলে একটি পৃষ্ঠা ফেরত দিন
        return new Response(200, [], $this->getMessage());
    }
}
```

এই হিসাবে জনক ব্যাপ্তি কে অনুরোধিত ভাবে ষষ্টি হবে
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('প্যারামিটার ভুল', 3000);
```
যদি JSON অনুরোধ দেওয়া হয়, তাহলে টাইপ তৈরি করুন যেমন নিম্নের মত একটি জন্য জনক রিটার্ন করুন
```json
{"code": 3000, "message": "প্যারামিটার ভুল"}
```

> **পরামর্শ**
> কারণ ব্যবসায়িক ব্যাপ্তি BusinessException এটা সাম্প্রদায়িক ভুল (উদাহরণঃ ব্যবহারকারীর ইনপুট প্যারামিটার ভুল) তাই ফ্রেমওয়ার্কগুলি এটা কিছুদিন কোড মেমোরিতে রাখবে বা লগ করবে না।

## সংক্ষেপন
যে কোনও সময় ব্যাপ্তি প্রসেসিং ব্যবহার করা যেতে পারে যেখানে বর্তমান রিকুয়েস্ট ভিত্তিক তথ্য দিয়ে ক্লায়েন্টকে তথ্য ফিরিয়ে দিতে চাই। `BusinessException` ব্যাপ্তি ব্যবহার করা বিবেচনা তোর।
