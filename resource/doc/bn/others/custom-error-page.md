## কাস্টম 404
webman পরিক্ষার 404 সময়ই `public/404.html` ফাইলের সামগ্রী স্বয়ংক্রিয়ভাবে ফিরিয়ে দেবে, তাই ডেভেলপাররা প্রাথমিক ভৌতিক `public/404.html` ফাইল পরিবর্তন করতে পারেন।

আপনি যদি 404 সামগ্রীটি ডাইনামিকভাবে নিয়ন্ত্রণ করতে চান, উদাহরণস্বরূপ, এজাক্স অনুরোধে JSON ডেটা ফিরিয়ে দিতে তাহলে `{"code:"404", "msg":"404 not found"}` এবং পৃষ্ঠা অনুরোধে ফিরিয়ে দিতে `app/view/404.html` টেমপ্লেট প্রদান করতে চান, তাহলে নীচের উদাহরণ দেখুন।

> নিম্নলিখিতদ্বারা পিএইচপি মূল টেমপ্লেট থেকে উদাহরণ দেওয়া হয়েছে, অন্য টেমপ্লেটে `twig` `blade` `think-tmplate` এর প্রিন্সিপ অনুরূপ।

**ফাইল তৈরি করুন `app/view/404.html`**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 পাওয়া যায়নি</title>
</head>
<body>
<?=htmlspecialchars($error)?>
</body>
</html>
```

**`config/route.php` তে নীচের কোডটি যোগ করুন:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // এজাক্স অনুরোধে JSON ফিরিয়ে দিবে
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 পাওয়া যায়নি']);
    }
    // পৃষ্ঠা অনুরোধে 404.html টেমপ্লেট ফিরিয়ে দিবে
    return view('404', ['error' => 'কিছু ভুল হয়েছে'])->withStatus(404);
});
```

## কাস্টম 500
**নতুন বানান `app/view/500.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 অভ্যন্তরীণ সার্ভার ত্রুটি</title>
</head>
<body>
কাস্টম ত্রুটি টেমপ্লেট:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**নতুন** `app/exception/Handler.php`(এক্ষেত্রে ডিরেক্টরি অস্তিত্ব না থাকলে স্বনিয়মে তৈরি করুন)**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * পাল্টা ফিরিয়ে দেওয়া
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // এজাক্স অনুরোধে JSON ডেটা ফিরিয়ে দেবে
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // পৃষ্ঠা অনুরোধে 500.html টেমপ্লেট ফিরিয়ে দেবে
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
