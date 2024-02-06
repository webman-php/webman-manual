# মিডলওয়্যার
মিডলওয়্যার সাধারণভাবে অনুরোধ বা প্রতিক্রিয়া অপসারণে ব্যবহৃত হয়। উদাহরণস্বরূপ, একটি নিয়ন্ত্রণকারীকে নামবে ব্যবহারকারীর সনাক্ত পরীক্ষা করার আগে বিন্যাস পত্রটি উপরনিবেশ করা, যদি ব্যবহারকারী লগইন না করে থাকে, যেমন আনুষ্ঠানিক উপরনিবেশের সাথে একটি 'হেডার' হেডার যোগ করা। উদাহরণস্বরূপ, কোনও ইউআরআই অনুরোধের অংশ অনুপাত পরিসংখ্যানের সহায়িকা করা।

## মিডলওয়্যার পেঁয়াজ মডেল

```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     মিডলওয়্যার 1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               মিডলওয়্যার 2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         মিডলওয়্যার 3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── অনুরোধ ───────────────────────> নিয়ন্ত্রক ─ প্রতিক্রিয়া ───────────────────────────> মূল্যধারক
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
মিডলওয়্যার এবং নিয়ন্ত্রক একটি পরিপূর্ণ পেঁয়াজ মডেল গঠন করে, যা অনুরোধ এমনির্ঘাত এক অঙ্কের মতো প্রতিষ্ঠিত হওয়ার পরে, নিয়ন্ত্রকটি ঠিকি থাকে। যেমন বোর্ড প্রদর্শন নন করে মন্তব্য কন্ট্রোলার দ্বারা মন্তব্য ক্রিয়াকরণ। মন্তব্যটি একটি প্রতিক্রিয়া দেয়, তারপর প্রতিক্রিয়া ট্রিপর্দায়ক 1, 2, 3 অনুসারে মধ্যমকে দেখাতে সম্প্রদায় দ্বারা ফিরে। অর্থাৎ প্রতিটি মিডলওয়্যারের মধ্যে আমরা অনুরোধ পেতে পারি, প্রতিক্রিয়াও পেতে পারি।

## অনুরোধ অপসারণ
সময়ে সময় আমরা চাই যে কোনও প্রতিক্রিয়া যায় না নিয়ন্ত্রক পর্যায়ে, উদাহরণস্বরূপ, আমরা যদি একটি পরিচয় যাচাইকৃত মিডলওয়্যারে বর্তমান ব্যবহারকারী লগইন না করে থাকেন, তবে আমরা অবৈধ দ্বারা একটি লগইন প্রতিক্রিয়া ত্বরিত ও ফিরিয়ে দেওয়ার উদ্দেশ্যে অপসারণ করতে পারি। তাহলে এই প্রক্রিয়া নীচের মত

```
                              
            ┌─────────────────────────────────────────────────────────┐
            │                    মিডলওয়্যার 1                           │ 
            │     ┌─────────────────────────────────────────────┐     │
            │     │            পরিচয় যাচাইকৃত মিডলওয়্যার             │     │
            │     │        ┌──────────────────────────────┐     │     │
            │     │        │        মিডলওয়্যার 3            │     │     │       
            │     │        │     ┌──────────────────┐     │     │     │
            │     │        │     │                  │     │     │     │
 　── অনুরোধ ────────┐      │     │      নিয়ন্ত্রক       │     │     │     │
            │     │ প্রতিক্রিয়া │     │                  │     │     │     │
   <────────────────┘      │     └──────────────────┘     │     │     │
            │     │        │                              │     │     │
            │     │        └──────────────────────────────┘     │     │
            │     │                                             │     │
            │     └─────────────────────────────────────────────┘     │
            │                                                         │
            └─────────────────────────────────────────────────────────┘
```

উল্লিখিত চিত্রতে দেখানো অনুরোধ যাচাইকৃত মিডলওয়্যারে পৌঁছানোর পর একটি লগইন প্রতিক্রিয়া উৎপন্ন হয়, প্রক্রিয়াটি একটি প্রতিক্রিয়া যাচাইকৃত মিডলওয়্যারথে পৌঁছানোর পরে প্রতিষ্ঠিত হয় এবং ব্রাউজারের দিকে ফিরো।

## মিডলওয়্যার ইন্টারফেস
মিডলওয়্যার অবশ্যই `Webman\MiddlewareInterface` ইন্টারফেসটি পালন করতে হবে।
```php
interface MiddlewareInterface
{
    /**
     * An incoming server request প্রক্রিয়া করা।
     *
     * একটি প্রতিষ্ঠিত করার জন্য একটি উপরবর্তী সার্ভার অনুরোধ প্রক্রিয়া করা।
     * যদি মাত্র না প্রতিষ্ঠিত করতে পারেন তবে এটি সরবরাহকারীর কাছে প্রতিষ্ঠিত করার জন্য শক্তি প্রত্যাহার করে দেয়।
     */
    public function process(Request $request, callable $handler): Response;
}
```
অর্থাৎ, অবশ্যই `process` পদ্ধতিটি পালন করতে হবে এবং `process` পদ্ধতিটি অবশ্যই একটি `support\Response` অবজেক্ট রিটার্ন করতে হবে, ডিফল্টভাবে এই অবজেক্টটি `$handler($request)` থেকে উৎপন্ন হয় (অনুরোধটি সামগ্রিকভাবে পেঁয়াজে পাশাপাশি হবে), এবং 'response()' 'json()' 'xml()' 'redirect()' ইত্যাদি হেল্পার ফাংশন দ্বারা উৎপন্ন করা হতে পারে (অনুরোধটি পেঁয়াজের দিকে থামানো হবে)।

## মিডলওয়্যারে অনুরোধ এবং প্রতিক্রিয়া পেতে
মিডলওয়্যারে আমরা অনুরোধ পেতে পারি, আর পরে তা প্রতিক্রিয়াও পেতে পারি, তাহলে মিডলওয়্যার অভ্যন্তরে তিনটি অংশে বিভক্ত হয়।
1. প্রতিষ্ঠিত ওয়াই অবশ্যই অনুরোধ পেয়ে পাঁচানোর পূর্বের পূর্বাগ্রহ যেমন অনুরোধ প্রক্রিয়া পূর্বাগ্রহের পরবর্তী পরবর্তী ধাপ
2. নিয়ন্ত্রক প্রক্রিয়া নিয়ন্ত্রক প্রস্তুতি। (অনুরোধ প্রক্রিয়া প্রস্তুতিতে)
3. উত্তর প্রতিষ্ঠিত পরবর্তী ধাপ, তবে অনুরোধ প্রক্রিয়া পরবর্তী পরবর্তী প্রক্রিয়ার পরে

মিডলওয়্যারটির তিনটি পরবর্তী ধাপ `অনুসারিত হয়
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Test implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        echo 'এখানে অনুরোধ প্রস্তুতি, অনুরোধ আগে';
        
        $response = $handler($request); // পিছুম প্রতিষ্ঠিত, নিয়ন্ত্রণকারীর দ্বারা প্রতিক্রিয়া উৎপন্ন হবে
        
        echo 'এখানে উত্তর প্রতিষ্ঠিত, পরে';
        
        return $response;
    }
}
```
## উদাহরণ: প্রমাণীকরণ মিডলওয়্যার
ফাইল তৈরি করুন `app/middleware/AuthCheckTest.php` (যদি ডিরেক্টরি না থাকে তাহলে স্বয়ং তৈরি করুন) এর মধ্যে নিম্নলিখিত কোড থাকবে:
```php
<?php
namespace app\middleware;

use ReflectionClass;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AuthCheckTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        if (session('user')) {
            // লগইন করা হয়েছে, অনুরোধটি আপলোড এর উপরে চলবে
            return $handler($request);
        }

        // নিষিদ্ধ হতে যাওয়ার কন্ট্রোলারের কোনও মেথডগুলি কি চেক করার জন্য রিফ্লেকশন ব্যবহার করুন
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // আপনি যে মেথডটি অ্যাক্সেস করতে চান তা লগইন করার প্রয়োজন
        if (!in_array($request->action, $noNeedLogin)) {
            // অনুরোধ ব্লক করুন, একটি রিডাইরেক্ট রিসপন্স ফিরিয়ে দিন, অনুরোধটি আপলোড এর উপরে চলে যাবে না
            return redirect('/user/login');
        }

        // লগইন দরকার নেই, অনুরোধটি আপলোড এর উপরে চলবে
        return $handler($request);
    }
}
```

নতুন কন্ট্রোলার বানান `app/controller/UserController.php`
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * লগইনের প্রয়োজন নেই মেথড
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request)
    {
        $request->session()->set('user', ['id' => 10, 'name' => 'webman']);
        return json(['code' => 0, 'msg' => 'login ok']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'ok', 'data' => session('user')]);
    }
}
```

> **লক্ষীয়বরণ**
> `$noNeedLogin` এখানে বর্তমান যতগুলি মেথড লগইন ছাড়াই অ্যাক্সেস করা যাবে তা রেকর্ড রাখা আছে।

`config/middleware.php` ফাইলে গ্লোবাল মিডলওয়ের মধ্যে যোগ করুন নিম্নলিখিত মতে:
```php
return [
    // গ্লোবাল মধ্যবর্তী
    '' => [
        // ... অন্যান্য মিডলওয়ের জন্য এখানে ছাড়া দেওয়া হয়েছে
        app\middleware\AuthCheckTest::class,
    ]
];
```

প্রমাণীকরণ মিডলওয়েআর সাথে, আমরা কন্ট্রোলার লেভেলে প্রস্তুত কোড লিখতে পারি এখন, কোনও ব্যক্তি লগইন করা আছে কি না এর চিন্তা করে না।

## উদাহরণ: ক্রোস-অ্বজেক্ট রিকুয়েস্ট মিডলওয়েয়ার
`app/middleware/AccessControlTest.php` ফাইল তৈরি করুন (যদি ডিরেক্টরি না থাকে তাহলে স্বয়ং তৈরি করুন) এর মধ্যে নিম্নলিখিত কোড থাকবে:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AccessControlTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // যদি অপশন রিকুয়েস্ট হয় তবে একটি খালি রেসপন্স ফেরত দিন, অথবা অন্যথায় উপরোক্ত রেসপন্স দেয়া হবে
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // রেসপন্সে ক্রস-অ্বজেক্ট সংক্রান্ত এইচটিটিপি শিরোনাম যুক্ত করুন
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', '*'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
        ]);
        
        return $response;
    }
}
```

> **নোট**
> ক্রস-অবজেক্ট রিকুয়েস্ট হতে বাচাতে তো অফশন্স রিকুয়েস্ট, আমাদের চাইব না। সুতরাং অফশন্স রিকুয়েস্ট থামাতে এখানের মাধ্যমে আমরা একটি খালি রেসপন্স ফেরত দিয়েছি (`response('')`)।
> যদি আপনার প্রোগ্রাম রাউট সেট করতে হয়, তবে অফশন্স রিকুয়েস্টগুলি জন্য বিালব ব্যবহার করুন `Route::any(..)` বা `Route::add(['POST', 'OPTIONS'], ..)` সেট করার জন্য।


`config/middleware.php` ফাইলটি বাইর যোগ করুন গ্লোবাল মিডলওয়ের মধ্যে:
```php
return [
    // গ্লোবাল মিডলওয়ে
    '' => [
        // ... অন্যান্য মিডলওয়ের জন্য এখানে ছাড়া দেওয়া হয়েছে
        app\middleware\AccessControlTest::class,
    ]
];
```

> **নোট**
> যদি Ajax রিকুয়েস্ট নিজস্ব হেডার সেট করে, তবে আপনার মিডলওয়েরে `Access-Control-Allow-Headers` ফিল্ডটি যুক্ত করতে হবে যে অফশন্স রিকুয়েস্ট প্রতিউত্তরে `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response` হবে।

## স্পষ্টীকরণ

- মিডলওয়ের দুটি প্রকার, গ্লোবাল মিডলওয়েয়ার, অ্যাপ্লিকেশন মিডলওয়েয়ার (অ্যাপ্লিকেশন মিডলওয়েয়ার শুধুমাত্র একাধিক অ্যাপ্লিকেশন মোডেলে কর্মক্ষম, এর বিবরণ দেখুন [মাল্টিঅ্যাপ](multiapp.md)) এবং রাউট মিডলওয়েয়ার
- বর্তমানে সিঙ্গেল কন্ট্রোলার মিডলওয়েয়ার সমর্থন করা হয়নি (তবে এইখানে মিডলওয়েয়ার ঠিক ঠাক যা করার জন্য নিয়মিতোভাবে ` $request->controller` যাতোয়া পরীক্ষা করা যেতে পারে)।
- মিডলওয়ের কনফিগারেশন ফাইল অবস্থান `config/middleware.php`
- গ্লোবাল মিডলওয়েয়ার গুলো আইটেম এখানের অধীনে যায়
`php
return [  
    // গ্লোবাল মিডলওয়েয়ার
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // অ্যাপলিকেশন মিডলওয়ের (অ্যাপ্লিকেশন মিডলওয়েয়ার শুধুমাত্র একাধিক অ্যাপ্লিকেশন মোডেলে কর্মক্ষম)
    'api' => [    
        app\middleware\ApiOnly::class,
    ]
];
`  

## রাউট মিডলওয়েয়ার

আমরা কোন একটা বা একাধিক রাউটগুলির জন্য মিডলওয়েয়ার সেট করতে পারি।

উদাহরণস্বরূপ, `config/route.php` ফাইলে নিম্নলিখিত কনফিগারেশন যোগ করুন:
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($r, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

## মিডলওয়ে এর মাধ্যমে প্যারামিটার পাস করা

> **নোট**
> এই সুযোগটি পাওয়ার জন্য webman-framework> = 1.4.8 এর পরের সংস্করণ প্রয়োজন।

1.4.8 সংস্করণের পরে, কনফিগারেশন ফাইল সরাসরি মিডলওয়েয়ারগুলি বা অনটিম্স ফাংশনে ইনস্ট্যান্স ইউজ করার এবং এর মাধ্যমে মিডলওয়ের প্যারামিটার পাস করা স
## মিডলওয়্যার বর্তমান অনুষ্ঠান পথ তথ্য পান
> **লক্ষ্য করুন**
> ওয়েবম্যান-ফ্রেমওয়ার্ক >= 1.3.2 প্রয়োজন

আমরা `$request->route` ব্যবহার করে রাউট অবজেক্ট পেতে পারি, এবং এর মাধ্যমে সাম্প্রতিক তথ্য পেতে বিভিন্ন মেথড কল করতে পারি।

**রাউট কনফিগারেশন**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**মিডলওয়্যার**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $route = $request->route;
        // যদি অনুরোধ কোনো রুটের সাথে মিলন না করে (ডিফল্ট রুট ব্যাতিত), তবে $request->route হবে null
        // ধরা যাক ব্রাউজার ঠিকানা /user/111, তাহলে আমরা নিম্নলিখিত তথ্য দেখতে পাবো
        if ($route) {
            var_export($route->getPath());       // /user/{uid}
            var_export($route->getMethods());    // ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD','OPTIONS']
            var_export($route->getName());       // user_view
            var_export($route->getMiddleware()); // []
            var_export($route->getCallback());   // ['app\\controller\\UserController', 'view']
            var_export($route->param());         // ['uid'=>111]
            var_export($route->param('uid'));    // 111 
        }
        return $handler($request);
    }
}
```

> **লক্ষ্য করুন**
> `$route->param()` মেথডের জন্য ওয়েবম্যান-ফ্রেমওয়ার্ক >= 1.3.16 প্রয়োজন।


## মিডলওয়্যার থেকে অস্বাভাবিক প্রাপ্তি
> **লক্ষ্য করুন**
> ওয়েবম্যান-ফ্রেমওয়ার্ক >= 1.3.15 প্রয়োজন

ব্যবসা প্রক্রিয়া পর্যন্ত অস্বাভাবিক হতে পারে, এমনকি আমরা মিডলওয়্যারে ব্যবহার করে `$response->exception()` দিয়ে অস্বাভাবিককে পেতে পারি।

**রাউট কনফিগারেশন**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('exception test');
});
```

**মিডলওয়্যার:**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $response = $handler($request);
        $exception = $response->exception();
        if ($exception) {
            echo $exception->getMessage();
        }
        return $response;
    }
}
```

## সুপার গ্লোবাল মিডলওয়্যার

> **লক্ষ্য করুন**
> এই বৈশিষ্ট্যটি ওয়েবম্যান-ফ্রেমওয়ার্ক >= 1.5.16 প্রয়োজন

প্রধান প্রকল্পের গ্লোবাল মিডলওয়্যার মুখোমুখি প্রভাব ফেলে কিন্তু [অ্যাপ প্লাগইন](app/app.md) এ কোনো প্রভাব পড়াবে না। সময় সময় আমরা সুপার গ্লোবাল মিডলওয়্যার যুক্ত করতে চাইবো যা প্রভাব পড়বে প্রধান প্রকল্প সহ সব প্লাগইনে।

`config/middleware.php` এ নিম্নলিখিত কনফিগ করুন:
```php
return [
    '@' => [ // প্রধান প্রজেক্ট এবং সমস্ত প্লাগইনে গ্লোবাল মিডলওয়্যার যুক্ত করুন
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // শুধুমাত্র প্রধান প্রকল্পে গ্লোবাল মিডলওয়্যার যুক্ত করুন
];
```

> **পরামর্শ**
> `@` সুপার গ্লোবাল মিডলওয়্যার আলোচনা যুক্ত করা যেতে পারে না শুধু মুখ্য প্রজেক্টে, বরং কোনও প্লাগইনের থাকা যেতে পারে, উদাহরণস্বরূপ `plugin/ai/config/middleware.php` এ যেহেতু সুপার গ্লোবাল মিডলওয়্যার কনফিগ করা হয়েছে, তাহলে এটি প্রধান প্রজেক্ট সহ সমস্ত প্লাগইনের উপর প্রভাব ফেলবে।

## প্লাগইনে মিডলওয়্যার যুক্ত করুন

> **লক্ষ্য করুন**
> এই বৈশিষ্ট্যটি ওয়েবম্যান-ফ্রেমওয়ার্ক >= 1.5.16 প্রয়োজন

সময় সময় আমরা কোনো [অ্যাপ প্লাগইন](app/app.md) এ মিডলওয়্যার যুক্ত করতে চাইবো, কিন্তু প্লাগইনে কোনো পরিবর্তন করতে চাইনা (কারণ আপগ্রেড করলে ওভাররাইড হবে) তখন আমরা প্রধান প্রকল্পে তার জন্য মিডলওয়্যার কনফিগ করতে পারি।

`config/middleware.php` এ নিম্নলিখিত কনফিগ করুন:
```php
return [
    'plugin.ai' => [], // ai প্লাগইনে মিডলওয়্যার যুক্ত করুন
    'plugin.ai.admin' => [], // ai প্লাগইনের অ্যাডমিন মডিউলে মিডলওয়্যার যুক্ত করুন
];
```

> **পরামর্শ**
> এছাড়াও আপনি অন্য কোনও প্লাগইনে এই কনফিগ যুক্ত করতে পারেন, উদাহরণস্বরূপ `plugin/foo/config/middleware.php` এ এ ধরনের কনফিগ যুক্ত হলে এটি ai প্লাগইনে প্রভাব ফেলবে।
