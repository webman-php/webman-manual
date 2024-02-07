# Cấu hình
Cơ sở dữ liệu và phiên bản được hỗ trợ dưới đây trong illuminate/database:

- MySQL 5.6+
- PostgreSQL 9.4+
- SQLite 3.8.8+
- SQL Server 2017+

Tệp cấu hình cơ sở dữ liệu được đặt tại `config/database.php`.

```php
return [
    // Cơ sở dữ liệu mặc định
    'default' => 'mysql',
    // Các cấu hình cơ sở dữ liệu khác nhau
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

## Sử dụng nhiều cơ sở dữ liệu
Sử dụng `Db::connection('tên cấu hình')` để chọn cơ sở dữ liệu nào sẽ được sử dụng, trong đó `tên cấu hình` là `key` tương ứng trong tệp cấu hình `config/database.php`.

Ví dụ cấu hình cơ sở dữ liệu như sau:

```php
return [
    // Cơ sở dữ liệu mặc định
    'default' => 'mysql',
    // Các cấu hình cơ sở dữ liệu khác nhau
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
    ],
];
```

Chuyển đổi cơ sở dữ liệu như sau.

```php
// Sử dụng cơ sở dữ liệu mặc định, tương đương với Db::connection('mysql')->table('users')->where('name', 'John')->first();
$users = Db::table('users')->where('name', 'John')->first(); 
// Sử dụng mysql2
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// Sử dụng pgsql
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
