الإضاءة / قاعدة البيانات
قاعدة البيانات ودعم الإصدارات على النحو التالي:

- MySQL 5.6+
- PostgreSQL 9.4+
- SQLite 3.8.8+
- SQL Server 2017+

موقع ملف تكوين قاعدة البيانات هو `config/database.php`.

```php
return [
    // قاعدة بيانات افتراضية
    'default' => 'mysql',
    // تكوينات مختلفة لقاعدة البيانات
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

## استخدام قواعد بيانات متعددة
استخدم `Db::connection('اسم_التكوين')` لاختيار قاعدة بيانات معينة، حيث يكون `اسم_التكوين` هو `key` الخاص بالتكوين المطابق في ملف التكوين `config/database.php`.

مثلاً، تكوينات قاعدة البيانات التالية:

```php
return [
    // قاعدة بيانات افتراضية
    'default' => 'mysql',
    // تكوينات مختلفة لقاعدة البيانات
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
            'port'     => 5432,
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

قم بتبديل قواعد البيانات كالتالي.

```php
// استخدام قاعدة البيانات الافتراضية، مكافئة لـ Db::connection('mysql')->table('users')->where('name', 'John')->first();
$users = Db::table('users')->where('name', 'John')->first(); 
// استخدام mysql2
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// استخدام pgsql
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
