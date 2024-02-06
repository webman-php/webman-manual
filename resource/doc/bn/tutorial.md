# সহজ উদাহরণ

## স্ট্রিং রিটার্ণ
**কন্ট্রোলার তৈরি**

তৈরি করুন ফাইল `app/controller/UserController.php` নিম্নলিখিত মত

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // get রিকোয়েস্ট থেকে নাম প্যারামিটার পেতে, যদি নাম প্যারামিটার না পাওয়া যায়, তবে $default_name রিটার্ণ করুন
        $name = $request->get('name', $default_name);
        // স্ট্রিং ফিরিয়ে দিন ব্রাউজারকে
        return response('hello ' . $name);
    }
}
```

**প্রবেশ**

ব্রাউজারে `http://127.0.0.1:8787/user/hello?name=tom` এ প্রবেশ করুন

ব্রাউজার হ্যালো টম ফিরিয়ে দিবে

## json রিটার্ণ
ফাইল `app/controller/UserController.php` পরিবর্তন করুন নিম্নলিখিত মত

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return json([
            'code' => 0, 
            'msg' => 'ok', 
            'data' => $name
        ]);
    }
}
```

**প্রবেশ**

ব্রাউজারে `http://127.0.0.1:8787/user/hello?name=tom` এ প্রবেশ করুন

ব্রাউজার এ আসবে `{"code":0,"msg":"ok","data":"tom"}`

json হেল্পার ফাংশন ব্যবহার করে ডাটা ফিরিয়ে দেওয়ার সময় স্বয়ংক্রিয়ভাবে একটি হেডার যুক্ত করা হয় `Content-Type: application/json`

## xml রিটার্ণ
একইভাবে, হেল্পার ফাংশন `xml($xml)` ব্যবহার করে `xml` রিস্পন্স ফিরিয়ে দিবে যেখানে `$xml` প্যারামিটারটি হতে পারে `xml` স্ট্রিং অথবা `SimpleXMLElement` অবজেক্ট

## jsonp রিটার্ণ
একইভাবে, হেল্পার ফাংশন `jsonp($data, $callback_name = 'callback')` ব্যবহার করে `jsonp` রিস্পন্স ফিরিয়ে দিবে.

## ভিউ রিটার্ণ
ফাইল `app/controller/UserController.php` পরিবর্তন করুন নিম্নলিখিত মত

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return view('user/hello', ['name' => $name]);
    }
}
```

নিম্নলিখিত মতে তৈরি করুন ফাইল `app/view/user/hello.html`

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

ব্রাউজারে `http://127.0.0.1:8787/user/hello?name=tom` এ প্রবেশ করুন
html পৃষ্ঠার কন্টেন্ট দেখতে পাবেন `hello tom`

লক্ষ্য করুন: webman মূলত php প্রাথমিক সিনট্যাক্স ব্যবহার করে টেমপ্লেট হিসেবে ব্যবহার করে। অন্য ভিউ ব্যবহার করতে [ভিউ](view.md) দেখুন।
