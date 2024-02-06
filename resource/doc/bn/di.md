# ডিপেন্ডেন্স অটো ইনজেকশন
ওয়েবম্যানে ডিপেন্ডেন্স অটো ইনজেকশন একটি ঐচ্ছিক ফিচার, যা ডিফল্টভাবে বন্ধ থাকে। যদি আপনি ডিপেন্ডেন্স অটো ইনজেকশন ব্যবহার করতে চাইলে, [php-di](https://php-di.org/doc/getting-started.html) ব্যবহারের জন্য সুন্দর আছে। নিচে ওয়েবম্যান এর `php-di` সঙ্গে ব্যবহার সম্পর্কিত বিবরণ দেয়া হল -

## ইনস্টলেশন
```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

`config/container.php` কনফিগারেশন ফাইল পরিবর্তন করুন, এর সর্বশেষ ধারণা হচ্ছে:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php` ফাইলটি `PSR-11` প্রয়োজনীয় অবজেক্ট শৈলির অনুসারে একটি অবজেক্ট ফাংশন রিটার্ন করে। আপনি `php-di` ব্যবহার করা না চাইলে, আপনি এখানে অন্যত্র `PSR-11` অবজেক্ট ইনিশিয়িয়েট ব্যবহার করতে পারেন।

## কনস্ট্রাক্টর ইনজেকশন
আপনি নতুন `app/service/Mailer.php`(যদি ডিরিক্টরি অস্তিত্ব না থাকে, তাহলে নিজে তৈরি করুন) হুবহু নিম্নলিখিত সামগ্রী দিয়ে তৈরি করুন:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // মেইল পাঠানোর কোড অপসারিত 
    }
}
```

`app/controller/UserController.php` এর কোড:
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
সাধারণভাবে, `app\controller\UserController` এর অবজেক্ট ইনিশিয়িয়েট করতে নিচের ধরনের কোড উপযুক্ত:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
আপনি `php-di` ব্যবহার করে থাকলে, অবজেক্ট ইনিশিয়েট স্বয়ংক্রিয়ভাবে করতে হয় না, ওয়েবম্যান আপনার জন্য স্বয়ংক্রিয়ভাবে সে কাজ সম্পাদন করবে। যদি মেইলার ব্যাক্তিগত ক্লাস এর সাথে কোনও অন্য ক্লাস এর ডিপেন্ডেন্স থাকে, তাহলে ওয়েবম্যান স্বয়ংক্রিয়ভাবে সে কাজ সম্পাদন করবে। ডেভেলপারের কাউন্টার কোনো প্রকারেই কোনো একটি শুরু কর্মসূচি প্রয়োজন করি৷

