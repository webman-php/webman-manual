# Base de données
Les plugins peuvent configurer leur propre base de données, par exemple le contenu de `plugin/foo/config/database.php` est le suivant :
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql est le nom de la connexion
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '数据库',
            'username'    => '用户名',
            'password'    => '密码',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin est le nom de la connexion
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '数据库',
            'username'    => '用户名',
            'password'    => '密码',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
Il est référencé comme suit : `Db::connection('plugin.{插件}.{连接名}');`, par exemple
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

Si vous souhaitez utiliser la base de données principale, vous pouvez l'utiliser directement, par exemple
```php
use support\Db;
Db::table('user')->first();
// Suppose que le projet principal a également configuré une connexion admin
Db::connection('admin')->table('admin')->first();
```

## Configuration de la base de données pour le modèle

Nous pouvons créer une classe Base pour le modèle, la classe Base utilise `$connection` pour spécifier la connexion à la base de données du plugin, par exemple

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

De cette façon, tous les modèles dans le plugin héritent de Base et utilisent automatiquement la base de données du plugin.

## Réutiliser la configuration de la base de données
Bien sûr, nous pouvons réutiliser la configuration de la base de données principale. Si vous utilisez [webman-admin](https://www.workerman.net/plugin/82), vous pouvez également réutiliser la configuration de la base de données de [webman-admin](https://www.workerman.net/plugin/82), par exemple
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
