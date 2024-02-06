# Exemple simple

## Renvoyer une chaîne de caractères
**Créer un contrôleur**

Créez un fichier `app/controller/UserController.php` comme suit

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Obtenez le paramètre nom de la requête GET, si aucun paramètre nom n'est passé, retournez $default_name
        $name = $request->get('name', $default_name);
        // Renvoyer une chaîne de caractères au navigateur
        return response('Bonjour ' . $name);
    }
}
```

**Accès**

Accédez à `http://127.0.0.1:8787/user/hello?name=tom` dans votre navigateur

Le navigateur renverra `Bonjour tom`

## Renvoyer du JSON
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

Accédez à `http://127.0.0.1:8787/user/hello?name=tom` dans votre navigateur

Le navigateur renverra `{"code":0,"msg":"ok","data":"tom""}`

L'utilisation de la fonction d'aide json pour renvoyer des données ajoutera automatiquement l'en-tête `Content-Type: application/json`

## Renvoyer du XML
De même, l'utilisation de la fonction d'aide `xml($xml)` renverra une réponse `xml` avec l'en-tête `Content-Type: text/xml`.

Le paramètre `$xml` peut être une chaîne `xml` ou un objet `SimpleXMLElement`

## Renvoyer du JSONP
De même, l'utilisation de la fonction d'aide `jsonp($data, $callback_name = 'callback')` renverra une réponse `jsonp`.

## Renvoyer une vue
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

Créez un fichier `app/view/user/hello.html` comme suit

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
Bonjour <?=htmlspecialchars($name)?>
</body>
</html>
```

Accédez à `http://127.0.0.1:8787/user/hello?name=tom` dans votre navigateur
Un contenu `Bonjour tom` sera renvoyé dans une page html.

Remarque : webman utilise par défaut la syntaxe native de PHP comme modèle. Si vous souhaitez utiliser d'autres vues, consultez [View](view.md).
