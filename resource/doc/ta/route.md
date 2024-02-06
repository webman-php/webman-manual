# ரூட்டிங்கு

## இயல்பான ரூட் விதி

webman-ன் இயல்பான ரூட் விதி `http://127.0.0.1:8787/{கட்டுப்பாட்டியல்}/{செயல்}` ஆகும்.

இயல்பான கட்டுப்பாட்டியல் `app\controller\IndexController` ஆகும், முன்புற செயல் `index` ஆகும்.

உதாரணமாக:
- `http://127.0.0.1:8787` உடன் இயல்பான முன்புற செயல் `app\controller\IndexController` வகையின் `index` செயலை இயல்பானமாக அணுப்புகிறது
- `http://127.0.0.1:8787/foo` உடன் இயல்பான முன்புற செயல் `app\controller\FooController` வகையின் `index` செயலை இயல்பானமாக அணுப்புகிறது
- `http://127.0.0.1:8787/foo/test` உடன் இயல்பான முன்புற செயல் `app\controller\FooController` வகையின் `test` செயலை இயல்பானமாக அணுப்புகிறது
- `http://127.0.0.1:8787/admin/foo/test` உடன் இயல்பான முன்புற செயல் `app\admin\controller\FooController` வகையின் `test` செயலை இயல்பானமாக அணுப்புகிறது (காண்பிக்க [பல விண்டோ](multiapp.md))

1.4 மூலம் வெளிஆகும் இனித இயல்பான ரூட் விதி ஆனது, உதாரணமாக
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

பெறுவீர்கள் உங்கள் விண்டோ வழிவகுக்க வின்முகமாக கொள்ளலாம், `config/route.php` கோப்பை மாற்ற வேண்டும்.

இயல்புநிலை ரூட்-ஐ மும்படி மூலம் நிந்தநர் பதிவாக்க விரும்பாதீர்கள் `config/route.php` கோப்பிற்கு கீழேயுள்ள உள்ளி மூலமையாக இணையவும்:
```php
Route::disableDefaultRoute();
```

## நிரப்தவதை ரூட்
`config/route.php` கோப்பில் கீழேயுள்ள ரூட் குறிக்கோள் சேர்க்கவும்
```php
Route::any('/test', function ($request) {
    return response('test');
});

```
> **குறிக்கோள்**
> கூடவே எந்த ஒரு கட்டுப்பாட்டியும்-க்குள் உள்நோக், ஆனால் `$request->app` `$request->controller` `$request->action` முழுமையாக கலப்பட எதுவுமில்லை.

`http://127.0.0.1:8787/test` பின்னர், `test` கொஞ்சம் பின்னரில் திருப்பும்.

> **குறிக்கோள்**
> ரூட் பாதை உள்ளி உடன்பாடு, `/` உள்ளி ஆரம்பிப்பது வேலைவந்தது
```php
// தவறான பயன்பாடு
Route::any('test', function ($request) {
    return response('test');
});

// சரியான பயன்பாடு
Route::any('/test', function ($request) {
    return response('test');
});
```


## வரைபு வாழ்ந்து
`config/route.php` கோப்பில் கீழேயுள்ள ரூட் குறிக்கோளை சேர்க்கவும்
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
`http://127.0.0.1:8787/testclass` பின்னர், `app\controller\IndexController` வகையின் `test` செயல் பின்னரில் மட்டெல் வெளித்தலங்கள் திருப்பும்.


## ரூட் பகுதிகள்
கூடும், ரூட் இலக்குகள் உள்ளியல், `{வினை}` மூலம் பொருயும், பொருய முடிவுகள் கூடுதல் முடியாக,  உதாரணமாக:
```php
// பொருய்யும் /user/123 /user/abc
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
பெயர்வு app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('பரிமள் பெறும் தொகை'.$id);
    }
}
```

கூடும் என்று உதாரணங்கள்:
```php
// பொருயில் இதை கூடும் /user/123, பொருய்யல் இதை கூடும் /user/abc
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// கூடும் /user/foobar, பொருய்யும் /user/foo/bar
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// கூடும் /user /user/123 இமாம் /user/abc
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// அனைத்து விரிவுகளுக்கு மூலம் முதலாக அனைவருக்கு பொருயும்
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## ரூட் குடுமா

பரிபாலநகூடுதலைக் கொள்கிறீர்கள் ரூட் குடுமாக மற்றும் உதாரணங்களுக்கு:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('உருவம்');});
   Route::any('/edit', function ($request) {return response('செய்ய');});
   Route::any('/view/{id}', function ($request, $id) {return response("$ காண்பி");});
});
```
ஒரேபக்க ஆனது
```php
Route::any('/blog/create', function ($request) {return response('உருவம்');});
Route::any('/blog/edit', function ($request) {return response('செய்ய');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("$ காண்பி");});
```

group அடைவு பயன்படுத்தி
```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('உருவம்');});
      Route::any('/edit', function ($request) {return response('செய்ய');});
      Route::any('/view/{id}', function ($request, $id) {return response("$ காண்பி");});
   });  
});
```

## ரூட் மத்தியாற்றி

நீங்கள் ஒரு அல்லது ஒரு குடுமாக ரூட் மத்தியாற்றி அமைக்க விரும்பும்.
உதாரணமாக:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('உருவம்');});
   Route::any('/edit', function () {return response('செய்ய');});
   Route::any('/view/{id}', function ($request, $id) {response("$ காண்பி");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

> **குறிக்கோள்**
> 1.5.6 இன் போது `->middleware()` ரூட் ஆமாக ரூட் group பிரிவுக்கு பிறகு, தற்போது ரூட் மூலம் கொள்கப்படும் நிலையிலும், 

```php
# தவறானபயன்பாடு (1.5.7 இன் பின் இந்த அவர்த்தம் நேர்கார்விலும் உள்ளியல் கிடைக்கும்)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('உருவம்');});
      Route::any('/edit', function ($request) {return response('செய்ய');});
      Route::any('/view/{id}', function ($request, $id) {return response("$ காண்பி");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# சரியானபயன்பாடு
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('உருவம்');});
      Route::any('/edit', function ($request) {return response('செய்ய');});
      Route::any('/view/{id}', function ($request, $id) {return response("$ காண்பி");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```

## வளங்குணம் வசுஷிடு ரூட்
```php
Route::resource('/test', app\controller\IndexController::class);

//குறிக்கப்பட்ட வளங்குணம் ரூட்
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

//குறியாந்த வளங்குணம் ரூட்
// எப்படி பெற<Vector/notify> ஒன்றும் any வழியில் /test/notifyஅல்லது /test/notify/{id} ரூ
# வழிச்சோதனை இணைப்புகள்

```php
// $uri க்கான எந்த மெதாடு கோரிக்கைகளையும் அமைப்பது
Route::any($uri, $callback);
// $uri க்கான get கோரிக்கையை அமைக்குக
Route::get($uri, $callback);
// $uri க்கான முன்னேற்றை அமைக்குக
Route::post($uri, $callback);
// $uri க்கான put கோரிக்கையை அமைக்குக
Route::put($uri, $callback);
// $uri க்கான patch கோரிக்கையை அமைக்குக
Route::patch($uri, $callback);
// $uri க்கான delete கோரிக்கையை அமைக்குக
Route::delete($uri, $callback);
// $uri க்கான head கோரிக்கையை அமைக்குக
Route::head($uri, $callback);
// பல கோரிக்கை வகைகளுடன் சேர்த்து ஒரு இணைப்பை அமைக்க
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// படி வழிச்சோதனை
Route::group($path, $callback);
// வளங்கள் வழிச்சோதனை
Route::resource($path, $callback, [$options]);
// வழிச்சோதனை முடுக்கு
Route::disableDefaultRoute($plugin = '');
// பின்பற்று வழிச்சோதனை, இயல்புநிலை வழியை அமைக்க
Route::fallback($callback, $plugin = '');
```

குறிப்பிட்ட uri க்கு (இயல்புநிலை வழிச்சோதனை உள்ளிட்டு, 404 பிழை புரட்சி செய்யும்).

## பல போது வழிச்சோதனை உள்ளிட்ட கோப்புகள்
ஒரு பல வழிச்சோதனை உள்ளிடும் போது, உதாரணம் [பல பயன்பாடு](multiapp.md) குழுக்களில் ஒவ்வொரு பயன்பாட்டிற்கும் தன்னுடைய வழிச்சோதனை உள்ளிட்டு, இவ்வாறு வெள外 வழிச்சோதனை கோப்புகளை `பொருத்து` வழக்கம் மூலம் ஏற்றுத் தொடங்கவும்.
உ஦ாகமாக, `config/route.php` பயன்பாடில் இருந்து
```php
<?php

// admin பயன்பாடு க்கான வழிச்சோதனை உள்ளிட்டு வைக்கின்றது
require_once app_path('admin/config/route.php');
// api பயன்பாடு க்கான வழிச்சோதனை உள்ளிட்டு வைக்கின்றது

require_once app_path('api/config/route.php');

```
