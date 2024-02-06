# সেশন ব্যবস্থাপনা

## উদাহরণ
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('hello ' . $session->get('name'));
    }
}
```

`$request->session();` দ্বারা `Workerman\Protocols\Http\Session` ইন্সট্যান্স প্রাপ্ত করা হয়। ইন্সট্যান্সের মাধ্যমে সেশন ডেটা যোগ, পরিবর্তন বা মুছে ফেলতে হয়।

> লক্ষ্য করুন: সেশন অবজেক্ট ধ্বংস হওয়ার সময় সেশন ডেটা স্বয়ংক্রিয়ভাবে সংরক্ষণ করা হয়, তাই সেশন অবজেক্টটি গ্লোবাল অ্যারেতে বা ক্লাস মেম্বারে সংরক্ষণ করবেন না, যাতে সেশন সংরক্ষণ বিফল হয় না।

## সমস্ত সেশন ডেটা প্রাপ্ত করুন
```php
$session = $request->session();
$all = $session->all();
```
এটি একটি অ্যারে ফেরত দেয়। যদি কোনও সেশন ডেটা না থাকে, তবে একটি ফাঁকা অ্যারে ফেরত দেয়।

## সেশনে নির্দিষ্ট মান প্রাপ্ত করুন
```php
$session = $request->session();
$name = $session->get('name');
```
ডেটা অস্তিত্ব না থাকলে NULL ফেরত দেয়।

আপনি যদি ডিফল্ট মান প্রাপ্ত করার জন্য get মেথডে দ্বিতীয় প্যারামিটার দেওয়ার জন্য ব্যবহার করতে চান, তবে এটি প্রাপ্ত মানটি চয়নিত মান ফেরত দেয়। উদাহরণ:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## সেশন সংরক্ষণ
একটি ডেটা সংরক্ষণ করতে সেট মেথডটি ব্যবহার করুন।
```php
$session = $request->session();
$session->set('name', 'tom');
```
set কোনও মান ফেরত দেয় না, সময় অব্জেক্ট ধ্বংস হওয়ার সময় সেশন স্বয়ংক্রিয়ভাবে সংরক্ষণ করা হয়।

একাধিক মান সংরক্ষণ করতে পুট মেথডটি ব্যবহার করুন।
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
একই ভাবে, পুট কোনও মান ফেরত দেয় না।

## সেশন ডেটা মুছে ফেলুন
একটি বা একাধিক সেশন ডেটা মুছে ফেলতে 'forget' মেথডটি ব্যবহার করুন।
```php
$session = $request->session();
// একটি আইটেম মুছে ফেলুন
$session->forget('name');
// একাধিক আইটেম মুছে ফেলুন
$session->forget(['name', 'age']);
```

আরও একটি ডিলিট মেথড যুক্ত করা হয়েছে, এটা 'forget' মেথডের সাথে পার্থক্য হল, ডিলিট মেথড কেবল একটি আইটেম মুছে ফেলতে পারে।
```php
$session = $request->session();
// একটি আইটেম মুছে ফেলার জন্য।
$session->delete('name');
```

## সেশন একটি মান প্রাপ্ত এবং মুছে ফেলুন
```php
$session = $request->session();
$name = $session->pull('name');
```
উপরের কোডের পারিণাম এই কোডের সাথে সম্যাপ্ট।
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
যদি সম্পর্কিত সেশন অনুপস্থিত হয়, তবে NULL ফেরত দেয়।

## সকল সেশন ডেটা মুছে ফেলুন
```php
$request->session()->flush();
```
কোনও প্রতিরোধ নেই, সেশন অবজেক্ট ধ্বংস হওয়ার সময় সেশন স্বয়ংক্রিয়ভাবে সংরক্ষণ থেকে মুছে ফেলে।

## প্রাপ্ত করুন এবং পরীক্ষা করুন যে কোনও সেশন ডেটা অনুপস্থিত আছে কি না
```php
$session = $request->session();
$has = $session->has('name');
```
উপরোল্লেখিত কোডের মধ্যে সেশন অনুপস্থিত থাকলে বা সেশন ভেরিয়েবল নাল হলে ফলাফল মিথ্যা দেখাবে, অন্যথায় সত্য দেখাবে।

```
$session = $request->session();
$has = $session->exists('name');
```
উপরোল্লেখিত কোডটি একই উদ্দেশ্যে ব্যবহৃত হয়, কিন্তু সেশন আইটেমের মান NULL হলেও এটি সত্য হিসেবে গণ্য করা হয়।

## হেল্পার ফাংশন session()
> 2020-12-09 যুক্ত

ওয়েবম্যান একই কার্যকলাপকে সম্পাদন করার জন্য `session()` সাহায্যিক ফাংশন প্রদান করে।

```php
// সেশন ইনস্ট্যান্স পান
$session = session();
// সমান
$session = $request->session();

// কোনও মান পান
$value = session('key', 'default');
// সমান
$value = session()->get('key', 'default');
// সমান
$value = $request->session()->get('key', 'default');

// সেশনে মান যোগ করুন
session(['key1'=>'value1', 'key2' => 'value2']);
// সমান
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// সমান
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
```

## কনফিগারেশন ফাইল
সেশন কনফিগারেশন ফাইল 'config/session.php' এ রয়েছে, যেমন নিম্নলিখিত কন্টেন্ট কারণেঃ
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class বা RedisSessionHandler::class অথবা RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // হ্যান্ডলার 'FileSessionHandler::class' থাকলে মান 'file',
    // হ্যান্ডলার 'RedisSessionHandler::class' থাকলে মান 'redis'
    // হ্যান্ডলার 'RedisClusterSessionHandler::class' থাকলে মান 'redis_cluster' অর্থাৎ রেডিস ক্লাস্টার
    'type'    => 'file',

    // বিভিন্ন হ্যান্ডলারের জন্য বিভিন্ন কনফিগারেশন ব্যবহার করুন
    'config' => [
        // type হল 'file' এর জন্য কনফিগারেশন
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // type হল 'redis' এর জন্য কনফিগারেশন
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
        
    ],

    'session_name' => 'PHPSID', // সেশন আইডি সিডি সংরক্ষণ করার জন্য কুকির নাম
    
    // === নতুন করে ১.৩.১৪ এর উপরে webman-তে workerman-ইয়াবার লাইফটাইম=== //
    'auto_update_timestamp' => false,  // কি সেশন স্বয়ংক্রিয়ভাবে পুনরায় লেনদেন করবে, ডিফল্ট অফ
    'lifetime' => 7*24*60*60,          // সেশনের মেয়াদ শেষ হওয়ার সময়
    'cookie_lifetime' => 365*24*60*60, // কুকির এইডিল্যাইফে সেশন আইডি স্টোর করার সময়
    'cookie_path' => '/',              // কুকির পথ
    'domain' => '',                    // কুকির ডোমেইন
    'http_only' => true,               // কি httpOnly চালু, ডিফল্ট চালু
    'secure' => false,                 // সেশন শুধুমাত্র https সংযোগে চালু হবে, ডিফল্ট বন্ধ
    'same_site' => '',                 // এনকাইনা CSRF হামলা এবং ব্যবহারকারীর ট্র্যাকিংের মধ্যে দুর্দান্ত করা জন্য, উপলভ্য মান
