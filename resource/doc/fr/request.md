# Explication

## Obtenir l'objet de la requête
Webman injecte automatiquement l'objet de la requête dans le premier paramètre de la méthode d'action, par exemple

**Exemple**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Obtenez le paramètre name de la requête GET, s'il n'est pas passé, retournez $default_name
        $name = $request->get('name', $default_name);
        // Retournez la chaîne au navigateur
        return response('hello ' . $name);
    }
}
```

À travers l'objet `$request`, nous pouvons obtenir n'importe quelle donnée liée à la requête.

**Parfois, nous voulons obtenir l'objet `$request` de la requête actuelle dans une autre classe, dans ce cas, nous utilisons simplement la fonction d'aide `request()`**;

## Obtenir les paramètres de la requête get

**Obtenir tout le tableau get**
```php
$request->get();
```
Si la requête ne contient pas de paramètres get, il renvoie un tableau vide.

**Obtenir une valeur spécifique du tableau get**
```php
$request->get('name');
```
S'il n'y a pas de valeur correspondante dans le tableau get, il renvoie null.

Vous pouvez également fournir une valeur par défaut en deuxième paramètre de la méthode get, qui sera renvoyée si aucune valeur correspondante n'est trouvée dans le tableau get. Par exemple :
```php
$request->get('name', 'tom');
```

## Obtenir les paramètres de la requête post
**Obtenir tout le tableau post**
```php
$request->post();
```
Si la requête ne contient pas de paramètres post, il renvoie un tableau vide.

**Obtenir une valeur spécifique du tableau post**
```php
$request->post('name');
```
S'il n'y a pas de valeur correspondante dans le tableau post, il renvoie null.

Comme pour la méthode get, vous pouvez également fournir une valeur par défaut en deuxième paramètre de la méthode post, qui sera renvoyée si aucune valeur correspondante n'est trouvée dans le tableau post. Par exemple :
```php
$request->post('name', 'tom');
```

## Obtenir le corps brut de la requête post
```php
$post = $request->rawBody();
```
Cette fonction est similaire à l'opération `file_get_contents("php://input")` dans `php-fpm`. Elle est utilisée pour obtenir le corps brut de la requête http. Cela est utile pour obtenir des données de requête post au format non `application/x-www-form-urlencoded`.

## Obtenir l'en-tête
**Obtenir tout le tableau d'en-têtes**
```php
$request->header();
```
Si la requête ne contient pas d'en-têtes, il renvoie un tableau vide. Notez que toutes les clés sont en minuscules.

**Obtenir une valeur spécifique du tableau d'en-têtes**
```php
$request->header('host');
```
Si la valeur correspondante n'est pas présente dans le tableau d'en-têtes, il renvoie null. Notez que toutes les clés sont en minuscules.

Tout comme la méthode get, vous pouvez également fournir une valeur par défaut en deuxième paramètre de la méthode header, qui sera renvoyée si aucune valeur correspondante n'est trouvée dans le tableau d'en-têtes. Par exemple :
```php
$request->header('host', 'localhost');
```

## Obtenir les cookies
**Obtenir tout le tableau de cookies**
```php
$request->cookie();
```
Si la requête ne contient pas de cookies, il renvoie un tableau vide.

**Obtenir une valeur spécifique du tableau de cookies**
```php
$request->cookie('name');
```
Si la valeur correspondante n'est pas présente dans le tableau de cookies, il renvoie null.

Tout comme la méthode get, vous pouvez également fournir une valeur par défaut en deuxième paramètre de la méthode cookie, qui sera renvoyée si aucune valeur correspondante n'est trouvée dans le tableau de cookies. Par exemple :
```php
$request->cookie('name', 'tom');
```

## Obtenir toutes les entrées
Comprend la collection `post` `get`.
```php
$request->all();
```

## Obtenir une valeur d'entrée spécifique
Obtenir une valeur spécifique de la collection `post` `get`.
```php
$request->input('name', $valeur_par_défaut);
```

## Obtenir des données d'entrée partielles
Obtenir des parties des données de la collection `post` `get`.
```php
// Obtenir un tableau composé de username et password, en ignorant les clés qui ne correspondent pas
$only = $request->only(['username', 'password']);
// Obtenir toutes les entrées sauf avatar et age
$except = $request->except(['avatar', 'age']);
```

## Obtenir des fichiers téléchargés
**Obtenir tous les fichiers téléchargés**
```php
$request->file();
```

Exemple de formulaire:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

La sortie de `$request->file()` ressemble à ceci:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
Il s'agit d'une série d'instances de `webman\Http\UploadFile`. La classe `webman\Http\UploadFile` hérite de la classe PHP intégrée [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) et fournit quelques méthodes utiles.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // Est-ce que le fichier est valide, par exemple true|false
            var_export($spl_file->getUploadExtension()); // Extension du fichier téléchargé, par exemple 'jpg'
            var_export($spl_file->getUploadMimeType()); // Type mime du fichier téléchargé, par exemple 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Obtenez le code d'erreur du téléchargement, par exemple UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Nom du fichier téléchargé, par exemple 'my-test.jpg'
            var_export($spl_file->getSize()); // Obtenir la taille du fichier, par exemple 13364, en octets
            var_export($spl_file->getPath()); // Obtenir le répertoire de téléchargement, par exemple '/tmp'
            var_export($spl_file->getRealPath()); // Obtenir le chemin du fichier temporaire, par exemple `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Remarque :**

- Une fois téléchargé, le fichier est nommé temporairement, par exemple `/tmp/workerman.upload.SRliMu`
- La taille du fichier téléchargé est limitée par la [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html), par défaut 10M, et peut être modifiée dans le fichier `config/server.php` en changeant `max_package_size` pour changer la valeur par défaut.
- Une fois la requête terminée, le fichier temporaire sera automatiquement effacé
- Si la requête ne contient pas de fichier téléchargé, `$request->file()` renvoie un tableau vide
- Les fichiers téléchargés ne prennent pas en charge la méthode `move_uploaded_file()`. Veuillez utiliser la méthode `$file->move()` comme substitution, voir l'exemple ci-dessous

### Obtenir un fichier téléchargé spécifique
```php
$request->file('avatar');
```
S'il existe, il renvoie l'instance correspondante de `webman\Http\UploadFile`, sinon il renvoie null.

**Exemple**
```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function file(Request $request)
    {
        $file = $request->file('avatar');
        if ($file && $file->isValid()) {
            $file->move(public_path().'/files/myfile.'.$file->getUploadExtension());
            return json(['code' => 0, 'msg' => 'upload success']);
        }
        return json(['code' => 1, 'msg' => 'file not found']);
    }
}
```

## Obtenir l'hôte
Obtient les informations d'hôte de la requête.
```php
$request->host();
```
Si l'adresse de la requête est sur un port non standard 80 ou 443, les informations d'hôte peuvent contenir le port, par exemple `example.com:8080`. Si vous ne voulez pas du port, vous pouvez passer `true` en premier paramètre.

```php
$request->host(true);
```

## Obtenir la méthode de la requête
```php
$request->method();
```
La valeur renvoyée peut être `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, ou `HEAD`.

## Obtenir l'uri de la requête
```php
$request->uri();
```
Renvoie l'uri de la requête, y compris la partie path et queryString.

## Obtenir le chemin de la requête
```php
$request->path();
```
Renvoie la partie path de la requête.

## Obtenir la queryString de la requête
```php
$request->queryString();
```
Renvoie la partie queryString de la requête.
## Obtenir l'URL de la requête
La méthode `url()` renvoie l'URL sans les paramètres de requête.
```php
$request->url();
```
Renvoie quelque chose comme `//www.workerman.net/workerman-chat`

La méthode `fullUrl()` renvoie l'URL avec les paramètres de requête.
```php
$request->fullUrl();
```
Renvoie quelque chose comme `//www.workerman.net/workerman-chat?type=download`

> **Remarque**
> Les méthodes `url()` et `fullUrl()` ne renvoient pas la partie du protocole (elles ne renvoient pas http ou https).
> Car dans un navigateur, l'utilisation d'une adresse qui commence par `//` reconnaîtra automatiquement le protocole du site actuel et enverra automatiquement une requête en http ou en https.

Si vous utilisez une proxy nginx, veuillez ajouter `proxy_set_header X-Forwarded-Proto $scheme;` dans la configuration nginx, [voir proxy nginx](others/nginx-proxy.md), de cette façon vous pouvez utiliser `$request->header('x-forwarded-proto');` pour vérifier si c'est du http ou du https, par exemple :
```php
echo $request->header('x-forwarded-proto'); // Renvoie http ou https
```

## Obtenir la version du protocole de la requête
```php
$request->protocolVersion();
```
Renvoie la chaîne `1.1` ou `1.0`.

## Obtenir l'identifiant de session de la requête
```php
$request->sessionId();
```
Renvoie une chaîne composée de lettres et de chiffres.

## Obtenir l'adresse IP du client de la requête
```php
$request->getRemoteIp();
```

## Obtenir le port du client de la requête
```php
$request->getRemotePort();
```

## Obtenir la véritable adresse IP du client de la requête
```php
$request->getRealIp($safe_mode=true);
```

Lorsque le projet utilise un proxy (comme nginx), l'utilisation de `$request->getRemoteIp()` renvoie souvent l'adresse IP du serveur proxy (comme `127.0.0.1` `192.168.x.x`) plutôt que l'adresse IP réelle du client. Dans ce cas, vous pouvez essayer d'utiliser `$request->getRealIp()` pour obtenir la véritable adresse IP du client.

`$request->getRealIp()` tentera d'obtenir la véritable adresse IP à partir des en-têtes HTTP `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, `via`.

> Étant donné que les en-têtes HTTP peuvent être facilement falsifiés, la méthode n'obtient pas l'adresse IP du client à 100 % de manière fiable, en particulier lorsque `$safe_mode` est désactivé. Une méthode plus fiable pour obtenir l'adresse IP réelle du client à travers un proxy est de connaître l'adresse IP sûre du serveur proxy et de savoir clairement quel en-tête HTTP contient l'adresse IP réelle. Si l'IP renvoyée par `$request->getRemoteIp()` est confirmée comme étant celle d'un serveur proxy sûr connu, alors utilisez `$request->header('en-tête HTTP contenant l'IP réelle')` pour obtenir l'adresse IP réelle.

## Obtenir l'adresse IP du serveur
```php
$request->getLocalIp();
```

## Obtenir le port du serveur
```php
$request->getLocalPort();
```

## Vérifier s'il s'agit d'une requête ajax
```php
$request->isAjax();
```

## Vérifier s'il s'agit d'une requête pjax
```php
$request->isPjax();
```

## Vérifier si une réponse JSON est attendue
```php
$request->expectsJson();
```

## Vérifier si le client accepte une réponse JSON
```php
$request->acceptJson();
```

## Obtenir le nom du plugin de la requête
Renvoie une chaîne vide `''` pour une requête non liée à un plugin.
```php
$request->plugin;
```
> Cette fonctionnalité requiert webman>=1.4.0

## Obtenir le nom de l'application de la requête
Pour une seule application, cela renvoie toujours une chaîne vide `''`, pour [plusieurs applications](multiapp.md), cela renvoie le nom de l'application.
```php
$request->app;
```
> Comme les fonctions de fermeture ne sont pas liées à une application, la requête de routes de fermeture `$request->app` renverra toujours une chaîne vide `''`
> Voir les routes de fermeture [Routes](route.md)

## Obtenir le nom de la classe du contrôleur de la requête
Obtenez le nom de la classe correspondant au contrôleur
```php
$request->controller;
```
Renvoie quelque chose comme `app\controller\IndexController`

> Comme les fonctions de fermeture ne sont pas liées à un contrôleur, la requête de routes de fermeture `$request->controller` renverra toujours une chaîne vide `''`
> Voir les routes de fermeture [Routes](route.md)

## Obtenir le nom de la méthode de la requête
Obtenez le nom de la méthode du contrôleur correspondant à la requête
```php
$request->action;
```
Renvoie quelque chose comme `index`

> Comme les fonctions de fermeture ne sont pas liées à une méthode de contrôleur, la requête de routes de fermeture `$request->action` renverra toujours une chaîne vide `''`
> Voir les routes de fermeture [Routes](route.md)
