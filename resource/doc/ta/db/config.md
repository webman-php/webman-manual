# கட்சியகம்
illuminate/database தரவுத்தளம் மற்றும் பதிப்பு ஆதரவு எப்படி இருக்கின்றது என்று கீழே குறியீடு சொல்லப்பட்டுள்ளது:
 - MySQL 5.6+ 
 - PostgreSQL 9.4+ 
 - SQLite 3.8.8+
 - SQL Server 2017+

தரவுத்தள அமைப்பு கோப்பு இடம் `config/database.php` ஆகும்.

```php
return [
    // இயல்பு தரவுத்தளம்
    'default' => 'mysql',
    // வேறுபட தரவுத்தள அமைப்பு
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

## பல தரவுத்தளங்களை பயன்படுத்துவது
`Db::connection('அமைப்புப் பெயர்')` பயன்படுத்தி எப்படி தரவுத்தளத்தை பயன்படுத்த வேண்டும் என்பதை தெரிவிக்கின்றது, அதன் பெயர் `config/database.php` கோப்பில் உள்ள அதிகூடினால்  'key' ஆகும்.

உ஦ாகாக, பின்னணித் தரவுத்தள அமைப்பு:

```php
return [
    // இயல்பு தரவுத்தளம்
    'default' => 'mysql',
    // வேறுபட தரவுத்தள அமைப்பு
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
    ],
];
```

வகைப்படுத்த தரவுத்தளங்கள் பயன்படுத்த வேண்டும்.
```php
// இயல்பு தரவுத்தளத்தை பயன்படுத்தும், Db::connection('mysql')->table('users')->where('name', 'John')->first(); என்று கொள்கிறது;
$users = Db::table('users')->where('name', 'John')->first();; 
// mysql2 ஐ பயன்படுத்தும்
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// pgsql ஐ பயன்படுத்தும்
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
