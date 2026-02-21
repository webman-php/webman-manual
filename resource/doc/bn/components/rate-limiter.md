# রেট সীমাবদ্ধতা

webman রেট সীমাবদ্ধতা, অ্যানোটেশন ভিত্তিক সীমাবদ্ধতা সমর্থন করে।
apcu, redis এবং memory ড্রাইভার সমর্থন করে।

## সোর্স কোড

https://github.com/webman-php/limiter

## ইনস্টলেশন

```
composer require webman/limiter
```

## ব্যবহার

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\annotation\Limit;

class UserController
{

    #[Limit(limit: 10)]
    public function index(): string
    {
        // ডিফল্ট IP ভিত্তিক সীমাবদ্ধতা, ডিফল্ট সময় উইন্ডো ১ সেকেন্ড
        return 'প্রতি IP প্রতি সেকেন্ডে সর্বোচ্চ ১০টি অনুরোধ';
    }

    #[Limit(limit: 100, ttl: 60, key: Limit::UID)]
    public function search(): string
    {
        // key: Limit::UID, ব্যবহারকারী ID অনুযায়ী সীমাবদ্ধতা, session('user.id') খালি নয় প্রয়োজন
        return 'প্রতি ব্যবহারকারী প্রতি ৬০ সেকেন্ডে সর্বোচ্চ ১০০টি অনুসন্ধান';
    }

    #[Limit(limit: 1, ttl: 60, key: Limit::SID, message: 'প্রতি ব্যক্তি প্রতি মিনিটে শুধুমাত্র ১টি ইমেইল')]
    public function sendMail(): string
    {
        // key: Limit::SID, session_id অনুযায়ী সীমাবদ্ধতা
        return 'ইমেইল সফলভাবে পাঠানো হয়েছে';
    }

    #[Limit(limit: 100, ttl: 24*60*60, key: 'coupon', message: 'আজকের কুপন শেষ, আগামীকাল আবার চেষ্টা করুন')]
    #[Limit(limit: 1, ttl: 24*60*60, key: Limit::UID, message: 'প্রতি ব্যবহারকারী প্রতিদিন শুধুমাত্র একটি কুপন নিতে পারবে')]
    public function coupon(): string
    {
        // key: 'coupon', গ্লোবাল সীমাবদ্ধতার জন্য কাস্টম কী, প্রতিদিন সর্বোচ্চ ১০০টি কুপন
        // ব্যবহারকারী ID অনুযায়ী সীমাবদ্ধতা, প্রতি ব্যবহারকারী প্রতিদিন একটি কুপন
        return 'কুপন সফলভাবে পাঠানো হয়েছে';
    }

    #[Limit(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: 'প্রতি নম্বর প্রতিদিন সর্বোচ্চ ৫টি এসএমএস')]
    public function sendSms2(): string
    {
        // key পরিবর্তনশীল হলে: [ক্লাস, স্ট্যাটিক_মেথড], যেমন [UserController::class, 'getMobile'] UserController::getMobile() রিটার্ন মান কী হিসেবে ব্যবহার করে
        return 'এসএমএস সফলভাবে পাঠানো হয়েছে';
    }

    /**
     * কাস্টম কী, মোবাইল নম্বর পান, স্ট্যাটিক মেথড হতে হবে
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[Limit(limit: 1, ttl: 10, key: Limit::IP, message: 'রেট সীমাবদ্ধ', exception: RuntimeException::class)]
    public function testException(): string
    {
        // সীমা অতিক্রম করলে ডিফল্ট এক্সেপশন: support\limiter\RateLimitException, exception প্যারামিটার দিয়ে পরিবর্তনযোগ্য
        return 'ok';
    }

}
```

**নোট**

* ফিক্সড উইন্ডো অ্যালগরিদম ব্যবহার করে
* ডিফল্ট ttl সময় উইন্ডো: ১ সেকেন্ড
* ttl দিয়ে উইন্ডো সেট করুন, যেমন `ttl:60` ৬০ সেকেন্ডের জন্য
* ডিফল্ট সীমাবদ্ধতা মাত্রা: IP (ডিফল্ট `127.0.0.1` সীমাবদ্ধ নয়, নিচে কনফিগারেশন দেখুন)
* অন্তর্নির্মিত: IP, UID (`session('user.id')` খালি নয় প্রয়োজন), SID (`session_id` অনুযায়ী) সীমাবদ্ধতা
* nginx প্রক্সি ব্যবহার করলে IP সীমাবদ্ধতার জন্য `X-Forwarded-For` হেডার পাস করুন, দেখুন [nginx proxy](../others/nginx-proxy.md)
* সীমা অতিক্রম করলে `support\limiter\RateLimitException` ট্রিগার করে, `exception:xx` দিয়ে কাস্টম এক্সেপশন ক্লাস
* সীমা অতিক্রম করলে ডিফল্ট এরর মেসেজ: `Too Many Requests`, `message:xx` দিয়ে কাস্টম মেসেজ
* ডিফল্ট এরর মেসেজ [অনুবাদ](translation.md) দিয়েও পরিবর্তনযোগ্য, Linux রেফারেন্স:

```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Too Many Requests' => '请求频率受限'
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

## API

কখনও কখনও ডেভেলপাররা কোডে সরাসরি সীমাবদ্ধতা কল করতে চায়, নিচের উদাহরণ দেখুন:

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // এখানে mobile কী হিসেবে ব্যবহার করা হয়
        Limiter::check($mobile, 5, 24*60*60, 'প্রতি নম্বর প্রতিদিন সর্বোচ্চ ৫টি এসএমএস');
        return 'এসএমএস সফলভাবে পাঠানো হয়েছে';
    }
}
```

## কনফিগারেশন

**config/plugin/webman/limiter/app.php**

```
<?php

use support\limiter\RateLimitException;

return [
    'enable' => true,
    'driver' => 'auto', // auto, apcu, memory, redis
    'stores' => [
        'redis' => [
            'connection' => 'default',
        ]
    ],
    // এই IP গুলো সীমাবদ্ধ নয় (শুধুমাত্র key Limit::IP হলে কার্যকর)
    'ip_whitelist' => [
        '127.0.0.1',
    ],
    'exception' => RateLimitException::class
];
```

* **enable**: রেট সীমাবদ্ধতা সক্রিয় করুন
* **driver**: `auto`, `apcu`, `memory`, `redis` এর একটি; `auto` স্বয়ংক্রিয়ভাবে `apcu` (অগ্রাধিকার) এবং `memory` এর মধ্যে বেছে নেয়
* **stores**: Redis কনফিগারেশন, `connection` `config/redis.php` এর কী এর সাথে মিলে যায়
* **ip_whitelist**: Whitelist এর IP গুলো সীমাবদ্ধ নয় (শুধুমাত্র key `Limit::IP` হলে কার্যকর)

## ড্রাইভার নির্বাচন

**memory**

* পরিচিতি
  কোনো এক্সটেনশন প্রয়োজন নেই, সেরা পারফরম্যান্স।

* সীমাবদ্ধতা
  সীমাবদ্ধতা শুধুমাত্র বর্তমান প্রসেসের জন্য কার্যকর, প্রসেসের মধ্যে ডেটা শেয়ার নেই, ক্লাস্টার সীমাবদ্ধতা সমর্থন করে না।

* ব্যবহারের ক্ষেত্র
  Windows ডেভেলপমেন্ট এনভায়রনমেন্ট; কঠোর সীমাবদ্ধতা প্রয়োজন নেই এমন ব্যবসা; CC অ্যাটাক প্রতিরক্ষা।

**apcu**

* এক্সটেনশন ইনস্টলেশন
  apcu এক্সটেনশন প্রয়োজন, php.ini সেটিংস:

```
apc.enabled=1
apc.enable_cli=1
```

php.ini অবস্থান `php --ini` দিয়ে খুঁজুন

* পরিচিতি
  খুব ভাল পারফরম্যান্স, মাল্টি-প্রসেস শেয়ার সমর্থন করে।

* সীমাবদ্ধতা
  ক্লাস্টার সমর্থন করে না

* ব্যবহারের ক্ষেত্র
  যেকোনো ডেভেলপমেন্ট এনভায়রনমেন্ট; প্রোডাকশন সিঙ্গেল সার্ভার সীমাবদ্ধতা; কঠোর সীমাবদ্ধতা প্রয়োজন নেই এমন ক্লাস্টার; CC অ্যাটাক প্রতিরক্ষা।

**redis**

* নির্ভরতা
  redis এক্সটেনশন এবং Redis কম্পোনেন্ট প্রয়োজন, ইনস্টলেশন:

```
composer require -W webman/redis illuminate/events
```

* পরিচিতি
  apcu থেকে কম পারফরম্যান্স, সিঙ্গেল সার্ভার এবং ক্লাস্টার সঠিক সীমাবদ্ধতা সমর্থন করে

* ব্যবহারের ক্ষেত্র
  ডেভেলপমেন্ট এনভায়রনমেন্ট; প্রোডাকশন সিঙ্গেল সার্ভার; ক্লাস্টার এনভায়রনমেন্ট
