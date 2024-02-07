## Vues

webman utilise par défaut la syntaxe PHP native en tant que modèle, offrant ainsi les meilleures performances une fois que `opcache` est activé. En plus du modèle PHP natif, webman propose également des moteurs de modèle [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377), et [think-template](https://www.kancloud.cn/manual/think-template/content).

## Activer opcache

Lors de l'utilisation de vues, il est fortement recommandé d'activer les options `opcache.enable` et `opcache.enable_cli` dans le fichier php.ini pour assurer les meilleures performances du moteur de modèle.

## Installation de Twig

1. Installation via Composer

   `composer require twig/twig`

2. Modifier la configuration `config/view.php` comme suit
   ```php
   <?php
   use support\view\Twig;

   return [
       'handler' => Twig::class
   ];
   ```
   > Remarque
   > D'autres options de configuration peuvent être transmises via l'option, par exemple
   ```php
   return [
       'handler' => Twig::class,
       'options' => [
           'debug' => false,
           'charset' => 'utf-8'
       ]
   ];
   ```

## Installation de Blade

1. Installation via Composer

   `composer require psr/container ^1.1.1 webman/blade`

2. Modifier la configuration `config/view.php` comme suit
   ```php
   <?php
   use support\view\Blade;

   return [
       'handler' => Blade::class
   ];
   ```

## Installation de think-template

1. Installation via Composer

   `composer require topthink/think-template`

2. Modifier la configuration `config/view.php` comme suit
   ```php
   <?php
   use support\view\ThinkPHP;

   return [
       'handler' => ThinkPHP::class,
   ];
   ```
   > Remarque
   > D'autres options de configuration peuvent être transmises via l'option, par exemple
   ```php
   return [
       'handler' => ThinkPHP::class,
       'options' => [
           'view_suffix' => 'html',
           'tpl_begin' => '{',
           'tpl_end' => '}'
       ]
   ];
   ```

## Exemple de moteur de modèle PHP natif

Créer le fichier `app/controller/UserController.php` comme suit
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```
Créer le fichier `app/view/user/hello.html` comme suit
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

## Exemple de moteur de modèle Twig

Modifier la configuration `config/view.php` comme suit
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
Fichier `app/controller/UserController.php` comme suit
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```
Fichier `app/view/user/hello.html` comme suit
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{name}}
</body>
</html>
```
Pour plus de documentation, voir [Twig](https://twig.symfony.com/doc/3.x/).

## Exemple de moteur de modèle Blade

Modifier la configuration `config/view.php` comme suit
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```
Fichier `app/controller/UserController.php` comme suit
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```
Fichier `app/view/user/hello.blade.php` comme suit
> Attention : la extension du modèle Blade est `.blade.php`

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{$name}}
</body>
</html>
```
Pour plus de documentation, voir [Blade](https://learnku.com/docs/laravel/8.x/blade/9377).

## Exemple de moteur de modèle ThinkPHP

Modifier la configuration `config/view.php` comme suit
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```
Fichier `app/controller/UserController.php` comme suit
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```
Fichier `app/view/user/hello.html` comme suit

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {$name}
</body>
</html>
```
Pour plus de documentation, voir [think-template](https://www.kancloud.cn/manual/think-template/content).

## Attribution de modèle

En plus d'utiliser `view(模版, 变量数组)` pour attribuer le modèle, nous pouvons également attribuer le modèle à tout endroit en appelant `View::assign()`. Par exemple :
```php
<?php
namespace app\controller;

use support\Request;
use support\View;

class UserController
{
    public function hello(Request $request)
    {
        View::assign([
            'name1' => 'valeur1',
            'name2'=> 'valeur2',
        ]);
        View::assign('name3', 'valeur3');
        return view('user/test', ['name' => 'webman']);
    }
}
```
`View::assign()` est très utile dans certains scénarios. Par exemple, si chaque page d'un système doit afficher les informations de l'utilisateur connecté dans l'en-tête, il serait fastidieux de transmettre ces informations à chaque page en utilisant `view('模版', ['user_info' => 'informations_utilisateur'])`. La solution consiste à obtenir les informations de l'utilisateur dans le middleware, puis à attribuer les informations de l'utilisateur au modèle via `View::assign()`.

## Concernant les chemins de fichier modèle

#### Contrôleur

Lorsqu'un contrôleur appelle `view('模版名',[]);`, le modèle est recherché selon les règles suivantes :

1. En l'absence de plusieurs applications, le modèle correspondant est recherché dans `app/view/`.
2. En cas de [multiples applications](multiapp.md), le modèle correspondant est recherché dans `app/应用名/view/`.

En résumé, si `$request->app` est vide, le modèle est recherché dans `app/view/`, sinon il est recherché dans `app/{$request->app}/view/`.

#### Fonction de fermeture

Étant donné que la variable `$request->app` est vide pour une fonction de fermeture qui n'appartient à aucune application, le modèle est recherché dans `app/view/`. Par exemple, lors de la définition des itinéraires dans le fichier `config/route.php` comme suit :
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
le modèle utilisé sera `app/view/user.html` (lorsque le modèle Blade est utilisé, le modèle sera `app/view/user.blade.php`).

#### Spécifier l'application

Pour permettre la réutilisation des modèles dans le cas de multiples applications, `view($template, $data, $app = null)` propose un troisième argument `$app` qui permet de spécifier le dossier d'application à partir duquel le modèle doit être chargé. Par exemple, `view('user', [], 'admin')` forcera l'utilisation du modèle dans `app/admin/view/`.

## Extension de twig

> Remarque
> Cette fonctionnalité nécessite webman-framework>=1.4.8.

Nous pouvons étendre l'instance de vue twig en utilisant le callback fourni dans la configuration `view.extension`, par exemple dans `config/view.php` comme suit :
```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // Ajouter une extension
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // Ajouter un filtre
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // Ajouter une fonction
    }
];
```

## Extension de Blade

> Remarque
> Cette fonctionnalité nécessite webman-framework>=1.4.8.

De la même manière, nous pouvons étendre l'instance de vue Blade en utilisant le callback fourni dans la configuration `view.extension`, par exemple dans `config/view.php` comme suit :
```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Ajouter des instructions à Blade
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```
## Utilisation de composants avec Blade

> **Remarque**
> Nécessite webman/blade>=1.5.2

Supposons que vous vouliez ajouter un composant Alert.

**Créez `app/view/components/Alert.php`**
```php
<?php

namespace app\view\components;

use Illuminate\View\Component;

class Alert extends Component
{
    
    public function __construct()
    {
    
    }
    
    public function render()
    {
        return view('components/alert')->rawBody();
    }
}
```

**Créez `app/view/components/alert.blade.php`**
```php
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**Dans `/config/view.php`, le code ressemblerait à ceci**
```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        $blade->component('alert', app\view\components\Alert::class);
    }
];
```

Ainsi, le composant Alert de Blade est configuré. Pour l'utiliser dans le modèle, faites comme suit
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>

<x-alert/>

</body>
</html>
```

## Extension de think-template
think-template utilise `view.options.taglib_pre_load` pour étendre les bibliothèques de balises, par exemple
```php
<?php
use support\view\ThinkPHP;
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'taglib_pre_load' => your\namspace\Taglib::class,
    ]
];
```

Pour plus de détails, voir [l'extension de balises de think-template](https://www.kancloud.cn/manual/think-template/1286424)
