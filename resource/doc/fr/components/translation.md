# Multilingue

L'utilisation de plusieurs langues est basée sur le composant [symfony/translation](https://github.com/symfony/translation).

## Installation
```php
composer require symfony/translation
```

## Création du package de langue
Par défaut, webman place le package de langue dans le répertoire `resource/translations` (veuillez le créer s'il n'existe pas) ; si vous souhaitez modifier ce répertoire, veuillez le spécifier dans le fichier `config/translation.php`. Chaque langue correspond à un sous-dossier, et par défaut, les définitions de langue sont placées dans le fichier `messages.php`. Voici un exemple :

```php
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
    // Langue de secours, utilisée lorsque la traduction n'est pas disponible dans la langue actuelle
    'fallback_locale' => ['zh_CN', 'en'],
    // Emplacement des fichiers de langue
    'path' => base_path() . '/resource/translations',
];
```

## Traduction

La traduction se fait à l'aide de la méthode `trans()`.

Créez le fichier de langue `resource/translations/zh_CN/messages.php` comme ceci :

```php
return [
    'hello' => '你好 世界！',
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
        $hello = trans('hello'); // 你好 世界！
        return response($hello);
    }
}
```

En accédant à `http://127.0.0.1:8787/user/get`, vous obtiendrez "你好 世界！"

## Changer la langue par défaut

Utilisez la méthode `locale()` pour changer de langue.

Ajoutez un fichier de langue `resource/translations/en/messages.php` comme ceci :
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
En accédant à `http://127.0.0.1:8787/user/get`, vous obtiendrez "hello world!"

Vous pouvez également utiliser le quatrième argument de la fonction `trans()` pour changer temporairement de langue. Par exemple, les deux codes ci-dessus sont équivalents à :
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Changer de langue avec le quatrième argument
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## Définir explicitement la langue pour chaque requête

La traduction est un singleton, ce qui signifie que toutes les requêtes partagent cette instance. Si une requête utilise `locale()` pour définir la langue par défaut, cela affectera toutes les requêtes ultérieures du processus. Par conséquent, il est conseillé de définir explicitement la langue pour chaque requête. Vous pouvez utiliser le middleware ci-dessous.

Créez le fichier `app/middleware/Lang.php` (créez-le s'il n'existe pas) comme ceci :

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

Ajoutez le middleware global dans `config/middleware.php` :

```php
return [
    // Middlewares globaux
    '' => [
        // ... Autres middlewares
        app\middleware\Lang::class,
    ]
];
```


## Utilisation de marqueurs de position
Parfois, un message contient des variables à traduire, par exemple :
PHP
```php
trans('hello ' . $name);
```
Dans ce cas, on utilise des marqueurs de position pour gérer cela.

Modifiez le fichier `resource/translations/zh_CN/messages.php` comme ceci :

```php
return [
    'hello' => '你好 %name%！',
```
Lors de la traduction, les données sont transmises via le deuxième paramètre pour faire correspondre les valeurs des marqueurs de position :
```php
trans('hello', ['%name%' => 'webman']); // 你好 webman！
```

## Gestion de la pluralité
Dans certaines langues, la phrase varie en fonction du nombre d'objets, par exemple `There is %count% apple`. La phrase est correcte lorsque `%count%` vaut 1, mais incorrecte lorsqu'elle est supérieure à 1.

Pour ces cas, utilisez le **pipe** (`|`) pour lister les formes plurielles.

Ajoutez une entrée `apple_count` dans le fichier de langue `resource/translations/en/messages.php` comme ceci :

```php
return [
    // ...
    'apple_count' => 'Il y a une pomme|Il y a %count% pommes',
];
```

```php
trans('apple_count', ['%count%' => 10]); // Il y a 10 pommes
```

Il est même possible de définir des plages de chiffres pour créer des règles de pluralité plus complexes :

```php
return [
    // ...
    'apple_count' => '{0} Il n\'y a pas de pommes|{1} Il y a une pomme|]1,19] Il y a %count% pommes|[20,Inf[ Il y a beaucoup de pommes'
];
```

```php
trans('apple_count', ['%count%' => 20]); // Il y a beaucoup de pommes
```

## Spécifier un fichier de langue
Par défaut, le fichier de langue est nommé `messages.php`, mais vous pouvez en créer d'autres.

Créez le fichier de langue `resource/translations/zh_CN/admin.php` comme ceci :

```php
return [
    'hello_admin' => '你好 管理员！',
];
```

Utilisez le troisième paramètre de `trans()` pour spécifier un fichier de langue (sans l'extension `.php`).

```php
trans('hello', [], 'admin', 'zh_CN'); // 你好 管理员！
```

## Pour en savoir plus
Consultez le [manuel de symfony/translation](https://symfony.com/doc/current/translation.html)
