## কাস্টম 404
ওয়েবম্যান 404 এর সময় স্বয়ংক্রিয়ভাবে `public/404.html` এর কন্টেন্ট প্রদান করে, তাই ডেভেলপাররা `public/404.html` ফাইলটি সরাসরি পরিবর্তন করতে পারেন।

আপনি যদি 404 এর কন্টেন্টটি গতিশীলভাবে নিয়ন্ত্রণ করতে চান, যেমন এজাক্স রিকোয়েস্টে জেএসওএন ডেটা `{"code:"404", "msg":"404 not found"}` রিটার্ন করা, পৃষ্ঠা অনুরোধে সম্পূর্ণ `app/view/404.html` টেমপ্লেটটি রিটার্ন করার ক্ষেত্রে নিম্নলিখিত উদাহরণটি মূল্যায়ন করুন

> নিচে ওয়েবম্যানের প্রধান টেমপ্লেট হিসাবে, অন্যান্য টেমপ্লেট `twig` `blade` `think-tmplate` সুপারিশভিত্তিক সূচনা

**`app/view/404.html` ফাইলটি তৈরি করুন**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 not found</title>
</head>
<body>
<?=htmlspecialchars($error)?>
</body>
</html>
```

**`config/route.php` ফাইলে নিম্নলিখিত কোডটি যোগ করুন:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // এজাক্স রিকোয়েস্টে জেসন রিটার্ন করুন
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // পৃষ্ঠা অনুরোধে 404.html টেমপ্লেটটি রিটার্ন করুন
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## কাস্টম 500
**`app/view/500.html` তৈরি করুন**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
কাস্টম ত্রুটি টেমপ্লেট:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**নতুন**`app/exception/Handler.php`**(যদি ডিরেক্টরি না থাকে তাহলে নিজে তৈরি করুন)**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * রিন্ডারিং প্রত্যাবর্তন
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // এজাক্স রিকোয়েস্টে জেসন ডেটা রিটার্ন করুন
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // পৃষ্ঠা অনুরোধে 500.html টেমপ্লেটটি রিটার্ন করুন
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**`config/exception.php` কনফিগার করুন**
```php
return [
    '' => \app\exception\Handler::class,
];
```
