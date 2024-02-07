# Plugins d'application
Chaque plugin d'application est une application complète dont le code source est placé dans le répertoire `{projet principal}/plugin`

> **Astuce**
> En utilisant la commande `php webman app-plugin:create {nom du plugin}` (nécessite webman/console>=1.2.16), vous pouvez créer localement un plugin d'application.
> Par exemple, `php webman app-plugin:create cms` créera la structure de répertoire suivante

```plaintext
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

Nous voyons qu'un plugin d'application a la même structure de répertoire et les mêmes fichiers de configuration que webman. En réalité, le développement d'un plugin d'application est pratiquement identique au développement d'un projet webman, il suffit de faire attention à quelques aspects.

## Espace de noms
Les répertoires et noms des plugins suivent la spécification PSR4. Comme tous les plugins sont placés dans le répertoire plugin, les espaces de noms commencent tous par plugin, par exemple `plugin\cms\app\controller\UserController`, où cms est le répertoire principal du code source du plugin.

## Accès URL
Les adresses URL des plugins d'application commencent toutes par `/app`, par exemple pour le contrôleur `plugin\cms\app\controller\UserController`, l'URL est `http://127.0.0.1:8787/app/cms/user`.

## Fichiers statiques
Les fichiers statiques sont placés dans `plugin/{plugin}/public`. Par exemple, accéder à `http://127.0.0.1:8787/app/cms/avatar.png` revient en réalité à récupérer le fichier `plugin/cms/public/avatar.png`.

## Fichiers de configuration
La configuration des plugins est identique à celle des projets webman, mais généralement la configuration d'un plugin n'a d'impact que sur ce plugin et non sur le projet principal.
Par exemple, la valeur de `plugin.cms.app.controller_suffix` n'a d'impact que sur le suffixe des contrôleurs du plugin et n'a aucun impact sur le projet principal.
De même, la valeur de `plugin.cms.app.controller_reuse` n'a d'impact que sur la réutilisation des contrôleurs du plugin et non sur le projet principal.
Il en va de même pour les autres configurations telles que `middleware` ou `view` qui n'ont d'effet que sur le plugin auquel elles sont associées.

Cependant, comme les routes sont globales, la configuration des routes du plugin affecte également l'ensemble du système.

## Obtenir la configuration
Pour obtenir la configuration d'un plugin spécifique, utilisez la méthode suivante : `config('plugin.{plugin}.{configuration spécifique}');`, par exemple pour obtenir toutes les configurations de `plugin/cms/config/app.php`, utilisez `config('plugin.cms.app')`.
De la même manière, le projet principal ou d'autres plugins peuvent utiliser `config('plugin.cms.xxx')` pour obtenir la configuration du plugin cms.

## Configurations non prises en charge
Les plugins d'application ne prennent pas en charge les configurations `server.php`, `session.php`, `app.request_class`, `app.public_path` et `app.runtime_path`.

## Base de données
Les plugins peuvent avoir leur propre configuration de base de données, par exemple le contenu de `plugin/cms/config/database.php` est le suivant :
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql est le nom de la connexion
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'database',
            'username'    => 'nom d'utilisateur',
            'password'    => 'mot de passe',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin est le nom de la connexion
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'database',
            'username'    => 'nom d'utilisateur',
            'password'    => 'mot de passe',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
Pour l'utiliser, utilisez `Db::connection('plugin.{plugin}.{nom de connexion}');`, par exemple

```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

Si vous souhaitez utiliser la base de données du projet principal, utilisez-la directement, par exemple

```php
use support\Db;
Db::table('user')->first();
// Supposons que le projet principal dispose également d'une connexion admin
Db::connection('admin')->table('admin')->first();
```
> **Astuce**
> thinkorm fonctionne de manière similaire

## Redis
L'utilisation de Redis est similaire à celle de la base de données. Par exemple, pour `plugin/cms/config/redis.php` :

```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ],
];
```
L'utilisation se fait ainsi :

```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```

De même, si vous souhaitez réutiliser la configuration Redis du projet principal :

```php
use support\Redis;
Redis::get('key');
// Supposons que le projet principal a également configuré une connexion cache
Redis::connection('cache')->get('key');
```

## Journalisation
L'utilisation de la journalisation est similaire à celle de la base de données :

```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Si vous souhaitez réutiliser la configuration de journalisation du projet principal, utilisez simplement :

```php
use support\Log;
Log::info('Contenu du journal');
// Supposons que le projet principal a configuré un journal test
Log::channel('test')->info('Contenu du journal');
```

# Installation et désinstallation des plugins d'application
Lors de l'installation d'un plugin d'application, il suffit de copier le répertoire du plugin dans le répertoire `{projet principal}/plugin`, puis de recharger ou redémarrer pour que les modifications prennent effet.
Pour désinstaller un plugin, il suffit de supprimer le répertoire du plugin correspondant dans `{projet principal}/plugin`.
