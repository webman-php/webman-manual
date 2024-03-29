# জীবনচক্র

## প্রসেস জীবনচক্র
- প্রতিটি প্রসেসের দীর্ঘস্থায়ী জীবনচক্র থাকে
- প্রতিটি প্রসেস স্বাধীনভাবে চলছে এবং অপরতদল নয়
- প্রতিটি প্রসেস এর জীবনচক্রের অংশে একাধিক অনুরোধ প্রসেস করা যেতে পারে
- প্রসেসটি `স্টপ`, `রিলোড`, `রিস্টার্ট` কমান্ড পেলে প্রসেসটি বন্ধ করবে এবং বর্তমান জীবনচক্র শেষ হবে।

> **সাজাতি**
> প্রতিটি প্রসেস স্বতন্ত্র এবং অপরতদল, এটা মানে যে প্রতিটি প্রসেস তার নিজের এসেট, পরিবর্তমান এবং ক্লাসের ইনস্ট্যান্স সহ নিয়েশন করে, প্রতিটি প্রসেস একটি নিজস্ব ডেটাবেস সংযুক্ত রাখে, কিছু সিঙ্গেলটন প্রতিটি প্রসেস আরম্ভিকরণ করে, তবে এর মাধ্যমে প্রসেস আমানত হয়।

## অনুরোধ জীবনচক্র
- প্রতিটি অনুরোধ `$request` অবজেক্ট উৎপন্ন করে
- অনুরোধ প্রক্রিয়া শেষে `$request` অবজেক্ট পুনর্বিন্যাস হবে

## নিয়ামক জীবনচক্র
- প্রতিটি নিয়ামক প্রতিটি প্রসেস একবার শুধুমাত্র ইনস্ট্যান্স করা হবে, এবং একাধিক প্রসেসে ইনস্ট্যান্স করা হবেনা (নিয়ামক পুনর্ব্যবহার বন্ধ করা ছাড়া, [নিয়ামক জীবনচক্র](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F) দেখুন)
- নিয়ামক ইনস্ট্যান্স বর্তমান প্রসেসের মধ্যে একাধিক অনুরোধ ভাগ করতে পারে (নিয়ামক পুনর্ব্যবহার বন্ধ করা ছাড়া)
- নিয়ামক জীবনচক্রটি প্রসেস থেকে পরিস্রব পেলে শেষ হবে (নিয়ামক পুনর্ব্যবহার বন্ধ করা ছাড়া)

## মূল্যের জীবনচক্রের সাথে সম্পর্কিত
webman এ php -র উপর ভিত্তি করে বিকাশ করে তাই এইটা পুরোপুরি php এর মূল্য মুক্তির মেকানিজম অনুসরণ করে। ব্যবসা লজিকে আমরা যেসব টিম্পোরারি মূল্য বহন করি (যেমন নিচের উদাহরণ মত $foo বা index পদক্ষেপে সীমাহীন বেড়ে), তা স্বয়ংক্রিয়ভাবে মুক্তি পেয়ে, হাতে নিবেনা প্রয়োজন নেই। অর্থাৎ webman ডেভেলপমেন্ট এবং পারম্পরিক ফ্রেমওর্ক ডেভেলপমেন্টের অভিজ্ঞতা প্রায় একই। যদিও যদি আপনি চান যেখানে কোন একটি ক্লাস ইনস্ট্যান্স পুনর্ব্যবহার করা হবে, তাহলে আপনি ক্লাসটি ক্লাসের স্ট্যাটিক প্রোপার্টিতে বা দীর্ঘ একই মূল্য অব্জেক্টে এ রাখতে পারেন, Container কন্টেনারের get পদক্ষেপ ব্যবহার করতে পারেন, যেমনঃ

```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Container;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = Container::get(Foo::class);
        return response($foo->sayHello());
    }
}
```

`Container::get()` পদক্ষেপটি ক্লাসের ইন্সট্যান্স তৈরি এবং সংরক্ষণ করার জন্য ব্যবহার করা হয়। পরবর্তীতে একই প্যারামিটারের সাথে পুনরায় কল করে তা পূর্বে তৈরি ইনস্ট্যান্স ফিরে।

> **নোট**
> `Container::get()` শুধুমাত্র কোনও নির্মাণ প্যারামিটার ছাড়া ইনস্ট্যান্স করতে পারে। `Container::make()` সাময়িক নির্মাণ প্যারামিটার সহ ইনস্ট্যান্স তৈরি করতে পারে, তবে `Container::get()` এবং `Container::make()` এর মধ্যে পার্থক্য হল যদিও তাদের সাথে একই প্যারামিটার দিয়ে `Container::make()` পরিবর্তনে ক্লাসের ইনস্ট্যান্স পুনর্ব্যবহার করা না করে সর্বদা নতুন ইনস্ট্যান্স ফিরবে।

# ক্ষেতের সম্পর্কে
অনেক ক্ষেত্রে, আমাদের ব্যবসা কোডগুলি মেমরি লিকেজে পরিণত হবেনা (ব্যবহারকারীদের অত্যন্ত পর্যালোচনা করে মেমরি লিকেজ ঘটলে, মাত্র ছোট পরিমাণ ব্যবসায়িক এক্সপ্যানশন থাকতে পারে)। আমাদের প্রধানত এমনটাও মনে রাখতে হবে যে দীর্ঘস্থায়ী অ্যারে ডেটা অসীম বৃদ্ধি না রেখে। অনুগ্রহ করে নিম্নলিখিত কোডটি দেখুন:

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    // অ্যারে অ্যাট্রিবিউট
    public $data = [];
    
    public function index(Request $request)
    {
        $this->data[] = time();
        return response('hello index');
    }

    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```
নিয়ন্ত্রক ডিফল্টভাবে দীর্ঘস্থায়ী (নিয়ন্ত্রণ পুনর্ব্যবহার বন্ধ করা ছাড়া), একই কেনসট্রোক্টরের বিভিন্ন অনুরোধ এ এবং অব্যাসিত্র ডেটা অ্যারে মেমরি লিকেজ বৃদ্ধি।

আরও বিস্তারিত তথ্যের জন্য [মেমরি লিকেজ](./memory-leak.md) দেখুন।
