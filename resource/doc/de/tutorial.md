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
        // Holen Sie sich den Namen-Parameter aus der GET-Anfrage. Wenn kein Name-Parameter übergeben wurde, wird $default_name zurückgegeben
        $name = $request->get('name', $default_name);
        // Gib den String an den Browser zurück
        return response('hallo ' . $name);
    }
}
```

**Zugriff**

Rufen Sie im Browser `http://127.0.0.1:8787/user/hello?name=tom` auf

Der Browser wird `hallo tom` zurückgeben

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

**Zugriff**

Rufen Sie im Browser `http://127.0.0.1:8787/user/hello?name=tom` auf

Der Browser wird `{"code":0,"msg":"ok","data":"tom"}` zurückgeben

Die Verwendung des JSON-Assistenten für die Datenrückgabe fügt automatisch einen Header hinzu: `Content-Type: application/json`

## Rückgabe von XML
Ebenso wird die Verwendung des Assistenten `xml($xml)` eine Antwort als `xml` mit dem Header `Content-Type: text/xml` zurückgeben.

Der Parameter `$xml` kann ein `xml`-String oder ein `SimpleXMLElement`-Objekt sein.

## Rückgabe von JSONP
Ebenso wird die Verwendung des Assistenten `jsonp($data, $callback_name = 'callback')` eine `jsonp`-Antwort zurückgeben.

## Rückgabe eines Views
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
hallo <?=htmlspecialchars($name)?>
</body>
</html>
```

Rufen Sie im Browser `http://127.0.0.1:8787/user/hello?name=tom` auf
Es wird eine HTML-Seite mit dem Inhalt `hallo tom` zurückgegeben.

Hinweis: Standardmäßig verwendet webman die nativen PHP-Syntax als Vorlage. Wenn Sie andere Ansichten verwenden möchten, siehe [View](view.md).
