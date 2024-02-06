## Vues
Webman utilise par défaut la syntaxe native de PHP comme modèle, avec les meilleures performances une fois que `opcache` est activé. En plus du modèle natif de PHP, Webman propose également des moteurs de modèle tels que [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377), et [think-template](https://www.kancloud.cn/manual/think-template/content).

## Activation de l'opcache
Lors de l'utilisation de vues, il est fortement recommandé d'activer les options `opcache.enable` et `opcache.enable_cli` dans le fichier php.ini pour optimiser les performances du moteur de modèle.

## Installation de Twig
1. Installation via composer

`composer require twig/twig`

2. Modifier la configuration dans `config/view.php` comme suit :
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
> **Remarque**
> D'autres options de configuration peuvent être transmises via l'option, par exemple :

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
1. Installation via composer

```bash
composer require psr/container ^1.1.1 webman/blade
```

2. Modifier la configuration dans `config/view.php` comme suit :
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## Installation de think-template
1. Installation via composer

`composer require topthink/think-template`

2. Modifier la configuration dans `config/view.php` comme suit :
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```
> **Remarque**
> D'autres options de configuration peuvent être transmises via l'option, par exemple :

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
Créez le fichier `app/controller/UserController.php` comme suit :

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

Créez le fichier `app/view/user/hello.html` comme suit :

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
Modifiez la configuration dans `config/view.php` comme suit :
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

Le fichier `app/controller/UserController.php` sera comme suit :

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

Le fichier `app/view/user/hello.html` sera comme suit :

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

Pour plus de documentation, consultez [Twig](https://twig.symfony.com/doc/3.x/).

## Exemple de moteur de modèle Blade
Modifiez la configuration dans `config/view.php` comme suit :
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

Le fichier `app/controller/UserController.php` sera comme suit :

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

Le fichier `app/view/user/hello.blade.php` sera comme suit :

> Notez que le modèle de Blade a une extension `.blade.php`

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

Pour plus de documentation, consultez [Blade](https://learnku.com/docs/laravel/8.x/blade/9377).

## Exemple de moteur de modèle ThinkPHP
Modifiez la configuration dans `config/view.php` comme suit :
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

Le fichier `app/controller/UserController.php` sera comme suit :

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

Le fichier `app/view/user/hello.html` sera comme suit :

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

Pour plus de documentation, consultez [think-template](https://www.kancloud.cn/manual/think-template/content).

(À suivre)
