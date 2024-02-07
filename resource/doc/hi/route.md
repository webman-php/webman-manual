## रूटिंग
## डिफ़ॉल्ट रूटिंग नियम
webman का डिफ़ॉल्ट रूटिंग नियम है `http://127.0.0.1:8787/{कंट्रोलर}/{क्रिया}`। 

डिफ़ॉल्ट कंट्रोलर `app\controller\IndexController` है, और डिफ़ॉल्ट क्रिया `index` है।

उदाहरण के लिए:
-  `http://127.0.0.1:8787` डिफ़ॉल्ट रूप से `app\controller\IndexController` की `index` मेथड पर जाएगा।
-  `http://127.0.0.1:8787/foo` `app\controller\FooController` की `index` मेथड पर जाएगा
-  `http://127.0.0.1:8787/foo/test` `app\controller\FooController` की `test` मेथड पर जाएगा
-  `http://127.0.0.1:8787/admin/foo/test` `app\admin\controller\FooController` की `test` मेथड पर जाएगा (रेफर [मल्टी एप](multiapp.md))

इसके अलावा, webman 1.4 से डिफ़ॉल्ट रूटिंग का समर्थन करता है, जैसे
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

जब आप किसी विशेष अनुरोध की रूटिंग फ़ॉर्म को बदलना चाहते हैं, तो कॉन्फ़िगरेशन फ़ाइल `config/route.php` को बदलें।

अगर आप डिफ़ॉल्ट रूटिंग को बंद करना चाहते हैं, तो कॉन्फ़िगरेशन फ़ाइल `config/route.php` में निम्न लाइन जोड़ें:
```php
Route::disableDefaultRoute();
```

## क्लोजर रूटिंग
`config/route.php` में निम्न रूटिंग कोड जोड़ें
```php
Route::any('/test', function ($request) {
    return response('test');
});

``` 
>  **ध्यान दें**  
> क्लोजर फ़ंक्शन क्योंकि किसी भी कंट्रोलर का हिस्सा नहीं होता है, इसलिए `$request->app` `$request->controller` `$request->action` सभी स्ट्रिंग में रहते हैं।

जब पता `http://127.0.0.1:8787/test` जाता है, `test` स्ट्रिंग वापस लौटाता है।

>  **ध्यान दें**  
> रूट पथ को स्लैश (`/`) से शुरू करना आवश्यक है, उदाहरण के लिए

```php
// गलत तरीका
Route::any('test', function ($request) {
    return response('test');
});

// सही तरीका
Route::any('/test', function ($request) {
    return response('test');
});
```


## कक्षा रूटिंग
`config/route.php` में निम्न रूटिंग कोड जोड़ें
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
``` 
पता `http://127.0.0.1:8787/testclass` जाते वक़्त, `app\controller\IndexController` की `test` मेथड का उत्तर मिलेगा।


## रूटिंग पैरामीटर
अगर रूटिंग में पैरामीटर हैं, तो `{key}` का उपयोग करके मैच किया जाएगा, मैचिंग परिणाम को संबंधित कंट्रोलर मेथड पैरामीटर में पास किया जाएगा(दूसरे पैरामीटर से शुरू होकर)। उदाहरण:
```php
// मैच करेगा /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
``` 
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('पैरामीटर '.$id.' प्राप्त किया गया है');
    }
}
```

अधिक उदाहरण:
```php
// मैच करेगा /user/123, /user/abc मैच नहीं करेगा
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// मैच करेगा /user/foobar, /user/foo/bar मैच नहीं करेगा
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// मैच करेगा /user, /user/123 और /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// सभी options अनुरोध को मैच करेगा
Route::options('[{path:.+}]', function () {
    return response('');
});
```


## रूटिंग समूह
कभी-कभी रूटिंग में बहुत सारे समान प्रिफ़िक्स होते हैं, इससे हमें रूटिंग की परिभाषा सरल करने के लिए रूटिंग समूह का इस्तेमाल कर सकते हैं। उदाहरण के लिए:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
इसका मतलब है

```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```


समूह नेस्टिंग का उपयोग करना
```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
});
```


## रूटिंग मिडलवेयर
हम किसी एक या किसी ग्रुप रूटिंग को मिडलवेयर सेट कर सकते हैं।
उदाहरण के लिए:
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

> **ध्यान दें**: 
> webman-framework <= 1.5.6  के बाद `->middleware()` रूट मिडलवेयर ग्रुप समूह के बाद कार्य करता है, तो वर्तमान रूट को वर्तमान समूह के अंदर प्राप्त होना आवश्यक है।

```php
# गलत उपयोग (webman-framework >= 1.5.7 में यह उपयोग वैध होता है)
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
# सही उपयोग
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


## संसाधन प्रकार रूटिंग
```php
Route::resource('/test', app\controller\IndexController::class);

// संसाधन रूटिंग का निर्दिष्टन
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// निर्धारित नस्तस्य संसाधन रूटिंग
// जैसे notify का पता /test/notify या /test/notify/{id} हो सकता है या routeName का test.notify
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
> ग्रुप नेस्टिंग की रूटिंग उत्पन्न करने का अभी समर्थन नहीं है  

उदाहरण के लिए रूटिंग:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
``` 
हम इस रूटिंग के लिए निम्न तरीके से url उत्पन्न कर सकते हैं।
```php
route('blog.view', ['id' => 100]); // परिणाम /blog/100 होगा
``` 

रूटिंग का url उपयोग करते समय इस तरह का उपयोग कर सकते हैं, इससे वेब रूट के किनारे के बदलाव से यह स्वत: उत्पन्न हो जाएगा, जिससे स्थिति फ़ाइलों को बदलने के कारण बड़ी संख्या में दृश्य फ़ाइलों का बदलाव रिक्त हो सकता है।


## पथ प्राप्त करें
> **नोट:**
> webman-framework >= 1.3.2 आवश्यक है

`$request->route` ऑब्जेक्ट के माध्यम से हम वर्तमान अनुरोध के रूट की जानकारी प्राप्त कर सकते हैं, जैसे

```php
$route = $request->route; // $route = request()->route; के समान
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // यह सुविधा webman-framework >= 1.3.16 की आवश्यकता है
}
```

> **नोट:**
> यदि वर्तमान अनुरोध config/route.php में किसी भी रूट से मेल नहीं खाता है, तो `$request->route` null होगा, अर्थात डिफ़ॉल्ट रूट को चलाने पर `$request->route` null होगा।

## 404 संसाधित करना
जब रूट नहीं मिलता है तो डिफ़ॉल्ट रूट `public/404.html` फ़ाइल की सामग्री डाल कर 404 स्थिति को लौटाया जाता है।

यदि डेवलपर को रूट नहीं मिलने पर रूट प्राप्त नहीं होते वाली व्यवस्था में हस्तक्षेप करना चाहते हैं, तो वेबमैन द्वारा प्रदान की गई फिर रूट `Route::fallback($callback)` विधि का उपयोग किया जा सकता है। उदाहरण के तौर पर नीचे की गई कोड तर्क है जब रूट नहीं मिलता है तो होमपेज पर रीडायरेक्ट करें।

```php
Route::fallback(function(){
    return redirect('/');
});
```
एक और उदाहरण के रूप में, रूट नहीं मिलने पर एक json डेटा लौटाना, जो वेबमैन को API इंटरफ़ेस के रूप में बहुत उपयोगी होता है।

```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

संबंधित लिंक [कस्टम 404 500 पेज](others/custom-error-page.md)

## रूट इंटरफ़ेस
```php
// $uri के किसी भी मेथड अनुरोध के रूट सेट करें
Route::any($uri, $callback);
// $uri के get अनुरोध के रूट सेट करें
Route::get($uri, $callback);
// $uri के किसी भी अनुरोध के रूट सेट करें
Route::post($uri, $callback);
// $uri के put अनुरोध के रूट सेट करें
Route::put($uri, $callback);
// $uri के पैच अनुरोध के रूट सेट करें
Route::patch($uri, $callback);
// $uri के डिलीट अनुरोध के रूट सेट करें
Route::delete($uri, $callback);
// $uri के हेड अनुरोध के रूट सेट करें
Route::head($uri, $callback);
// एक साथ कई प्रकार के अनुरोध प्रकार के रूट सेट करें
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// समूह रूट
Route::group($path, $callback);
// संसाधित रूट
Route::resource($path, $callback, [$options]);
// रूट अक्षम करें
Route::disableDefaultRoute($plugin = '');
// फिर रूट, डिफ़ॉल्ट रूट को पूरा करने के लिए
Route::fallback($callback, $plugin = '');
```
यदि uri के कोई भी रूट (डिफ़ॉल्ट रूट सहित) नहीं है, और फिर रूट भी सेट नहीं है, तो 404 लौटाया जाएगा।

## विभिन्न रूट कॉन्फ़िग फ़ाइल
यदि आप रूट को प्रबंधित करने के लिए विभिन्न रूट कॉन्फ़िग फ़ाइल का उपयोग करना चाहते हैं, उदाहरण के लिए [बहु-अनुप्रयोग](multiapp.md) में हर अनुप्रयोग के नीचे अपनी रूट कॉन्फ़िग फ़ाइल होती है, इससे आप बाहरी रूट कॉन्फ़िग फ़ाइल को `require` करने के रूप में उपयोग कर सकते हैं।
उदाहरण के लिए `config/route.php` में
```php
<?php

// ऐडमिन अनुप्रयोग के रूट कॉन्फ़िग लोड करें
require_once app_path('admin/config/route.php');
// एपीआई अनुप्रयोग के रूट कॉन्फ़िग लोड करें
require_once app_path('api/config/route.php');

```
