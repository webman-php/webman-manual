# Database
Il plugin può configurare il proprio database, ad esempio il contenuto di `plugin/foo/config/database.php` sarebbe:

```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql è il nome della connessione
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'database',
            'username'    => 'username',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin è il nome della connessione
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

Il modo per richiamare questa configurazione è `Db::connection('plugin.{plugin}.{nome_connessione}');`, per esempio

```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

Se si desidera utilizzare il database del progetto principale, è sufficiente utilizzarlo direttamente, per esempio

```php
use support\Db;
Db::table('user')->first();
// Supponiamo che il progetto principale abbia anche una connessione admin
Db::connection('admin')->table('admin')->first();
```

## Configurare il database per il Model

È possibile creare una classe Base per il Model, con la proprietà `$connection` che specifica la connessione al database del plugin, per esempio

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

In questo modo, tutti i Model nel plugin che ereditano da Base utilizzeranno automaticamente il database del plugin.

## Riutilizzare la configurazione del database
Naturalmente è possibile riutilizzare la configurazione del database del progetto principale. Se si sta utilizzando [webman-admin](https://www.workerman.net/plugin/82), è anche possibile riutilizzare la configurazione del database di [webman-admin](https://www.workerman.net/plugin/82), per esempio

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
