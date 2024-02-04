## Benutzerdefinierte 404
Wenn webman auf einen 404-Fehler stößt, wird automatisch der Inhalt von `public/404.html` zurückgegeben, daher können Entwickler die Datei `public/404.html` direkt ändern.

Wenn Sie den Inhalt des 404-Fehlers dynamisch steuern möchten, zum Beispiel JSON-Daten wie `{"code:"404", "msg":"404 not found"}` für AJAX-Anfragen zurückgeben möchten, oder für Seitenanforderungen das Template `app/view/404.html` zurückgeben möchten, beachten Sie das folgende Beispiel

> Im folgenden Beispiel wird PHP-Templating als Beispiel verwendet, die Funktionsweise für andere Templates wie `twig`, `blade`, `think-tmplate` ist ähnlich

**Erstellen Sie die Datei `app/view/404.html`**
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

**Fügen Sie den folgenden Code in `config/route.php` ein:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // Rückgabe von JSON für AJAX-Anfragen
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // Seitenanforderung gibt das 404.html Template zurück
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## Benutzerdefinierte 500
**Erstellen von `app/view/500.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
Benutzerdefiniertes Fehler-Template:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**Erstellen von `app/exception/Handler.php`** (Falls das Verzeichnis nicht existiert, bitte erstellen)
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * Rendern und zurückgeben
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // Rückgabe von JSON-Daten für AJAX-Anfragen
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // Seitenanforderung gibt das 500.html Template zurück
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**Konfiguration von `config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```

