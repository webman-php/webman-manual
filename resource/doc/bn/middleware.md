# মিডলওয়্যার
মিডলওয়্যার সাধারণভাবে অনুরোধ বা সার্বিক উত্তর বাধাগ্রস্ত করার জন্য ব্যবহৃত হয়। উদাহরণস্বরূপ, একটি নিয়ামকের আগে ব্যবহারকারীর পরিচয় যাচাই করা, ব্যবহারকারী লগইন না থাকলে লগইন পৃষ্ঠাতে পাঠানো, উত্তরে কোনও শিরোনাম থাকা একটি হেডার যোগ করা, কোনও uri অনুরোধের সংখ্যা গণনা করা ইত্যাদি।

## মিডলওয়্যার প্রবাহ মডেল

```  
                              
            ┌──────────────────────────────────────────────────────┐
            │                     মিডলওয়্যার ১                       │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               মিডলওয়্যার ২                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         মিডলওয়্যার ৩          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
   ── অনুরোধ ───────────────────────> নিয়ামক ─ উত্তর ───────────────────────────> ক্লায়েন্ট
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
মিডলওয়্যার এবং নিয়ামক একটি ক্লাসিক্যাল প্রবাহ মডেল তৈরি করে, যেখানে মিডলওয়্যার একটি একটি প্রস্তুতির পাতা এবং নিয়ামক হল মিডওয়ের গভ্রভিত। নিম্নলিখিত ছবিতে প্রদর্শিত মত, অনুরোধ তীর মাধ্যমে মিডলওয়্যার 1, 2, 3 নিয়ামকের প্রাপ্তি করে, নিয়ামক একটি উত্তর ফেরত দেয়, এরপর উত্তরটি 3, 2, 1 অনুসরণ করে ফিরে মিডলওয়্যারের থেকে প্রাপ্তি করে ক্লায়েন্টের দিকে। এটা মানে যে, প্রতিটি মিডলওয়্যারে আমরা অনুরোধ পেতে এবং উত্তর পেতে পারি।

## অনুরোধ অনুপ্রবেশ
কখনওখানি আমাদের চাইতে পারে না যে কোনও অনুরোধটি নিয়ামক স্তরে পৌঁছানো যায়, যদি নির্দিষ্ট মিডলওয়্যার 2 এ আমরা বর্তমান ব্যবহারকারীটি লগইন না করে পাই, তবে আমরা অপ্রাক্তি অনুরোধটি আপিউপ করে এবং লগইন স্থিতির জন্য একটি লগইন উত্তর ফেরত দিতে পারি। তাই, এই প্রক্রিয়াটি নিম্নলিখিত অনুপ্রবেশের মতো হতে পারে

```  
                              
            ┌────────────────────────────────────────────────────────────┐
            │                         মিডলওয়্যার ১                         │ 
            │     ┌────────────────────────────────────────────────┐     │
            │     │                   মিডলওয়্যার ২                   │     │
            │     │          ┌──────────────────────────────┐      │     │
            │     │          │        মিডলওয়্যার ৩            │      │     │       
            │     │          │    ┌──────────────────┐      │      │     │
            │     │          │    │                  │      │      │     │
   ── অনুরোধ ──────────┐     │    │   নিয়ামক         │      │      │     │
            │     │   উত্তর   │    │                  │      │      │     │
   <───────────────────┘     │    └──────────────────┘      │      │     │
            │     │          │                              │      │     │
            │     │          └──────────────────────────────┘      │     │
            │     │                                                │     │
            │     └────────────────────────────────────────────────┘     │
            │                                                            │
            └────────────────────────────────────────────────────────────┘
```

উল্লেখিত ছবিতে দেখা যাচ্ছে যে, মিডলওয়্যার 2 পেরিয়ে লগইনের উত্তর তৈরি করে, এরপর মিডলওয়্যার 1 এ থেকে উত্তরটি ফিরে যায় এবং ক্লায়েন্টের দিকে ফিরে যায়।

## মিডলওয়্যার ইন্টারফেস
মিডলওয়্যার অবশ্যই `Webman\MiddlewareInterface` ইন্টারফেস অনুসরণ করতে হবে।
```php
interface MiddlewareInterface
{
    /**
     * এসোয়াত incoming সার্ভারের অনুরোধ প্রসেস করুন।
     *
     * একটি উত্তর তৈরি করার জন্য ভেঁকে একটি অনুরোধ প্রসেস করে।
     * যদি প্রত্যুত্তর তৈরি করতে অক্ষম, তবে তা নিয়মিত দা tor কে সমর্থন করতে পারে response handler
     */
    public function process(Request $request, callable $handler): Response;
}
```
অর্থাৎ এটি `প্রসেস` মেথডটি অবশ্যই অনুসরণ করতে হবে, `প্রসেস` মেথডটি অবশ্যই একটি `support\Response` অবজেক্ট ফেরত দেওয়া উচিত, ডিফল্টভাবে এই অবজেক্টটি `$handler($request)` দ্বারা তৈরি হয় (অনুরোধটি মধ্যে থাকবে)। পরবর্তীতে তা হতে পারে `response()` `json()` `xml()` `redirect()` ইত্যাদি সহায়ক ফাংশন দ্বারা তৈরি হয় (অনুরোধটি থামাবে)।

## অনুরোধ এবং সত্ত্বা পেতে মিডলওয়্যার
মিডলওয়্যারে আমরা অনুরোধ পেতে এবং নিয়ামকের পরে উত্তর পেতে পারি। তাই মিডলওয়্যারে তিনটি পর্বে ভাগ করা হয়।
1. অনুরোধ প্রসেস পদক্ষেপ, অর্থাৎ অনুরোধের প্রক্রিয়া করার আগের পদক্ষেপ
2. নিয়ামকের প্রসেস পদক্ষেপ, অর্থাৎ অনুরোধের প্রক্রিয়া পদক্ষেপ
3. উত্তর প্রসেস পদক্ষেপ, অর্থাৎ অনুরোধের সাবধানের পদক্ষেপ

মিডলওয়্যারে এই তিনটি পর্ব নিম্নলিখিতভাবে প্রদর্শিত হতে পারে
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
        echo 'এখানে অনুরোধ প্রসেস পদক্ষেপ, অর্থাৎ অনুরোধ প্রক্রিয়া আগে';
        
        $response = $handler($request); // অনুরোধটি মধ্যে থাকবে অস্ত্যায়ন নিয়ামকের জন্য হ্যান্ডলারসহ
        
        echo 'এখানে উত্তর প্রসেস পদক্ষেপ, অর্থাৎ অনুরোধ প্রক্রিয়ার পরে';
        
        return $response;
    }
}
```
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
            // লগইন করা হয়েছে, অনুরোধ অবিচ্ছিন্নভাবে পরান
            return $handler($request);
        }

        // রিফ্লেকশন ফ্রোম কন্ট্রোলার এর কোনও মেথডগুলি যারা লগইন প্রয়োজন নেই, সেটা পেতে
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // অ্যাক্সেস করা পদ্ধতি লগইন প্রয়োজন
        if (!in_array($request->action, $noNeedLogin)) {
            // অনুরোধ বন্ধ গণতে, একটি পুনর্নির্দেশনা রিসপন্স প্রদান করুন, অনুরোধটি অবিরত পর্যন্ত চলমান
            return redirect('/user/login');
        }

        // লগইনের প্রয়োজন নেই, অনুরোধ অবিচ্ছিন্নভাবে পরান
        return $handler($request);
    }
}
```

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

```php
return [
    // সার্বিক মধ্যবর্তী
    '' => [
        // ...অন্যান্য মধ্যবর্তী অংশগুলি অক্ষরিভুক্ত
        app\middleware\AuthCheckTest::class,
    ]
];
```

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
        // যদি অপশনস অনুরোধ করেন তাহলে একটি ফাঁকা রেসপন্স প্রদান করুন, অন্যথায় অবিচ্ছিন্নভাবে পরান এবং একটি রেসপন্স প্রাপ্ত করুন
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // রেসপন্সে ক্রশ-ওভার সংক্রান্ত এইচটিটিপি শিরোনাম যুক্ত করুন
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

```php
return [
    // সার্বিক মধ্যবর্তী
    '' => [
        // ...অন্যান্য মধ্যবর্তী অংশগুলি অক্ষরিভুক্ত
        app\middleware\AccessControlTest::class,
    ]
];
```
## মিডলওয়্যারে নিয়ে কন্ট্রোলারকে প্যারামিটার পাঠানো

সময়ের সাথে কণ্ট্রোলারের দরকার পড়ে মিডলওয়্যার থেকে উৎপন্ন ডেটা। এই সময়ে আমরা মিডলওয়্যারকে `$request` অবজেক্টে প্রোপার্টি অ্যাড করে কন্ট্রোলারকে প্যারামিটার হিসেবে পাঠানোর মাধ্যমে ডেটা পৌঁছাতে পারি। উদাহরণঃ

**মিডলওয়্যার**
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
        $request->data = 'some value';
        return $handler($request);
    }
}
```

**কন্ট্রোলারঃ**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response($request->data);
    }
}
```

## মিডলওয়্যার এর মাধ্যমে বর্তমান অনুরোধ রুটের তথ্য পূর্ণ করা

> **লক্ষণীয়**
> webman-framework >= 1.3.2 প্রয়োজন।

আমরা `$request->route` ব্যবহার করে রুট অবজেক্ট পেতে পারি এবং সাথে মেথডগুলি কল করে সেই তথ্য প্রাপ্ত করতে পারি। 

**রুট কনফিগারেশন**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**মিডলওয়্যারঃ**
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
        // যদি রুট না মেলে (ডিফল্ট রুটের বাইরে) তাহলে  $request->route  হবে null
        // ধরা যাক, ব্রাউজার ঠিকানায় /user/111 প্রাপ্ত করে, তাহলে নিম্নরূপ তথ্য দেখায়
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

> **লক্ষণীয়**
> `$route->param()` মেথডটি webman-framework >= 1.3.16 প্রয়োজন।

## মিডলওয়্যার এর মাধ্যমে অসুস্থতা পাওয়া সম্পর্কে

> **লক্ষণীয়**
> webman-framework >= 1.3.15 প্রয়োজন।

ব্যবসা প্রসেসে সময় অনুমান অসুস্থতা ঘটতে পারে, মিডলওয়্যারে `$response->exception()` ব্যবহার করে অনুমান প্রাপ্ত করা যায়।

**রুট কনফিগারেশন**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('exception test');
});
```

**মিডলওয়্যারঃ**
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

> **লক্ষণীয়**
> এই বৈশিষ্ট্য webman-framework >= 1.5.16 প্রয়োজন।

প্রধান প্রজেক্টের সর্বগোণী মিডলওয়্যার শুধুমাত্র প্রধান প্রজেক্ট প্রভাবিত করে, [অ্যাপ্লিকেশন প্লাগইন](app/app.md) দেওয়া মিডলওয়্যারের উপর কোনও প্রভাব প্রদান করে না। কখনও আমরা যদি একটি সমস্ত প্লাগইনকে প্রভাবিত করার জন্য একটি মিডলওয়্যার যোগ করতে চাই, তবে আমরা ব্যবহার করতে পারি সুপার গ্লোবাল মিডলওয়্যার।

`config/middleware.php` তে নিম্নলিখিত কনফিগারেশন করুন:
```php
return [
    '@' => [ // প্রধান প্রোজেক্ট এবং সমস্ত প্লাগইনে সুরক্ষিত মিডলওয়্যার যোগ করুন
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // শুধুমাত্র প্রধান প্রোজেক্টে সাধারণ মিডলওয়্যার যোগ করুন
];
```

> **পরামর্শ**
> `@` সুপার গ্লোবাল মিডলওয়্যার প্রধান প্রজেক্টের মাঝে না বসে, প্লাগইনে বা অতিরিক্ত কোনও জায়গায় বসানো যায়, উদাহরণস্বরূপ, `plugin/ai/config/middleware.php` এই ফাইলে তার `@` সুপার গ্লোবাল মিডলওয়্যার যোগ করা যায়, তারপর এটি প্রধান প্রজেক্টকে এবং সমস্ত প্লাগইনকে প্রভাবিত করবে।

## কোনও প্লাগইনে মিডলওয়্যার যোগ করা

> **লক্ষণীয়**
> এই বৈশিষ্ট্য webman-framework >= 1.5.16 প্রয়োজন।

সময়ের সাথে আমরা একটি [অ্যাপ্লিকেশন প্লাগইন](app/app.md)একটি মিডলওয়্যার যোগ করতে চাই, তবে প্লাগইনের কোড পরিবর্তন করতে চাই না (যেহেতু আপগ্রেড করলে অভ্যন্তরীণ হবে), তাহলে আমরা এটি প্রধান প্রজেক্টে যোগ করতে পারি।

`config/middleware.php` তে নিম্নলিখিত কনফিগারেশন করুন:
```php
return [
    'plugin.ai' => [], // ai প্লাগইনে মিডলওয়্যার যোগ করুন
    'plugin.ai.admin' => [], // ai প্লাগইনের অ্যাডমিন মডিউলে মিডলওয়্যার যোগ করুন
];
```

> **পরামর্শ**
> নির্দিষ্ট প্লাগইনে শরূ হতে অন্যূন কনফিগারেশন এড করার মাধ্যমে অন্য প্লাগইনগুলিতে প্রভাব ডালা যায়, উদাহরণস্বরূপ, `plugin/foo/config/middleware.php` এই ফাইলে এই ধরনের একটি কনফিগারেশন যোগ করার মাধ্যমে আপনার অন্যান্য প্লাগইনগুলিতে প্রভাব ডালা যেতে পারে।
