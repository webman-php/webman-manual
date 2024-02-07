# Database
Il plugin può configurare il proprio database, ad esempio `plugin/foo/config/database.php` contiene il seguente contenuto:
```php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql è il nome della connessione
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'nome del database',
            'username'    => 'nome utente',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin è il nome della connessione
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'nome del database',
            'username'    => 'nome utente',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```

Usa `Db::connection('plugin.{plugin}.{nome connessione}');`, per esempio
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

## Configurare il database per il Modello
Possiamo creare una classe Base per il Modello, dove la classe Base usa `$connection` per specificare la connessione al database del plugin, ad esempio
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

In questo modo, tutti i Modelli all'interno del plugin ereditano da Base e utilizzano automaticamente il proprio database del plugin.

## Riuso della configurazione del database
Naturalmente possiamo riutilizzare la configurazione del database del progetto principale, ad esempio, se si sta utilizzando [webman-admin](https://www.workerman.net/plugin/82), è possibile riutilizzare la configurazione del database di [webman-admin](https://www.workerman.net/plugin/82), ad esempio
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
