# Konfiguration
Die Datenbankunterstützung und -versionen von illuminate/database sind wie folgt:

- MySQL 5.6+
- PostgreSQL 9.4+
- SQLite 3.8.8+
- SQL Server 2017+

Die Datenbankkonfigurationsdatei befindet sich unter `config/database.php`.

```php
return [
    // Standarddatenbank
    'default' => 'mysql',
    // Verschiedene Datenbankkonfigurationen
    'connections' => [

        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'webman',
            'username'    => 'webman',
            'password'    => '',
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
        
        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => '',
            'prefix'   => '',
        ],

        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => '127.0.0.1',
            'port'     => 5432,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'sslmode'  => 'prefer',
        ],

        'sqlsrv' => [
            'driver'   => 'sqlsrv',
            'host'     => 'localhost',
            'port'     => 1433,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'charset'  => 'utf8',
            'prefix'   => '',
        ],
    ],
];
```
## Verwendung mehrerer Datenbanken
Verwenden Sie `Db::connection('config_name')`, um festzulegen, welche Datenbank verwendet werden soll. Dabei ist `config_name` der `key`, der der entsprechenden Konfiguration in der Datei `config/database.php` entspricht.

Beispielhaft eine Datenbankkonfiguration:

```php
return [
    // Standarddatenbank
    'default' => 'mysql',
    // Verschiedene Datenbankkonfigurationen
    'connections' => [

        'mysql' => [
            'driver'      => 'mysql',
            'host'        =>   '127.0.0.1',
            'port'        => 3306,
            'database'    => 'webman',
            'username'    => 'webman',
            'password'    => '',
            'unix_socket' =>  '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
        
        'mysql2' => [
             'driver'      => 'mysql',
             'host'        => '127.0.0.1',
             'port'        => 3306,
             'database'    => 'webman2',
             'username'    => 'webman2',
             'password'    => '',
             'unix_socket' => '',
             'charset'     => 'utf8',
             'collation'   => 'utf8_unicode_ci',
             'prefix'      => '',
             'strict'      => true,
             'engine'      => null,
        ],
        'pgsql' => [
             'driver'   => 'pgsql',
             'host'     => '127.0.0.1',
             'port'     =>  5432,
             'database' => 'webman',
             'username' =>  'webman',
             'password' => '',
             'charset'  => 'utf8',
             'prefix'   => '',
             'schema'   => 'public',
             'sslmode'  => 'prefer',
         ],
 ];
```

So wechselt man die Datenbanken.

```php
// Verwendung der Standarddatenbank, gleichbedeutend mit Db::connection('mysql')->table('users')->where('name', 'John')->first();
$users = Db::table('users')->where('name', 'John')->first();; 
//  Verwendung von mysql2
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
//  Verwendung von pgsql
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
