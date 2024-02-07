# การกำหนดค่า
illuminate/database สนับสนุนฐานข้อมูลและเวอร์ชันดังต่อไปนี้:

- MySQL 5.6+
- PostgreSQL 9.4+
- SQLite 3.8.8+
- SQL Server 2017+

ตำแหน่งของไฟล์กำหนดค่าฐานข้อมูลคือ `config/database.php`.

```php
 return [
     // ฐานข้อมูลเริ่มต้น
     'default' => 'mysql',
     // คอนเน็กชั่นฐานข้อมูลต่าง ๆ
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

 ## ใช้ฐานข้อมูลหลายระบบ
ใช้ `Db::connection('ชื่อค่ากำหนด')` เพื่อเลือกใช้ฐานข้อมูลไหน ๆ ซึ่ง `ชื่อค่ากำหนด` คือคีย์ภายในไฟล์กำหนดค่า `config/database.php` 

ตัวอย่างเช่น การกำหนดค่าฐานข้อมูลดังนี้:

```php
 return [
     // ฐานข้อมูลเริ่มต้น
     'default' => 'mysql',
     // คอนเน็กชันฐานข้อมูลต่าง ๆ
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

สร้างการเลือกฐานข้อมูลดังนี้.
```php
// ใช้ฐานข้อมูลเริ่มต้น ซึ่งเทียบเท่ากับ Db::connection('mysql')->table('users')->where('name', 'John')->first();
$users = Db::table('users')->where('name', 'John')->first();; 
// ใช้ mysql2
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// ใช้ pgsql
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
