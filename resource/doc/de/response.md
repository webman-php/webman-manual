# Antwort

Die Antwort ist tatsächlich ein `support\Response`-Objekt, und um dieses Objekt bequem zu erstellen, bietet webman einige Hilfsfunktionen.

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
        return response('Hallo webman');
    }
}
```

Die Implementierung der Funktion `response` ist wie folgt:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

Sie können auch zunächst ein leeres `response`-Objekt erstellen und dann an geeigneter Stelle mit `$response->cookie()`, `$response->header()`, `$response->withHeaders()`, `$response->withBody()` den Rückgabewert festlegen.
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
                'X-Header-One' => 'Wert des Headers 1',
                'X-Header-Tow' => 'Wert des Headers 2',
            ]);

    // .... Geschäftslogik ausgelassen

    // Festlegen der zurückzugebenden Daten
    $response->withBody('Return-Daten');
    return $response;
}
```

## Rückgabe von JSON
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
Die Implementierung der Funktion `json` lautet wie folgt:
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```

## Rückgabe von XML
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
Die Implementierung der Funktion `xml` lautet wie folgt:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## Rückgabe von Ansichten
Erstellen Sie eine neue Datei `app/controller/FooController.php` wie folgt:
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
Erstellen Sie eine neue Datei `app/view/foo/hello.html` wie folgt:
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

## Weiterleitung
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return redirect('/user');
    }
}
```
Die Implementierung der Funktion `redirect` lautet wie folgt:
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

## Header setzen
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('Hallo webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Header-Wert' 
        ]);
    }
}
```
Sie können auch die Methoden `header` und `withHeaders` verwenden, um einzelne oder mehrere Header zu setzen.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('Hallo webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'Header Wert 1',
            'X-Header-Tow' => 'Header Wert 2',
        ]);
    }
}
```
Sie können auch Header im Voraus festlegen und zuletzt die zurückzugebenden Daten festlegen.
```php
public function hello(Request $request)
{
    // Objekt erstellen
    $response = response();
    
    // .... Geschäftslogik ausgelassen
  
    // HTTP-Header setzen
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Wert 1',
                'X-Header-Tow' => 'Header Wert 2',
            ]);

    // .... Geschäftslogik ausgelassen

    // Festlegen der zurückzugebenden Daten
    $response->withBody('Return-Daten');
    return $response;
}
```

## Cookie setzten
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('Hallo webman')
        ->cookie('foo', 'Wert');
    }
}
```
Sie können auch Cookies im Voraus setzen und zuletzt die zurückzugebenden Daten festlegen.
```php
public function hello(Request $request)
{
    // Objekt erstellen
    $response = response();
    
    // .... Geschäftslogik ausgelassen
    
    // Cookie setzen
    $response->cookie('foo', 'Wert');
    
    // .... Geschäftslogik ausgelassen

    // Festlegen der zurückzugebenden Daten
    $response->withBody('Return-Daten');
    return $response;
}
```
Die kompletten Parameter der Methode `cookie` sind wie folgt:
`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## Rückgabe von Datei-Streaming
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

- webman unterstützt den Versand von sehr großen Dateien
- Für große Dateien (über 2 MByte) liest webman die gesamte Datei nicht in den Speicher, sondern liest die Datei in geeigneten Intervallen und sendet sie
- Webman optimiert die Lese- und Sendegeschwindigkeit der Datei je nach der Geschwindigkeit, mit der der Client Daten empfängt, um sicherzustellen, dass die Datei so schnell wie möglich gesendet wird und dabei der Speicherverbrauch minimiert wird
- Die Datenübertragung ist nicht blockierend und beeinträchtigt nicht die Verarbeitung anderer Anfragen
- Die Methode `file` fügt automatisch den `if-modified-since`-Header hinzu und überprüft diesen Header bei der nächsten Anfrage. Wenn die Datei unverändert ist, wird direkt ein 304-Statuscode zurückgegeben, um Bandbreite zu sparen
- Die zu sendende Datei wird automatisch mit dem richtigen `Content-Type`-Header an den Browser gesendet
- Wenn die Datei nicht existiert, wird automatisch eine 404-Antwort erstellt

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
Die Methode `download` ist im Wesentlichen identisch mit der Methode `file`, der Unterschied besteht darin, dass
1. nach dem Setzen des herunterladbaren Dateinamens die Datei heruntergeladen wird und nicht im Browser angezeigt wird
2. die Methode `download` überprüft den `if-modified-since`-Header nicht
## Output abrufen
Einige Bibliotheken geben den Dateiinhalt direkt auf die Standardausgabe aus, was bedeutet, dass die Daten in der Befehlszeilenoberfläche gedruckt werden und nicht an den Browser gesendet werden. In solchen Fällen müssen wir die Daten in eine Variable erfassen und dann an den Browser senden, indem wir `ob_start();` und `ob_get_clean();` verwenden, zum Beispiel:

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

        // Output abrufen
        ob_start();
        // Bild ausgeben
        imagejpeg($im);
        // Bildinhalt abrufen
        $image = ob_get_clean();
        
        // Bild senden
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
