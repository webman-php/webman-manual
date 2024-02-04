# Antwort
Die Antwort ist tatsächlich ein `support\Response`-Objekt. Um die Erstellung dieses Objekts zu vereinfachen, bietet webman einige Hilfsfunktionen.

## Rückgabe einer beliebigen Antwort

**Beispiel**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('Hallo Webman');
    }
}
```

Die Funktion `response` ist wie folgt implementiert:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

Sie können auch zuerst ein leeres `Response`-Objekt erstellen und dann an geeigneten Stellen mit `$response->cookie()`, `$response->header()`, `$response->withHeaders()`, `$response->withBody()` den Rückgabewert festlegen.
```php
public function hello(Request $request)
{
    // Objekt erstellen
    $response = response();
    
    // .... Geschäftslogik ausgelassen
    
    // Cookie setzen
    $response->cookie('foo', 'Wert');
    
    // .... Geschäftslogik ausgelassen
    
    // HTTP-Header setzen
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header-Wert 1',
                'X-Header-Tow' => 'Header-Wert 2',
            ]);

    // .... Geschäftslogik ausgelassen

    // Festlegen der zurückzugebenden Daten
    $response->withBody('Rückgabedaten');
    return $response;
}
```

## JSON zurückgeben
**Beispiel**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```
Die Funktion `json` ist wie folgt implementiert:
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```

## XML zurückgeben
**Beispiel**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        $xml = <<<XML
               <?xml version='1.0' standalone='yes'?>
               <values>
                   <truevalue>1</truevalue>
                   <falsevalue>0</falsevalue>
               </values>
               XML;
        return xml($xml);
    }
}
```
Die Funktion `xml` ist wie folgt implementiert:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## Ansicht zurückgeben
Erstellen Sie die Datei `app/controller/FooController.php` wie folgt:
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return view('foo/hello', ['name' => 'webman']);
    }
}
```

Erstellen Sie die Datei `app/view/foo/hello.html` wie folgt:
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
Hallo <?=htmlspecialchars($name)?>
</body>
</html>
```

## Umleitung
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return redirect('/benutzer');
    }
}
```
Die Funktion `redirect` ist wie folgt implementiert:
```php
function redirect($location, $status = 302, $headers = [])
{
    $response = new Response($status, ['Location' => $location]);
    if (!empty($headers)) {
        $response->withHeaders($headers);
    }
    return $response;
}
```

## Header festlegen
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('Hallo Webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Header-Wert' 
        ]);
    }
}
```
Sie können auch die Methoden `header` und `withHeaders` verwenden, um einzelne oder mehrere Header festzulegen.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('Hallo Webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'Header-Wert 1',
            'X-Header-Tow' => 'Header-Wert 2',
        ]);
    }
}
```
Sie können auch zuerst Header setzen und schließlich die zurückzugebenden Daten festlegen.
```php
public function hello(Request $request)
{
    // Objekt erstellen
    $response = response();
    
    // .... Geschäftslogik ausgelassen
  
    // HTTP-Header setzen
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header-Wert 1',
                'X-Header-Tow' => 'Header-Wert 2',
            ]);

    // .... Geschäftslogik ausgelassen

    // Festlegen der zurückzugebenden Daten
    $response->withBody('Rückgabedaten');
    return $response;
}
```

## Cookie setzen
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('Hallo Webman')
        ->cookie('foo', 'Wert');
    }
}
```
Sie können auch zuerst Cookies setzen und schließlich die zurückzugebenden Daten festlegen.
```php
public function hello(Request $request)
{
    // Object erstellen
    $response = response();
    
    // .... Geschäftslogik ausgelassen
    
    // Cookie setzen
    $response->cookie('foo', 'Wert');
    
    // .... Geschäftslogik ausgelassen

    // Festlegen der zurückzugebenden Daten
    $response->withBody('Rückgabedaten');
    return $response;
}
```
Die vollständigen Parameter der Methode `cookie` sind wie folgt:
`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## Dateistream zurückgeben

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->file(public_path() . '/favicon.ico');
    }
}
```

- webman unterstützt den Versand von sehr großen Dateien.
- Für große Dateien (über 2 MB) liest webman nicht die gesamte Datei auf einmal in den Speicher ein, sondern liest die Datei zu gegebener Zeit in Abschnitten ein und sendet sie.
- webman optimiert das Lese- und Sendetempo der Datei auf der Grundlage der Empfangsgeschwindigkeit des Clients, um gleichzeitig die Datei schnellstmöglich zu senden und den Speicherplatzbedarf auf das Mindeste zu reduzieren.
- Die Datenübertragung ist nicht blockierend und beeinflusst die Verarbeitung anderer Anfragen nicht.
- Die Methode `file` fügt automatisch den Header `if-modified-since` hinzu und überprüft in der nächsten Anfrage den Header `if-modified-since`. Wenn die Datei nicht geändert wurde, wird direkt ein 304-Statuscode zurückgegeben, um Bandbreite zu sparen.
- Die zu sendende Datei wird automatisch mit dem geeigneten `Content-Type`-Header an den Browser gesendet.
- Wenn die Datei nicht existiert, wird automatisch eine 404-Antwort generiert.

## Datei herunterladen
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'Dateiname.ico');
    }
}
```
Die Methode `download` ist fast identisch mit der Methode `file`, mit dem Unterschied, dass
1. Nachdem der Name der herunterzuladenden Datei festgelegt wurde, wird die Datei heruntergeladen, anstatt im Browser angezeigt zu werden.
2. Die Methode `download` überprüft den Header `if-modified-since` nicht.

## Ausgabe abrufen
Einige Bibliotheken geben die Dateiinhalte direkt auf die Standardausgabe aus, also werden die Daten in der Befehlszeilenumgebung gedruckt und nicht an den Browser gesendet. In solchen Fällen müssen wir die Daten in eine Variable erfassen und dann an den Browser senden, z.B. mittels `ob_start();` und `ob_get_clean();`, wie in folgendem Beispiel:

```php
<?php
namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // Bild erstellen
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'Ein einfacher Textstring', $text_color);

        // Starten der Ausgabeaufzeichnung
        ob_start();
        // Bild ausgeben
        imagejpeg($im);
        // Bildinhalt erhalten
        $image = ob_get_clean();
        
        // Bild senden
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
