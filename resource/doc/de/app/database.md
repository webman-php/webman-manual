# Datenbank
Plugins können ihre eigene Datenbank konfigurieren, z. B. der Inhalt von `plugin/foo/config/database.php`:

```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql ist der Verbindungsnamen
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'Datenbank',
            'username'    => 'Benutzername',
            'password'    => 'Passwort',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin ist der Verbindungsnamen
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'Datenbank',
            'username'    => 'Benutzername',
            'password'    => 'Passwort',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```

Die Verwendung erfolgt über `Db::connection('plugin.{Plugin}.{Verbindungsnamen}');`, z. B.

```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

Wenn Sie die Datenbank des Hauptprojekts nutzen möchten, verwenden Sie sie direkt, z. B.

```php
use support\Db;
Db::table('user')->first();
// Angenommen, das Hauptprojekt hat auch eine Admin-Verbindung konfiguriert
Db::connection('admin')->table('admin')->first();
```

## Konfiguration der Datenbank für Models
Wir können eine Base-Klasse für das Model erstellen, in der die Variable `$connection` auf die Verbindung der Plugin-Datenbank gesetzt wird, z. B.

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

Auf diese Weise erben alle Models im Plugin automatisch von Base und verwenden somit automatisch die Datenbank des Plugins.

## Wiederverwendung der Datenbankkonfiguration
Natürlich können wir die Datenbankkonfiguration des Hauptprojekts wiederverwenden. Wenn Sie beispielsweise [webman-admin](https://www.workerman.net/plugin/82) integriert haben, können Sie auch die Datenbankkonfiguration von [webman-admin](https://www.workerman.net/plugin/82) wiederverwenden, z. B.

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
