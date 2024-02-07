## வழிமாற்றுப்பு
## இயல்புநிலை வழிமாற்று கோப்பைக்கு
வழிமாற்றின் இயல்புநிலை விதிகள் webman-ஐ அமைக்கும்: `http://127.0.0.1:8787/{நிருபவகம்}/{செயல்பாடு}`.

இயல்புநிலையான நிருபகம் `app\controller\IndexController` ஆகும், இயல்புநிலையான செயல்பாடு `இந்தக் கூட்டுநிர்வாகம்` ஆகும்.

உதாரணமாக, பார்க்க:

- `http://127.0.0.1:8787` இனைப் பார்க்க`app\controller\IndexController` வகையின் `index` செயல்முறை.
- `http://127.0.0.1:8787/foo` கொண்டு பார்க்க`app\controller\FooController` வகையின் `index` செயல்முறை.
- `http://127.0.0.1:8787/foo/test` கொண்டு பார்க்க`app\controller\FooController` வகையின் `test` செயல்முறை.
- `http://127.0.0.1:8787/admin/foo/test` கொண்டு பார்க்க`app\admin\controller\FooController` வகையின் `test` செயல்முறை (பல பயன்பாட்டன்மைகளுக்கு முன் காண்பி).

நிகழ்ச்சியின் மேல் webman 1.4 இல் இன்னும் பெருகுநினைவத்தை ஆதரிக்கின்றது, உதாரணமாக
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

ஒரு கோரிக்கை மாற்றப்பட விரும்பினால், கட்டமைப்புக்கான கோப்பு `config/route.php` ஐ மாற்றுக. 

இயல்புநிலை வழிமாற்றை மூட, `config/route.php` கோப்பின் கடைசி வரியில் கீழ்க்கண்டவாறு அமைக்கவும்:
```php
Route::disableDefaultRoute();
```

## கூட்டுமிக்க வழிமாற்று
`config/route.php` கோப்பில் கீழ்க்கண்ட வழிமாற்று குறிக்கை சேர்க்கவும்:
```php
Route::any('/test', function ($கோரியுள்ளவை) {
    return response('சோதனை');
});

``` 
> **குறிப்பு**
> வையிடப்படும் பெருமானம், ஒரு நிருவினருக்குத் தொடர்புடைய எந்த நிர்வாகாசிரியமும் அல்லது செய்யும் செயல்பாட்டினின்றும் பிரிவுகள் அல்லாததுமாகும் `$கோரியுள்ளவை->app` `$கோரியுள்ளவை->controller` `$கோரியுள்ளவை->action` அனைத்தையும் கொண்டிருக்காது.

`http://127.0.0.1:8787/test` அன்று, `சோதனை` சரமாக பின்கிறப்பிரதி செய்யப்படும்.

> **குறிப்பு**
> வழிமாற்ற பாதைக்குத் தவிர மூச்சுவடங்கள் `/` மூலத்துக்கு தொடங்க வேண்டும், உதாரணமாக

```php
// தவிர்க்கப்பட்ட முறை
Route::any('test', function ($கோரியுள்ளவை) {
    return response('சோதனை');
});

// சரியான முறை
Route::any('/test', function ($கோரியுள்ளவை) {
    return response('சோதனை');
});
```
## கிளாஸ் வழி
`config/route.php`இல் கீழே குறுகிய வழியைச் சேர்க்கவும்
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
`http://127.0.0.1:8787/testclass` ஐப் பார்க்கும்போது, `app\controller\IndexController` வர்க்கப்பட்ட கிளாஸின் `test` முறையைத் திருப்பும்.


## வழிபாட்டு அளவுகள்
வழியில் மாற்றங்கள் உள்ளால், `{விருப்பம்}` உபயோகிக்கப்படும், பொருந்தும் முடிவுகள் அவ்வப்பெட்டியில் பகுப்பாய்வுக்கு அனுப்பப்படும் (இரண்டாவது ஆர்க்கமுகம் முதல் மூன்றாவது ஆர்க்கமுகம் தொடங்கி ஒன்றும் பரிமாற்றப்படும்)அத்துடன், உதாரணமாக:
```php
// /user/123 /user/abc ஐ பொருந்தும்
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
இக்குறிப்பில்;
class UserController
{
    public function get($request, $id)
    {
        return response('அளவு' . $id . ' ஐ பெற்றுள்ளது');
    }
}
```

மேலும் உதாரணங்கள்:
```php
// 123 /user/123 ஐ பொருத்தும், /user/abc ஐ பொருத்தாமல் இருக்காது
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// foobar /user/foobar ஐ பொருத்தும், /user/foo/bar ஐ பொருத்தாமல் இருக்காது
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// /user /user/123 மற்றும் /user/abc ஐ பொருத்தும்
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'தம்');
});

// அனைத்து options கேட்டங்களுமும் பொருத்தப்படும்
Route::options('[{path:.+}]', function () {
    return response('');
});
```
வேலைநாட்கள் பலப்பட்டுள்ள அல்லதுப் பொதுவான முனைவுகளை ஒப்படைக்கும்போது, நாங்கள் முறைவாக முறைவேற்கப்படுத்த முடியும் என்பது பெற வேண்டும். உதாரணத்தால்:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
மாற்றுப்பட்டது
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

குரூப் சேர்த்தல்

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
});
```
மேடையர் ஒன்றையோ மேடையர் குழுவையோ ஏற்றுக்கொள்ள முடியும். உதாரணமாகவே:
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

> குறிக்கோள்:
> webman-framework <= 1.5.6 இல் `->middleware()` குறித்து இணையம் மேடைகள் groupக்கு அநுப்பபபட்டுள்ளது என்பது என்றாலும், 
> தற்போது webman-framework >= 1.5.7 இல் இந்த பயன்பாடு செல்லுபடியாகும்.

```php
# பிழை மாதிரி உபயோகம் (webman-framework >= 1.5.7 இல் இந்த பயன்பாடு செல்லும்)
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
# வழங்கும் உரையாடல்
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
மூலப் பாதையான வழிமுறை

```php
Route::resource('/test', app\controller\IndexController::class);

//மேலதிக வழிமுறை ஒட்டு
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

//வருகாமை வழிமுறை
// என்னும் வழிமுறைக்கான "/test/notify" என்பது "any" வழிமுறை /test/notify அல்லது /test/notify/{id} அதிகாரப் பெயர் test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| வருகை | URI | செயல்முறை | வழிப்பெயர் |
|-----|---------------------|----------|------------------|
| GET | /test | index | test.index |
| GET | /test/create | create | test.create |
| POST | /test | store | test.store |
| GET | /test/{id} | show | test.show |
| GET | /test/{id}/edit | edit | test.edit |
| PUT | /test/{id} | update | test.update |
| DELETE | /test/{id} | destroy | test.destroy |
| PUT | /test/{id}/recovery | recovery | test.recovery |


## தள URL உருவாக்கம்
> **குறிப்பு**
> தற்பாலும் சுவரிப்பக்க உருவாக்கத்திற்கு ஏதாவது உடனடியாக route உருவாக்க கொண்டேன் ஐவர் அளிக்கலாம் அதை உணரமுடியும்

உதாரணமாக ரூட்டு:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
நாம் மேலும் உரையாட தொடர்புடைய மற்றும் செல்ல முடியும் முறைகளுடன் இந்த மூலப் பாதையின் URL ஐ உருவாக்குவதற்கு பயன்படுத்தலாம்.
```php
route('blog.view', ['id' => 100]); //முடிவு /blog/100 ஆகும்
```

விளைத்திரவில் இந்த முறை பயன்பாடு செய்யும் போது, வழிமுறையின் விதிகள் எவ்வாறு மாறும் அவ்வாறு, URL தானாக உருவாக்கப்படும் என முடிவு பெற சிலுவையில் உள்ள பாத்திரங்களைக் கட்டுப்படுத்துகின்றன.
## ரூட் தகவலைப் பெறுக
> **குறிப்பு**
> webman-framework >= 1.3.2 பேரப்புவைக்கப்படுகிறது

மூலம்`$request->route` மாறியைப் பயன்படுத்தி நடவடிக்கை ரூட்டின் தகவல்களைப் பெற முடியும். உதாரணமாக,

```php
$route = $request->route; // ஒரு ஒருங்கினை $route = request()->route;
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // இந்த செயல்பாட்டு webman-framework >= 1.3.16 தேவை
}
```

> **குறிப்பு**
> தற்போது ரூட் config/route.php படிப்பில் அமைக்கப்பட்ட யாவும் ஒரு ஒரு வழி கிடையாது என்பதும் உள்ளது, ஒரு உருவாக்கப்பட்ட வழி முனை `$request->route`என்பதற்கு null என்று குறிக்கப்படும்


## 404 காணப்படாதைகளை செயலாக்குக
ரூட் காணப்படாதைகளைப் பெற்றபோது, இயல்புநிலையாக 404 நிலைகொண்டிருக்கிறது மற்றும் `public/404.html` கோப்பின் உள்ளடக்கத்தை வெளியிடுகிறது.

தட்டச்சு ரூடு`Route::fallback($callback)` வழியாக அழுத்தப்படலாம். உதாரணமாக, ரூட் காணப்படாதையிலும் மீள்பாட்டிற்கு முன் முடிவேற்றப்படுகின்றதுஅதிக பொருள்.
```php
Route::fallback(function(){
    return redirect('/');
});
```
மேலும், ரூட்டு எதுவும் இல்லையானால் ஒரு json தரவைப் பின்பற்றுவது மிகவும் பயனுள்ளது.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

தொடர்பான இணைப்புகள் [தனிப்பட்ட 404 500 பக்கங்கள்](others/custom-error-page.md)
## வழிமுறை இடைவேள்
```php
// எடுத்துக்குள் இருப்பு புதுப்பிப்பு கொண்டு வழிமுறை அமைக்குக
Route::any($uri, $callback);
// எடுத்துக்குள் GET கோரிக்கையாக வழிமுறை அமைக்குக
Route::get($uri, $callback);
// எடுத்துக்குள் தகவல் அனுப்பு உரைப்படுத்தல் கோரிக்கையாக வழிமுறை அமைக்குக
Route::post($uri, $callback);
// எடுத்துக்குள் தகவல் மாற்று கோரிக்கையாக வழிமுறை அமைக்குக
Route::put($uri, $callback);
// எடுத்துக்குள் தகவல் மாற்ற் மாற்று கோரிக்கையாக வழிமுறை அமைக்குக
Route::patch($uri, $callback);
// எடுத்துக்குள் தகவல் நீக்கு கோரிக்கையாக வழிமுறை அமைக்குக
Route::delete($uri, $callback);
// எடு
