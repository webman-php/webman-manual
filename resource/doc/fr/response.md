# Réponse

La réponse est en réalité un objet `support\Response`. Pour faciliter la création de cet objet, le webman fournit quelques fonctions d'aide.

## Renvoyer une réponse quelconque

**Exemple**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

La fonction de réponse est implémentée comme suit :
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

Vous pouvez également créer un objet `response` vide, puis utiliser les méthodes `$response->cookie()`, `$response->header()`, `$response->withHeaders()` et `$response->withBody()` pour définir le contenu à renvoyer à l'endroit approprié.
```php
public function hello(Request $request)
{
    // Créer un objet
    $response = response();
    
    // .... Logique métier omise
    
    // Définir le cookie
    $response->cookie('foo', 'valeur');
    
    // .... Logique métier omise
    
    // Définir les en-têtes HTTP
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Valeur en-tête 1',
                'X-Header-Deux' => 'Valeur en-tête 2',
            ]);

    // .... Logique métier omise

    // Définir les données à renvoyer
    $response->withBody('Données à renvoyer');
    return $response;
}
```

## Renvoyer du JSON
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
La fonction JSON est implémentée comme suit :
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```


## Renvoyer du XML
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

La fonction XML est implémentée comme suit :
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## Renvoyer une vue
Créez un fichier `app/controller/FooController.php` comme suit :

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

Créez un fichier `app/view/foo/hello.html` comme suit :

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
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

La fonction de redirection est implémentée comme suit :
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

## Réglage des en-têtes
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Valeur en-tête' 
        ]);
    }
}
```
Vous pouvez également utiliser les méthodes `header` et `withHeaders` pour définir les en-têtes individuellement ou en lot.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'Valeur en-tête 1',
            'X-Header-Deux' => 'Valeur en-tête 2',
        ]);
    }
}
```
Vous pouvez également définir les en-têtes à l'avance, puis définir les données à renvoyer à la fin.
```php
public function hello(Request $request)
{
    // Créer un objet
    $response = response();
    
    // .... Logique métier omise
  
    // Définir les en-têtes HTTP
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Valeur en-tête 1',
                'X-Header-Deux' => 'Valeur en-tête 2',
            ]);

    // .... Logique métier omise

    // Définir les données à renvoyer
    $response->withBody('Données à renvoyer');
    return $response;
}
```

## Configuration du cookie
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->cookie('foo', 'valeur');
    }
}
```

Vous pouvez également configurer le cookie à l'avance, puis définir les données à renvoyer à la fin.
```php
public function hello(Request $request)
{
    // Créer un objet
    $response = response();
    
    // .... Logique métier omise
    
    // Configurer le cookie
    $response->cookie('foo', 'valeur');
    
    // .... Logique métier omise

    // Définir les données à renvoyer
    $response->withBody('Données à renvoyer');
    return $response;
}
```

Les paramètres complets de la méthode cookie sont les suivants :

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## Renvoyer un flux de fichiers
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

- webman prend en charge l'envoi de fichiers de très grande taille
- Pour les fichiers volumineux (plus de 2 Mo), webman ne lira pas tout le fichier en une seule fois en mémoire, mais lira et enverra le fichier par segments au moment approprié
- webman optimisera la vitesse de lecture et d'envoi du fichier en fonction de la vitesse de réception du client, afin de garantir l'envoi le plus rapide du fichier tout en réduisant la consommation de mémoire au minimum
- L'envoi de données est non bloquant et n'affectera pas le traitement des autres requêtes
- La méthode file ajoutera automatiquement l'en-tête `if-modified-since` et vérifiera l'en-tête `if-modified-since` lors de la prochaine requête. Si le fichier n'a pas été modifié, il retournera directement 304 pour économiser de la bande passante
- Le fichier envoyé utilisera automatiquement les en-têtes `Content-Type` appropriés pour l'envoi au navigateur
- Si le fichier n'existe pas, il se convertira automatiquement en une réponse 404


## Télécharger un fichier
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'nomfichier.ico');
    }
}
```
La méthode download est presque identique à la méthode file, sauf que :
1. après avoir défini le nom du fichier à télécharger, le fichier sera téléchargé au lieu d'être affiché dans le navigateur
2. la méthode download ne vérifie pas l'en-tête `if-modified-since`

## Obtenir la sortie
Certaines bibliothèques écrivent le contenu du fichier directement sur la sortie standard, c'est-à-dire que les données s'affichent dans le terminal de la ligne de commande et ne sont pas envoyées au navigateur. Dans ce cas, nous devons capturer les données dans une variable à l'aide de `ob_start();` `ob_get_clean();`, puis envoyer les données au navigateur, par exemple :

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
        imagestring($im, 1, 5, 5,  'Un simple texte', $text_color);

        // Commencer la capture de la sortie
        ob_start();
        // Afficher l'image
        imagejpeg($im);
        // Récupérer le contenu de l'image
        $image = ob_get_clean();
        
        // Envoyer l'image
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
