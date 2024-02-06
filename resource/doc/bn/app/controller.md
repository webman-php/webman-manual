# কন্ট্রোলার

PSR4 প্রয়োজন মোতাবেক, কন্ট্রোলার ক্লাসের নেমস্পেস `plugin\{প্লাগইন আইডেন্টিফায়ার}` দিয়ে শুরু হয়, যেমন

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

`http://127.0.0.1:8787/app/foo/foo` পয়েন্টগুলি যাচাই করার সময়, পৃষ্ঠাটি `hello index` ফিরে দেয়।

`http://127.0.0.1:8787/app/foo/foo/hello` পয়েন্টগুলি যাচাই করার সময়, পৃষ্ঠাটি `hello webman` ফিরে দেয়।

## URL অ্যাক্সেস
অ্যাপ্লিকেশন প্লাগইন ইউআরএল ঠিকানা পথগুলি `/app` দিয়ে শুরু হয়, পরে প্লাগইন আইডেন্টিফায়ার এবং পরবর্তীতে নির্দিষ্ট কন্ট্রোলার এবং পদ্ধতি হয়।
উদাহরণস্বরূপ `plugin\foo\app\controller\UserController` ইউআরএল ঠিকানা হল `http://127.0.0.1:8787/app/foo/user`
