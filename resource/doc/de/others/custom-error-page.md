## Benutzerdefinierte 404
Webman gibt bei einem 404-Fehler automatisch den Inhalt von `public/404.html` zurück, sodass Entwickler die Datei `public/404.html` direkt ändern können.

Wenn Sie den Inhalt von 404 dynamisch steuern möchten, z.B. bei ajax-Anfragen JSON-Daten `{"code:"404", "msg":"404 not found"}` zurückgeben oder bei Seitenanfragen das Template `app/view/404.html`, befolgen Sie das folgende Beispiel.

> Im Folgenden wird das Beispiel anhand von einfachen PHP-Templates erklärt, aber das Prinzip gilt auch für andere Templates wie `Twig`, `Blade`, `Think-Template`.

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

**Fügen Sie den folgenden Code zu `config/route.php` hinzu:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // Rückgabe von JSON bei ajax-Anfragen
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // Seitenanfrage gibt das 404.html-Template zurück
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## Benutzerdefinierte 500
**Erstellen Sie `app/view/500.html`**

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

**Erstellen Sie **app/exception/Handler.php** (Erstellen Sie das Verzeichnis, wenn es nicht vorhanden ist):**
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
        // Rückgabe von JSON bei ajax-Anfragen
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // Seitenanfrage gibt das 500.html-Template zurück
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**Konfigurieren Sie `config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```
