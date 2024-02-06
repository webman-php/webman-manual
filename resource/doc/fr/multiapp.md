# Applications multiples
Parfois, un projet peut être divisé en plusieurs sous-projets, par exemple, un magasin peut être divisé en trois sous-projets : le projet principal du magasin, l'interface API du magasin et le back-office de gestion du magasin, ils utilisent tous la même configuration de base de données.

webman vous permet de planifier le répertoire des applications de la manière suivante :
```
app
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
Lorsque vous accédez à l'adresse `http://127.0.0.1:8787/shop/{contrôleur}/{méthode}`, vous accédez au contrôleur et à la méthode du répertoire `app/shop/controller`.

Lorsque vous accédez à l'adresse `http://127.0.0.1:8787/api/{contrôleur}/{méthode}`, vous accédez au contrôleur et à la méthode du répertoire `app/api/controller`.

Lorsque vous accédez à l'adresse `http://127.0.0.1:8787/admin/{contrôleur}/{méthode}`, vous accédez au contrôleur et à la méthode du répertoire `app/admin/controller`.

Dans webman, il est même possible de planifier le répertoire des applications de la manière suivante.
```
app
├── controller
├── model
├── view
│
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```

Ainsi, lorsque vous accédez à l'adresse `http://127.0.0.1:8787/{contrôleur}/{méthode}`, vous accédez au contrôleur et à la méthode du répertoire `app/controller`. Lorsque le chemin commence par api ou admin, vous accédez aux contrôleurs et méthodes des répertoires correspondants.

Pour les applications multiples, l'espace de noms des classes doit être conforme à `psr4`, par exemple le fichier `app/api/controller/FooController.php` ressemble à ceci :

```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## Configuration du middleware pour les applications multiples
Parfois, vous souhaitez configurer des middlewares différents pour différentes applications. Par exemple, l'application `api` pourrait avoir besoin d'un middleware de cross-origin, tandis que l'application `admin` aurait besoin d'un middleware de vérification de connexion d'administrateur. La configuration du fichier `config/midlleware.php` pourrait ressembler à ceci :
```php
return [
    // Middleware global
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // Middleware de l'application api
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // Middleware de l'application admin
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> Les middlewares mentionnés ci-dessus peuvent ne pas exister, il s'agit simplement d'un exemple pour illustrer la configuration des middlewares par application.

L'ordre d'exécution des middlewares est le suivant : `middleware global` -> `middleware de l'application`.

Pour le développement de middlewares, consultez la section [Middleware](middleware.md).

## Configuration de la gestion des exceptions pour les applications multiples
De même, vous pouvez souhaiter configurer des classes de gestion des exceptions différentes pour différentes applications. Par exemple, en cas d'exception dans l'application `shop`, vous pourriez souhaiter afficher une page d'erreur conviviale ; en revanche, en cas d'exception dans l'application `api`, vous pourriez souhaiter renvoyer une chaîne JSON plutôt qu'une page. La configuration du fichier `config/exception.php` pour différentes applications ressemblerait à ceci :
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> Contrairement aux middlewares, chaque application ne peut configurer qu'une seule classe de gestion des exceptions.

> Les classes de gestion des exceptions mentionnées ci-dessus peuvent ne pas exister, il s'agit simplement d'un exemple pour illustrer la configuration de la gestion des exceptions par application.

Pour le développement de la gestion des exceptions, consultez la section [Gestion des exceptions](exception.md).
