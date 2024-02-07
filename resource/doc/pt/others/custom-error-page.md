## Personalizar 404
Quando o webman retorna um erro 404, ele automaticamente mostra o conteúdo do arquivo `public/404.html`, então os desenvolvedores podem simplesmente mudar o arquivo `public/404.html`.

Se você quer controlar dinamicamente o conteúdo do erro 404, por exemplo, retornar dados JSON `{"code:"404", "msg":"404 not found"}` durante uma requisição AJAX, ou retornar o template `app/view/404.html` durante uma requisição de página, por favor siga o exemplo abaixo.

> O exemplo a seguir é baseado no uso de templates PHP nativos, mas o princípio é similar para outros templates como `twig`, `blade` ou `think-template`.

**Crie o arquivo `app/view/404.html`**
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

**Adicione o seguinte código no`config/route.php`:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // retorna um JSON durante uma requisição AJAX
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // retorna o template 404.html durante uma requisição de página
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## Personalizar 500
**Crie o arquivo `app/view/500.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
Erro personalizado:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**Crie o arquivo `app/exception/Handler.php` (se o diretório não existir, por favor crie-o):**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * Renderizar e retornar
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // Retorna dados JSON durante uma requisição AJAX
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // Retorna o template 500.html durante uma requisição de página
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**Configure `config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```
