# বহুভাষা

বহুভাষা ব্যবহার করা হয় [symfony/translation](https://github.com/symfony/translation) মডিউল।

## ইনস্টলেশন
```composer require symfony/translation```

## ভাষা প্যাক তৈরি
webman মূলত `resource/translations` ফোল্ডারে (না থাকলে স্বয়ংক্রিয়ভাবে তৈরি করুন) ভাষা প্যাক রাখে, যদি পূর্বনির্ধারিত ফোল্ডার পরিবর্তন করতে চান তাহলে `config/translation.php` ফাইলে সেট করুন।
প্রতিটি ভাষার জন্য একটি সাব ডিরেক্টরি আছে, ভাষা ডিফাইন পূর্বনির্ধারিতভাবে `messages.php` এ থাকে। উদাহরণস্বরূপ:
```php
resource/
└── translations
    ├── en
    │ └── messages.php
    └── zh_CN
        └── messages.php
```

সমস্ত ভাষা ফাইল একটি অ্যারে রিটার্ন করে, উদাহরণস্বরূপ:
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
    // পূর্বনির্ধারিত ভাষা
    'locale' => 'zh_CN',
    // ফলব্যবস্থা ভাষা, সে সময় কোনও অনুবাদ খুঁজে পাওয়া যায় না সে সময় ফলব্যবস্থা ভাষায় অনুবাদ চেষ্টা করার চেষ্টা করা হয়,
    'fallback_locale' => ['zh_CN', 'en'],
    // ভাষা ফাইলটি ফোল্ডার
    'path' => base_path() . '/resource/translations',
];
```

## অনুবাদ

অনুবাদে `trans()` মেথড ব্যবহার করা হয়।

ভাষা ফাইল তৈরি `resource/translations/zh_CN/messages.php` এ নিম্নলিখিত:
```php
return [
    'hello' => 'নমস্কার  বিশ্ব!',
];
```

ফাইল তৈরি `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // নমস্কার  বিশ্ব!
        return response($hello);
    }
}
```

`http://127.0.0.1:8787/user/get` এ যান "নমস্কার  বিশ্ব!" পাবেন

## পূর্বনির্ধান ভাষা পরিবর্তন

ভাষা পরিবর্তনে `locale()` মেথড ব্যবহার করা হয়।

ভাষা ফাইল তৈরি `resource/translations/en/messages.php` এ নিম্নলিখিত:
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
`http://127.0.0.1:8787/user/get` এ যান "hello world!" পাবেন

আপনি `trans()` ফাংশনের 4 শেষ প্যারামিটার ব্যবহার করে এই পদক্ষেপে সাধারণ করতে পারেন, উপরের উদাহরণের সাথে এই উদাহরণটি একই:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // 4 শেষ প্যারামিটার দিয়ে ভাষা পরিবর্তন
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## প্রতিটি অনুরোধে ভাষা নির্ধারণ করা
translation একটি সিংগিলটন, এটা একক অনুরোধ বাদে সমস্ত অনুরোধে ভাগ করে, যদি কোনও রিকুয়েস্ট `locale()` ব্যবহার করে পূর্বনির্ধারিত ভাষা সেট করে তবে এটি পরবর্তী অনুরোধগুলির জন্য প্রক্রিয়াকরণের পরিণামের ভারাদ্বারে প্রভাব ডালবে। সুতরাং আমাদের প্রতিটি অনুরোধে ভাষা নির্ধারণ করতে হবে। উদাহরণস্বরূপ এই মিডলওয়্যার ব্যবহার করে:

ফাইল তৈরি `app/middleware/Lang.php` (যদি ডিরেক্টরি অস্তিত্ব না থাকে তাহলে স্বয়ংক্রিয়ভাবে তৈরি করুন) নিম্নলিখিত হতে হবে:
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

`config/middleware.php` এ গ্লোবাল মিডলওয়্যার যোগ করুন:
```php
return [
    // গ্লোবাল মিডলওয়্যার
    '' => [
        // ... অন্যান্য মিডলওয়্যারগুলি এখানে অভিথান করা হয়নি
        app\middleware\Lang::class,
    ]
];
```

## ভিত্তিক বিভাজন
সময়, একটি তথ্য ধারণ করে অনুবাদ করা ইচ্ছুক, উদাহরণস্বরূপ
```php
trans('hello ' . $name);
```
এই ধরণের অনুকূলে আমরা ভিত্তিক বিভাজন ব্যবহার করতে পারি।

`resource/translations/zh_CN/messages.php` এ নিম্নলিখিত পরিবর্তন করুন:
```php
return [
    'hello' => 'নমস্কার %name%!',
];
```
অনুবাদের সময় ডেটাকে দ্বিতীয় প্যারামিটার ব্যবহার করে বিভাজনের সাথে বিভাজনিত ভ্যালু পাঠিয়ে দেয়া হয়
```php
trans('hello', ['%name%' => 'webman']); // নমস্কার webman!
```

## বহুবচন ব্যবস্থাপনা
কিছু ভাষা এর কারণে বস্তুর সংখ্যা পরবর্তীতে বিভিন্ন বাক্য প্রদর্শিত করে, উদাহরণস্বরূপ `There is %count% apple`, যখন `%count%` একটি বা ১ হয় তখন বাক্যটি ঠিক, যখন এর চেয়ে অধিক হয় তখন ভুল।

এই ধরণের সৃষ্টি করার জন্য আমরা বস্তুবিশেষ ভাবে **পাইপ** (`|`) ব্যবহার করি।

ভাষা ফাইল `resource/translations/en/messages.php` এ `apple_count` যোগ করুন:
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

আমরা আগামীকাল তো সুগবিশেষ রাখ সৃষ্টি করেও পর্যবেক্ষণ করতে পারি:
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## নির্দিষ্ট ভাষা ফাইল ব্যবহার

ভাষা ফাইলের পূর্বনির্ধারিত নাম `messages.php` হয়, তবে আপনি বাকি নামের ভাষা ফাইল তৈরি করতে পারেন।

ভাষা ফাইল তৈরি করুন `resource/translations/zh_CN/admin.php` এ নিম্নলিখিত হয়:
```php
return [
    'hello_admin' => 'নমস্কার প্রশাসক!',
];
```

`trans()` দ্বিতীয় প্যারামিটার ব্যবহার করে ভাষা ফাইল পরিচিত করুন (`.php` অপশনাল)।
```php
trans('hello', [], 'admin', 'zh_CN'); // নমস্কার প্রশাসক!
```

## অধিক তথ্য
[সাইমফোনি / অনুবাদ গাইড](https://symfony.com/doc/current/translation.html) দেখুন
