## Personalizzazione del 404
Quando si verifica un errore 404, webman restituirà automaticamente il contenuto del file `public/404.html`, quindi gli sviluppatori possono modificare direttamente il file `public/404.html`.

Se desideri controllare dinamicamente il contenuto del 404, ad esempio restituire dati json `{"code:"404", "msg":"404 not found"}` durante una richiesta ajax, e restituire il template `app/view/404.html` durante una richiesta di pagina, segui l'esempio seguente.

> Di seguito viene utilizzato PHP come esempio per il template nativo, ma il principio è simile per altri template come `twig`, `blade`, `think-tmplate`.

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

**Aggiungi il seguente codice a `config/route.php`:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // Restituisci json durante una richiesta ajax
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // Restituisci il template 404.html durante una richiesta di pagina
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## Personalizzazione del 500
**Crea il file `app/view/500.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
Template di errore personalizzato:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**Crea il file** `app/exception/Handler.php` **(crea la directory se non esiste)**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * Renderizza e restituisce
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // Restituisci dati json durante una richiesta ajax
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // Restituisci il template 500.html durante una richiesta di pagina
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**Configura `config/exception.php`:**
```php
return [
    '' => \app\exception\Handler::class,
];
```
