# ডিপেন্ডেন্স অটো ইনজেকশন
webman-এ ডিপেন্ডেন্স অটো ইনজেকশন একটি ঐচ্ছিক ফিচার, এই ফিচারটি ডিফল্টভাবে বন্ধ থাকে। আপনি যদি ডিপেন্ডেন্স অটো ইনজেকশন ব্যবহার করতে চান, তবে [php-di](https://php-di.org/doc/getting-started.html) ব্যবহার করা সুপরিক্ষিত। নিম্নলিখিত হল কিভাবে `php-di` এর সাথে webman এসোসিয়েশন করতে হবে।

## ইনস্টলেশন
```sh
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```
`config/container.php` কনফিগারেশন ফাইল সম্পাদনা করুন, এবার তার মাঝখানের বিষয়গুলি হবে:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```
> `config/container.php` ফাইলটি শেষ পর্যন্ত `PSR-11` স্পেসিফিকেশন অনুসারে পারদর্শী ইল্লিট রিটার্ন করবে। আপনি যদি `php-di` ব্যবহার না করতে চান, তবে এখানে অন্য যে কোন পারদর্শী `PSR-11` স্পেসিফিকেশন অনুসারে কনটেনের ইন্সট্যান্স রিটার্ন করতে পারেন।

## কনস্ট্রাক্টর ইনজেকশন
নতুন একটি `app/service/Mailer.php` ফাইল তৈরি করুন (যদি ফোল্ডার না থাকে তাহলে নিজে তৈরি করুন) এবং তার কন্টেন্ট দেখুন:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // ইমেইল পাঠানোর কোড অনুমোদিত নয়
    }
}
```
`app/controller/UserController.php` ফাইলে নিম্নলিখিত কনটেন্ট দেখুন:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```
সাধারণভাবে, `app\controller\UserController` ইন্সট্যান্স করার জন্য নিম্নলিখিত কোডগুলি ব্যবহার করা প্রয়োজন:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
তবে `php-di` ব্যবহার করার পরে, ডেভেলপারের কোনো ম্যানুয়াল ইনস্ট্যান্স তৈরি করার প্রয়োজন নেই, ওয়েবম্যান স্বয়ংক্রিয়ভাবে সাহায্য করবে। ইনস্ট্যান্স মানও আছে যদি ইনস্ট্যান্সে অন্যান্য ডিপেন্ডেন্স থাকে, তাহলে ওয়েবম্যান স্বয়ংক্রিয়ভাবে ইনস্ট্যান্স তৈরি করে আপনাকে বিস্তারিত হাতোয়ারী করবে। ডেভেলপারের কোন প্রাথমিক প্রস্তুতি কাজ করা প্রয়োজন নেই।

> **দৃষ্টিভঙ্গি**
> ডিপেন্ডেন্স অটো ইনজেকশন সম্পন্ন করার জন্য কেবল ফ্রেমওয়ার্ক অথবা `php-di` তৈরি ইনস্ট্যান্স দরকার, ম্যানুয়াল `new` স্টেটমেন্ট দ্বারা তৈরি করা ইনস্ট্যান্স ডিপেন্ডেন্স অটো ইনজেকশন সম্পন্ন করতে পারবে না, যদি ইনজেকশন করতে চান তাহলে `support/Container` ইন্টারফেস ব্যবহার করে `new` স্টেটমেন্ট প্রতিস্থাপন করতে হবে, উদাহরণস্বরূপ:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// নিউ কীওয়ার্ড ব্যবহার করে তৈরি ইনস্ট্যান্স ডিপেন্ডেন্স পাঠানো যাবে না
$user_service = new UserService;
// নিউ কীওয়ার্ড ব্যবহার করে তৈরি ইন্স্ট্যান্স ডিপেন্ডেন্স পাঠানো যাবে না
$log_service = new LogService($path, $name);

// কনটেনের ইন্স্ট্যান্স ব্যবহার করে ইন্জেক্শন করা যাবে
$user_service = Container::get(UserService::class);
// কনটেনের ইন্স্ট্যান্স ব্যবহার করে ইন্জেক্শন করা যাবে
$log_service = Container::make(LogService::class, [$path, $name]);
```

## অ্যানোটেশন ইনজেকশন
কনস্ট্রাক্টর ডিপেন্ডেন্স অটো ইনজেকশনের পাশাপাশি, আমরা এনোটেশন ইনজেকশন ব্যবহার করতে পারি। উপরের উদাহরণে, `app\controller\UserController` -কে নিম্নলিখিত মতে পরিবর্তন করুন:
```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;
use DI\Annotation\Inject;

class UserController
{
    /**
     * @Inject
     * @var Mailer
     */
    private $mailer;

    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```
এই উদাহরণটি `@Inject` এনোটেশন ইনজেকশনের মাধ্যমে ইনজেকশন করে এবং `@var` এনোটেশন দিয়ে অবজেক্টের ধরন ঘোষণা করে। এই উদাহরণ কনস্ট্রাক্টর ইনজেকশনের ফলাফল যথার্থ তবে কোডটি আরও সংক্ষিপ্ত।

> **দৃষ্টিভঙ্গি**
> webman-এ 1.4.6 ভার্শনের আগে কন্ট্রোলার প্যারামিটার ইনজেকশন সমর্থন করে না, উদাহরণস্বরূপ নিম্নলিখিত কোডটি 1.4.6 <= webman এ সমর্থিত নয়:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // 1.4.6 ভার্শনের পূর্বে কন্ট্রোলার প্যারামিটার ইনজেকশন সমর্থন করে না
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```
## কাস্টম কন্সট্রাক্টর ইনজেকশন

কখনও কখনও কনস্ট্রাকটরে প্যারামিটার ক্লাসের ইনস্ট্যান্স নয়, পিএইইচপি-ডিআই চেয়ে প্রয়োজনীয় ডাটা যোগাযোগ সার্ভার আইপি এবং পোর্টের মতো। উদাহরণস্বরূপ, মেইলার কন্সট্রাক্টরে এসএমটিপি সার্ভার আইপি এবং পোর্ট প্যারামিটার গ্রহণ করতে হবে -
```php
<?php
namespace app\service;

class Mailer
{
    private $smtpHost;

    private $smtpPort;

    public function __construct($smtp_host, $smtp_port)
    {
        $this->smtpHost = $smtp_host;
        $this->smtpPort = $smtp_port;
    }

    public function mail($email, $content)
    {
        // ইমেইল পাঠানোর কোড অনুপ্তম করা হলেও
    }
}
``` 

এই ধরনের সমস্যার জন্য আমরা পিইএইইচ-ডিআই দ্বারা প্রস্তাবিত কনস্ট্রাক্টরের আইডেন্টিটি নিশ্চিত না হওয়ার কারণে পূর্ব পর্বে আলোচনা করা তুলনা করার কারণে এই ধরনের কোড কে স্বয়ংক্রিয়ভাবে আপনার ইচ্ছিত ফলাফল দেয়। 

### কাস্টম কন্সট্রাক্টর ইনজেকশন
`config/dependence.php` এ নিম্নলিখিত কোডটি যোগ করুন -
```php
return [
    // ... অন্যান্য কনফিগারেশন এর অনস্কিপেড থাকলেও 
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
এইভাবে যেখানে ডিপেন্ডেন্সি ইনজেকশনের প্রয়োজন হয়, সেই সময় ডিপেন্ডেন্সি ইনজেকশনের জন্য এই `app\service\Mailer` ইনস্ট্যান্স ব্যবহৃত হবে। 

আমরা `config/dependence.php` তে দেখতে পাচ্ছি, `new` ব্যবহার করে `Mailer` ক্লাস ইনস্ট্যান্স বানানো আছে, ক্ষমতা রেখেছে এই উদাহরণে কিন্তু `Mailer` ক্লাস অন্য কোনও প্যাকেজের নির্ভরণা থাকতে পারে, অথবা `Mailer` ক্লাস আইচ্ছার মাঝে অ্যানোটেশন ইনজেকশন ব্যবহার করা হত পারে। সমস্যা সমাধান হল - `Container::get(ক্লাস নাম)` অথবা `Container::make(ক্লাস নাম, [কনস্ট্রাক্টর প্যারামিটার])` ফাংশনের মাধ্যমে ক্লাস ইনিশিয়ালাইজ করা।

### কাস্টম ইন্টারফেস ইনজেকশন
রিয়েল প্রজেক্টে, আমরা ইন্টারফেস এর দিকে ভাবতে চা খ, অবশ্যই ক্লাস এর সাথে। যেমনঃ `app\controller\UserController` এ `app\service\MailerInterface` এর সাথে যুক্ত হওয়া উচিত, `app\service\Mailer` না। 

`MailerInterface` ইন্টারফেস সংজ্ঞা -
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

`MailerInterface` ইন্টারফেসের বাস্তবায়ন -
```php
<?php
namespace app\service;

class Mailer implements MailerInterface
{
    private $smtpHost;

    private $smtpPort;

    public function __construct($smtp_host, $smtp_port)
    {
        $this->smtpHost = $smtp_host;
        $this->smtpPort = $smtp_port;
    }

    public function mail($email, $content)
    {
        // ইমেইল পাঠানোর কোড অনুপ্তম করা হলেও
    }
}
```

`MailerInterface` ইন্টারফেস সেরা, না বাস্তব পরিবর্তন নিতে, কেবল মাত্র, `config/dependence.php` এর সাহায্যে, এই ইন্টারফেস ব্যবহার করা যাবে। ইউজ করা হতে পারে যেকোনো ব্যবস্থা পাইলে পূর্বলিখিত ইন্টারফেস প্রয়োজন হয়, `MailerInterface` ইন্টারফেস প্রথমে ইমপ্লিমেন্ট করা যাবে।

> ইন্টারফেস ব্যবহারের সুবিধা হল, যখন আমরা কোনও পৃথক কোড প্রয়োজন হয় যেমন, কোনও যন্ত্রাংশ পরিবর্তন করুন, তখন ব্যবহারকারীগণ পরিবর্তনিযোগ্য নয়, হলেও `config/dependence.php` এ কোন নির্দিষ্ট ব্যবহার পরিবর্তন করা যাবে। এটা একটি ইউনিট-টেস্টিং করার জন্য বেশ ব্যবহারী হলো।

### অন্যান্য কাস্টম ইনজেকশন
`config/dependence.php` এ ক্লাস এর ডিপেন্ডেন্সি, বা পূর্ণাঙ্গ স্ট্রিং, নম্বর, অ্যারে ইত্যাদি মূল্য পরিবর্তনের সাথে যোগ করা যা যায়। 

যেমন, `config/dependence.php` এ নিম্নলিখিত সম্পর্কে সংজ্ঞা করা যাবে -
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

এই সময়ে, আমরা `@Inject` ব্যবহার থেকে `smtp_host` `smtp_port` অনুপ্তরে সাধারণ মানে আবিশ্কার করব -
```php
<?php
namespace app\service;

use DI\Annotation\Inject;

class Mailer
{
    /**
     * @Inject("smtp_host")
     */
    private $smtpHost;

    /**
     * @Inject("smtp_port")
     */
    private $smtpPort;

    public function mail($email, $content)
    {
        // ইমেইল পাঠানোর কোড অনুপ্তম করা হলেও
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; //  আউটপুট 192.168.1.11:25
    }
}
```

> লক্ষ্য করুন: `@Inject("key")` এখানে ডাবল কোটেশন টি আছে।

### অধিক পড়ুন
[পিএইইচ-ডিআই ম্যানুয়ালে](https://php-di.org/doc/getting-started.html) এ বেশি জানুন।
