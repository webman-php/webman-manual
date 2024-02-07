## Traitement des fichiers statiques
webman prend en charge l'accès aux fichiers statiques, qui sont tous situés dans le répertoire `public`. Par exemple, lors de l'accès à `http://127.0.0.8787/upload/avatar.png`, il s'agit en réalité de l'accès à `{répertoire principal du projet}/public/upload/avatar.png`.

> **Remarque**
> À partir de la version 1.4, webman prend en charge les plugins d'application. L'accès aux fichiers statiques commençant par `/app/xx/nom_du_fichier` correspond en réalité à l'accès au répertoire `public` du plugin d'application. Autrement dit, à partir de webman >=1.4.0, l'accès au répertoire `{répertoire principal du projet}/public/app/` n'est pas pris en charge.
> Pour plus d'informations, veuillez consulter le document sur les [plugins d'application](./plugin/app.md).

### Désactiver la prise en charge des fichiers statiques
Si la prise en charge des fichiers statiques n'est pas nécessaire, modifiez le fichier `config/static.php` pour passer l'option `enable` à `false`. Une fois la prise en charge désactivée, l'accès à tous les fichiers statiques renverra un code 404.

### Changement du répertoire des fichiers statiques
Par défaut, webman utilise le répertoire public comme répertoire des fichiers statiques. Si vous souhaitez le modifier, veuillez modifier la fonction d'assistance `public_path()` dans le fichier `support/helpers.php`.

### Middleware pour les fichiers statiques
webman inclut par défaut un middleware pour les fichiers statiques, situé dans `app/middleware/StaticFile.php`.
Parfois, nous devons effectuer des traitements sur les fichiers statiques, par exemple ajouter des en-têtes HTTP de type Cross-Origin Resource Sharing (CORS) ou interdire l'accès aux fichiers commençant par un point (`.`). Cela peut être réalisé à l'aide de ce middleware.

Le contenu de `app/middleware/StaticFile.php` est similaire à ce qui suit :
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // Interdire l'accès aux fichiers masqués commençant par un point
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Ajouter des en-têtes HTTP CORS
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
Si ce middleware est nécessaire, il doit être activé dans l'option `middleware` du fichier `config/static.php`.
