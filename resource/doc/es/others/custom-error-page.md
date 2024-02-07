## Personalizar el error 404
Cuando ocurre un error 404 en webman, automáticamente se devuelve el contenido de `public/404.html`, por lo que los desarrolladores pueden cambiar directamente el archivo `public/404.html`.

Si desea controlar dinámicamente el contenido del error 404, por ejemplo, devolver datos json `{"code:"404", "msg":"404 not found"}` en una solicitud ajax y devolver la plantilla `app/view/404.html` en una solicitud de página, consulte el siguiente ejemplo.

> A continuación se muestra un ejemplo utilizando una plantilla nativa de PHP, el principio es similar para otras plantillas como `twig`, `blade`, `think-template`.

**Crear el archivo `app/view/404.html`**
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

**Agregue el siguiente código a `config/route.php`:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // Devolver json en solicitudes ajax
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // Devolver la plantilla 404.html en solicitudes de página
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## Personalizar el error 500
**Crear `app/view/500.html`**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
Plantilla de error personalizada:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**Crear `app/exception/Handler.php` (si el directorio no existe, créelo usted mismo)**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * Renderizar y devolver
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // Devolver datos json en solicitudes ajax
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // Devolver la plantilla 500.html en solicitudes de página
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**Configurar `config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```
