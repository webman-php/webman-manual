## தனிப்படுத்த 404
வெப்மான் 404 நேரத்தில் தானாகவே `public/404.html` கோப்பில் உள்ள உள்ளடக்கத்தை திருப்திப்படுத்தும், எனவே உருவாக்குநர்கள் ஒருவரும் செயலிகள் செய்யலாம் `public/404.html` கோப்பை செய்யட்டும்.

நீங்கள் 404 உள்ளடக்கத்தை நிரவாகமாக கட்டக்கூடியதனால், உதாரணமாக ஆஜாக்ஸ் வினகல் சோதனை பெற்றால் ஜெசன் தரவை `{"குறியீடு:"404", "செய்தி":"404 காணப்படவில்லை"}` என்ற உள்ளடக்கத்தை மாற்ற விரும்புகின்றீர்கள், பக்கத் தோற்றத்தைப் பெற விரும்புகின்றீர்கள் `app/view/404.html` வார்த்தைப் தாக்குவதற்காக, தயவுசெய்து கீழே உள்ள உதாரணத்தைக் கருதும்.

> கீழே php பூர்வமைக்க உதாரணமாக பயன்பாட்டை வரையாகாக உள்ளடக்கிணைப்புக்கு மாதிரி ஒன்று அழைக்கப்பட்டுவிடும், பின்பு மற்ற படிகள் `twig` `blade` `think-tmplate` உள்ளடக்கிணைப்பு அடர்பாக உள்ளது.

**`app/view/404.html` கோப்பை உருவாக்கவும்**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 காணப்படவில்லை</title>
</head>
<body>
<?=htmlspecialchars($error)?>
</body>
</html>
```

**`config/route.php` கோப்பில் கீழே உள்ள குறிப்புகளை சேர்க்கவும்:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // ஆஜாக்ஸ் வினகல் பெற்றால் ஜெசன் மாற்று
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 காணப்படவில்லை']);
    }
    // பக்கம் வினகல்களை 404.html வார்த்தைப் தாக்கு
    return view('404', ['error' => 'சவால் வெற்றி'])->withStatus(404);
});
```


## தனிப்படுத்த 500
**`app/view/500.html` ஐ புதிய உள்ளடக்கமாக உருவாக்கவும்**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 உள்ளடக்க சேவை பிழை</title>
</head>
<body>
தனிப்படுத்த பிழை உள்ளடக்கம்:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**`app/exception/Handler.php` ஐ புதியாக உருவாக்கவும்** (அதற்கு வரையாடாவும் அல்லது உருவாக்க வேண்டியவை)

```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * பதிப்பி எழுப்பு
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // ஆஜாக்ஸ் வினகல் ஜெசன் தரவை ஜெசன் மாற்றுகின்றது
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // பக்கம் வினகல் 500.html வார்த்தைப் தாக்கு
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```


**`config/exception.php` ஐ கட்டமைக்க**
```php
return [
    '' => \app\exception\Handler::class,
];
```
