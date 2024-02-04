## Behandlung von statischen Dateien
webman unterstützt den Zugriff auf statische Dateien, die sich alle im Verzeichnis "public" befinden. Zum Beispiel wird der Zugriff auf "http://127.0.0.8787/upload/avatar.png" tatsächlich auf "{Hauptprojektverzeichnis}/public/upload/avatar.png" zugegriffen.

> **Hinweis**
> Ab webman 1.4 unterstützt die Anwendung Plugins. Der Zugriff auf statische Dateien, die mit "/app/xx/Filname" beginnen, erfolgt tatsächlich auf das Verzeichnis "public" des Anwendungs-Plugins. Das bedeutet, dass webman >= 1.4.0 den Zugriff auf Verzeichnisse unter "{Hauptprojektverzeichnis}/public/app/" nicht unterstützt.
> Weitere Informationen finden Sie unter [Anwendungs-Plugins](./plugin/app.md)

### Deaktivierung der Unterstützung für statische Dateien
Wenn keine Unterstützung für statische Dateien erforderlich ist, ändern Sie die Option "enable" in der Datei "config/static.php" auf "false". Nach dem Ausschalten führt der Zugriff auf alle statischen Dateien zu einem 404-Fehler.

### Ändern des Verzeichnisses für statische Dateien
webman verwendet standardmäßig das Verzeichnis "public" als Verzeichnis für statische Dateien. Wenn Sie dies ändern möchten, ändern Sie bitte die Funktion `public_path()` in der Datei `support/helpers.php`.

### Statische Datei-Middleware
webman hat eine eingebaute Middleware für statische Dateien, die sich im Verzeichnis "app/middleware/StaticFile.php" befindet.
Manchmal müssen wir statische Dateien bearbeiten, z.B. um statischen Dateien Header für Cross-Origin-Anfragen hinzuzufügen oder den Zugriff auf Dateien zu verbieten, die mit einem Punkt (`.`) beginnen.

Der Inhalt von `app/middleware/StaticFile.php` sieht ungefähr so aus:
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
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Hinzufügen von Cross-Origin-Headern
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
Wenn diese Middleware benötigt wird, muss sie im Abschnitt "middleware" in der Datei "config/static.php" aktiviert werden.
