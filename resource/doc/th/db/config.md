```php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'unix_socket' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => '',
            'prefix' => '',
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' => 5432,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => 'localhost',
            'port' => 1433,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'charset' => 'utf8',
            'prefix' => '',
        ],
    ],
];
```

## การใช้งานฐานข้อมูลหลายรายการ
ใช้ `Db::connection('ชื่อการกำหนด')` เพื่อเลือกการใช้ฐานข้อมูลใด ๆ โดย `ชื่อการกำหนด` คือ `key` ของการกำหนดในไฟล์ `config/database.php` ที่เกี่ยวข้อง

ตัวอย่างการกำหนดฐานข้อมูล:

```php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'unix_socket' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
        'mysql2' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'webman2',
            'username' => 'webman2',
            'password' => '',
            'unix_socket' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' => 5432,
            'database' => 'webman',
            'username' => 'webman',
            'password' => '',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
    ],
];
```

เปลี่ยนฐานข้อมูลดังนี้

```php
// ใช้ฐานข้อมูลเริ่มต้น ถือเป็นเทียบเท่ากับ Db::connection('mysql')->table('users')->where('name', 'John')->first();
$users = Db::table('users')->where('name', 'John')->first();;
// ใช้ mysql2
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// ใช้ pgsql
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
