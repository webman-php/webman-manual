```php
# Einfaches Beispiel

## Rückgabe eines Strings
**Controller erstellen**

Erstellen Sie die Datei `app/controller/UserController.php` wie folgt

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Holen des Namensparameters aus der GET-Anfrage; falls kein Nameparameter übergeben wurde, wird `$default_name` zurückgegeben
        $name = $request->get('name', $default_name);
        // Rückgabe eines Strings an den Browser
        return response('hello ' . $name);
    }
}
```

**Aufruf**

Rufen Sie im Browser `http://127.0.0.1:8787/user/hello?name=tom` auf.

Der Browser gibt `hello tom` zurück.

## Rückgabe von JSON
Ändern Sie die Datei `app/controller/UserController.php` wie folgt

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return json([
            'code' => 0, 
            'msg' => 'ok', 
            'data' => $name
        ]);
    }
}
```

**Aufruf**

Rufen Sie im Browser `http://127.0.0.1:8787/user/hello?name=tom` auf.

Der Browser gibt `{"code":0,"msg":"ok","data":"tom"}` zurück.

Die Verwendung des JSON-Hilfsfunktionen für die Datenrückgabe fügt automatisch einen Header `Content-Type: application/json` hinzu.

## Rückgabe von XML
Ebenso gibt die Verwendung der Hilfsfunktion `xml($xml)` eine Antwort in Form von `xml` mit dem Header `Content-Type: text/xml` zurück.

Der Parameter `$xml` kann ein `xml`-String oder ein `SimpleXMLElement`-Objekt sein.

## Rückgabe von JSONP
Ebenso gibt die Verwendung der Hilfsfunktion `jsonp($data, $callback_name = 'callback')` eine `jsonp`-Antwort zurück.

## Rückgabe von Ansichten
Ändern Sie die Datei `app/controller/UserController.php` wie folgt

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return view('user/hello', ['name' => $name]);
    }
}
```

Erstellen Sie die Datei `app/view/user/hello.html` wie folgt

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

Rufen Sie im Browser `http://127.0.0.1:8787/user/hello?name=tom` auf.
Es wird eine HTML-Seite mit dem Inhalt `hello tom` zurückgegeben.

Hinweis: Standardmäßig verwendet webman die PHP-Native-Syntax als Vorlage. Weitere Informationen zur Verwendung von anderen Ansichten finden Sie unter [View](view.md).
```

