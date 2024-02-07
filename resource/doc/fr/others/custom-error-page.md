## Personnaliser la page 404
Lorsqu'une erreur 404 survient, webman renvoie automatiquement le contenu de `public/404.html`, ce qui permet aux développeurs de modifier directement le fichier `public/404.html`.

Si vous souhaitez contrôler dynamiquement le contenu de la page 404, par exemple en renvoyant des données JSON `{"code:"404", "msg":"404 not found"}` lors d'une requête AJAX, ou en renvoyant le modèle `app/view/404.html` lors d'une requête de page, veuillez suivre l'exemple suivant.

> L'exemple ci-dessous utilise le modèle PHP natif, mais le principe est similaire pour d'autres modèles tels que `twig`, `blade`, `think-template`, etc.

**Créez le fichier `app/view/404.html`**
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

**Ajoutez le code suivant dans `config/route.php` :**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // Renvoyer du JSON lors d'une requête AJAX
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // Renvoyer le modèle 404.html lors d'une requête de page
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## Personnaliser la page 500
**Créez le fichier `app/view/500.html`**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
Modèle d'erreur personnalisé :
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**Créez le fichier `app/exception/Handler.php` (créez le répertoire s'il n'existe pas)**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * Rendu et renvoi
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // Renvoyer des données JSON lors d'une requête AJAX
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // Renvoyer le modèle 500.html lors d'une requête de page
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**Configurez `config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```
