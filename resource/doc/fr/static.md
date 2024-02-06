## Traitement des fichiers statiques
webman prend en charge l'accès aux fichiers statiques qui sont tous placés dans le répertoire `public`. Par exemple, l'accès à `http://127.0.0.8787/upload/avatar.png` équivaut en réalité à accéder à `{répertoire principal du projet}/public/upload/avatar.png`.

> **Remarque**
> Depuis la version 1.4, webman prend en charge les plugins d'application. Lors de l'accès aux fichiers statiques commençant par `/app/xx/nomdufichier`, il s'agit en réalité d'accéder au répertoire `public` du plugin d'application. En d'autres termes, à partir de webman >=1.4.0, l'accès aux répertoires sous `{répertoire principal du projet}/public/app/` n'est pas pris en charge.
> Pour en savoir plus, veuillez consulter [Plugins d'application](./plugin/app.md).

### Désactiver la prise en charge des fichiers statiques
Si vous n'avez pas besoin de la prise en charge des fichiers statiques, ouvrez le fichier `config/static.php` et changez l'option `enable` sur false. Après avoir désactivé cette fonctionnalité, tous les accès aux fichiers statiques renverront un code 404.

### Changer le répertoire des fichiers statiques
Par défaut, webman utilise le répertoire public comme répertoire de fichiers statiques. Si vous souhaitez le modifier, veuillez modifier la fonction d'aide `public_path()` dans `support/helpers.php`.

### Middleware pour les fichiers statiques
webman est livré avec un middleware pour les fichiers statiques, situé dans `app/middleware/StaticFile.php`.
Parfois, nous devons effectuer certains traitements sur les fichiers statiques, par exemple ajouter des en-têtes HTTP de cross-origin pour les fichiers statiques, ou interdire l'accès aux fichiers commençant par un point (`.`).

Le contenu de `app/middleware/StaticFile.php` est similaire à ceci :
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
        // Interdire l'accès aux fichiers cachés commençant par un point
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Ajouter des en-têtes HTTP de cross-origin
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
Si vous avez besoin de ce middleware, vous devez l'activer dans l'option `middleware` du fichier `config/static.php`.
