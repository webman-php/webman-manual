# Настройка
illuminate/database поддерживает следующие базы данных и их версии:

 - MySQL 5.6+
 - PostgreSQL 9.4+
 - SQLite 3.8.8+
 - SQL Server 2017+

Файл настроек базы данных находится в `config/database.php`.

```php
 return [
     // По умолчанию используется база данных MySQL
     'default' => 'mysql',
     // Настройки для различных баз данных
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

## Использование нескольких баз данных
Чтобы выбрать, какую базу данных использовать, используйте `Db::connection('имя_настройки')`, где `имя_настройки` - это `key` соответствующей настройки в файле конфигурации `config/database.php`.

Например, конфигурация базы данных может выглядеть следующим образом:

```php
 return [
     // По умолчанию используется база данных MySQL
     'default' => 'mysql',
     // Настройки для различных баз данных
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

Пример использования различных баз данных:

```php
// Использование базы данных по умолчанию, эквивалентно Db::connection('mysql')->table('users')->where('name', 'John')->first();
$users = Db::table('users')->where('name', 'John')->first();
// Использование mysql2
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// Использование pgsql
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
