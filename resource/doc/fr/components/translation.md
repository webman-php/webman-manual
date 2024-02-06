# Multilingue

Le multilinguisme utilise le composant [symfony/translation](https://github.com/symfony/translation).

## Installation

```
composer require symfony/translation
```

## Création de packages de langues

Par défaut, webman place les packages de langues dans le répertoire `resource/translations` (créez-le si nécessaire). Si vous souhaitez modifier le répertoire, veuillez le faire dans `config/translation.php`. Chaque langue correspond à un sous-dossier, et les définitions de langue sont par défaut stockées dans `messages.php`. Voici un exemple :

```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```
Tous les fichiers de langue retournent un tableau, par exemple :

```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## Configuration

`config/translation.php`

```php
return [
    // Langue par défaut
    'locale' => 'zh_CN',
    // Langue de repli, essayez d'utiliser les traductions de la langue de repli si aucune traduction n'est trouvée dans la langue actuelle
    'fallback_locale' => ['zh_CN', 'en'],
    // Dossier de stockage des fichiers de langue
    'path' => base_path() . '/resource/translations',
];
```

## Traduction

La traduction se fait avec la méthode `trans()`.

Créez le fichier de langues `resource/translations/zh_CN/messages.php` comme suit :
```php
return [
    'hello' => '你好 世界!',
];
```

Créez le fichier `app/controller/UserController.php` :
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // 你好 世界!
        return response($hello);
    }
}
```

En accédant à `http://127.0.0.1:8787/user/get`, vous obtiendrez "你好 世界!".

## Changer la langue par défaut

Pour changer de langue, utilisez la méthode `locale()`.

Ajoutez un fichier de langue `resource/translations/en/messages.php` comme suit :
```php
return [
    'hello' => 'hello world!',
];
```

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Changer de langue
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
En accédant à `http://127.0.0.1:8787/user/get`, vous obtiendrez "hello world!".

Vous pouvez également utiliser le quatrième paramètre de la fonction `trans()` pour changer temporairement de langue. Par exemple, l'exemple ci-dessus est équivalent à celui ci-dessous :
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Changer de langue avec le quatrième paramètre
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## Définir explicitement la langue pour chaque requête

La traduction est un singleton, ce qui signifie que toutes les requêtes partagent la même instance. Si une requête utilise `locale()` pour définir la langue par défaut, cela affectera toutes les requêtes ultérieures du processus. Il est donc recommandé de définir explicitement la langue pour chaque requête. Par exemple, en utilisant le middleware suivant :

Créez le fichier `app/middleware/Lang.php` (créez-le si nécessaire) comme suit :
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        locale(session('lang', 'zh_CN'));
        return $handler($request);
    }
}
```

Ajoutez le middleware global dans `config/middleware.php` comme suit :
```php
return [
    // Middleware global
    '' => [
        // ... Autres middlewares omis
        app\middleware\Lang::class,
    ]
];
```

## Utilisation de placeholders

Parfois, un message contient des variables à traduire, par exemple
```php
trans('hello ' . $name);
```
Dans ce cas, nous utilisons des espaces réservés pour gérer cela.

Modifiez `resource/translations/zh_CN/messages.php` comme suit :
```php
return [
    'hello' => '你好 %name%!',
];
```
Lors de la traduction, les données correspondant aux espaces réservés sont transmises à l'aide du deuxième paramètre
```php
trans('hello', ['%name%' => 'webman']); // 你好 webman!
```

## Traitement des pluriels

Dans certaines langues, la phrase varie en fonction du nombre d'éléments, par exemple `There is %count% apple`, la phrase est correcte lorsque `%count%` vaut 1, mais incorrecte lorsque cette valeur est supérieure à 1.

Dans ce cas, nous utilisons un **pipe** (`|`) pour lister les différentes formes plurielles.

Ajoutez une nouvelle clé `apple_count` dans le fichier de langue `resource/translations/en/messages.php` :
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

Il est même possible de spécifier une plage de nombres pour créer des règles plurielles plus complexes :
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## Spécifier le fichier de langue

Par défaut, le fichier de langue s'appelle `messages.php`, mais en réalité, vous pouvez créer des fichiers de langue avec d'autres noms.

Créez un fichier de langue `resource/translations/zh_CN/admin.php` comme suit :
```php
return [
    'hello_admin' => '你好 管理员!',
];
```

Spécifiez le fichier de langue en utilisant le troisième paramètre de `trans()` (omettez l'extension `.php`).
```php
trans('hello', [], 'admin', 'zh_CN'); // 你好 管理员!
```

## Pour plus d'informations

Consultez le [manuel de symfony/translation](https://symfony.com/doc/current/translation.html)
