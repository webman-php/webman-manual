## தன்னியக்க தவறு 404
webman கொண்டிருக்கும் 404 நேரத்திற்கு, `public/404.html` பொருள் தானாகவே மீளமைக்கப்படும், அதனால் உருவாக்கலாம்.

நீங்கள் 404 உள்ளீடு பொருநரில் உள்ளக தமிழ்நாட்டில் control செய்ய விரும்புவதற்கு, எடுத்துக்காட்டில் JSON தரும் விதத்தில் `{"code:"404", "msg":"404 not found"}` புதியவை, பக்க கோரிக்கையைப் பிரிக்கும் `app/view/404.html` வார்ப்புருவை பெற விரும்பினால், பின்வரும் உதாரணம் பார்க்கவும்

> கீழே PHP பிராமாத்திக வார்ப்புருவை பயன்படுத்தி, பிற வார்ப்புருகள் `twig` `blade` `think-tmplate` மாயங்காதியாக உள்ளது

**கோப்பை உருவாக்கு`app/view/404.html`**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 not found</title>
</head>
<body>
<?=htmlspecialchars($error)?>
</body>
</html>
```

**`config/route.php` இல் பின்வருவாயின் குறிப்பிடி அடைவைச் சேர்க்கவும்：**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // ajax கோரிக்கை போன்றவற்றை வழங்கும்
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // பக்க கோரிக்கை 404.html வார்ப்புருவை எதிர்காலமாக்கும்
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## தன்னியக்க 500
**புதிய`app/view/500.html` பொருள்**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
தன்னியக்க பிழை வார்ப்புரு:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**புதிய**app/exception/Handler.php**(இல் இல்லையெனில் உங்களே உருமைப்பாரீகையா உருவாக்குக)**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * திறக்க கூறுகை
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // ajax கோரிக்கை JSON தரும் விதம்
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // பக்க கோரிக்கை 500.html வார்ப்புருவை எதிர்காலமாக்கும்
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**கட்டமை`config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```
