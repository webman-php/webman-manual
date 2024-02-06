# Esempio Semplice

## Ritorno di una stringa
**Creare un controller**

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
        // Ottieni il parametro 'name' dalla richiesta GET, se non è stato passato, restituisci $default_name
        $name = $request->get('name', $default_name);
        // Restituisci una stringa al browser
        return response('ciao ' . $name);
    }
}
```

**Accesso**

Accedi dall'browser a `http://127.0.0.1:8787/user/hello?name=tom`

Il browser restituirà `ciao tom`

## Ritorno di JSON
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

Accedi dall'browser a `http://127.0.0.1:8787/user/hello?name=tom`

Il browser restituirà `{"code":0,"msg":"ok","data":"tom""}`

L'utilizzo della funzione di supporto json per restituire i dati aggiungerà automaticamente l'header `Content-Type: application/json`.

## Ritorno di XML
Analogamente, utilizzando la funzione di supporto `xml($xml)` si otterrà una risposta XML con header `Content-Type: text/xml`.

Il parametro `$xml` può essere una stringa XML o un oggetto `SimpleXMLElement`.

## Ritorno di JSONP
Analogamente, utilizzando la funzione di supporto `jsonp($data, $callback_name = 'callback')` si otterrà una risposta JSONP.

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
ciao <?=htmlspecialchars($name)?>
</body>
</html>
```

Accedi dall'browser a `http://127.0.0.1:8787/user/hello?name=tom`
Verrà restituita una pagina HTML con il contenuto `ciao tom`.

Nota: Per impostazione predefinita, Webman utilizza la sintassi PHP nativa per i template. Per ulteriori opzioni di visualizzazione, vedere [Viste](view.md).
