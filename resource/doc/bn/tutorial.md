# সহজ উদাহরণ

## স্ট্রিং রিটার্ন
**কন্ট্রোলার তৈরি**

নিচের মত `app/controller/UserController.php` ফাইলটি তৈরি করুন

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // গেট রিকুয়েস্ট থেকে 'name' প্যারামিটারটি নিন, যদি 'name' প্যারামিটারটি না থাকে তাহলে $default_name রিটার্ন করুন
        $name = $request->get('name', $default_name);
        // ব্রাউজারে স্ট্রিং রিটার্ন করুন
        return response('hello ' . $name);
    }
}
```

**ভিজিট**

ব্রাউজারে `http://127.0.0.1:8787/user/hello?name=tom` এ ভিজিট করুন

ব্রাউজার সে `hello tom` রিটার্ন করবে।

## JSON রিটার্ন
`app/controller/UserController.php` ফাইলটি নিচের মত পরিবর্তন করুন

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

**ভিজিট**

ব্রাউজারে `http://127.0.0.1:8787/user/hello?name=tom` এ ভিজিট করুন

ব্রাউজার সে `{"code":0,"msg":"ok","data":"tom"}` রিটার্ন করবে।

তথ্য রিটার্ন করার জন্য json সহায়ক ফাংশন ব্যবহার করা হলে স্বয়ংক্রিয়ভাবে হেডার `Content-Type: application/json` এড করা হয়।

## XML রিটার্ন
আমলে, `xml($xml)` সহায়ক ফাংশন ব্যবহার করে প্রতিক্রিয়া পাঠানো হবে যা `Content-Type: text/xml` হেডার সহ।

এখানে `$xml` প্যারামিটারটি একটি `xml` স্ট্রিং বা `SimpleXMLElement` অবজেক্ট হতে পারে।

## JSONP রিটার্ন
সমানভাবে, `jsonp($data, $callback_name = 'callback')` সহায়ক ফাংশন ব্যবহার করে `jsonp` প্রতিক্রিয়া পাঠানো হবে।

## ভিউ রিটার্ন
`app/controller/UserController.php` ফাইলটি নিচের মত পরিবর্তন করুন

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

নিম্নোক্ত `app/view/user/hello.html` নামে ফাইল তৈরি করুন

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

ব্রাউজারে `http://127.0.0.1:8787/user/hello?name=tom` এ ভিজিট করলে একটি পেজ রিটার্ন হবে যেখানে মূলনামা `hello tom` থাকবে।

লক্ষ্য করুন: webman পূর্বনির্ধারিত ভাবে মূলনামা হিসাবে পিএইচপি নির্ধারিত সিনট্যাক্স ব্যবহার করে। অন্যান্য ভিউ ব্যবহার করতে [ভিউ](view.md) দেখুন।
