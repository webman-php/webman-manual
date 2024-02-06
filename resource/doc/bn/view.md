## ভিউ
webman ডিফল্টভাবে টেমপ্লেট হিসেবে পিএইচপির মূল সিনট্যাকটি ব্যবহার করে এবং 'opcache' চালু করার পর সে সর্বোত্তম কার্যকর হয়। পিএইচপির মূল টেমপ্লেটের পাশাপাশি, webman এ [Twig](https://twig.symfony.com/doc/3.x/) , [Blade](https://learnku.com/docs/laravel/8.x/blade/9377), [think-template](https://www.kancloud.cn/manual/think-template/content) টেমপ্লেট ইঞ্জিন প্রদান করে।

## opcache চালু করা
ভিউ ব্যবহার করার সময়, পিএইচপি.ইনি-তে `opcache.enable` এবং `opcache.enable_cli` দুটি অপশন চালু করা প্রয়োজন, যাতে টেমপ্লেট ইঞ্জিনটি সর্বোত্তম কার্যকর হতে পারে।

## Twig ইনস্টল করুন
1. composer ইনস্টল

   `composer require twig/twig`

2. কনফিগারেশন পরিবর্তন করুন `config/view.php` এভাবে
   ```php
   <?php
   use support\view\Twig;
   
   return [
       'handler' => Twig::class
   ];
   ```
   > **দ্রষ্টব্য:**
   > অন্যান্য কনফিগারেশন বিকল্পগুলি অপশনস্ এভাবে প্রবেশ করায়, যেমন  

   ```php
   return [
       'handler' => Twig::class,
       'options' => [
           'debug' => false,
           'charset' => 'utf-8'
       ]
   ];
   ```

## Blade ইনস্টল করুন
1. composer ইনস্টল

   `composer require psr/container ^1.1.1 webman/blade`

2. কনফিগারেশন পরিবর্তন করুন `config/view.php` এভাবে
   ```php
   <?php
   use support\view\Blade;

   return [
       'handler' => Blade::class
   ];
   ```

## think-template ইনস্টল করুন
1. composer ইনস্টল

   `composer require topthink/think-template`

2. কনফিগারেশন পরিবর্তন করুন `config/view.php` এভাবে
   ```php
   <?php
   use support\view\ThinkPHP;

   return [
       'handler' => ThinkPHP::class,
   ];
   ```
   > **দ্রষ্টব্য:**
   > অন্যান্য কনফিগারেশন বিকল্পগুলি `options` দ্বারা প্রবেশ করায়, উদাহরণস্বরূপ

   ```php
   return [
       'handler' => ThinkPHP::class,
       'options' => [
           'view_suffix' => 'html',
           'tpl_begin' => '{',
           'tpl_end' => '}'
       ]
   ];
   ```

## মূল PHP টেমপ্লেট ইঞ্জিনের উদাহরণ
নিম্নলিখিতকে যেমনঃ `app/controller/UserController.php` ফাইল তৈরি করুন

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

নিম্নলিখিতটির মধ্যে নতুন ফাইল `app/view/user/hello.html` তৈরি করুন

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

##  Twig টেমপ্লেট ইঞ্জিনের উদাহরণ
`config/view.php` কনফিগারেশন পরিবর্তন করুন এভাবে

   ```php
   <?php
   use support\view\Twig;
   
   return [
       'handler' => Twig::class
   ];
   ```

   `app/controller/UserController.php` এভাবে
   ```php
   <?php
   namespace app\controller;
   
   use support\Request;
   
   class UserController
   {
       public function hello(Request $request)
       {
           return view('user/hello', ['name' => 'webman']);
       }
   }
   ```

   `app/view/user/hello.html` ফাইল এভাবে
   ```html
   <!doctype html>
   <html>
   <head>
       <meta charset="utf-8">
       <title>webman</title>
   </head>
   <body>
   hello {{name}}
   </body>
   </html>
   ```

   [Twig এর অধিক ডকুমেন্ট](https://twig.symfony.com/doc/3.x/) দেখুন।

## Blade টেমপ্লেট ইঞ্জিনের উদাহরণ
`config/view.php` কনফিগারেশন পরিবর্তন করুন এভাবে
   ```php
   <?php
   use support\view\Blade;
   
   return [
       'handler' => Blade::class
   ];
   ```

   `app/controller/UserController.php` এভাবে
   ```php
   <?php
   namespace app\controller;
   
   use support\Request;
   
   class UserController
   {
       public function hello(Request $request)
       {
           return view('user/hello', ['name' => 'webman']);
       }
   }
   ```

   `app/view/user/hello.blade.php` ফাইল এভাবে
   > লক্ষ্যঃ blade টেমপ্লেটের পরের সাফকে `blade.php`

   ```html
   <!doctype html>
   <html>
   <head>
       <meta charset="utf-8">
       <title>webman</title>
   </head>
   <body>
   hello {{$name}}
   </body>
   </html>
   ```

   [Blade এর অধিক ডকুমেন্ট](https://learnku.com/docs/laravel/8.x/blade/9377) দেখুন।

## ThinkPHP টেমপ্লেট ইঞ্জিনের উদাহরণ
`config/view.php` কনফিগারেশন পরিবর্তন করুন এভাবে
   ```php
   <?php
   use support\view\ThinkPHP;
   
   return [
       'handler' => ThinkPHP::class
   ];
   ```

   `app/controller/UserController.php` এভাবে
   ```php
   <?php
   namespace app\controller;
   
   use support\Request;
   
   class UserController
   {
       public function hello(Request $request)
       {
           return view('user/hello', ['name' => 'webman']);
       }
   }
   ```

   `app/view/user/hello.html` ফাইল এভাবে
   ```html
   <!doctype html>
   <html>
   <head>
       <meta charset="utf-8">
       <title>webman</title>
   </head>
   <body>
   hello {$name}
   </body>
   </html>
   ```

   [think-template এর অধিক ডকুমেন্ট](https://www.kancloud.cn/manual/think-template/content) দেখুন।

## টেমপ্লেট মান প্রদান
টেমপ্লেটে মান প্রদান করার জন্য `view(টেমপ্লেট, ভেরিয়েবল অ্যারে)` ব্যবহার করার পাশাপাশি, আমরা `View::assign()` কল করে যেকোনো অবস্থায় টেমপ্লেটে মান প্রদান করতে পারি। উদাহরণস্বরূপ:
```php
<?php
namespace app\controller;

use support\Request;
use support\View;

class UserController
{
    public function hello(Request $request)
    {
        View::assign([
            'name1' => 'value1',
            'name2'=> 'value2',
        ]);
        View::assign('name3', 'value3');
        return view('user/test', ['name' => 'webman']);
    }
}
```

`View::assign()` কিছু পরিস্থিতি অনুষ্ঠানে খুব বড়ভাবে ব্যবহার্য, উদাহরণস্বরূপ, যদি কোনও সিস্টেমে প্রতিটি পৃষ্ঠা প্রদর্শন করতে বর্তমান লগইনকৃত ব্যবহারকারীর তথ্য প্রদর্শন করা লাগে, তবে প্রতিটি পৃষ্ঠা নির্দিষ্ট তথ্য লক্ষ্য করে `view('টেমপ্লেট', ['user_info' => 'ব্যবহারকারীর তথ্য']);` করা খুব ঝামেলাপূর্ণ হতে পারে। এর সমাধান হচ্ছে মধ্যমপাতায় ব্যবহারকারী তথ্য প্রাপ্ত করা এবং `View::assign()` দ্বারা ব্যবহারকারীর তথ্যটি টেমপ্লেটে প্রদান করা।

## ভিউ ফাইলের পথ সম্পর্কে

#### কন্ট্রোলার
যখন কন্ট্রোলার `view('টেমপ্লেটনাম',[]);` কল করে, ভিউ ফাইলগুলি নিম্নলিখিত নিয়মে সনাক্ত করা হয়:

1. একমাত্র অ্যাপলিকেশন হলে, `app/view/` এখানে মূল ভিউ ফাইলগুলি প্রয়োগ করা হয়
2. [এপ্লিকেশন](multiapp.md) এবংযাদিরপরে `app/আবেদননাম/view/` এখানে মূল ভিউ ফাইলগুলি প্রয়োগ করা হয়

সারংশ হল, যদি `$request->app` ফাঁকা থাকে, তবে `app/view/` এর মধ্যের ভিউ ফাইলগুলি প্রয়োগ করা হয় এবং ভাবার ক্ষেত্রে `app/{$request->app}/view/` ফাইলের ভিউ ফাইলগুলি প্রয়োগ করা হয়।

#### বন্ধ করা ফাংশন
বন্ধ করা ফাংশনে `request->app` ফাঁকা থাকে ইন্ডিকেট করে, বন্ধ ফাংশন `app/view/` এই ভিউ ফাইলগুলি প্রয়োগ করে, যেমন `config/route.php` এ রাউট সংজ্ঞায়ন করা যায়
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
এটি `app/view/user.html` ফাইল যন্ত্রবিশেষত ব্লেড টেমপ্লেট ব্যবহার করার ক্ষেত্রে `app/view/user.blade.php` হিসেবে ব্যবহার করে।

#### আগামিতে অ্যাপ নির্দিষ্ট করা
অনেকটা অ্যাপ্লিকেশন মোডেলে ভিউটি পুনরাবৃত্তি করা উপযুক্ত হোক, `view($template, $data,
