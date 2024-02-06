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
        // Obtenir le paramètre name de la requête get, s'il n'est pas passé, retourner $default_name
        $name = $request->get('name', $default_name);
        // Retourner une chaîne à afficher dans le navigateur
        return response('hello ' . $name);
    }
}
```

Avec l'objet `$request`, nous pouvons obtenir n'importe quelle donnée liée à la requête.

**Parfois, nous souhaitons obtenir l'objet `$request` dans d'autres classes, dans ce cas, il suffit d'utiliser la fonction d'aide `request()`**.

## Obtenir les paramètres de la requête GET

**Obtenir l'ensemble du tableau GET**
```php
$request->get();
```
Si la requête ne contient pas de paramètres GET, cela renvoie un tableau vide.

**Obtenir une valeur spécifique du tableau GET**
```php
$request->get('name');
```
Si la valeur spécifiée n'est pas présente dans le tableau GET, cela renvoie null.

Vous pouvez également fournir une valeur par défaut au deuxième paramètre de la méthode get. Si aucune valeur correspondante n'est trouvée dans le tableau GET, la valeur par défaut est renvoyée. Par exemple :
```php
$request->get('name', 'tom');
```

## Obtenir les paramètres de la requête POST
**Obtenir l'ensemble du tableau POST**
```php
$request->post();
```
Si la requête ne contient pas de paramètres POST, cela renvoie un tableau vide.

**Obtenir une valeur spécifique du tableau POST**
```php
$request->post('name');
```
Si la valeur spécifiée n'est pas présente dans le tableau POST, cela renvoie null.

Comme pour la méthode get, vous pouvez fournir une valeur par défaut au deuxième paramètre de la méthode post. Si aucune valeur correspondante n'est trouvée dans le tableau POST, la valeur par défaut est renvoyée. Par exemple :
```php
$request->post('name', 'tom');
```

## Obtenir le corps brut de la requête POST
```php
$post = $request->rawBody();
```
Cette fonction est similaire à l'opération `file_get_contents("php://input");` dans `php-fpm`, elle est utilisée pour obtenir le corps brut de la requête HTTP. Cela est très utile pour obtenir des données de requêtes POST au format non `application/x-www-form-urlencoded`. 

## Obtenir l'en-tête
**Obtenir l'ensemble des en-têtes**
```php
$request->header();
```
Si la requête ne contient pas d'en-tête, cela renvoie un tableau vide. Veuillez noter que toutes les clés sont en minuscules.

**Obtenir une valeur spécifique de l'en-tête**
```php
$request->header('host');
```
Si la clé spécifiée n'est pas présente dans les en-têtes, cela renvoie null. Veuillez noter que toutes les clés sont en minuscules.

Comme pour la méthode get, vous pouvez également fournir une valeur par défaut au deuxième paramètre de la méthode header. Si aucune valeur correspondante n'est trouvée dans les en-têtes, la valeur par défaut est renvoyée. Par exemple :
```php
$request->header('host', 'localhost');
```

## Obtenir les cookies
**Obtenir l'ensemble des cookies**
```php
$request->cookie();
```
Si la requête ne contient pas de cookie, cela renvoie un tableau vide.

**Obtenir une valeur spécifique dans les cookies**
```php
$request->cookie('name');
```
Si la valeur spécifiée n'est pas présente dans les cookies, cela renvoie null.

Comme pour la méthode get, vous pouvez également fournir une valeur par défaut au deuxième paramètre de la méthode cookie. Si aucune valeur correspondante n'est trouvée dans les cookies, la valeur par défaut est renvoyée. Par exemple :
```php
$request->cookie('name', 'tom');
```

## Obtenir toutes les entrées
Cela inclut la collection `post` et `get`.
```php
$request->all();
```

## Obtenir une valeur spécifique d'entrée
Obtenir une valeur spécifique de la collection `post` et `get`.
```php
$request->input('name', $valeur_par_défaut);
```

## Obtenir des données d'entrée partielle
Obtenir des données d'entrée partielle à partir des collections `post` et `get`.
```php
// Obtenir un tableau composé de username et password, en ignorant les clés manquantes
$only = $request->only(['username', 'password']);
// Obtenir toutes les entrées sauf avatar et age
$except = $request->except(['avatar', 'age']);
```

## Obtenir les fichiers téléchargés
**Obtenir l'ensemble des fichiers téléchargés**
```php
$request->file();
```

Dans un formulaire similaire à :
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

La sortie de `$request->file()` est similaire à :
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
Ceci est un tableau d'instances de `webman\Http\UploadFile`. La classe `webman\Http\UploadFile` étend la classe intégrée [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) en PHP, et fournit quelques méthodes pratiques.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // Vérifie si le fichier est valide, vrai ou faux par exemple
            var_export($spl_file->getUploadExtension()); // Retourne l'extension de téléchargement du fichier, par exemple 'jpg'
            var_export($spl_file->getUploadMimeType()); // Retourne le type MIME de téléchargement du fichier, par exemple 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Obtient le code d'erreur de téléchargement, par exemple UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Retourne le nom du fichier téléchargé, par exemple 'my-test.jpg'
            var_export($spl_file->getSize()); // Obtient la taille du fichier, par exemple 13364 octets
            var_export($spl_file->getPath()); // Obtient le répertoire de téléchargement, par exemple '/tmp'
            var_export($spl_file->getRealPath()); // Obtient le chemin du fichier temporaire, par exemple `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Note :**

- Les fichiers téléchargés sont nommés avec un nom de fichier temporaire, par exemple `/tmp/workerman.upload.SRliMu`
- La taille des fichiers téléchargés est limitée par [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html), par défaut 10M, vous pouvez modifier la valeur par défaut en modifiant `max_package_size` dans le fichier `config/server.php`.
- Une fois la requête terminée, les fichiers temporaires sont automatiquement supprimés
- Si aucun fichier n'est téléchargé dans la requête, `$request->file()` retourne un tableau vide
- La méthode `move_uploaded_file()` n'est pas prise en charge pour les fichiers téléchargés, veuillez utiliser la méthode `$file->move()` comme indiqué dans l'exemple ci-dessous

### Obtenir un fichier téléchargé spécifique
```php
$request->file('avatar');
```
Si le fichier existe, cela retourne une instance de `webman\Http\UploadFile` correspondant au fichier. Sinon, cela retourne null.

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
Obtenir les informations d'hôte de la requête.
```php
$request->host();
```
Si l'adresse de la requête est sur un port non standard 80 ou 443, les informations d'hôte peuvent inclure le port, par exemple `example.com:8080`. Si le port n'est pas nécessaire, le premier paramètre peut être passé en tant que `true`.

```php
$request->host(true);
```

## Obtenir la méthode de la requête
```php
 $request->method();
```
La valeur renvoyée peut être `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, ou `HEAD`.

## Obtenir l'URI de la requête
```php
$request->uri();
```
Renvoie l'URI de la requête, y compris la partie path et queryString.

## Obtenir le chemin de la requête

```php
$request->path();
```
Renvoie la partie path de la requête.

## Obtenir la queryString de la requête

```php
$request->queryString();
```
Renvoie la queryString de la requête.

## Obtenir l'URL de la requête
La méthode `url()` renvoie l'URL sans les paramètres de la `Query`.
```php
$request->url();
```
Renvoie quelque chose comme `//www.workerman.net/workerman-chat`

La méthode `fullUrl()` renvoie l'URL avec les paramètres de la `Query`.
```php
$request->fullUrl();
```
Renvoie quelque chose comme `//www.workerman.net/workerman-chat?type=download`

> **Remarque**
> `url()` et `fullUrl()` n'incluent pas la partie protocole (http ou https). Cela est dû au fait que l'utilisation de `//example.com` dans le navigateur reconnaît automatiquement le protocole du site, en lançant automatiquement la requête en http ou https.

Si vous utilisez un proxy nginx, veuillez ajouter `proxy_set_header X-Forwarded-Proto $scheme;` à la configuration de nginx, [référence au proxy nginx](others/nginx-proxy.md),
ainsi vous pouvez utiliser `$request->header('x-forwarded-proto');` pour déterminer s'il s'agit du protocole http ou https, par exemple :
```php
echo $request->header('x-forwarded-proto'); // Renvoie http ou https
```

## Obtenir la version HTTP de la requête

```php
$request->protocolVersion();
```
Renvoie une chaîne de caractères `1.1` ou`1.0`.

## Obtenir l'ID de session de la requête

```php
$request->sessionId();
```
Renvoie une chaîne composée de lettres et de chiffres

## Obtenir l'adresse IP du client de la requête
```php
$request->getRemoteIp();
```

## Obtenir le port client de la requête
```php
$request->getRemotePort();
```
## Obtenir l'adresse IP réelle du client

```php
$request->getRealIp($safe_mode=true);
```

Lorsque le projet utilise un proxy (comme nginx), l'utilisation de `$request->getRemoteIp()` renvoie souvent l'adresse IP du serveur proxy (tel que `127.0.0.1` `192.168.x.x`) au lieu de l'adresse IP réelle du client. Dans ce cas, vous pouvez essayer d'utiliser `$request->getRealIp()` pour obtenir l'adresse IP réelle du client.

`$request->getRealIp()` essaie d'obtenir l'adresse IP réelle à partir des en-têtes HTTP `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, `via`.

> Étant donné que les en-têtes HTTP peuvent être facilement falsifiés, l'adresse IP du client obtenue par cette méthode n'est pas fiable à 100%, surtout lorsque `$safe_mode` est défini sur false. Une méthode plus fiable pour obtenir l'adresse IP réelle du client via un proxy est de connaître l'adresse IP sécurisée du serveur proxy et de savoir exactement quel en-tête HTTP contient l'adresse IP réelle. Si l'adresse IP renvoyée par `$request->getRemoteIp()` est confirmée comme étant celle du serveur proxy sécurisé connu, alors vous pouvez utiliser `$request->header('nom_de_l_en_tête_contenant_l_adresse_IP_réelle')` pour obtenir l'adresse IP réelle.


## Obtenir l'adresse IP du serveur

```php
$request->getLocalIp();
```

## Obtenir le port du serveur

```php
$request->getLocalPort();
```

## Vérifier s'il s'agit d'une requête Ajax

```php
$request->isAjax();
```

## Vérifier s'il s'agit d'une requête Pjax

```php
$request->isPjax();
```

## Vérifier s'il s'agit d'une demande de retour JSON

```php
$request->expectsJson();
```

## Vérifier si le client accepte le retour JSON

```php
$request->acceptJson();
```

## Obtenir le nom du plugin de la requête
Pour une requête qui ne provient pas d'un plugin, retourne une chaîne vide `''`.
```php
$request->plugin;
```
> Cette fonctionnalité nécessite webman>=1.4.0

## Obtenir le nom de l'application de la requête
Pour une seule application, cela retourne toujours une chaîne vide `''`, et pour [plusieurs applications](multiapp.md), cela retourne le nom de l'application.
```php
$request->app;
```

> Étant donné que les fonctions de fermeture ne sont pas associées à une application, la requête provenant d'une route de fermeture retourne toujours une chaîne vide `''`.
> Voir la route de fermeture dans [Route](route.md)

## Obtenir le nom de la classe du contrôleur de la requête
Obtient le nom de la classe correspondant au contrôleur
```php
$request->controller;
```
Renvoie quelque chose comme `app\controller\IndexController`

> Étant donné que les fonctions de fermeture ne sont pas associées à un contrôleur, la requête provenant d'une route de fermeture retourne toujours une chaîne vide `''`.
> Voir la route de fermeture dans [Route](route.md)

## Obtenir le nom de la méthode de la requête
Obtient le nom de la méthode du contrôleur correspondant à la requête
```php
$request->action;
```
Renvoie quelque chose comme `index`

> Étant donné que les fonctions de fermeture ne sont pas associées à une méthode de contrôleur, la requête provenant d'une route de fermeture retourne toujours une chaîne vide `''`.
> Voir la route de fermeture dans [Route](route.md)
