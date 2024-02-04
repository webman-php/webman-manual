# Konfiguration
Illuminate/Database unterstützt die folgenden Datenbanken und Versionen:

- MySQL 5.6+
- PostgreSQL 9.4+
- SQLite 3.8.8+
- SQL Server 2017+

Die Konfigurationsdatei für die Datenbank befindet sich unter `config/database.php`.

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
Verwenden Sie `Db::connection('Konfigurationsname')`, um festzulegen, welche Datenbank verwendet werden soll. Dabei entspricht 'Konfigurationsname' dem 'key' der entsprechenden Konfiguration in der Datei `config/database.php`.

Beispiel für die Datenbankkonfiguration:

```php
 return [
     // Standarddatenbank
     'default' => 'mysql',
     // Verschiedene Datenbankkonfigurationen
     'connections' => [
 
         'mysql' => [
             'driver'      => 'mysql',
             'host'        => '127.0.0.1',
             'port'        =>  3306,
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

Um die Datenbank zu wechseln, kann wie folgt vorgegangen werden:
```php
// Standarddatenbank verwenden, entspricht Db::connection('mysql')->table('users')->where('name', 'John')->first();
$users = Db::table('users')->where('name', 'John')->first();; 
// mysql2 verwenden
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// pgsql verwenden
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
