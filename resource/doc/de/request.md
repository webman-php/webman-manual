# Erklärung

## Zugriff auf Anfrageobjekt
Webman injiziert automatisch das Anfrageobjekt in den ersten Parameter der Aktionsmethode. Zum Beispiel:

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
        // Holen des Namensparameters aus der GET-Anfrage, wenn kein Namensparameter übergeben wurde, wird $default_name zurückgegeben
        $name = $request->get('name', $default_name);
        // Rückgabe eines Strings an den Browser
        return response('Hallo ' . $name);
    }
}
```

Durch das `$request`-Objekt können wir auf alle relevanten Anfragedaten zugreifen.

**Manchmal möchten wir das `$request`-Objekt in anderen Klassen erhalten, in diesem Fall verwenden wir einfach die Hilfsfunktion `request()`**;

## Abrufen von GET-Anfrageparametern

**Abrufen des gesamten GET-Arrays**
```php
$request->get();
```
Wenn die Anfrage keine GET-Parameter hat, wird ein leeres Array zurückgegeben.

**Abrufen eines Werts aus dem GET-Array**
```php
$request->get('name');
```
Wenn das GET-Array diesen Wert nicht enthält, wird null zurückgegeben.

Sie können auch einen Standardwert als zweiten Parameter an die get-Methode übergeben. Wenn im GET-Array kein entsprechender Wert gefunden wird, wird der Standardwert zurückgegeben. Zum Beispiel:
```php
$request->get('name', 'tom');
```

## Abrufen von POST-Anfrageparametern

**Abrufen des gesamten POST-Arrays**
```php
$request->post();
```
Wenn die Anfrage keine POST-Parameter hat, wird ein leeres Array zurückgegeben.

**Abrufen eines Werts aus dem POST-Array**
```php
$request->post('name');
```
Wenn das POST-Array diesen Wert nicht enthält, wird null zurückgegeben.

Wie bei der get-Methode können Sie der post-Methode auch einen Standardwert als zweiten Parameter übergeben. Wenn im POST-Array kein entsprechender Wert gefunden wird, wird der Standardwert zurückgegeben. Zum Beispiel:
```php
$request->post('name', 'tom');
```

## Abrufen des ursprünglichen POST-Anfragekörpers
```php
$post = $request->rawBody();
```
Diese Funktion ähnelt der `file_get_contents("php://input");`-Operation in `php-fpm` und wird verwendet, um den ursprünglichen HTTP-Anfragekörper zu erhalten. Dies ist nützlich, um POST-Anfragedaten im Format `application/x-www-form-urlencoded` zu erhalten.

## Abrufen des Headers
**Abrufen des gesamten Header-Arrays**
```php
$request->header();
```
Wenn die Anfrage keine Header-Parameter hat, wird ein leeres Array zurückgegeben. Beachten Sie, dass alle Schlüssel klein geschrieben sind.

**Abrufen eines Werts aus dem Header-Array**
```php
$request->header('host');
```
Wenn das Header-Array diesen Wert nicht enthält, wird null zurückgegeben. Beachten Sie, dass alle Schlüssel klein geschrieben sind.

Wie bei der get-Methode können Sie der header-Methode auch einen Standardwert als zweiten Parameter übergeben. Wenn im Header-Array kein entsprechender Wert gefunden wird, wird der Standardwert zurückgegeben. Zum Beispiel:
```php
$request->header('host', 'localhost');
```

## Cookies abrufen
**Abrufen des gesamten Cookie-Arrays**
```php
$request->cookie();
```
Wenn die Anfrage keine Cookie-Parameter hat, wird ein leeres Array zurückgegeben.

**Abrufen eines Werts aus dem Cookie-Array**
```php
$request->cookie('name');
```
Wenn das Cookie-Array diesen Wert nicht enthält, wird null zurückgegeben.

Wie bei der get-Methode können Sie der cookie-Methode auch einen Standardwert als zweiten Parameter übergeben. Wenn im Cookie-Array kein entsprechender Wert gefunden wird, wird der Standardwert zurückgegeben. Zum Beispiel:
```php
$request->cookie('name', 'tom');
```

## Abrufen aller Eingaben
enthält eine Kombination von `post` und `get`.
```php
$request->all();
```

## Abrufen eines bestimmten Eingabewerts
Abrufen eines Werts aus der Kombination aus `post` und `get`.
```php
$request->input('name', $default_value);
```

## Abrufen von Teilen der Eingabedaten
Abrufen von Teilen der Daten aus der Kombination aus `post` und `get`.
```php
// Holen Sie sich ein Array, das aus Benutzername und Passwort besteht. Wenn der entsprechende Schlüssel fehlt, wird er ignoriert
$only = $request->only(['username', 'password']);
// Holen Sie sich alle Eingaben außer Avatar und Alter
$except = $request->except(['avatar', 'age']);
```

## Abrufen von Upload-Dateien
**Abrufen des gesamten Upload-Datei-Arrays**
```php
$request->file();
```

Ein Formular ähnlich:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

`$request->file()` hat ein ähnliches Format wie:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
Es ist ein Array von `webman\Http\UploadFile`-Instanzen. Die Klasse `webman\Http\UploadFile` erweitert die in PHP integrierte [`SplFileInfo`](https://www.php.net/manual/en/class.splfileinfo.php) Klasse und bietet einige praktische Methoden.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // Überprüfen, ob die Datei gültig ist, z.B. true|false
            var_export($spl_file->getUploadExtension()); // Hochladen der Dateierweiterung, z.B. 'jpg'
            var_export($spl_file->getUploadMimeType()); // Hochladen des Datei-MIME-Typs, z.B. 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Abrufen des Upload-Fehlercodes, z.B. UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Hochladen des Dateinamens, z.B. 'mein-test.jpg'
            var_export($spl_file->getSize()); // Dateigröße abrufen, z.B. 13364, in Bytes
            var_export($spl_file->getPath()); // Upload-Verzeichnis abrufen, z.B. '/tmp'
            var_export($spl_file->getRealPath()); // Abrufen des temporären Dateipfads, z.B. `/tmp/workerman.upload.SRliMu`
        }
        return response('Ok');
    }
}
```

**Hinweis:**

- Die Datei wird nach dem Hochladen in eine temporäre Datei mit einem Namen wie `/tmp/workerman.upload.SRliMu` umbenannt.
- Die Größe der hochgeladenen Datei ist durch [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html) beschränkt, standardmäßig auf 10 MB festgelegt, der Standardwert kann in der Datei `config/server.php` durch Ändern von `max_package_size` angepasst werden.
- Die temporären Dateien werden automatisch nach Abschluss der Anfrage gelöscht.
- Wenn die Anfrage keine Dateien hochlädt, gibt `$request->file()` ein leeres Array zurück.
- Das Hochladen von Dateien unterstützt die Methode `move_uploaded_file()` nicht. Verwenden Sie stattdessen die Methode `$file->move()`, wie im folgenden Beispiel beschrieben.

### Abrufen einer spezifischen Upload-Datei
```php
$request->file('avatar');
```
Wenn die Datei existiert, wird die entsprechende `webman\Http\UploadFile`-Instanz zurückgegeben, andernfalls null.

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
            return json(['code' => 0, 'msg' => 'Upload erfolgreich']);
        }
        return json(['code' => 1, 'msg' => 'Datei nicht gefunden']);
    }
}
```

## Host abrufen
Abrufen von Host-Informationen der Anfrage.
```php
$request->host();
```
Wenn die Anforderungsadresse nicht der Standardport 80 oder 443 ist, können die Host-Informationen einen Port enthalten, z.B. `example.com:8080`. Wenn der Port nicht benötigt wird, kann der Methode der erste Parameter `true` übergeben werden.

```php
$request->host(true);
```

## Abrufen der Anfrage-Methode
```php
 $request->method();
```
Der Rückgabewert kann einer der folgenden Werte sein: `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, `HEAD`.

## Abrufen des Anfrage-URI
```php
$request->uri();
```
Gibt den URI der Anfrage zurück, einschließlich Pfad- und Query-String-Teile.

## Abrufen des Anfragepfads

```php
$request->path();
```
Gibt den Pfadteil der Anfrage zurück.

## Abrufen des Anfrage-Query-Strings

```php
$request->queryString();
```
Gibt den Query-String-Teil der Anfrage zurück.

## Abrufen der Anfrage-URL
Die Methode `url()` gibt die URL ohne `Query`-Parameter zurück.
```php
$request->url();
```
Gibt etwas Ähnliches zurück wie `//www.workerman.net/workerman-chat`.

Die Methode `fullUrl()` gibt die URL mit `Query`-Parametern zurück.
```php
$request->fullUrl();
```
Gibt etwas Ähnliches zurück wie `//www.workerman.net/workerman-chat?type=download`.

> **Hinweis**
> `url()` und `fullUrl()` enthalten keinen Protokollteil (nicht http oder https). Dies liegt daran, dass Browser, die mit `//` beginnende Adressen verwenden, automatisch das aktuelle Protokoll (http oder https) erkennen und die Anfrage starten.

Wenn Sie einen nginx-Proxy verwenden, fügen Sie `proxy_set_header X-Forwarded-Proto $scheme;` in die nginx-Konfiguration ein, [nginx proxy Referenz](others/nginx-proxy.md), dann können Sie `request->header('x-forwarded-proto')` verwenden, um festzustellen, ob es sich um http oder https handelt, z.B.:
```php
echo $request->header('x-forwarded-proto'); // gibt http oder https zurück
```

## Abrufen der Anfrage-HTTP-Version

```php
$request->protocolVersion();
```
Gibt den String `1.1` oder `1.0` zurück.

## Abrufen der Anfrage-Session-ID

```php
$request->sessionId();
```
Gibt einen String zurück, der aus Buchstaben und Zahlen besteht.

## Abrufen der Client-IP-Anfrage
```php
$request->getRemoteIp();
```

## Abrufen des Client-Port-Anfrage
```php
$request->getRemotePort();
```
## Echte IP-Adresse des Client-Anforderers abrufen
```php
$request->getRealIp($safe_mode=true);
```

Wenn das Projekt einen Proxy wie nginx verwendet, liefert `$request->getRemoteIp()` oft die IP-Adresse des Proxy-Servers (ähnlich wie `127.0.0.1` `192.168.x.x`) anstelle der echten Client-IP-Adresse. In diesem Fall kann versucht werden, mit `$request->getRealIp()` die echte IP-Adresse des Clients zu erhalten.

`$request->getRealIp()` versucht, die echte IP-Adresse aus den HTTP-Headern `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip` und `via` zu erhalten.

> Da HTTP-Header leicht gefälscht werden können, ist die mit dieser Methode erhaltene Client-IP-Adresse nicht zu 100% vertrauenswürdig, insbesondere wenn `$safe_mode` auf false gesetzt ist. Eine zuverlässigere Methode, die echte Client-IP-Adresse über einen Proxy zu erhalten, besteht darin, die IP-Adresse des bekannten sicheren Proxy-Servers zu kennen und zu wissen, welcher HTTP-Header die echte IP-Adresse enthält. Wenn die von `$request->getRemoteIp()` zurückgegebene IP-Adresse als die des bekannten sicheren Proxy-Servers bestätigt wird, kann die echte IP-Adresse mit `$request->header('Header mit echter IP-Adresse')` erhalten werden.


## Server-IP-Adresse abrufen
```php
$request->getLocalIp();
```

## Server-Port abrufen
```php
$request->getLocalPort();
```

## Überprüfen, ob es sich um eine Ajax-Anfrage handelt
```php
$request->isAjax();
```

## Überprüfen, ob es sich um eine Pjax-Anfrage handelt
```php
$request->isPjax();
```

## Überprüfen, ob eine JSON-Antwort erwartet wird
```php
$request->expectsJson();
```

## Überprüfen, ob der Client eine JSON-Antwort akzeptiert
```php
$request->acceptJson();
```

## Den Plugin-Namen der Anfrage erhalten
Bei Nicht-Plugin-Anfragen wird ein Leerstring `''` zurückgegeben.
```php
$request->plugin;
```
> Diese Funktion erfordert webman>=1.4.0

## Den Anwendungsname der Anfrage erhalten
Bei einer einzelnen Anwendung wird immer ein Leerstring `''` zurückgegeben. Bei [mehreren Anwendungen](multiapp.md) wird der Anwendungsname zurückgegeben.
```php
$request->app;
```

> Da anonyme Funktionen keiner Anwendung zugeordnet sind, gibt `$request->app` für Anfragen von Closure-Routen immer einen Leerstring `''` zurück.
> Siehe Closure-Routen unter [Routen](route.md)

## Den Controller-Klassennamen der Anfrage erhalten
Den Klassennamen des Controllers erhalten
```php
$request->controller;
```
Gibt beispielsweise `app\controller\IndexController` zurück.

> Da anonyme Funktionen keinem Controller zugeordnet sind, gibt `$request->controller` für Anfragen von Closure-Routen immer einen Leerstring `''` zurück.
> Siehe Closure-Routen unter [Routen](route.md)

## Den Methodennamen der Anfrage erhalten
Den Namen der Methode der Anfrage erhalten
```php
$request->action;
```
Gibt beispielsweise `index` zurück.

> Da anonyme Funktionen keiner Methode zugeordnet sind, gibt `$request->action` für Anfragen von Closure-Routen immer einen Leerstring `''` zurück.
> Siehe Closure-Routen unter [Routen](route.md)
