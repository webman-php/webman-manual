## रूटिंग
## डिफ़ॉल्ट रूटिंग नियम
webman का डिफ़ॉल्ट रूटिंग नियम है `http://127.0.0.1:8787/{कंट्रोलर}/{क्रिया}`।

डिफ़ॉल्ट कंट्रोलर `app\controller\IndexController` है, डिफ़ॉल्ट क्रिया `index` है।

उदाहरण के लिए, यदि आप इस तरह पहुंचते हैं:
- `http://127.0.0.1:8787` तो डिफ़ॉल्ट रूप में `app\controller\IndexController` की `index` मेथड तक पहुंचा जाएगा
- `http://127.0.0.1:8787/foo` तो डिफ़ॉल्ट रूप में `app\controller\FooController` की `index` मेथड तक पहुंचा जाएगा
- `http://127.0.0.1:8787/foo/test` तो डिफ़ॉल्ट रूप में `app\controller\FooController` की `test` मेथड तक पहुंचा जाएगा
- `http://127.0.0.1:8787/admin/foo/test` तो डिफ़ॉल्ट रूप में `app\admin\controller\FooController` की `test` मेथड तक पहुंचा जाएगा (रेफर [मल्टी एप्लिकेशन](multiapp.md))

इसके अलावा, webman 1.4 से डिफ़ॉल्ट रूटिंग को और जटिल समर्थन शुरू हुआ है, जैसे
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
जब आप किसी अनुरोध की रूटिंग बदलना चाहते हैं तो कृपया कॉन्फ़िगरेशन फ़ाइल `config/route.php` को बदलें।

अगर आप डिफ़ॉल्ट रूटिंग को बंद करना चाहते हैं, तो कृपया कॉन्फ़िगरेशन फ़ाइल `config/route.php` के अंत में निम्न कॉन्फ़िगरेशन जोड़ें:
```php
Route::disableDefaultRoute();
```

## बंद चालू रूटिंग
`config/route.php` में निम्न रूटिंग कोड जोड़ें
```php
Route::any('/test', function ($request) {
    return response('test');
});

```
> **ध्यान दें**
> क्योंकि बंद फ़ंक्शन किसी भी कंट्रोलर का हिस्सा नहीं होता है, इसलिए `$request->app` `$request->controller` `$request->action` सभी खाली स्ट्रिंग होती हैं।

जब पता `http://127.0.0.1:8787/test` की ओर होता है, तो `test` स्ट्रिंग वापस लौटता है।

> **ध्यान दें**
> रूटिंग पथ को `/` से शुरू होना चाहिए, उदाहरण के लिए

```php
// गलत तरीके से उपयोग करना
Route::any('test', function ($request) {
    return response('test');
});

// सही तरीके से उपयोग करना
Route::any('/test', function ($request) {
    return response('test');
});
```


## कक्षा रूटिंग
`config/route.php` में निम्न रूटिंग कोड जोड़ें
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
जब पता `http://127.0.0.1:8787/testclass` की ओर होता है, तो `app\controller\IndexController` की `test` मेथड का परिणाम वापस लौटता है।


## रूटिंग पैरामीटर
यदि रूटिंग मध्ये पैरामीटर हैं, तो `{कुंजी}` के माध्यम से मिलाया जाता है, परिणाम को संबंधित कंट्रोलर मेथड पैरामीटर में भेजा जाता है (दूसरे पैरामीटर से शुरू करके)। उदाहरण:
```php
// मिलाने /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('पैरामीटर मिला'.$id);
    }
}
```

अधिक उदाहरण:
```php
// मेल /user/123, मेल /user/abc, मेल /user
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'राहुल');
});

// सभी विकल्प अनुरोध मेल मिलाना
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## रूटिंग समूह
कभी-कभी रूटिंग में बड़े संख्या में समान उपसर्ग होते हैं, इस समय हम रूटिंग समूह का उपयोग करके परिभाषण को सरल बना सकते हैं। उदाहरण के लिए:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("नजर $id");});
});
```
इसका मान है
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("नजर $id");});
```

समूह नेस्ट करना
```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("नजर $id");});
   });  
});
```

## रूटिंग मध्यपथवार
हम किसी व्यक्तिगत और समूह रूटिंग के लिए मध्यपथवार सेट कर सकते हैं। उदाहरण:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {response("नजर $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

> **ध्यान दें**: 
> webman-framework <= 1.5.6 पर `->middleware()` रूटिंग मध्यपथवार ग्रुप के बाद कार्य करता है, तो वर्तमान रूट निश्चित क्रिया करने की आवश्यकता होती है।

```php
# गलत उपयोग उदाहरण (webman-framework >= 1.5.7 पर यह प्रयोग्य है)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("नजर $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# सही उपयोग उदाहरण
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("नजर $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```

## संसाधन रूटिंग
```php
Route::resource('/test', app\controller\IndexController::class);

//निर्दिष्ट संसाधन रूटिंग
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

//निर्दिष्ट रूटिंग अशावस्थ संसाधन
// उदाहरण के लिए notify अनुरोध का पता /test/notify या /test/notify/{id} dono routeName test.notify ke use ke liye route kiya jaa sakta hai।.
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




## url उत्पन्न
> **ध्यान दें** 
> अभी तक बहुपद जोड़ी हुई रूटिंग का url उत्पन्न नहीं किया गया है

उदाहरण रूटिंग:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
हम इस रूटिंग के url को निम्नलिखित तरीके से उत्पन्न कर सकते हैं।
```php
route('blog.view', ['id' => 100]); // परिणाम होगा /blog/100
```

रूटिंग के url पर व्यू का उपयोग करते समय इस विधि का उपयोग किया जा सकता है, जिससे कि रूटिंग विधान के बदलने के कारण, url स्वचालित रूप से उत्पन्न हो जाए, जिससे बड़ी संख्या में व्यू फ़ाइलों को बदलने से बचा जा सकता है।


## रूटिंग जानकारी प्राप्त करें
> **ध्यान दें**
> webman-framework >= 1.3.2 की आवश्यकता होती है

`$request->route` ऑब्जेक्ट के माध्यम से हम वर्तमान अनुरोध रूटिंग जानकारी प्राप्त कर सकते हैं, उदाहरण के लिए

```php
$route = $request->route; // यह समान है $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName
## राउटिंग इंटरफ़ेस

```php
// किसी भी मेथड के लिए $uri को रूट करें
Route::any($uri, $callback);
// $uri के लिए get अनुरोध को रूट करें
Route::get($uri, $callback);
// $uri के लिए पोस्ट अनुरोध को रूट करें
Route::post($uri, $callback);
// $uri के लिए पुट अनुरोध को रूट करें
Route::put($uri, $callback);
// $uri के लिए पैच अनुरोध को रूट करें
Route::patch($uri, $callback);
// $uri के लिए डिलीट अनुरोध को रूट करें
Route::delete($uri, $callback);
// $uri के लिए हेड अनुरोध को रूट करें
Route::head($uri, $callback);
// कई विभिन्न प्रकार के अनुरोधों के लिए एक साथ रूट करें
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// समूह रूटिंग
Route::group($path, $callback);
// संसाधन रूटिंग
Route::resource($path, $callback, [$options]);
// रूट सक्षम करें
Route::disableDefaultRoute($plugin = '');
// फॉलबैक रूट, डिफ़ॉल्ट रूट सेट करें
Route::fallback($callback, $plugin = '');
```

अगर uri के लिए कोई रूट (डिफ़ॉल्ट रूट सम्मिलित है) नहीं है, और फिर फॉलबैक रूट भी सेट नहीं है, तो 404 वापस भेजा जाएगा।

## कई रूट कॉन्फ़िग फ़ाइल

यदि आप रूट को प्रबंधित करने के लिए कई रूट कॉन्फ़िग फ़ाइल का उपयोग करना चाहते हैं, जैसे [मल्टी एप](multiapp.md) के लिए, जहां प्रत्येक एप्लिकेशन के नीचे अपनी रूट कॉन्फ़िगरेशन होती है, तो इसका उपयोग बाहरी रूट कॉन्फ़िग फाइल को 'रिक्वायर' करके किया जा सकता है।
उदाहरण के लिए `config/route.php` में

```php
<?php

// एडमिन ऐप की रूट कॉन्फ़िग को लोड करें
require_once app_path('admin/config/route.php');
// एपीआई ऐप की रूट कॉन्फ़िग को लोड करें
require_once app_path('api/config/route.php');
```
