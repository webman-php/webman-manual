# করো

> **কর্মী প্রয়োজন**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> ওয়েবম্যান আপগ্রেড কমান্ড `composer require workerman/webman-framework ^1.5.0`
> workerman আপগ্রেড কমান্ড `composer require workerman/workerman ^5.0.0`
> ফাইবার করো ইনস্টল করতে কমান্ড `composer require revolt/event-loop ^1.0.0`

# উদাহরণ
### বিলম্বিত প্রতিক্রিয়া

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // 1.5 সেকেন্ড শয়ন
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` একটি PHPনীতির `sleep()` ফাংশনের মতো, এর ব্যাপারে `Timer::sleep()` প্রক্রিয়াকে বন্ধ করে না


### এইচটিটিপি অনুরোধ প্রেরণ

> **সতর্ক থাকুন**
> ইন্সটল এর জন্য প্রার্থিত composer require workerman/http-client ^2.0.0

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // অসমস্যিত পদক্ষেপ শুরু করে
        return $response->getBody()->getContents();
    }
}
```
একইভাবে `$client->get('http://example.com')` অনুরোধটি অবন্ধ হয় নাই, এটি ওয়েবম্যানে জন্য অবন্ধ স্থিতিতে এইচটিটিপি অনুরোধ প্রেরণ করার জন্য ব্যবহার করা যেতে পারে অ্যাপ্লিকেশন সম্পর্কে উন্নতি করে।

বিস্তারিত দেখুন [ওয়ার্কাম্যান/এইচটিটিপি ক্লায়েন্ট](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### সাপোর্ট/কনটেক্স্ট ক্লাস যোগ করুন

`sপোর্ট/কনটেক্স্ট` ক্লাসটি অনুরোধের সংদর্ভ ডেটা সংরক্ষণ করার জন্য ব্যবহৃত হয়, অনুরোধ সম্পূর্ণ হলে, সম্পর্কিত কনটেক্স্ট ডেটা স্বয়ংক্রিয়ভাবে মুছে যায়। অর্থাৎ কনটেক্স্ট ডেটা জীবনকাল অনুরোধের জীবনকালের জন্য। `সাপোর্ট/কনটেক্স্ট` ফাইবার, স্বোল, স্বো করোম এনভায়ারন্মেন্ট সমর্থন করে।


### স্বোল করোম

স্বোল এক্সটেনশন ইনস্টল করুন (প্রয়োজন স্বোল >=5.0), পরিকল্পনা config/server.php কনফিগার করার মাধ্যমে স্বোল করোম চালু করুন
```php
'event_loop' => \Workerman\Events\Swoole::class,
```
সবার জন্য বিস্তারিত দেখুন [ওয়ার্কাম্যান ইভেন্টস্ ড্রাইভেন](https://www.workerman.net/doc/workerman/appendices/event.html)

### গ্লোবাল ভেরিয়েবল কন্টামিনেশন

করোম পরিবেশে অসুবিধা করে অনুরোধ সম্পর্কিত অবস্থানের তথ্য গ্লোবাল ভেরিয়েবল বা স্ট্যাটিক ভেরিয়েবলে সংরক্ষণ করা নিষিদ্ধ, কারণ এটা গ্লোবাল ভেরিয়েবল কন্টামিনেশনের কারণ হতে পারে, উদাহরণরূপ,

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```
প্রক্রিয়ার সংখ্যা সেট করুন 1, আমরা দুটি অনুরোধ প্রেরণ করলে যখন
http://127.0.0.1:8787/test?নাম=lilei
http://127.0.0.1:8787/test?নাম=hanmeimei
আমরা প্রত্যেকটি অনুরোধের জন্য আমরা প্রত্যেকটি ফলাফলকে দেখতে পারেন `lilei` এবং `hanmeimei`, তবে আসলে `hanmeimei` এর প্রতিক্রিয়া দেয়।
এটা প্রথম অনুরোধের স্থির ভেরিয়েবল `নাম` অধিআকার করেছে, প্রথম অনুরোধের নিদিষ্ট শয়নকাল শেষ হওয়ার সময়টি শেষে`নাম`  টা হয়ে যায়।

**সঠিক প্রক্রিয়া হলো অনুরোধের অবস্থান উপাত্ত সংরক্ষণ করতে ব্যবহার করা**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('নাম', $request->get('নাম'));
        Timer::sleep(5);
        return Context::get('নাম');
    }
}
```

**লোকাল ভেরিয়েবলের ব্যবহার কারণে ডেটা কন্টামিনেশন না হওয়ার জন্য**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('নাম');
        Timer::sleep(5);
        return $name;
    }
}
```
কারন `নাম` একটি লোকাল ভেরিয়েবল, অতএব অর্থোড় প্রক্রিয়া সুরক্ষিত করা যায় কর্ণক্ষম।

# করো সম্পর্কে
করো কোনো যুদ্ধান্ত নয়, কারণ করো এনেছে গ্লোবাল বৈরি/স্ট্যাটিকযাদি বৈরি কন্টামিনেশন প্রতিসনির্ভেষ করতে হবে এবং কনটেক্স্ট সেট করতে হবে। আরও ভালো তো করোম পরিবেশে বাগ ডিবাগ করার জন্য উপায় একটুটা অধিক জটিল।

ওয়েবম্যান অবরোধি প্রাথমিকভাবে পর্যাপ্ত দ্রুততায়, [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) এর সাথে তুলনা করে, আতিথেয় তিন বছরের ক্ষেত্রে তিন্টি ক্ষেত্রেবিন্যাসের তথ্য দেখা যায়, ওয়েবম্যান অবরোধি ডাটাবেস বিজনেসে প্রাদেশিক সম্রাজ্য ওয়েব এপি gin, echo এবং প্রথাগত ফ্রেমওয়ার্ক লারাভেল এর পরিসংখ্যান দেখে তথ্যো প্রদান করেছে।
![](../../assets/img/benchemarks-go-sw.png?)


ডাটাবেস, রিডিস ইত্যাদি সবকিছু অভ্যন্তরীণ নেটওয়ার্কে থাকলে, বহু প্রক্রিয়া করো সম্ভাব্যতা অনুরূপ ভাবে তাদের পারফর্মেন্স উত্থান অধিক ফর্যাদ করে, কারণ কর্মী তৈরি, সংচালন এবং প্রত্যাহারণ খরচের মাত্রা প্রক্রিয়া পরিবর্তনের খরচ এর চেয়ে  বেশি হতে পারে, তাদের বরাত থাকলে আমি এই ধরণের সময় প্রদর্শন দেয়া হয়।

# কখন করো ব্যবহার করবেন
ব্যবসা অবস্থায় বিলম্ব অ্যাকসেস থাকলে, যেমন ব্যবসা পর্যালোচনা তৃতীয় পক্ষ এ্যাপিতে যাওয়ার সময়, আপনি পারফরমেন্স ক্ষমতা বাড়ানোর জন্য [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) ব্যবহার করতে পারেন এবং অভিব্যক্তির স্বাধীনভাবে সমান্বিতকরণ করেন।
