# Esempio Semplice

## Ritorno di una stringa
**Creare un Controller**

Creare il file `app/controller/UserController.php` come segue

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Ottenere il parametro name dalla richiesta get, se il parametro name non è presente, restituire $default_name
        $name = $request->get('name', $default_name);
        // Restituire una stringa al browser
        return response('hello ' . $name);
    }
}
```

**Accesso**

Accedere da un browser a `http://127.0.0.1:8787/user/hello?name=tom`

Il browser restituirà `hello tom`

## Ritorno di un json
Modificare il file `app/controller/UserController.php` come segue

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

**Accesso**

Accedere da un browser a `http://127.0.0.1:8787/user/hello?name=tom`

Il browser restituirà `{"code":0,"msg":"ok","data":"tom""}`

Utilizzare la funzione di supporto json per restituire i dati aggiungerà automaticamente un'intestazione `Content-Type: application/json`

## Ritorno di un xml
Allo stesso modo, utilizzando la funzione di supporto `xml($xml)` si otterrà una risposta `xml` con l'intestazione `Content-Type: text/xml`.

Il parametro `$xml` può essere una stringa `xml` o un oggetto `SimpleXMLElement`.

## Ritorno di un jsonp
Allo stesso modo, utilizzando la funzione di supporto `jsonp($data, $callback_name = 'callback')` si otterrà una risposta `jsonp`.

## Ritorno di una vista
Modificare il file `app/controller/UserController.php` come segue

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

Creare il file `app/view/user/hello.html` come segue

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

Accedendo da un browser a `http://127.0.0.1:8787/user/hello?name=tom`
si otterrà una pagina html con il contenuto `hello tom`.

Nota: webman utilizza di default la sintassi nativa di PHP come modello. Se si desidera utilizzare altre viste, consultare [Vista](view.md).
