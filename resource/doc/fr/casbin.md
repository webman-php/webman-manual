# Bibliothèque de contrôle d'accès Casbin pour webman-permission

## Description

Il est basé sur [PHP-Casbin](https://github.com/php-casbin/php-casbin), un framework de contrôle d'accès open source puissant et efficace, prenant en charge des modèles de contrôle d'accès tels que `ACL`, `RBAC`, `ABAC`, etc.

## Adresse du projet

https://github.com/Tinywan/webman-permission

## Installation

```php
composer require tinywan/webman-permission
```
> Cette extension nécessite PHP 7.1+ et [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998). Manuel officiel : https://www.workerman.net/doc/webman#/db/others

## Configuration

### Enregistrement du service
Créez un fichier de configuration `config/bootstrap.php` avec un contenu similaire à :
```php
    // ...
    webman\permission\Permission::class,
```
### Fichier de configuration du modèle

Créez un fichier de configuration `config/casbin-basic-model.conf` avec un contenu similaire à :
```conf
[request_definition]
r = sub, obj, act

[policy_definition]
p = sub, obj, act

[role_definition]
g = _, _

[policy_effect]
e = some(where (p.eft == allow))

[matchers]
m = g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act
```
### Fichier de configuration de la politique

Créez un fichier de configuration `config/permission.php` avec un contenu similaire à :
```php
<?php

return [
    /*
     * Autorisations par défaut
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * Configuration du modèle
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // Adaptateur .
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * Configuration de la base de données.
            */
            'database' => [
                // Nom de la connexion à la base de données, laisser vide pour utiliser la configuration par défaut.
                'connection' => '',
                // Nom de la table des règles (sans le préfixe de la table)
                'rules_name' => 'rule',
                // Nom complet de la table des règles.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## Démarrage rapide

```php
use webman\permission\Permission;

// Ajoute des autorisations à un utilisateur
Permission::addPermissionForUser('eve', 'articles', 'read');
// Ajoute un rôle pour un utilisateur
Permission::addRoleForUser('eve', 'writer');
// Ajoute des autorisations à une règle
Permission::addPolicy('writer', 'articles','edit');
```

Vous pouvez vérifier si l'utilisateur a une telle autorisation

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // autorise eve à modifier les articles
} else {
    // refuser la demande, afficher une erreur
}
````

## Middleware d'autorisation

Créez le fichier `app/middleware/AuthorizationMiddleware.php` (créez le répertoire si nécessaire) avec le contenu suivant :
```php
<?php

/**
 * Middleware d'autorisation
 * @author ShaoBo Wan (Tinywan)
 * @datetime 2021/09/07 14:15
 */

declare(strict_types=1);

namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use Casbin\Exceptions\CasbinException;
use webman\permission\Permission;

class AuthorizationMiddleware implements MiddlewareInterface
{
	public function process(Request $request, callable $next): Response
	{
		$uri = $request->path();
		try {
			$userId = 10086;
			$action = $request->method();
			if (!Permission::enforce((string) $userId, $uri, strtoupper($action))) {
				throw new \Exception('Désolé, vous n'avez pas l'autorisation d'accéder à cette interface.');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('Erreur d'autorisation' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

Ajoutez le middleware global dans `config/middleware.php` comme suit :

```php
return [
    // Middleware global
    '' => [
        // ... autres middlewares
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## Remerciements

[Casbin](https://github.com/php-casbin/php-casbin), vous pouvez consulter toute la documentation sur son [site officiel](https://casbin.org/).

## Licence

Ce projet est sous licence [Apache 2.0](LICENSE).
