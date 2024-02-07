## Behandlung von statischen Dateien
Webman unterstützt den Zugriff auf statische Dateien, die alle im Verzeichnis `public` platziert sind. Zum Beispiel wird der Zugriff auf `http://127.0.0.8787/upload/avatar.png` tatsächlich auf `{Hauptprojektverzeichnis}/public/upload/avatar.png` zugreifen.

> **Hinweis**
> Ab Version 1.4 unterstützt Webman die Anwendungs-Plugins. Der Zugriff auf statische Dateien, die mit `/app/xx/Dateiname` beginnen, erfolgt tatsächlich auf das `public`-Verzeichnis des Anwendungs-Plugins. Das heißt, Webman ab Version >=1.4.0 unterstützt den Zugriff auf Verzeichnisse unter `{Hauptprojektverzeichnis}/public/app/` nicht.
> Weitere Informationen finden Sie unter [Application Plugins](./plugin/app.md).

### Deaktivieren der statischen Dateiunterstützung
Wenn keine Unterstützung für statische Dateien benötigt wird, ändern Sie die Option `enable` in der Datei `config/static.php` auf `false`. Nach der Deaktivierung wird für alle Anfragen an statische Dateien ein 404-Fehler zurückgegeben.

### Ändern des Verzeichnisses für statische Dateien
Standardmäßig verwendet Webman das Verzeichnis `public` als Verzeichnis für statische Dateien. Wenn Sie dies ändern müssen, ändern Sie bitte die Funktion `public_path()` in der Datei `support/helpers.php`.

### Middleware für statische Dateien
Webman bietet standardmäßig eine Middleware für statische Dateien an, die sich im Verzeichnis `app/middleware/StaticFile.php` befindet.
Manchmal müssen wir statische Dateien bearbeiten, z.B. um statischen Dateien Header für Cross-Origin hinzuzufügen oder den Zugriff auf Dateien zu verbieten, die mit einem Punkt (`.`) beginnen.

Der Inhalt von `app/middleware/StaticFile.php` ähnelt dem folgenden Beispiel:
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // Zugriff auf versteckte Dateien, die mit einem Punkt beginnen, verbieten
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 verboten</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Cross-Origin-Header hinzufügen
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
Wenn diese Middleware benötigt wird, muss sie in der Datei `config/static.php` unter der Option `middleware` aktiviert werden.
