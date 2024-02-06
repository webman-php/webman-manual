## স্ট্যাটিক ফাইল প্রসেসিং
ওয়েবম্যান স্ট্যাটিক ফাইল অ্যাক্সেস সাপোর্ট করে, স্ট্যাটিক ফাইলগুলি সমস্তটি `public` ডিরেক্টরির মধ্যে থাকে, উদাহরণ হিসেবে `http://127.0.0.8787/upload/avatar.png` অ্যাক্সেস করার মানে হচ্ছে `{মেইন প্রজেক্ট ডিরেক্টরি}/public/upload/avatar.png`।

> **লক্ষ্য করুন**
> webman 1.4 থেকে অ্যাপ্লিকেশন প্লাগইনস সাপোর্ট করে, `/app/xx/ফাইলের_নাম` এর মধ্যে স্ট্যাটিক ফাইলগুলি অ্যাক্সেস সাপোর্ট অবস্থান হচ্ছে অ্যাপ্লিকেশন প্লাগইনের `public` ডিরেক্টরিতে, অর্থাৎ webman >=1.4.0 এর সময় `{মূল প্রজেক্ট ডিরেক্টরি}/public/app/` এর ডিরেক্টরি অ্যাক্সেস সাপোর্ট করেন না।
> অধিক জানতে [অ্যাপ্লিকেশন প্লাগইন](./plugin/app.md) দেখুন

### স্ট্যাটিক ফাইল সাপোর্ট বন্ধ করুন
স্ট্যাটিক ফাইলের সাপোর্ট প্রয়োজন না থাকলে, `config/static.php` ফাইলটি খোলে `enable` অপশনটি বদলে দিন। বন্ধ করার পরে সমস্ত স্ট্যাটিক ফাইল অ্যাক্সেস করলে 404 রিটার্ন হবে।

### স্ট্যাটিক ফাইল ডিরেক্টরি পরিবর্তন
webman ডিফল্টভাবে স্ট্যাটিক ফাইল ডিরেক্টরি হিসেবে public ডিরেক্টরিটি ব্যবহার করে। যদি পরিবর্তন করতে চান, তবে `support/helpers.php` ফাইলে থাকা `public_path()` সাহায্যকারী ফাংশনটি পরিবর্তন করুন।

### স্ট্যাটিক ফাইল মিডলওয়ার
webman নিজস্ব একটি স্ট্যাটিক ফাইল মিডলওয়ার ব্যবহার করে, অবস্থান `app/middleware/StaticFile.php`।
সময় এর মধ্যে, আমরা কিছু স্ট্যাটিক ফাইলের প্রসেসিং করার দরকার পরে, যেমন স্ট্যাটিক ফাইলগুলির জন্য ক্রস অরিজিন এইচটিটি হেডার যোগ করা, বা ডট (`.`) দিয়ে শুরু করে ফাইল অ্যাক্সেস নিষিদ্ধ করা, এই মিডলওয়ারটি ব্যবহার করা যেতে পারে।

`app/middleware/StaticFile.php` এর বিষয়বস্তু নিম্নলিখিতভাবে হতে পারে:
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // . দিয়ে শুরু হওয়া লুকায়েক ফাইলগুলি অ্যাক্সেস নিষিদ্ধ
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // ক্রস অরিজিন এইচটিটি হেডার যোগ করুন
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
যদি এই মিডলওয়ার প্রয়োজন হয়, তবে `config/static.php` ফাইলে মিডলওয়ার অপশন চালু করা প্রয়োজন।
