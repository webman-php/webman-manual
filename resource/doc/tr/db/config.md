# Yapılandırma
illuminate/database, aşağıdaki veritabanı ve versiyon desteğine sahiptir:

 - MySQL 5.6+ 
 - PostgreSQL 9.4+ 
 - SQLite 3.8.8+
 - SQL Server 2017+ 
 
 Veritabanı yapılandırma dosyası `config/database.php` konumundadır.
 
 ```php
 return [
     // varsayılan veritabanı
     'default' => 'mysql',
     // çeşitli veritabanı yapılandırmaları
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
 
 ## Birden Fazla Veritabanı Kullanımı
Hangi veritabanını kullanacağını seçmek için `Db::connection('config_name')` kullanılır, burada `config_name` parametresi `config/database.php` dosyasındaki ilgili yapılandırmanın `key`idir.
 
 Örneğin aşağıdaki gibi bir veritabanı yapılandırması bulunabilir:

```php
 return [
     // varsayılan veritabanı
     'default' => 'mysql',
     // çeşitli veritabanı yapılandırmaları
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

Veritabanı değişimi şu şekilde yapılır:
```php
// varsayılan veritabanı kullanımı, şu kod ile aynıdır: Db::connection('mysql')->table('users')->where('name', 'John')->first();
$users = Db::table('users')->where('name', 'John')->first();; 
// mysql2 kullanımı
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// pgsql kullanımı
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
