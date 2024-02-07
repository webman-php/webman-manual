# Base de données
Les plugins peuvent configurer leur propre base de données, par exemple le contenu de `plugin/foo/config/database.php` est comme suit :
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql est le nom de la connexion
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'base_de_données',
            'username'    => 'nom_utilisateur',
            'password'    => 'mot_de_passe',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin est le nom de la connexion
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'base_de_données',
            'username'    => 'nom_utilisateur',
            'password'    => 'mot_de_passe',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
La méthode d'appel est `Db::connection('plugin.{plugin}.{nom_de_connexion}');`, par exemple
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('utilisateur')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

Si vous souhaitez utiliser la base de données du projet principal, vous pouvez simplement l'utiliser, par exemple
```php
use support\Db;
Db::table('utilisateur')->first();
// Suppose que le projet principal a également configuré une connexion admin
Db::connection('admin')->table('admin')->first();
```

## Configurer la base de données pour un modèle

Vous pouvez créer une classe de base pour un modèle, la classe base utilise `$connection` pour spécifier la connexion à la base de données du plugin, par exemple

```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.foo.mysql';

}
```

De cette façon, tous les modèles du plugin héritent de la classe `Base` et utilisent automatiquement la base de données du plugin.

## Réutilisation de la configuration de la base de données

Bien entendu, vous pouvez réutiliser la configuration de la base de données du projet principal. Si vous avez intégré [webman-admin](https://www.workerman.net/plugin/82), vous pouvez également réutiliser la configuration de la base de données de [webman-admin](https://www.workerman.net/plugin/82), par exemple
```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.admin.mysql';

}
```
