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
 
 ## 使用多个数据库
通过`Db::connection('配置名')`来选择使用哪个数据库，其中`配置名`为配置文件`config/database.php`中的对应配置的`key`。
 
 例如如下数据库配置：

```php
 return [
     // 默认数据库
     'default' => 'mysql',
     // 各种数据库配置
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

像这样切换数据库。
```php
// 使用默认数据库，等价于Db::connection('mysql')->table('users')->where('name', 'John')->first();
$users = Db::table('users')->where('name', 'John')->first();; 
// 使用mysql2
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// 使用pgsql
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
