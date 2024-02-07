## ভিউ
webman ডিফল্টভাবে টেমপ্লেট হিসেবে php কোড ব্যবহার করে এবং `opcache` চালু করা হলে সে সেরা কার্যক্ষমতা দেয়। php এর মূল টেমপ্লেট বাদে, webman এ [Twig](https://twig.symfony.com/doc/3.x/) , [Blade](https://learnku.com/docs/laravel/8.x/blade/9377) , [think-template](https://www.kancloud.cn/manual/think-template/content) টেমপ্লেট ইঞ্জিন প্রদান করে।

## opcache চালু করুন
ভিউ ব্যবহার করার সময়, টেমপ্লেট ইঞ্জিনের জন্য সেরা কার্যক্ষমতা পেতে প্রশাসকের জন্য প্রধানত প্রস্তাবিত করা হয় `opcache.enable` এবং `opcache.enable_cli` নমুনা দুটি বিকল্প।

## Twig ইনস্টলেশন
1. কম্পোজার ইনস্টলেশন

```bash
composer require twig/twig
```

2. `config/view.php` কনফিগারেশন পরিবর্তন করুন

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
> **পরামর্শ**
> অন্যান্য কনফিগারেশন বিকল্পগুলি অপশনস দিয়ে পাস করা যায়, উদাহরণ স্বরূপ  

```php
return [
    'handler' => Twig::class,
    'options' => [
        'debug' => false,
        'charset' => 'utf-8'
    ]
];
```


## ব্লেডে ইনস্টলেশন
1. কম্পোজার ইনস্টলেশন

```bash
composer require psr/container ^1.1.1 webman/blade
```

2. কনফিগারেশন পরিবর্তন `config/view.php` কনফিগারেশন পরিবর্তন করুন
 
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## think-template ইনস্টলেশন
1. কম্পোজার ইনস্টলেশন

```bash
composer require topthink/think-template
```

2. কনফিগারেশন পরিবর্তন `config/view.php` কনফিগারেশন পরিবর্তন করুন

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```
> **পরামর্শ**
> অন্যান্য কনফিগারেশন বিকল্পগুলি অপশনস দিয়ে পাস করা যায়, উদাহরণ স্বরূপ

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


## অপরিষ্কৃত PHP টেমপ্লেট ইঞ্জিন উদাহরণ
নীচের মত `app/controller/UserController.php` ফাইল তৈরি করুন

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
নিচের মত নতুন ফাইল `app/view/user/hello.html` তৈরি করুন

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

## Twig টেমপ্লেট ইঞ্জিন উদাহরণ

`config/view.php` কনফিগারেশন নিম্নলিখিত মত পরিবর্তন করুন

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` নিম্নলিখিত মত

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

ফাইল `app/view/user/hello.html` নিম্নলিখিত মত

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

[Twig](https://twig.symfony.com/doc/3.x/) এ অধিক ডকুমেন্টস দেখুন।  

## Blade টেমপ্লেট উদাহরণ
`config/view.php` কনফিগারেশন নিম্নলিখিত মত পরিবর্তন করুন

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` নিম্নলিখিত মত

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

ফাইল `app/view/user/hello.blade.php` নোট

> লক্ষ্য করুন ব্লেড টেমপ্লেটের সাফটের পর্বতী পরিবর্তন হল `.blade.php`

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

[Blade](https://learnku.com/docs/laravel/8.x/blade/9377) এ অধিক ডকুমেন্টস দেখুন।  


## ThinkPHP টেমপ্লেট উদাহরণ
`config/view.php` কনফিগারেশন নিম্নলিখিত মত পরিবর্তন করুন

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` নিম্নলিখিত মত

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

ফাইল `app/view/user/hello.html` নিম্নলিখিত মত

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
[think-template](https://www.kancloud.cn/manual/think-template/content) এ অধিক ডকুমেন্টস দেখুন।
## টেম্পলেট অ্যাসাইন করা
`view(টেম্পলেট, ভ্যারিয়েবল_অ্যারে)` এই মেথডের ব্যবহারের পাশাপাশি, আমরা যেকোনো জায়গায় টেমপ্লেটে ভ্যারিয়েবল অ্যাসাইন করার জন্য `View::assign()` কল করা যেতে পারে। উদাহরণস্বরূপ:
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

`View::assign()` অন্যান্য কিছু পরিস্থিতিতে মাথামোচাও হতে পারে, উদাহরণ সরব যখন কোনো সিস্টেমে প্রতিটি পৃষ্ঠার শীর্ষ অংশে বর্তমান লগইন ইনফরমেশন প্রদর্শন করা প্রয়োজন, তখন প্রতিটি পৃষ্ঠার জন্য এই তথ্যটি `view('টেম্পলেট', ['user_info' => 'ব্যবহারকারী তথ্য'])` দ্বারা অ্যাসাইন করা অত্যতি ঝাঁকাবুজি হতে পারে। সমাধান দেওয়ার মাধ্যমে মিডলওয়্যারে ব্যবহারকারী তথ্য পেতে পারি, এরপরে `View::assign()` দ্বারা ব্যবহারকারী তথ্য টেমপ্লেট কলিক্স কারয়়ের জন্য অ্যাসাইন করা হয়।

## ভিউ ফাইল পাথ
#### কন্ট্রোলার
কন্ট্রোলার যখন `view('মডেলনাম',[]);` দিলে, ভিউ ফাইল নিয়ে নিতে নিম্নলিখিত নির্দেশিকা অনুসরণ করে:

1. একাধিক অ্যাপ্লিকেশন না থাকলে, `app/view/` এর অধীনে মডেল ফাইল চালাবে
2. [বহূপরিকল্পনা](multiapp.md) যখন থাকবে, `app/অ্যাপ্লিকেশননাম/view/` এর অধীনে মডেল ফাইল চালাবে

সংক্ষেপে বলা যায় যে, যদি `$request->app` ফাঁকা থাকে, তবে `app/view/` এর অধীনে ভিউ ফাইল ব্যবহার হবে, অন্যথায় `app/{$request->app}/view/` এর অধীনে ভিউ ফাইল ব্যবহার হবে।
#### ক্লোজার ফাংশন
ক্লোজার ফাংশনে `$request->app` ফাঁকা থাকে, ব্যবহারকারীর কোনো অ্যাপ্লিকেশনের অংশ নয়, এর ফলে ক্লোজার ফাংশন ব্যবহার করে `app/view/` এর ভিউ ফাইল ব্যবহার হবে, উদাহরণ সরব এমনকি রুট ফাইলো সারি ওয়ার্ব ডেফাইন রুট,
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
``` ভিউ নিয়ে `app/view/user.html` ফাইলটি মডেল হিসাবে ব্যবহার করা হয়.

#### অ্যাপ সংজ্ঞায়িত
একটি বহূপরিকল্পনা পদ্ধতির ভিউ যখন মোটাংশ ব্যবহারের জন্য পুনরাবৃত্তি করা যায়, `view($মডেলনাম, $তথ্য, $অ্যাপ = null)` তৃতীয় পরামিতি `$app` দিয়ে কোন অ্যাপ্লিকেশন ডিরেক্টরির ভিউ ফাইল ব্যবহার করার জন্য ব্যবহার করা যেতে পারে। উদাহরণ সরব `view('user', [], 'admin');` ফাঁকা করতে থাকবে `app/admin/view/` এর ভিউ ফাইল ব্যবহার করা হবে।
## twig প্রসারণ
> **নোট**
> এই বৈশিষ্ট্যটি webman-framework> = 1.4.8 প্রয়োজন

আমরা কনফিগারেশন `view.extension` এ একটি কলব্যাক দিয়ে twig ভিউ ইনস্ট্যান্স এক্সটেন্শন করার মাধ্যমে একটি twig ভিউ ইনস্ট্যান্স এক্সটেন্শন করা যেতে পারে, উদাহরণ সরব `config/view.php` নিম্নলিখিত করে:

```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // এক্সটেনশন যোগ করুন
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // ফিল্টার যোগ করুন
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // ফং যোগ করুন
    }
];
```


## ব্লেড এক্সটেনশন
> **নোট**
> এই বৈশিষ্ট্যটি webman-framework> = 1.4.8 প্রয়োজন
একইভাবে আমরা কনফিগারেশন `view.extension` এ একটি কলব্যাক দিয়ে blade ভিউ ইনস্ট্যান্স এক্সটেন্শন করার মাধ্যমে blade ভিউ ইনস্ট্যান্স এক্সটেন্শন করা যেতে পারে, উদাহরণ সরব `config/view.php` নিম্নলিখিত করে

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // ব্লেড কে অ্যাড ডায়েরেকটিভ 
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```


## blade ব্যবহার করা যাক
> **নোট**
> webman/blade> = 1.5.2 প্রয়োজন
যদি একটি Alert কম্পোনেন্ট যুক্ত করতে হয়, তবে:
**নতুন করো `app/view/components/Alert.php`**
```php
<?php

namespace app\view\components;

use Illuminate\View\Component;

class Alert extends Component
{
    
    public function __construct()
    {
    
    }
    
    public function render()
    {
        return view('components/alert')->rawBody();
    }
}
```

**নতুন করো `app/view/components/alert.blade.php`**
```html
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php` অনুযায়ী যথাক্রমে নিম্ন্লিখিত করো**

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        $blade->component('alert', app\view\components\Alert::class);
    }
];
```

এরপর, Blade কম্পোনেন্ট Alert সেট করার পরে, টেমপ্লেটে ব্যবহার করা যেতে পারে নিম্নলিখি সম্পর্কিত

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>

<x-alert/>

</body>
</html>
```


## think-template মডিফাই করা
think-template ট্যাগলিবকে প্রসারিত করার চেহারা `view.options.taglib_pre_load` ব্যবহার করে, উদাহরণ সরব `config/view.php` নিম্নলিখিত করে

```php
<?php
use support\view\ThinkPHP;
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'taglib_pre_load' => your\namspace\Taglib::class,
    ]
];
```
