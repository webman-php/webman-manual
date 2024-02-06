## Personnalisation du code 404
Lorsque webman rencontre une erreur 404, il renvoie automatiquement le contenu du fichier `public/404.html`, ce qui permet aux développeurs de modifier directement le fichier `public/404.html`.

Si vous souhaitez contrôler dynamiquement le contenu de l'erreur 404, par exemple, retourner des données JSON lors d'une requête ajax `{"code:"404", "msg":"404 introuvable"}`, ou retourner un modèle `app/view/404.html` lors d'une demande de page, veuillez vous référer à l'exemple ci-dessous:

> L'exemple suivant est basé sur un modèle PHP natif, mais le principe est similaire pour d'autres modèles tels que `twig`, `blade`, `think-template`.

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

**Ajoutez le code suivant dans `config/route.php`：**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // Retourner du JSON lors d'une requête ajax
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 introuvable']);
    }
    // Retourner le modèle 404.html lors d'une demande de page
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## Personnalisation du code 500
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

**Créez le fichier** `app/exception/Handler.php` (créez le répertoire s'il n'existe pas)**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * Rendu de la réponse
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // Retourner des données JSON lors d'une requête ajax
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // Retourner le modèle 500.html lors d'une demande de page
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
