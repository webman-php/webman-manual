illuminate/database ডাটাবেস এবং সংস্করণের সমর্থন নিম্নলিখিত:

- MySQL 5.6+
- PostgreSQL 9.4+
- SQLite 3.8.8+
- SQL Server 2017+

ডাটাবেস কনফিগ ফাইল অবস্থান: `config/database.php`।

```php
return [
    // ডিফল্ট ডাটাবেস
    'default' => 'mysql',
    // বিভিন্ন ডাটাবেস কনফিগারেশন
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

## একাধিক ডাটাবেস ব্যবহার
`Db::connection('কনফিগ নাম')` ব্যবহার করে কোন ডাটাবেস ব্যবহার করতে, 'কনফিগ নাম' এর মান হল কনফিগ ফাইল `config/database.php` এর সাথে মিলতে হবে।

উদাহরণ হিসাবে নিম্নলিখিত ডাটাবেস কনফিগারেশন:

```php
return [
    // ডিফল্ট ডাটাবেস
    'default' => 'mysql',
    // বিভিন্ন ডাটাবেস কনফিগারেশন
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

এমনভাবে ডাটাবেস চলাচল সুযোগ করুন।

```php
// ডিফল্ট ডাটাবেস ব্যবহার করুন, সমহচারে হলো Db::connection('mysql')->table('users')->where('name', 'John')->first();
$users = Db::table('users')->where('name', 'John')->first();; 
// mysql2 ব্যবহার করুন
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// pgsql ব্যবহার করুন
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
