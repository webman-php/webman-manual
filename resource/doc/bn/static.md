## স্থির ফাইল প্রসেসিং
webman স্ট্যাটিক ফাইল এক্সেস সাপোর্ট করে, স্ট্যাটিক ফাইলগুলি সাধারণভাবে `public` ডিরেক্টরির মধ্যে থাকে, উদাহরণস্বরূপ এক্সেস করা হয় `http://127.0.0.8787/upload/avatar.png` এ বাস্তবে `{মূল প্রজেক্টের ডিরেক্টরি}/public/upload/avatar.png`।

> **লক্ষ্য করুন**
> webman 1.4 থেকে অ্যাপ্লিকেশন প্লাগইন সাপোর্ট করতে লাগল, `/app/xx/ফাইল নাম` শুরু করে স্ট্যাটিক ফাইল এক্সেস বাস্তবে অ্যাপ্লিকেশন প্লাগইনের `public` ডিরেক্টরিতে অ্যাক্সেস করে, অর্থাৎ webman >=1.4.0 এর সময় `{মূল প্রজেক্টের ডিরেক্টরি}/public/app/` এর ডিরেক্টরি এক্সেস না করুন।
> অধিক জানতে দেখুন [অ্যাপ্লিকেশন প্লাগইন](./plugin/app.md)

### স্ট্যাটিক ফাইল সাপোর্ট বন্ধ
যদি স্ট্যাটিক ফাইল সাপোর্ট প্রয়োজন না হয়, `config/static.php` ফাইলটি খোলে `enable` অপশন পরিবর্তন করুন না। বন্ধ করার পরে সমস্ত স্ট্যাটিক ফাইল অ্যাক্সেস পাবে 404 রিটার্ন করবে।

### স্ট্যাটিক ফাইল ডিরেক্টরি পরিবর্তন করুন
webman ডিফল্টভাবে স্ট্যাটিক ফাইলগুলির জন্য public ডিরেক্টরি ব্যবহার করে। যদি পরিবর্তন করতে চান, তবে `support/helpers.php` ফাইলের `public_path()` সহায়ক ফাংশন পরিবর্তন করুন।

### স্ট্যাটিক ফাইল মিডলওয়্যার
webman স্বয়ংক্রিয়ভাবে একটি স্ট্যাটিক ফাইল মিডলওয়্যার সহেজ করে, `app / middleware / StaticFile.php`।
কখনও কখনও আমরা স্ট্যাটিক ফাইলগুলির জন্য কিছু প্রসেসিং করতে চাই, যেমন স্ট্যাটিক ফাইলে ক্রস সাইট এইচটিটি শিরোনাম যুক্ত করা, ডট (.) দিয়ে শুরু করে ফাইলে অ্যাক্সেস নিষিদ্ধ করা মিডলওয়্যারটি ব্যবহার করতে পারেন।

`app / middleware / StaticFile.php` এর কন্টেন্ট এমন হতে পারে:

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
        // বন্ধ করা যাবে না . শুরু হওয়া লুকানো ফাইলগুলি
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // ক্রস সাইট এইচটিটি শিরোনাম যুক্ত করুন
        /*$response->withHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]); */
        return $response;
    }
}
```
এই মিডলওয়্যার প্রয়োজন হলে, আপনাকে `config/static.php` ফাইলের `middleware` অপশন চালু করতে হবে।
