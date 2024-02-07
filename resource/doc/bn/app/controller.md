# কন্ট্রোলার

PSR4 শৈলী অনুযায়ী, কন্ট্রোলার ক্লাসের নেমস্পেস শুরু হয় `plugin\{প্লাগইন আইডেন্টিফায়ার}` দিয়ে, উদাহরণস্বরূপ

নতুন কন্ট্রোলার ফাইল তৈরি করুন `plugin/foo/app/controller/FooController.php`।

```php
<?php
namespace plugin\foo\app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

`http://127.0.0.1:8787/app/foo/foo` এ অ্যাক্সেস করা হলে, পেজটি `hello index` রিটার্ন করবে

`http://127.0.0.1:8787/app/foo/foo/hello` এ অ্যাক্সেস করা হলে, পেজটি `hello webman` রিটার্ন করবে


## URL অ্যাক্সেস
অ্যাপ্লিকেশন প্লাগইনের URL ঠিকানা সবসময় `/app` দিয়ে শুরু হয়, এরপরে প্লাগইন আইডেন্টিফায়ার এবং তারপরে নির্দিষ্ট কন্ট্রোলার এবং মেথডের নাম থাকে।
উদাহরণস্বরূপ `plugin\foo\app\controller\UserController` এর URL ঠিকানা হল `http://127.0.0.1:8787/app/foo/user`
