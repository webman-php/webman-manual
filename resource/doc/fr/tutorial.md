# Exemple simple

## Retourner une chaîne de caractères
**Créer un contrôleur**

Créez le fichier `app/controller/UserController.php` comme suit

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Obtenir le paramètre "name" de la requête GET, si aucun paramètre "name" n'est passé, retourner $default_name
        $name = $request->get('name', $default_name);
        // Renvoyer une chaîne de caractères au navigateur
        return response('hello ' . $name);
    }
}
```

**Accès**

Accédez à `http://127.0.0.1:8787/user/hello?name=tom` dans le navigateur

Le navigateur affichera `hello tom`

## Retourner du JSON
Modifiez le fichier `app/controller/UserController.php` comme suit

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return json([
            'code' => 0, 
            'msg' => 'ok', 
            'data' => $name
        ]);
    }
}
```

**Accès**

Accédez à `http://127.0.0.1:8787/user/hello?name=tom` dans le navigateur

Le navigateur affichera `{"code":0,"msg":"ok","data":"tom"}`

L'utilisation de la fonction d'aide json pour renvoyer des données ajoutera automatiquement l'en-tête `Content-Type: application/json`

## Retourner du XML
De la même manière, l'utilisation de la fonction d'aide `xml($xml)` renverra une réponse `xml` avec l'en-tête `Content-Type: text/xml`.

Le paramètre `$xml` peut être une chaîne `xml` ou un objet `SimpleXMLElement`.

## Retourner du JSONP
De la même manière, l'utilisation de la fonction d'aide `jsonp($data, $callback_name = 'callback')` renverra une réponse `jsonp`.

## Retourner une vue
Modifiez le fichier `app/controller/UserController.php` comme suit

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return view('user/hello', ['name' => $name]);
    }
}
```

Créez le fichier `app/view/user/hello.html` comme suit

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

Accédez à `http://127.0.0.1:8787/user/hello?name=tom`
un page html contenant `hello tom` sera renvoyée.

Remarque : webman utilise par défaut la syntaxe PHP native comme modèle. Pour utiliser d'autres vues, voir [View](view.md)
