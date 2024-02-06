## Personalizar o erro 404
Quando ocorre um erro 404 no webman, ele automaticamente retorna o conteúdo de `public/404.html`, então os desenvolvedores podem simplesmente modificar o arquivo `public/404.html`.

Se você deseja controlar dinamicamente o conteúdo do erro 404, por exemplo, retornando dados JSON `{"code:"404", "msg":"404 not found"}` em requisições ajax, e retornando o modelo `app/view/404.html` em requisições de página, siga o exemplo abaixo.

> O exemplo a seguir é baseado em modelos PHP puros, outros modelos como `twig`, `blade`, `think-tmplate` são semelhantes em princípio.

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

**Adicione o seguinte código em `config/route.php`:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // Retorna JSON em requisições ajax
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // Retorna o modelo 404.html em requisições de página
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## Personalizar o erro 500
**Crie `app/view/500.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
Modelo de erro personalizado:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**Crie** `app/exception/Handler.php` **(crie o diretório se ele não existir)**

```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * Renderizar retorno
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // Retorna dados JSON em requisições ajax
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // Retorna o modelo 500.html em requisições de página
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
