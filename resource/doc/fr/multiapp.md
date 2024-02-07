# Applications multiples
Parfois, un projet peut être divisé en plusieurs sous-projets, par exemple, une boutique en ligne peut être divisée en trois sous-projets : la principale boutique en ligne, l'API de la boutique en ligne et le tableau de bord de gestion de la boutique en ligne. Ils utilisent tous la même configuration de base de données.

webman vous permet de structurer votre répertoire d'applications de la manière suivante :
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
Lorsque vous accédez à l'adresse `http://127.0.0.1:8787/shop/{controller}/{method}`, vous accédez au contrôleur et à la méthode situés dans `app/shop/controller`.

Lorsque vous accédez à l'adresse `http://127.0.0.1:8787/api/{controller}/{method}`, vous accédez au contrôleur et à la méthode situés dans `app/api/controller`.

Lorsque vous accédez à l'adresse `http://127.0.0.1:8787/admin/{controller}/{method}`, vous accédez au contrôleur et à la méthode situés dans `app/admin/controller`.

Dans webman, vous pouvez même organiser votre répertoire d'applications de la manière suivante :
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
Ainsi, lorsque l'adresse est `http://127.0.0.1:8787/{controller}/{method}`, vous accédez au contrôleur et à la méthode situés dans `app/controller`. Lorsque le chemin commence par "api" ou "admin", vous accédez aux contrôleurs et méthodes situés dans les répertoires correspondants.

Pour les applications multiples, l'espace de noms des classes doit être conforme à `PSR4`, par exemple le fichier `app/api/controller/FooController.php` ressemblera à ceci :
```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}
```

## Configuration des middleware pour les applications multiples
Parfois, vous pouvez souhaiter configurer différents middlewares pour différentes applications. Par exemple, l'application `api` peut nécessiter un middleware de contrôle d'accès, tandis que l'application `admin` peut nécessiter un middleware de vérification de la connexion de l'administrateur. La configuration des middleware dans `config/midlleware.php` pourrait ressembler à ceci :
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
> Les middlewares mentionnés ci-dessus peuvent ne pas exister, cela est simplement un exemple pour illustrer comment configurer les middlewares pour chaque application.

L'ordre d'exécution des middlewares est `global middleware` -> `application middleware`.

Pour le développement de middlewares, veuillez consulter la section [middlewares](middleware.md).

## Configuration de la gestion des exceptions pour les applications multiples
De même, vous pouvez souhaiter configurer des classes de gestion des exceptions différentes pour différentes applications. Par exemple, en cas d'exception dans l'application `shop`, vous souhaiterez peut-être afficher une page d'erreur conviviale, tandis que dans l'application `api`, vous souhaiterez peut-être renvoyer une chaîne JSON. La configuration des classes de gestion des exceptions pour différentes applications dans le fichier de configuration `config/exception.php` ressemblera à ceci :
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> Contrairement aux middlewares, chaque application ne peut configurer qu'une seule classe de gestion des exceptions.

> Les classes de gestion des exceptions mentionnées ci-dessus peuvent ne pas exister, cela est simplement un exemple pour illustrer comment configurer les classes de gestion des exceptions pour chaque application.

Pour le développement de la gestion des exceptions, veuillez consulter la section [exceptions](exception.md).
