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

`$request->session();` দ্বারা `Workerman\Protocols\Http\Session` ইনস্ট্যান্স পেতে এবং ইনস্ট্যান্সের মেথড ব্যবহার করে সেশন ডেটা যোগ করা, পরিবর্তন করা, বা মুছে ফেলা যায়।

> লক্ষ্য করুন: সেশন অবজেক্ট পরিনাম অন্তর্ভুক্ত ডেটা স্বতঃমুক্ত হওয়ার সময় সেশন ডেটা স্বয়ংক্রিয়ভাবে সংরক্ষণ করা হবে, তাই সেশন অবজেক্টকে পুরোপুরি গ্লোবাল অ্যারে বা ক্লাস মেম্বারে সংরক্ষণ করা না হলে সেশন সাংভাবিকভাবে সংরক্ষিত না হওয়ার জন্য না ভুলে যাবেন।

## সমস্ত সেশন ডেটা পেতে
```php
$session = $request->session();
$all = $session->all();
```
এটি একটি অ্যারে প্রদান করে। যদি কোনো সেশন ডেটা না থাকে তবে এটি খালি অ্যারে রিটার্ন করে।

## সেশনে কোনো মান পেতে
```php
$session = $request->session();
$name = $session->get('name');
```
যদি ডেটা অস্তিত্ব না থাকে তবে এটি null রিটার্ন করে।

আপনি যদি ডিফল্ট ভ্যালু পাঠান তাহলে গেট মেথডে দ্বিতীয় আর্গুমেন্ট হিসেবে পাঠান। যদি সেশন অ্যারেতে পাওয়া না যায় তবে ডিফল্ট ভ্যালুটি রিটার্ন করে। যেমন:
```php
$session = $request->session();
$name = $session->get('name', 'টম');
```

## সেশন সংরক্ষণ
ডাটা সংরক্ষণ করার সময় set মেথড ব্যবহার করুন।
```php
$session = $request->session();
$session->set('name', 'টম');
```
set এর কোনো রিটার্ন মূল্য নেই, সেশন অবজেক্ট পরিনাম অতএব সেশন স্বতঃমুক্তভাবে সংরক্ষিত হবে।

একাধিক মান সংরক্ষণ করার সময় পুট মেথড ব্যবহার করুন।
```php
$session = $request->session();
$session->put(['name' => 'টম', 'বয়স' => 12]);
```
এই মধ্যেও পুটের কোনো রিটার্ন মূল্য নেই।

## সেশন ডেটা মুছে ফেলা
যখন কোনো স্পেশাল কীটি ডেটা মুছে ফেলতে হবে তখন ফরগেট মেথডটি ব্যবহার করুন।
```php
$session = $request->session();
// একটি আইটেম মুছে ফেলা
$session->forget('name');
// একাধিক মুছে ফেলা
$session->forget(['name', 'বয়স']);
```

আরও অন্যান্য মেথড উপলব্ধ, ফরগেট মেথডের সাথে তুলনা করা হলে, মুছে ফেলা মেথড একটি আইটেম মুছে ফেলতে পারে।
```php
$session = $request->session();
// delete মেথড আমদানি
$session->delete('name');
// এটি একইভাবে $session->forget('name'); এর সাথে সমান
```

## সেশনের মান পেতে এবং মুছে ফেলতে
```php
$session = $request->session();
$name = $session->pull('name');
```
এটির ফলে নিম্নলিখিত কোডের সাথে একই ফলাফল পেতে পারে
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
যদি মূল্য অনুপস্থিত হয়, তাহলে null রিটার্ন করে।

## সমস্ত সেশন ডেটা মুছা
```php
$request->session()->flush();
```
কোনো রিটার্ন মূল্য নেই, সেশন অবজেক্ট পরিনাম অতএব সেশন স্বতঃমুক্তভাবে সংরক্ষণ থেকে মুছে ফেলবে।

## বিশেষ কীটি ডেটা অস্তিত্ব পরীক্ষা করা
```php
$session = $request->session();
$has = $session->has('name');
```
উপরোক্ত কোডাটির মাধ্যমে যদি বিশেষ কীটি ডেটা অস্তিত্ব না থাকে বা কোনো বিশেষ কীটির মান null মান হয় তাহলে ফলাফল হবে false, অন্যথায় হ্যাঁ ফলাফল দেয়া হবে।
```php
$session = $request->session();
$has = $session->exists('name');
```
উপরোক্ত কোডটিও বিশেষ কীটি ডেটা অস্তিত্ব পরীক্ষা করার জন্য ব্যবহৃত হয়, তবে, যখন বিশেষ কীটির মান null হয় তখনই এটি সত্য মান প্রদান করে।

## সাহায্যকারী হেল্পার ফাংশন session()
> 2020-12-09 যোগ করা হয়েছে

ওয়েবম্যান সমতুল্য করার জন্য হেল্পার ফাংশন `session()` প্রদান করে।
```php
// সেশন ইনস্ট্যান্স পেতে
$session = session();
// সমান
$session = $request->session();

// কোনো মান পেতে
$value = session('key', 'ডিফল্ট');
// সমান
$value = session()->get('key', 'ডিফল্ট');
// সমান
$value = $request->session()->get('key', 'ডিফল্ট');

// সেশন প্রদান করা
session(['key1'=>'value1', 'key2' => 'value2']);
// সমান
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// সমান
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
```
# কনফিগ ফাইল
সেশন কনফিগারেশন ফাইলটি `config/session.php` তে থাকে, যা নিম্নলিখিত অনুরূপ তথ্য ধারণ করে:
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class অথবা RedisSessionHandler::class অথবা RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // handler যখন FileSessionHandler::class হলে type এর মান হবে file,
    // handler যখন RedisSessionHandler::class হলে type এর মান হবে redis
    // handler যখন RedisClusterSessionHandler::class হলে type এর মান হবে redis_cluster, অর্থাৎ redis ক্লাস্টার
    'type'    => 'file',

    // পূর্ণ হ্যান্ডলারগুলির জন্য বিভিন্ন সেটিং
    'config' => [
        // type যখন file হলে কনফিগারেশন
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // type যখন redis হলে কনফিগারেশন
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

    'session_name' => 'PHPSID', // সেশন আইডির কুকির নাম
    
    // === webman-framework>=1.3.14 এবং workerman>=4.0.37 এর জন্য নিম্নলিখিত সেটিং ===
    'auto_update_timestamp' => false,  // সেশন নিজভুক্তি পূনঃচলিত সাথে নিজভোক্তি করবে কিনা, ডিফল্ট অফ
    'lifetime' => 7*24*60*60,          // সেশন মেয়াদ উত্তীর্ণ হইবার পর সময়সীমা
    'cookie_lifetime' => 365*24*60*60, // সেশন আইডির কুকি মেয়াদ উত্তীর্ণ হইবার পর সময়সীমা
    'cookie_path' => '/',              // সেশন আইডির কুকির পথ
    'domain' => '',                    // সেশন আইডির কুকির ডোমেইন
    'http_only' => true,               // httpOnly চালু করা হবে কিনা, ডিফল্ট চালু
    'secure' => false,                 // শুধুমাত্র https এ সেশন চালু করা হবে কিনা, ডিফল্ট বন্ধ
    'same_site' => '',                 // CSRF হামলা এবং ব্যবহারকারীর ট্র্যাকিং প্রতিরোধে ব্যবহৃত হয়, বিকল্প মান strict/lax/none
    'gc_probability' => [1, 1000],     // সেশন পুনরায় উদ্ধারের সম্ভাবনা
];
```

> **লক্ষ্য করুন** 
> webman 1.4.0 থেকে SessionHandler এর নেমস্পেসের পরিবর্তন করা হয়েছে, অবশেষে পূর্বের মত
> `use Webman\FileSessionHandler;`  
> `use Webman\RedisSessionHandler;`  
> `use Webman\RedisClusterSessionHandler;`  
> এখন  
> `use Webman\Session\FileSessionHandler;`  
> `use Webman\Session\RedisSessionHandler;`  
> `use Webman\Session\RedisClusterSessionHandler;`  
> হয়েছে।  


## মেয়াদ সেটিং
webman-framework < 1.3.14 এর জন্য, সেশন মেয়াদ সেটিংগুলি `php.ini` কনফিগারেশনে আছে।

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

মেয়াদ সেট করার জন্য, 1440 সেকেন্ডের জন্য কনফিগার অথবা যদি আপনি ইংরেজিতে মেয়াদ বিরাম দেন তবে আপনি নিম্নলিখিত চেক করুন
```php
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **নোট**  
> আপনি যদি `php.ini` ফাইলটি খুঁজে পান না তাহলে আপনি `php --ini` কমান্ড ব্যবহার করতে পারেন।
