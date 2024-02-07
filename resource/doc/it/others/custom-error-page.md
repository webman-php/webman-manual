## Personalizzare il 404
In caso di errore 404, webman restituirà automaticamente il contenuto di `public/404.html`, quindi gli sviluppatori possono modificare direttamente il file `public/404.html`.

Se si desidera controllare dinamicamente il contenuto del 404, ad esempio restituire dati JSON come `{"code:"404", "msg":"404 not found"}` durante le richieste AJAX, o restituire il template` app/view/404.html` durante le richieste di pagina, si prega di fare riferimento all'esempio seguente.

> Nell'esempio seguente viene utilizzato il modello PHP nativo, ma il funzionamento è simile per altri modelli come `twig`, `blade`, `think-tmplate`.

**Creare il file `app/view/404.html`**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 not found</title>
</head>
<body>
<?=htmlspecialchars($error)?>
</body>
</html>
```

**Aggiungere il seguente codice a `config/route.php`:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // restituisci JSON durante le richieste AJAX
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // restituisci il modello 404.html durante le richieste di pagina
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## Personalizzare il 500
**Creare un nuovo file `app/view/500.html`**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
Modello di errore personalizzato:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**Creare** `app/exception/Handler.php` **(crea la cartella se non esiste)**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * Rendere e restituire
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // restituisci dati JSON durante la richiesta AJAX
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // restituisci il modello 500.html durante le richieste di pagina
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**Configura `config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```
