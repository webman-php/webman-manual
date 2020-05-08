# 配置
illuminate/database 数据库及版本支持情况如下：

 - MySQL 5.6+ 
 - PostgreSQL 9.4+ 
 - SQLite 3.8.8+
 - SQL Server 2017+ 
 
 数据库配置文件位置为 `config/database.php`。
 
 ```php
 return [
     // 默认数据库
     'default' => 'mysql',
     // 各种数据库配置
     'connections' => [
 
         'mysql' => [
             'driver' => 'mysql',
             'host' => env('DB_HOST', '127.0.0.1'),
             'port' => env('DB_PORT', '3306'),
             'database' => env('DB_DATABASE', 'forge'),
             'username' => env('DB_USERNAME', 'forge'),
             'password' => env('DB_PASSWORD', ''),
             'unix_socket' => env('DB_SOCKET', ''),
             'charset' => 'utf8',
             'collation' => 'utf8_unicode_ci',
             'prefix' => '',
             'strict' => true,
             'engine' => null,
         ],
 
         'sqlite' => [
             'driver' => 'sqlite',
             'database' => env('DB_DATABASE', ''),
             'prefix' => '',
         ],
 
         'pgsql' => [
             'driver' => 'pgsql',
             'host' => env('DB_HOST', '127.0.0.1'),
             'port' => env('DB_PORT', '5432'),
             'database' => env('DB_DATABASE', 'forge'),
             'username' => env('DB_USERNAME', 'forge'),
             'password' => env('DB_PASSWORD', ''),
             'charset' => 'utf8',
             'prefix' => '',
             'schema' => 'public',
             'sslmode' => 'prefer',
         ],
 
         'sqlsrv' => [
             'driver' => 'sqlsrv',
             'host' => env('DB_HOST', 'localhost'),
             'port' => env('DB_PORT', '1433'),
             'database' => env('DB_DATABASE', 'forge'),
             'username' => env('DB_USERNAME', 'forge'),
             'password' => env('DB_PASSWORD', ''),
             'charset' => 'utf8',
             'prefix' => '',
         ],
     ],
 ];
 ```