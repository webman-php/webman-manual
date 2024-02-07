## রুট
## ডিফল্ট রুটিং নিয়ম
webman-এর ডিফল্ট রুটিং নিয়ম হল `http://127.0.0.1:8787/{কন্ট্রোলার}/{অ্যাকশন}`.

ডিফল্ট কন্ট্রোলার হল `app\controller\IndexController`, ডিফল্ট অ্যাকশন হল `index`.

যেমন:
- `http://127.0.0.1:8787` এর ডিফল্ট আবেদন `app\controller\IndexController` এর `index` মেথডে যাবে।
- `http://127.0.0.1:8787/foo` এর ডিফল্ট আবেদন `app\controller\FooController` এর `index` মেথডে যাবে।
- `http://127.0.0.1:8787/foo/test` এর ডিফল্ট আবেদন `app\controller\FooController` এর `test` মেথডে যাবে।
- `http://127.0.0.1:8787/admin/foo/test` এর ডিফল্ট আবেদন `app\admin\controller\FooController` এর `test` মেথডে যাবে (বিস্তারিত দেখুন [বহুমুখী অ্যাপ](multiapp.md))।

এছাড়া, webman-এ 1.4 থেকে ডিফল্ট রুটিং এর পূর্বের চেয়ে বেশি সমৃদ্ধ পদক্ষেপই সমর্থন করে।
```php
app
├──admin
│   └── v1
│       └── v2
│          └── v3
│               └── কন্ট্রোলার
│                   └── IndexController.php
└── controller
   ├── v1
   │   └── IndexController.php
   └── v2
       └── v3
           └── IndexController.php
```

কোনো রিকুয়েস্ট রুট পরিবর্তন করতে চাইলে `config/route.php` কনফিগারেশন ফাইল পরিবর্তন করুন।

যদি আপনি ডিফল্ট রুটিং বন্ধ করতে চান, `config/route.php` কনফিগারেশন ফাইলে সর্বশেষ সারি যোগ করুন:
```php
Route::disableDefaultRoute();
```

## ক্লোজার রুট
`config/route.php` ফাইলে নিম্নলিখিত রুটিং কোড যোগ করুন
```php
Route::any('/টেস্ট', function ($request) {
    return response('টেস্ট');
});
```
> **লক্ষ্য**
> যেহেতু ক্লোজার ফাংশন কোনো কন্ট্রোলারের অংশ নয়, তাই `$request->app` `$request->controller` `$request->action` সব খালি স্ট্রিং হবে।

যখন ঠিকানা `http://127.0.0.1:8787/টেস্ট` অ্যাক্সেস করা হবে, তখন `টেস্ট` স্ট্রিং পাঠানো হবে।
> **লক্ষ্য**
> রুট পাথ একটি `/` দ্বারা শুরু করা আবশ্যক, উদাহরণস্বরূপ

```php
// ভুল ব্যবহার
Route::any('টেস্ট', function ($request) {
    return response('টেস্ট');
});

// সঠিক ব্যবহার
Route::any('/টেস্ট', function ($request) {
    return response('টেস্ট');
```

## ক্লাস রুট
`config/route.php` ফাইলে নিম্নলিখিত রুটিং কোড যোগ করুন
```php
Route::any('/ক্লাসের-টেস্ট', [app\controller\IndexController::class, 'test']);
```
যখন ঠিকানা `http://127.0.0.1:8787/ক্লাসের-টেস্ট` অ্যাক্সেস করা হবে, তখন `app\controller\IndexController` ক্লাসের `test` মেথডের ফলাফল পাঠানো হবে।

## রুট প্যারামিটার
যদি রুটে প্যারামিটার থাকে, তবে `{key}` দিয়ে ম্যাচ করা হবে, ম্যাচিং ফলাফলটি সেরায় পরিমানুশে কন্ট্রোলারের মেথডের প্যারামিটারে পাঠানো হবে (দ্বিতীয় প্যারামিটার থেকে শুরুতেতে পাঠানো হবে), উদাহরণ:
```php
// ম্যাচ /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('প্যারামিটার' .$id. ' পেয়েছেন');
    }
}
```

অনেক অন্যান্য উদাহরণ:
```php
// ম্যাচ /user/123, ম্যাচ না /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// ম্যাচ /user/foobar, ম্যাচ না /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// ম্যাচ /user /user/123 এবং /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'টম');
});

// সমস্ত অপশনের জন্য ম্যাচ করুন
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## রুট গ্রুপ
সময়ের সাথে গুরুত্বপূর্ণ জটিল প্রিফিক্স সম্পূর্ণ করা রুটের পরিবর্তে, রুট গ্রুপ ব্যবহারে আমরা রুটিং সাজাতে পারি। উদাহরণ:
```php
Route::group('/ব্লগ', function () {
   Route::any('/create', function ($request) {return response('তৈরি করুন');});
   Route::any('/edit', function ($request) {return response('সম্পাদনা করুন');});
   Route::any('/view/{id}', function ($request, $id) {return response("$id দেখুন");});
});
```
এটি সমান:
```php
Route::any('/ব্লগ/তৈরি', function ($request) {return response('তৈরি করুন');});
Route::any('/ব্লগ/সম্পাদনা', function ($request) {return response('সম্পাদনা করুন');});
Route::any('/ব্লগ/দেখুন/{id}', function ($request, $id) {return response("$id দেখুন");});
```

গ্রুপের ভিতরে গ্রুপ নেস্ট
```php
Route::group('/ব্লগ', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('তৈরি করুন');});
      Route::any('/edit', function ($request) {return response('সম্পাদনা করুন');});
      Route::any('/view/{id}', function ($request, $id) {return response("$id দেখুন");});
   });  
});
```
## রুট মিডলওয়্যার

আমরা একটি রুট বা রুটের একটি গ্রুপে মিডলওয়্যার সেট করতে পারি।
যেমন:
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

> **দ্য নোট**: 
> একের বেশি ১.5.6 পর্যন্ত webman-framework <= এ `->middleware()` রুট মিডলওয়্যার গ্রুপের পরে ব্যবহার করতে, বর্তমান রুট অবশ্যই বর্তমান গ্রুপের অধীনে থাকা আবশ্যক।

```php
# ভুল ব্যবহার উদাহরণ (webman-framework >= 1.5.7 এই ব্যবহার প্রয়োজনীয়)
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
# সঠিক ব্যবহার উদাহরণ
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

## সম্পদ রুট

```php
Route::resource('/test', app\controller\IndexController::class);

// সংশোধিত সরঞ্জাম রুট
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// অনির্দিষ্ট সম্পদ রুট
// উদাহরণস্বরূপ নোটিফাই অ্যাক্সেস পথের  যোকারী কোন  প্যাথে /test/notify অথবা /test/notify/{id} হয় তবে রুটনেম হবে  টেস্ট নোটিফাই
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| Verb   | URI                 | ক্রিয়া   | রুট নেম    |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |



## URL উৎপন্ন করা
> **দ্য নোট** 
> গ্রুপ গোডাউনেসেটেড রুটস জেনারেট করার জন্য প্রতারিত 

উদাহরণ রুট:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
আমরা এই রুটের URL তৈরি করতে নিম্নলিখিত পদক্ষেপগুলি অনুসরণ করতে পারি।
```php
route('blog.view', ['id' => 100]); // ফলাফল /blog/100
```

রুটের URL ব্যবহারের সময় ভিউ ফাইল ইতিমধ্যে পরিমাণ পরিবর্তনের মূল্যায়নের পূর্ণাঙ্গ করতে, এই পদক্ষেপটি ব্যবহার করা উচিত।


## রুট তথ্য পূর্ণ করা
> **দ্য নোট**
> webman-framework >= ১.৩.২ প্রয়োজন 

`$request->route` অবজেক্ট ব্যবহার করে আমরা বর্তমান অনুরোধ রুটের তথ্য অনুবাদ করতে পারি, উদাহরণস্বরূপ

```php
$route = $request->route; // বাংগা সমান $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); //এই বৈশিষ্ট্য webman-framework >= 1.3.16 প্রয়োজন
}
```

> **দ্য নোট**
> যদি বর্তমান অনুরোধটি config / route.php-তে প্রস্তুত কোনও রুটের সাথে মেলে না, তাহলে `$request->route` null হবে, অর্থাত্ বৈশিষ্ট্য অব্যাহত রাউটিং সময় ক্রোতা null


## ৪০৪ নয়
রুট পাওয়া না গেলে ডিফল্ট 404 স্ট্যাটাস কোড দেওয়া হবে এবং `public/404.html` ফাইলের মধ্যে প্রদর্শিত হবে।

ডেভেলপাররা যদি রুট পাওয়া না গেলের ব্যবসায়িক প্রসঙ্গে আসাতে চান, তাহলে webman সরবরাহ করা ফলব্যাপ্ত রুট ফল্ব্যাক রুট `Route::fallback($callback)` মেথড ব্যবহার করতে পারে। উদাহরণ হচ্ছে নিম্নের কোডটি যখন রুট পাওয়া যায়নি তাহলে হোমপেজে পুনর্নির্দেশ করা হবে ।
```php
Route::fallback(function(){
    return redirect('/');
});
```
আর এমনকি যখন রুট পাওয়া যায়নি তাহলে একটি জেসন ডাটা রিটার্ন করা হবে, এটি webman যখন API ইন্টারফেস হিসাবে ব্যবহৃত হয় তখন এটি খুব উপকারী।
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

সংশ্লিষ্ট লিংক [কাস্টম 404 500 পেজ](others/custom-error-page.md)
## রাউটিং ইন্টারফেস
```php
// যেকোনো মেথডের অনুরোধের রাউট সেট করুন
Route::any($uri, $callback);
// GET অনুরোধের রাউট সেট করুন
Route::get($uri, $callback);
// POST অনুরোধের রাউট সেট করুন
Route::post($uri, $callback);
// PUT অনুরোধের রাউট সেট করুন
Route::put($uri, $callback);
// PATCH অনুরোধের রাউট সেট করুন
Route::patch($uri, $callback);
// DELETE অনুরোধের রাউট সেট করুন
Route::delete($uri, $callback);
// HEAD অনুরোধের রাউট সেট করুন
Route::head($uri, $callback);
// একই সাথে একাধিক অনুরোধের রাউট সেট করুন
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// গ্রুপ রাউট
Route::group($path, $callback);
// সম্পদ রাউট
Route::resource($path, $callback, [$options]);
// রাউট নিষেধাজ্ঞা
Route::disableDefaultRoute($plugin = '');
// ফলব্যাক রাউট, ডিফল্ট রাউট সেট করুন
Route::fallback($callback, $plugin = '');
```
যদি কোনও URI-র জন্য কোনও রাউট না থাকে (ডিফল্ট রাউট সহ), এবং ফলব্যাক রাউটও সেট না করা হয়ে থাকে, তবে 404 রিটার্ন হবে।

## একাধিক রাউট কনফিগারেশন ফাইল
আপনি যদি রাউট পরিচালনা করতে বলতে চান একাধিক রাউট কনফিগারেশন ফাইল ব্যবহার করতে, উদাহরণস্বরূপ [মাল্টিঅ্যাপস](multiapp.md) এপ্লিকেশনে প্রতিটি অ্যাপের নিজস্ব রাউট কনফিগারেশন ফাইল আছে, তাহলে আপনি বাইরের রাউট কনফিগারেশন ফাইলগুলি লোড করার জন্য `require` উপরোক্ত পদ্ধতি ব্যবহার করতে পারেন।
উদাহরণস্বরূপ `config/route.php` এ
```php
<?php

// অ্যাডমিন অ্যাপের রাউট কনফিগারেশন লোড করুন
require_once app_path('admin/config/route.php');
// অ্যাপি অ্যাপের রাউট কনফিগারেশন লোড করুন
require_once app_path('api/config/route.php');

```
