# Erklärung

## Zugriff auf das Request-Objekt

Webman injiziert automatisch das Request-Objekt in den ersten Parameter der Aktionsmethode, zum Beispiel

**Beispiel**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Abrufen des name-Parameters aus der GET-Anfrage. Falls der Name-Parameter nicht übergeben wird, wird der Standardname zurückgegeben
        $name = $request->get('name', $default_name);
        // Zurückgeben einer Zeichenfolge an den Browser
        return response('Hallo ' . $name);
    }
}
```

Mit dem `$request` -Objekt können wir auf alle relevanten Anfrage-Daten zugreifen.

**Manchmal möchten wir das `$request`-Objekt in anderen Klassen abrufen. In diesem Fall verwenden wir einfach die Hilfsfunktion `request()`**;

## Abrufen von GET-Parametern

**Abrufen des gesamten GET-Arrays**
```php
$request->get();
```
Wenn die Anfrage keine GET-Parameter enthält, wird ein leeres Array zurückgegeben.

**Abrufen eines Werts aus dem GET-Array**
```php
$request->get('name');
```
Wenn der Wert nicht im GET-Array enthalten ist, wird `null` zurückgegeben.

Sie können auch einen Standardwert als zweiten Parameter an die `get`-Methode übergeben. Wenn der Wert im GET-Array nicht gefunden wird, wird der Standardwert zurückgegeben. Zum Beispiel:
```php
$request->get('name', 'tom');
```

## Abrufen von POST-Parametern
**Abrufen des gesamten POST-Arrays**
```php
$request->post();
```
Wenn die Anforderung keine POST-Parameter enthält, wird ein leeres Array zurückgegeben.

**Abrufen eines Werts aus dem POST-Array**
```php
$request->post('name');
```
Wenn der Wert nicht im POST-Array enthalten ist, wird `null` zurückgegeben.

Wie bei der `get`-Methode können Sie auch der `post`-Methode einen Standardwert als zweiten Parameter übergeben. Wenn der Wert im POST-Array nicht gefunden wird, wird der Standardwert zurückgegeben. Zum Beispiel:
```php
$request->post('name', 'tom');
```

## Abrufen des Original-POST-Körpers der Anfrage
```php
$post = $request->rawBody();
```
Diese Funktion ähnelt der Verwendung von `file_get_contents("php://input");` in `php-fpm` und dient zum Abrufen des ursprünglichen HTTP-Anforderungskörpers. Dies ist besonders nützlich, um POST-Anfrage-Daten in einem Format abzurufen, das nicht `application/x-www-form-urlencoded` entspricht.

## Abrufen von Headern
**Abrufen des gesamten Header-Arrays**
```php
$request->header();
```
Wenn die Anfrage keine Header-Parameter enthält, wird ein leeres Array zurückgegeben. Beachten Sie, dass alle Schlüssel klein geschrieben sind.

**Abrufen eines Werts aus dem Header-Array**
```php
$request->header('host');
```
Wenn der Wert nicht im Header-Array enthalten ist, wird `null` zurückgegeben. Beachten Sie, dass alle Schlüssel klein geschrieben sind.

Wie bei der `get`-Methode können Sie auch der `header`-Methode einen Standardwert als zweiten Parameter übergeben. Wenn der Wert im Header-Array nicht gefunden wird, wird der Standardwert zurückgegeben. Zum Beispiel:
```php
$request->header('host', 'localhost');
```

## Abrufen von Cookies
**Abrufen des gesamten Cookie-Arrays**
```php
$request->cookie();
```
Wenn die Anfrage keine Cookie-Parameter enthält, wird ein leeres Array zurückgegeben.

**Abrufen eines Werts aus dem Cookie-Array**
```php
$request->cookie('name');
```
Wenn der Wert nicht im Cookie-Array enthalten ist, wird `null` zurückgegeben.

Wie bei der `get`-Methode können Sie auch der `cookie`-Methode einen Standardwert als zweiten Parameter übergeben. Wenn der Wert im Cookie-Array nicht gefunden wird, wird der Standardwert zurückgegeben. Zum Beispiel:
```php
$request->cookie('name', 'tom');
```

## Abrufen aller Eingaben
Beinhaltet die Sammlung von `POST` und `GET`.
```php
$request->all();
```

## Abrufen eines bestimmten Eingabewerts
Abrufen eines Werts aus der Sammlung von `POST` und `GET` mit einem Standardwert, falls erforderlich.
```php
$request->input('name', $standardwert);
```

## Abrufen von Teilen der Eingabedaten
Abrufen von Teilen der Daten aus der Sammlung von `POST` und `GET`.
```php
// Abrufen eines Arrays, das aus Benutzername und Passwort besteht. Wenn der entsprechende Schlüssel nicht vorhanden ist, wird dieser ignoriert
$only = $request->only(['username', 'password']);
// Abrufen aller Eingaben außer Avatar und Alter
$except = $request->except(['avatar', 'age']);
```

## Abrufen von hochgeladenen Dateien
**Abrufen des gesamten hochgeladenen Datei-Arrays**
```php
$request->file();
```

Formular ähnlich:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

Das von `$request->file()` zurückgegebene Format ist ähnlich wie:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
Es handelt sich um ein Array von Instanzen der Klasse `webman\Http\UploadFile`. Die Klasse `webman\Http\UploadFile` erbt von der eingebauten PHP-Klasse [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) und stellt einige nützliche Methoden bereit.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // Gibt an, ob die Datei gültig ist, z. B. true|false
            var_export($spl_file->getUploadExtension()); // Hochgeladene Dateierweiterung, z. B. 'jpg'
            var_export($spl_file->getUploadMimeType()); // Hochgeladener Datei-MIME-Typ, z. B. 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Abrufen des Upload-Fehlercodes, z. B. UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_NO_FILE, UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Hochgeladener Dateiname, z. B. 'my-test.jpg'
            var_export($spl_file->getSize()); // Abrufen der Dateigröße, z. B. 13364, in Bytes
            var_export($spl_file->getPath()); // Abrufen des Upload-Verzeichnisses, z. B. '/tmp'
            var_export($spl_file->getRealPath()); // Abrufen des temporären Dateipfads, z. B. `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Hinweis:**

- Nach dem Hochladen wird die Datei in eine temporäre Datei mit einem Namen wie `/tmp/workerman.upload.SRliMu` umbenannt
- Die Größe der hochgeladenen Datei ist durch die [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html) begrenzt, standardmäßig auf 10 MB. Sie können den Standardwert in der Datei `config/server.php` ändern, indem Sie `max_package_size` anpassen.
- Nach Abschluss der Anfrage wird die temporäre Datei automatisch gelöscht
- Wenn keine Datei hochgeladen wird, gibt `$request->file()` ein leeres Array zurück
- Die hochgeladenen Dateien unterstützen die Methode `move_uploaded_file()` nicht. Verwenden Sie stattdessen die Methode `$file->move()`, wie im folgenden Beispiel beschrieben

### Abrufen einer bestimmten hochgeladenen Datei
```php
$request->file('avatar');
```
Wenn die Datei existiert, wird eine Instanz von `webman\Http\UploadFile` für die entsprechende Datei zurückgegeben, ansonsten `null`.

**Beispiel**
```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function file(Request $request)
    {
        $file = $request->file('avatar');
        if ($file && $file->isValid()) {
            $file->move(public_path().'/files/myfile.'.$file->getUploadExtension());
            return json(['code' => 0, 'msg' => 'Erfolgreich hochgeladen']);
        }
        return json(['code' => 1, 'msg' => 'Datei nicht gefunden']);
    }
}
```

## Abrufen des Hosts
Abrufen der Host-Informationen der Anfrage.
```php
$request->host();
```
Wenn die Adresse der Anfrage keinen Standard-Port (80 oder 443) aufweist, enthält die Host-Information möglicherweise den Port, z. B. `example.com:8080`. Wenn der Port nicht benötigt wird, kann das erste Argument als `true` übergeben werden.

```php
$request->host(true);
```

## Abrufen der Anfragemethode
```php
 $request->method();
```
Der zurückgegebene Wert kann `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS` oder `HEAD` sein.

## Abrufen der Anfrage-URI
```php
$request->uri();
```
Gibt die URI der Anfrage zurück, einschließlich Pfad und Query-String-Teil.

## Abrufen des Anfragepfads

```php
$request->path();
```
Gibt den Anfragepfad zurück.

## Abrufen der Anfrage-Query-String

```php
$request->queryString();
```
Gibt den Query-String-Teil der Anfrage zurück.
## Erhalte die Anfrage-URL
Die Methode `url()` gibt die URL ohne `Query`-Parameter zurück.
```php
$request->url();
```
Gibt etwas ähnliches wie `//www.workerman.net/workerman-chat` zurück.

Die Methode `fullUrl()` gibt die URL mit `Query`-Parameter zurück.
```php
$request->fullUrl();
```
Gibt etwas ähnliches wie `//www.workerman.net/workerman-chat?type=download` zurück.

> **Hinweis**
> `url()` und `fullUrl()` geben keinen Protokollteil zurück (kein http oder https).
> Dies liegt daran, dass in Browsern Adressen, die mit `//` beginnen, automatisch das aktuelle Protokoll der Website erkennen und automatisch eine Anfrage per http oder https senden.

Wenn Sie einen nginx-Proxy verwenden, fügen Sie `proxy_set_header X-Forwarded-Proto $scheme;` zur nginx-Konfiguration hinzu. [Siehe nginx-Proxy](others/nginx-proxy.md).
Auf diese Weise können Sie mit `$request->header('x-forwarded-proto');` prüfen, ob es sich um http oder https handelt. Zum Beispiel:
```php
echo $request->header('x-forwarded-proto'); // gibt http oder https aus
```

## Erhalte die Anfrage-HTTP-Version
```php
$request->protocolVersion();
```
Gibt den String `1.1` oder `1.0` zurück.

## Erhalte die Anfragesitzungs-ID
```php
$request->sessionId();
```
Gibt einen String aus Buchstaben und Zahlen zurück.

## Erhalte die IP-Adresse des Client-Anfragers
```php
$request->getRemoteIp();
```

## Erhalte den Port des Client-Anfragers
```php
$request->getRemotePort();
```

## Erhalte die wirkliche IP-Adresse des Client-Anfragers
```php
$request->getRealIp($safe_mode=true);
```

Wenn ein Projekt einen Proxy verwendet (z. B. nginx), gibt `$request->getRemoteIp()` normalerweise die IP-Adresse des Proxy-Servers zurück (ähnlich wie `127.0.0.1` `192.168.x.x`) und nicht die wirkliche IP-Adresse des Client-Anfragers. In diesem Fall können Sie versuchen, die wirkliche IP-Adresse des Client-Anfragers mit `$request->getRealIp()` zu erhalten.

`$request->getRealIp()` versucht, die wirkliche IP-Adresse aus den HTTP-Headern `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, `via` zu erhalten.

> Da HTTP-Header leicht gefälscht werden können, ist die mit dieser Methode erhaltene IP-Adresse des Client-Anfragers nicht zu 100% vertrauenswürdig, insbesondere wenn `$safe_mode` auf false gesetzt ist. Eine verlässlichere Methode, die die wirkliche IP-Adresse des Client-Anfragers über einen Proxy erhält, besteht darin, die als sicher bekannte Proxy-Server-IP zu kennen und zu wissen, welcher HTTP-Header die wirkliche IP-Adresse übermittelt. Falls die IP-Adresse, die von `$request->getRemoteIp()` zurückgegeben wird, als die des bekannten sicheren Proxy-Servers bestätigt wird, kann die wirkliche IP-Adresse dann über `$request->header('Name des Headers mit der wirklichen IP-Adresse')` erhalten werden.

## Erhalte die Server-IP-Adresse
```php
$request->getLocalIp();
```

## Erhalte den Server-Port
```php
$request->getLocalPort();
```

## Überprüfe, ob es sich um eine Ajax-Anfrage handelt
```php
$request->isAjax();
```

## Überprüfe, ob es sich um eine Pjax-Anfrage handelt
```php
$request->isPjax();
```

## Überprüfe, ob eine JSON-Antwort erwartet wird
```php
$request->expectsJson();
```

## Überprüfe, ob der Client eine JSON-Antwort akzeptiert
```php
$request->acceptJson();
```

## Erhalte den Plugin-Namen der Anfrage
Bei einer Anfrage, die kein Plugin ist, wird ein Leerstring `''` zurückgegeben.
```php
$request->plugin;
```
> Diese Funktion erfordert webman>=1.4.0

## Erhalte den Anwendungsnamen der Anfrage
Bei einer einzelnen Anwendung wird immer ein Leerstring `''` zurückgegeben. Bei [mehreren Anwendungen](multiapp.md) wird der Anwendungsnamen zurückgegeben.
```php
$request->app;
```
> Da Closure-Funktionen keiner Anwendung zugeordnet sind, gibt eine Anfrage von einer Closure-Route immer einen Leerstring `''` zurück.
> Siehe auch [Routing](route.md) für Closure-Routen.

## Erhalte den Namen der Controller-Klasse der Anfrage
Erhalte den Klassennamen des Controllers.
```php
$request->controller;
```
Gibt etwas ähnliches wie `app\controller\IndexController` zurück.

> Da Closure-Funktionen keiner Controller-Klasse zugeordnet sind, gibt eine Anfrage von einer Closure-Route immer einen Leerstring `''` zurück.
> Siehe auch [Routing](route.md) für Closure-Routen.

## Erhalte den Namen der Anfrage-Methode
Erhalte den Namen der Controller-Methode der Anfrage.
```php
$request->action;
```
Gibt etwas ähnliches wie `index` zurück.

> Da Closure-Funktionen keiner Controller-Methode zugeordnet sind, gibt eine Anfrage von einer Closure-Route immer einen Leerstring `''` zurück.
> Siehe auch [Routing](route.md) für Closure-Routen.
