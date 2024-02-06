# Les plugins d'application
Chaque plugin d'application est une application complète, dont le code source est situé dans le répertoire `{projet principal}/plugin`

> **Remarque**
> En utilisant la commande `php webman app-plugin:create {nom du plugin}` (nécessite webman/console>=1.2.16), vous pouvez créer localement un plugin d'application,
> par exemple, `php webman app-plugin:create cms` créera la structure de répertoires suivante :

```
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

Nous observons qu'un plugin d'application possède la même structure de répertoires et les mêmes fichiers de configuration que webman. En réalité, développer un plugin d'application est similaire au développement d'un projet webman, à quelques petites différences près.

## Espace de noms
Le répertoire du plugin et son nom suivent la spécification PSR4. Puisque les plugins sont tous situés dans le répertoire plugin, les espaces de noms commencent tous par `plugin`, par exemple `plugin\cms\app\controller\UserController`, où cms est le répertoire principal du code source du plugin.

## Accès à l'URL
Les adresses URL des plugins d'application commencent toutes par `/app`, par exemple, l'URL de `plugin\cms\app\controller\UserController` est `http://127.0.0.1:8787/app/cms/user`.

## Fichiers statiques
Les fichiers statiques sont situés dans `plugin/{plugin}/public`, par exemple, pour accéder à `http://127.0.0.1:8787/app/cms/avatar.png`, il s'agit en réalité du fichier `plugin/cms/public/avatar.png`.

## Fichiers de configuration
La configuration des plugins est similaire à celle des projets webman normaux, mais la configuration des plugins est généralement spécifique au plugin actuel et n'a généralement aucun impact sur le projet principal.
Par exemple, la valeur de `plugin.cms.app.controller_suffix` n'affecte que le suffixe du contrôleur du plugin et n'a aucun impact sur le projet principal.
Par exemple, la valeur de `plugin.cms.app.controller_reuse` n'affecte que la réutilisation du contrôleur du plugin et n'a aucun impact sur le projet principal.
Par exemple, la valeur de `plugin.cms.middleware` n'affecte que les middlewares du plugin et n'a aucun impact sur le projet principal.
Par exemple, la valeur de `plugin.cms.view` n'affecte que la vue utilisée par le plugin et n'a aucun impact sur le projet principal.
Par exemple, la valeur de `plugin.cms.container` n'affecte que le conteneur utilisé par le plugin et n'a aucun impact sur le projet principal.
Par exemple, la valeur de `plugin.cms.exception` n'affecte que la classe de gestion des exceptions du plugin et n'a aucun impact sur le projet principal.

Cependant, étant donné que les routes sont globales, la configuration des routes des plugins affecte également globalement.

## Obtenir la configuration
Pour obtenir la configuration d'un plugin spécifique, utilisez `config('plugin.{plugin}.{configuration spécifique}')`, par exemple, pour obtenir toutes les configurations de `plugin/cms/config/app.php`, utilisez `config('plugin.cms.app')`
De même, le projet principal ou d'autres plugins peuvent utiliser `config('plugin.cms.xxx')` pour obtenir la configuration du plugin cms.

## Configurations non prises en charge
Les plugins d'application ne prennent pas en charge les configurations server.php, session.php, `app.request_class`, `app.public_path`, et `app.runtime_path`.

## Base de données
Les plugins peuvent configurer leur propre base de données, par exemple le contenu de `plugin/cms/config/database.php` est le suivant :
```php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql est le nom de connexion
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'database',
            'username'    => 'username',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin est le nom de connexion
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'database',
            'username'    => 'username',
            'password'    => 'password',
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

Si vous souhaitez utiliser la base de données du projet principal, vous pouvez l'utiliser directement, par exemple
```php
use support\Db;
Db::table('user')->first();
// Supposons que le projet principal a également configuré une connexion admin
Db::connection('admin')->table('admin')->first();
```

> **Remarque**
> thinkorm fonctionne de la même manière

## Redis
L'utilisation de Redis est similaire à celle des bases de données, par exemple dans `plugin/cms/config/redis.php`
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
Pour l'utiliser :
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```

De même, si vous souhaitez réutiliser la configuration Redis du projet principal, vous pouvez l'utiliser directement, par exemple
```php
use support\Redis;
Redis::get('key');
// Supposons que le projet principal ait également configuré une connexion cache
Redis::connection('cache')->get('key');
```

## Journalisation
La manipulation des journaux est similaire à celle des bases de données, par exemple
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Si vous souhaitez réutiliser la configuration des journaux du projet principal, vous pouvez l'utiliser directement, par exemple
```php
use support\Log;
Log::info('Contenu du journal');
// Supposons que le projet principal possède une configuration de journal test
Log::channel('test')->info('Contenu du journal');
```

# Installation et désinstallation des plugins d'application
Lors de l'installation des plugins d'application, il suffit de copier le répertoire du plugin dans le répertoire `{projet principal}/plugin`, puis de recharger ou redémarrer pour que cela prenne effet.
Pour désinstaller, supprimez simplement le répertoire du plugin correspondant dans le répertoire `{projet principal}/plugin`.
