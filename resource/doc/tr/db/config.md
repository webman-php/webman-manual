# Yapılandırma
illuminate/database, aşağıdaki veritabanı ve sürüm desteğine sahiptir:

- MySQL 5.6+
- PostgreSQL 9.4+
- SQLite 3.8.8+
- SQL Server 2017+

Veritabanı yapılandırma dosyası `config/database.php` konumundadır.

```php
return [
    // Varsayılan veritabanı
    'default' => 'mysql',
    // Çeşitli veritabanı yapılandırmaları
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
`Db::connection('config_name')` ile hangi veritabanının kullanılacağı seçilebilir. `config_name`, `config/database.php` dosyasındaki ilgili yapılandırmanın `key` değeri olacaktır.

Örneğin, aşağıdaki gibi veritabanı yapılandırması:

```php
return [
    // Varsayılan veritabanı
    'default' => 'mysql',
    // Çeşitli veritabanı yapılandırmaları
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
['pgsql' => [
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

Bu şekilde veritabanı değiştirilebilir.

```php
// Varsayılan veritabanı kullanımı, eşdeğeri: Db::connection('mysql')->table('users')->where('name', 'John')->first();
$users = Db::table('users')->where('name', 'John')->first();
// mysql2 kullanımı
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// pgsql kullanımı
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
