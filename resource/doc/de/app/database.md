# Datenbank
Plugins können ihre eigene Datenbank konfigurieren. Zum Beispiel sieht der Inhalt von `plugin/foo/config/database.php` wie folgt aus:

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

Der Aufruf erfolgt über `Db::connection('plugin.{Plugin}.{Verbindungsnamen}');`, zum Beispiel:

```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

Wenn Sie die Datenbank des Hauptprojekts verwenden möchten, können Sie dies direkt tun, zum Beispiel:

```php
use support\Db;
Db::table('user')->first();
// Angenommen, das Hauptprojekt hat auch eine Admin-Verbindung konfiguriert
Db::connection('admin')->table('admin')->first();
```

## Konfiguration der Datenbank für Model

Wir können eine Basis-Klasse für Model erstellen, in der die Basis-Klasse mit `$connection` die Verbindung zur Datenbank des Plugins angibt, zum Beispiel:

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

Auf diese Weise erben alle Modelle im Plugin automatisch von Base und verwenden automatisch die Datenbank des Plugins.

## Datenbankkonfiguration wiederverwenden
Natürlich können wir die Datenbankkonfiguration des Hauptprojekts wiederverwenden. Wenn Sie beispielsweise [webman-admin](https://www.workerman.net/plugin/82) integriert haben, können Sie auch die Datenbankkonfiguration von [webman-admin](https://www.workerman.net/plugin/82) verwenden, zum Beispiel:

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
