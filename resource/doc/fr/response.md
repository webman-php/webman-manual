# Réponse
La réponse est en fait un objet `support\Response`. Pour faciliter la création de cet objet, webman fournit quelques fonctions d'aide.

## Retourner une réponse arbitraire

**Exemple**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('bonjour webman');
    }
}
```

La fonction de réponse est implémentée comme suit：
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

Vous pouvez également d'abord créer un objet `response` vide, puis utiliser `$response->cookie()`, `$response->header()`, `$response->withHeaders()`, `$response->withBody()` pour définir le contenu de retour à l'endroit approprié.
```php
public function hello(Request $request)
{
    // Créer un objet
    $response = response();
    
    // .... Logique métier omise
    
    // Définir un cookie
    $response->cookie('foo', 'valeur');
    
    // .... Logique métier omise
    
    // Définir les en-têtes HTTP
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Valeur d'en-tête 1',
                'X-Header-Tow' => 'Valeur d'en-tête 2',
            ]);

    // .... Logique métier omise

    // Définir les données à retourner
    $response->withBody('Données à retourner');
    return $response;
}
```

## Retourner du JSON
**Exemple**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```
La fonction json est implémentée comme suit：
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```


## Retourner du XML
**Exemple**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        $xml = <<<XML
               <?xml version='1.0' standalone='yes'?>
               <values>
                   <truevalue>1</truevalue>
                   <falsevalue>0</falsevalue>
               </values>
               XML;
        return xml($xml);
    }
}
```
La fonction xml est implémentée comme suit：
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```


## Retourner une vue
Créez un fichier `app/controller/FooController.php` comme suit:

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return view('foo/hello', ['name' => 'webman']);
    }
}
```

Créez un fichier `app/view/foo/hello.html` comme suit:

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
bonjour <?=htmlspecialchars($name)?>
</body>
</html>
```

## Redirection
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return redirect('/user');
    }
}
```
La fonction de redirection est implémentée comme suit：
```php
function redirect($location, $status = 302, $headers = [])
{
    $response = new Response($status, ['Location' => $location]);
    if (!empty($headers)) {
        $response->withHeaders($headers);
    }
    return $response;
}
```

## Configuration d'en-tête
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('bonjour webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Valeur de l'en-tête' 
        ]);
    }
}
```
Vous pouvez également utiliser les méthodes `header` et `withHeaders` pour définir individuellement ou en lot les en-têtes.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('bonjour webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'Valeur d'en-tête 1',
            'X-Header-Tow' => 'Valeur d'en-tête 2',
        ]);
    }
}
```
Vous pouvez également définir les en-têtes à l'avance, puis définir les données à retourner à la fin.
```php
public function hello(Request $request)
{
    // Créer un objet
    $response = response();
    
    // .... Logique métier omise
  
    // Définir les en-têtes HTTP
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Valeur d'en-tête 1',
                'X-Header-Tow' => 'Valeur d'en-tête 2',
            ]);

    // .... Logique métier omise

    // Définir les données à retourner
    $response->withBody('Données à retourner');
    return $response;
}
```

## Configuration des cookies
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('bonjour webman')
        ->cookie('foo', 'valeur');
    }
}
```
Vous pouvez également définir les cookies à l'avance, puis définir les données à retourner à la fin.
```php
public function hello(Request $request)
{
    // Créer un objet
    $response = response();
    
    // .... Logique métier omise
    
    // Définir un cookie
    $response->cookie('foo', 'valeur');
    
    // .... Logique métier omise

    // Définir les données à retourner
    $response->withBody('Données à retourner');
    return $response;
}
```
Les paramètres complets de la méthode `cookie` sont les suivants：
`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## Retourner un flux de fichiers
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->file(public_path() . '/favicon.ico');
    }
}
```

- webman prend en charge l'envoi de fichiers très volumineux
- Pour les fichiers volumineux (plus de 2 Mo), webman ne lira pas tout le fichier en mémoire une seule fois, mais lira et enverra le fichier par segments au moment approprié
- webman optimisera la vitesse de lecture et d'envoi du fichier en fonction de la vitesse de réception du client, garantissant l'envoi le plus rapide du fichier tout en réduisant l'utilisation de la mémoire au minimum
- L'envoi de données est non bloquant et n'affecte pas le traitement d'autres requêtes
- La méthode file ajoutera automatiquement l'en-tête `if-modified-since` et vérifiera cet en-tête lors de la prochaine requête ; si le fichier n'a pas été modifié, il renverra directement le code 304 pour économiser de la bande passante
- Le fichier envoyé sera automatiquement utilisé avec l'en-tête `Content-Type` approprié pour être envoyé au navigateur
- Si le fichier n'existe pas, il sera automatiquement converti en réponse 404


## Télécharger des fichiers
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'nom_du_fichier.ico');
    }
}
```
La méthode de téléchargement est presque identique à la méthode de fichier, la différence est que
1. après avoir configuré le nom du fichier à télécharger, le fichier sera téléchargé au lieu d'être affiché dans le navigateur
2. la méthode de téléchargement ne vérifiera pas l'en-tête `if-modified-since`
## Obtenir la sortie
Certains librairies impriment directement le contenu d'un fichier dans la sortie standard, c'est-à-dire que les données s'affichent dans le terminal de la ligne de commande et ne sont pas envoyées au navigateur. Dans ce cas, nous devons utiliser `ob_start();` et `ob_get_clean();` pour capturer les données dans une variable, puis les envoyer au navigateur, par exemple :

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // Créer une image
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

        // Commencer l'obtention de la sortie
        ob_start();
        // Sortir l'image
        imagejpeg($im);
        // Obtenir le contenu de l'image
        $image = ob_get_clean();
        
        // Envoyer l'image
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
