# বহুভাষা

বহুভাষা ব্যবহার করে [সিংফোনি/অনুবাদ](https://github.com/symfony/translation) কাজ করে।

## ইনস্টলেশন
```
composer require symfony/translation
```

## ভাষার প্যাক তৈরি
webman ডিফল্টভাবে ভাষার প্যাকটি `resource/translations` ফোল্ডারে রাখে (যদি না থাকে তাহলে নিজে তৈরি করুন)। আপনি যদি ফোল্ডারটি পরিবর্তন করতে চান তাহলে `config/translation.php` ফাইলে সেট করুন।
প্রতি ভাষার জন্য একটি সাব-ফোল্ডার আছে এবং ভাষার নির্দিষ্টকরণ মূলত `messages.php` ফাইলে থাকে। উদাহরণ:
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

সমস্ত ভাষা ফাইলটি একটি অ্যারে রিটার্ন করে, উদাহরণ হিসেবে:
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## কনফিগারেশন

`config/translation.php`

```php
return [
    // ডিফল্ট ভাষা
    'locale' => 'zh_CN',
    // ফলব্যাক ভাষা, যখন বর্তমান ভাষাতে অনুবাদ খোঁজা যায় না তখন পরীক্ষা করার জন্য ফলব্যাক ভাষার অনুবাদ
    'fallback_locale' => ['zh_CN', 'en'],
    // ভাষা ফাইলগুলির ফোল্ডার
    'path' => base_path() . '/resource/translations',
];
```

## অনুবাদ

অনুবাদ করা `trans()` মেথড ব্যবহার করে।

ভাষা ফাইল তৈরি করুন `resource/translations/zh_CN/messages.php` উদাহরণ হিসেবে:
```php
return [
    'hello' => 'হ্যালো ওয়েবম্যান!',
];
```

ফাইল তৈরি করুন `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // হ্যালো ওয়েবম্যান!
        return response($hello);
    }
}
```

`http://127.0.0.1:8787/user/get` পরিদর্শন করার জন্য "হ্যালো ওয়েবম্যান!" ফাঁকে পাবেন।

## ডিফল্ট ভাষা পরিবর্তন করুন

ভাষা পরিবর্তন করতে `locale()` মেথড ব্যবহার করুন।

ভাষা ফাইল তৈরি করুন `resource/translations/en/messages.php` উদাহরণ হিসেবে:
```php
return [
    'hello' => 'hello world!',
];
```

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // ভাষা পরিবর্তন
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
`http://127.0.0.1:8787/user/get` পরিদর্শন করার জন্য "hello world!" ফাঁকে পাবেন।

আপনি এছাড়া `trans()` ফাংশনের চতুর্থ প্যারামিটার দিয়ে সাময়িক ভাষা পরিবর্তন করতে পারেন, উপরের উদাহরণ এবং নিচের উদাহরণটি একই।
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // চতুর্থ প্যারামিটার দিয়ে ভাষা পরিবর্তন
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## প্রতিটি অনুরোধের ভাষা নির্দিষ্ট করা
অনুবাদ একটি সিঙ্গেলটন, এর মানে এটি সমস্ত অনুরোধ এই ইনস্ট্যান্সটি শেয়ার করে, যদি কোনও অনুরোধ `locale()` ব্যবহার করে ডিফল্ট ভাষা সেট করে, তবে এটি প্রক্রিয়ার পরবর্তী অনুরোধগুলির উদ্বোধনে প্রভাব ফেলবে। তাই আমাদের প্রতিটি অনুরোধে ভাষা নির্দিষ্ট করতে হবে। এর উদাহরণ এখানকার মিডলওয়্যারটি ব্যবহার করা।

ফাইল তৈরি করুন `app/middleware/Lang.php` (এমন ফোল্ডার না থাকলে নিজে তৈরি করুন) উদাহরণ হিসেবে:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        locale(session('lang', 'zh_CN'));
        return $handler($request);
    }
}
```

`config/middleware.php` ফাইলে গ্লোবাল মিডলওয়্যার যোগ করুন যেমন:
```php
return [
    // গ্লোবাল মিডলওয়্যার
    '' => [
        // ... অন্যান্য মিডলওয়্যার অংশটি জানান এখানে
        app\middleware\Lang::class,
    ]
];
```

## প্লেসহোল্ডার ব্যবহার করা
কখন কখন একটি মেসেজে অনুবাদ করা ভ্যারিয়েবল ধারণ করে, উদাহরণস্বরূপ
```php
trans('hello ' . $name);
```
এই ধরণের সমস্যার সম্মুখীনে আমরা প্লেসহোল্ডার ব্যবহার করে প্রক্রিয়া করি।

`resource/translations/zh_CN/messages.php` পরিবর্তন করুন উদাহরণ হিসেবে:
```php
return [
    'hello' => 'হ্যালো %name%!',
];
```
অনুবাদ সময়কালে প্লেসহোল্ডারের মাধ্যমে মানগুলি অনুরোধক দিয়ে মূলসূচী সরবরাহ করুন
```php
trans('hello', ['%name%' => 'webman']); // হ্যালো webman!
```

## বহুপরিবর্তন নির্দেশকে সামলান
কিছু ভাষায় প্রাণী সংখ্যার দৃষ্টিভঙ্গিতে বিভিন্ন বাক্যরচনা দেয়, উদাহরণস্বরূপ`There is %count% apple` যখন `%count%` এক হলে বাক্যরচনা সঠিক আবার একটি অধিক হলে ভুল।

এই ধরণের সমস্যায় আমরা একাধিক পরিবর্তন নির্দেশক ব্যবহার করি `|`।

ভাষা ফাইল `resource/translations/en/messages.php` নতুন`apple_count` যোগ করুন উদাহরণ হিসেবে:
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

আমরা আরও সংখ্যা আয়তন নির্দিষ্ট করতে পারি, আরও প্রস্তুত বহুপরিবর্তন নির্দেশক তৈরি করতে:
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## পূর্বনির্ধারিত ভাষাফাইল নির্দিষ্ট করা
ভাষাফাইলেर ডিফল্ট নাম 'messages.php', তবে আপনি আবারও সর্বনিম্নভাবে তৈরি করতে পারেন।

ভাষা ফাইল তৈরি করুন `resource/translations/zh_CN/admin.php` উদাহরণ হিসেবে:
```php
return [
    'hello_admin' => 'হ্যালো ম্যানেজার!',
];
```

`trans()`-র তৃতীয় প্যারামিটার ব্যবহার করে ভাষা ফাইলের নাম নির্দিষ্ট করুন (`.php` এর পূর্বের অংশটি জাঁকাতে)।
```php
trans('hello', [], 'admin', 'zh_CN'); // হ্যালো ম্যানেজার!
```

## আরও তথ্য
[সিংফোনি/অনুবাদ ম্যানুয়াল](https://symfony.com/doc/current/translation.html) দেখুন

