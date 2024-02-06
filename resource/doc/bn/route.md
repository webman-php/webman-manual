## রুট
## ডিফল্ট রুটিং নিয়ম
ওয়েবম্যানের ডিফল্ট রুটিং নিয়ম হল `http://127.0.0.1:8787/{কন্ট্রোলার}/{অ্যাকশন}`।

ডিফল্ট কন্ট্রোলার হল `app\controller\IndexController` এবং ডিফল্ট অ্যাকশন হল `index`।

উদাহরণস্বরূপ:
- `http://127.0.0.1:8787` এ যাওয়া হবে ডিফল্ট কন্ট্রোলার `app\controller\IndexController` এর `index` মেথডে।
- `http://127.0.0.1:8787/foo` এ যাওয়া হবে `app\controller\FooController` ক্লাসের `index` মেথডে।
- `http://127.0.0.1:8787/foo/test` এ যাওয়া হবে `app\controller\FooController` ক্লাসের `test` মেথডে।
- `http://127.0.0.1:8787/admin/foo/test` এ যাওয়া হবে `app\admin\controller\FooController` ক্লাসের `test` মেথডে (দেখুন [মাল্টি অ্যাপ](multiapp.md))।

অতএব ১.৪ বার্সন থেকে ওয়েবম্যান ডিফল্ট রুটিং এর প্রতিবিম্বমূলক পরিবর্তন সমর্থন করে। উদাহরণস্বরূপ
```php
app
├── admin
│   └── v1
│       └── v2
│           └── v3
│               └── controller
│                   └── IndexController.php
└── controller
    ├── v1
    │   └── IndexController.php
    └── v2
        └── v3
            └── IndexController.php
```

আপনি যদি কোনও রিকোয়েস্ট রুটিং পরিবর্তন করতে চান তবে কনফিগারেশন ফাইল `config/route.php` এ পরিবর্তন করুন।

আপনি যদি ডিফল্ট রুটিং বন্ধ করতে চান তবে কনফিগারেশন ফাইল `config/route.php` এর শেষে নিম্নলিখিত কনফিগারেশন যোগ করুনঃ
```php
Route::disableDefaultRoute();
```

## ক্লোজার রুটিং
`config/route.php` এ প্রিস্তাবিত কোড টুকরা যোগ করুন
```php
Route::any('/test', function ($request) {
    return response('test');
});

```
> **দ্রষ্টব্য**
> ক্লোজার ফাংশন কোনো কন্ট্রোলারের অংশ নয়, তাই `$request->app` `$request->controller` `$request->action` সবকিছু শূন্য স্ট্রিং।

যখন ঠিকানা লিখলে `http://127.0.0.1:8787/test` এর হাতে সৃষ্টি হবে `test` স্ট্রিং।

> **দ্রষ্টব্য**
> রুটের পথ অবশ্যই `/` দিয়ে শুরু করতে হবে, যেমন

```php
// ভুল ব্যবহার
Route::any('test', function ($request) {
    return response('test');
});

// সঠিক ব্যবহার
Route::any('/test', function ($request) {
    return response('test');
});
```


## ক্লাস রুটিং
`config/route.php` এর সামনের রুটিং কোড নিম্নেরভাবে যোগ করুন
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
যখন ঠিকানা লিখলে `http://127.0.0.1:8787/testclass` এর হাতে সৃষ্টি হবে`app\controller\IndexController` ক্লাসের `test` মেথডের রিটার্ন মান।


## রুটিং প্যারামিটার
যদি রুটিংটি প্যারামিটার অবশ্যই থাকে তাহলে `{key}` দ্বারা ম্যাচ করুন, ম্যাচিং ফলাফলগুলি যে ম্যাচিং গুলি তিনিরপি কন্ট্রোলার মেথড প্যারামিটারে পাঠানো হবে (প্যারামিটার প্রতি প্রথমে থেকে চলে যাবে)। উদাহরণ:
```php
// ম্যাচ করে /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('প্যারামিটার'.$id.' রিসিভ করা হয়েছে');
    }
}
```

আরও উদাহরণ:
```php
// ম্যাচ করে /user/123, ম্যাচ হবে না /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// ম্যাচ /user/foobar, ম্যাচ হবে না /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// ম্যাচ করে /user /user/123 এবং /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'টম');
});

// সব অপশন রিকোয়েস্টের ম্যাচ করুন
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## রুটিং গ্রুপ
কখনওকিছু সময়ে রুটিং বড় সংখ্যক একই প্রিফিক্স অন্তর্ভুক্ত হয়ে থাকে, তাদের কেন্দ্রীয়ে রুটিং গ্রুপ ব্যবহার করে রুটিং সংজ্ঞায়ন করতে পারি। উদাহরণ:
```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
সমতুলিত
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

গ্রুপ নেস্টিং ব্যবহার
```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
});
```

## রুটিং মিডলওয়্যার
আমরা কোনো একটি বা একটি গ্রুপ রুটিংর জন্য মিডলওয়্যার সেট করতে পারি।
উদাহরণ:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

> **দ্রষ্টব্য**:
> ওয়েবম্যন-ফ্রেমওয়ার্ক <= 1.5.6 হলে `->middleware()` রুটিং গ্রুপ মিডলওয়্যারগুলির জন্য কাজ করে তখন নতুন গ্রুপটি ওয়াইজ থাকা প্রয়োজন
```php
# ভুল ব্যবহার উদাহরণ (১.5.7 এর উপরে ওয়েবম্যান-ফ্রেমওয়ার্কে এই ব্যবহার দীর্ঘ যোগ্য)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# সঠিক ব্যবহার
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```

## রিসোর্স রুটিং
```php
Route::resource('/test', app\controller\IndexController::class);

// স্পেসিফিক রিসোর্স রুটিং
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// অন ডিফাইন্ড রিসোর্স রুটিং
// উদাহরণস্বরূপ যদি notify ঠিকানা পরির্বতী এমনভাবে হতে যা যাচ্ছে any টাইপর রুট /test/notify অথবা /test/notify/{id} এই উভয়তথ্যেঁ। routeName দেখায় test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| Verb   | URI                 | Action   | Route Name    |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |




## ইউআরএল জেনারেট
> **দ্রষ্টব্য** 
> এখনকার রুটিং যুগল নেস্টিং সমর্থন করেনা  

উদাহরণ রুটিং:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
আমরা এই রুটের url জেনারেট করার জন্য নিম্নলিখিত পদক্ষেপ ব্য
## রাউট ইন্টারফেস

```php
// যে কোন মেথড রিকুয়েস্টের রাউট সেট করুন
Route::any($uri, $callback);
// GET রিকুয়েস্টের রাউট সেট করুন
Route::get($uri, $callback);
// পোস্ট রিকুয়েস্টের রাউট সেট করুন
Route::post($uri, $callback);
// পুট রিকুয়েস্টের রাউট সেট করুন
Route::put($uri, $callback);
// প্যাচ রিকুয়েস্টের রাউট সেট করুন
Route::patch($uri, $callback);
// ডিলিট রিকুয়েস্টের রাউট সেট করুন
Route::delete($uri, $callback);
// হেড রিকুয়েস্টের রাউট সেট করুন
Route::head($uri, $callback);
// একাধিক মেথডের সাথে একসাথে রাউট সেট করুন
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// গ্রুপ রাউট
Route::group($path, $callback);
// রিসোর্স রাউট
Route::resource($path, $callback, [$options]);
// রাউট অক্ষম করুন
Route::disableDefaultRoute($plugin = '');
// ফলব্যাক রাউট, ডিফল্ট রাউট সেট করুন
Route::fallback($callback, $plugin = '');
```

যদি কোনও রাউটের জন্য uri থাকে না (ডিফল্ট রাউট সহ) এবং ফলব্যাক রাউটও সেট না করা হয়, তাহলে 404 ফিরে আসবে।

## একাধিক রাউট কনফিগারেশন ফাইল
আপনি যদি রাউট নিয়ে ব্যবস্থাপনা করার জন্য একাধিক রাউট কনফিগারেশন ফাইল ব্যবহার করতে চান, উদাহরণস্বরূপ [মাল্টিঅ্যাপ](multiapp.md) এপ্লিকেশন থাকলে প্রতিটি অ্যাপের নিজস্ব রাউট কনফিগারেশন থাকে, তাহলে আপনি বাইরের রাউট কনফিগারেশন ফাইলগুলি লোড করার জন্য `require` ব্যবহার করতে পারেন।
উদাহরণস্বরূপ `config/route.php` ফাইলে
```php
<?php

// এডমিন অ্যাপের রাউট কনফিগারেশন লোড করুন
require_once app_path('admin/config/route.php');
// অ্যাপ অ্যাপের রাউট কনফিগারেশন লোড করুন
require_once app_path('api/config/route.php');

```
