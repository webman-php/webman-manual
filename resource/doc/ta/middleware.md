# வெப்மான் சிறுபட 
வெப்மான் ஒரு workerman ஆதாரமான உயர்த்துக்களமான PHP கட்டமையாகும் சம்பவச் சாதத்களாய்க் கொண்டு, கீழ் பத்திகளைக் கொண்டுள்ளது.

## இடம் மதிப்பி மாதிரி

```
                              
            ┌───────────────────────────────────────────────────┐
            │                      மத்தியநி                      │ 
            │     ┌─────────────────────────────────────────┐     │
            │     │                மத்தியநி2                   │     │
            │     │     ┌─────────────────────────────┐     │     │
            │     │     │         மத்தியநி3           │     │     │        
            │     │     │     ┌────────────────┐     │     │     │
 　── கோர
## விளக்கம்

- வேலைச் API பதிப்பிக்க வழி (வேலைச் கண்டோரொல் வேள்வி பதிப்பிக்க வழி, [பல்லிஉப்](multiapp.md) என்று குழுவில்).
- சென்னையந்த API மாபெக்ட்டுகளில் `''` படுக்கோட்டு * `api` * வகையான குழுவில் இருந்து உருவாக்கப்பட்டுள்ளது.
- API மாபெக்ட்டுகளில் `''` படுக்கோட்டுகளுக்கு * `api` * வகையான குழுவில் `('POST', 'OPTIONS')` அமைப்பாடு உருவானது.
- API பதிப்பிக்க வழிகளைக் கொண்ட ஓட்டங்களை
  ```php
  'api' => [
      app\middleware\ApiOnly::class,
  ]
  ```
  என்று அமைக்கலாம்.

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
})->middleware
```

## மத்திய வழி செஷ்யம் பெறுவது
> **பொருள்**
> பதிப்பி 1.3.2 பொருளுக்கான webman புரட்சியான பொருளுக்கு

நீங்கள் `$request->route` ஐ பயன்படுத்தி மற்றும் அதன் அளவின் மூலங்களைக் கொண்டு சரியான விபரங்களைப் பெறலாம்.

**வழி எடுத்துச்செல்**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**மத்தியவரி**
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
        // கோரிக்கையில் எந்த பாதையையும் பொருந்தவில்லை(இயக்க பொருத்தம் பொருந்தத் தவிர， அப்போது $request->route ஐ null எனச் சொல்லலாம்
        // இயக்க உரை /user/111 ஸ்ரோ அடிகாரம் அணுகினால் கீழே காணப்படும் விவரங்களை விடும்
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

> **பொருள்**
> `$route->param()` முறையின் பயன்பாடு webman-framework >= 1.3.16 பதிப்பு தேவை.

## மத்திய வழி பிழை பெறும்
> **பொருள்**
> பதிப்பி 1.3.15 பொருளுக்கான webman புரட்சியான பொருளுக்கு

செவிக் கூரல்களான செயல்பாடுகளை விடுவில் கொண்டு, பிழையை கட்டுப்படுத்தி செவிக் செயல்பாடுகளை தைத்துவினால், `response->exception()` அமைக்கு

**வழியடாக**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('பிழைப் பிழை');
});
```

**செவிக்**
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

## மேல்கோவை மத்திய வழி

> **பொருள்**
> இந்த பதிப்புகாக webman புரட்சியான பதிப்புக்காக 1.5.16.

மேல்கோவை தேவையான மேல்கோவை தேவையான மேல்கோவை தேவையான மேல்கோவை தேவையான மேல்கோவை தேவையான தேகேன்து செவிக் மூச்சப்படுத்து: மத்தி ா!
