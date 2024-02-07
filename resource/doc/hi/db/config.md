वेबमैन कॉन्फ़िगरेशन
Illuminate/database डेटाबेस और संस्करण समर्थन निम्नलिखित हैं:

- MySQL 5.6+
- PostgreSQL 9.4+
- SQLite 3.8.8+
- SQL Server 2017+

डेटाबेस कॉन्फ़िगरेशन फ़ाइल का स्थान `config/database.php` है।

```php
return [
    // डिफ़ॉल्ट डेटाबेस
    'default' => 'mysql',
    // विभिन्न डेटाबेस कॉन्फ़िगरेशन
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

## एकाधिक डेटाबेस का प्रयोग
`Db::connection('कॉन्फ़िग_नाम')` से डेटाबेस का चयन करने के लिए, जिसमें `कॉन्फ़िग_नाम` कॉन्फ़िग फ़ाइल `config/database.php` में अनुरूप कूंजी है।

उदाहरण के रूप में निम्नलिखित डेटाबेस कॉन्फ़िगरेशन:

```php
return [
    // डिफ़ॉल्ट डेटाबेस
    'default' => 'mysql',
    // विभिन्न डेटाबेस कॉन्फ़िगरेशन
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

इस तरह से डेटाबेस को स्विच करें।
```php
// डिफ़ॉल्ट डेटाबेस का प्रयोग करें, Db::connection('mysql')->table('users')->where('name', 'John')->first(); के समान।
$users = Db::table('users')->where('name', 'John')->first(); 
// mysql2 का प्रयोग करें
$users = Db::connection('mysql2')->table('users')->where('name', 'John')->first();
// pgsql का प्रयोग करें
$users = Db::connection('pgsql')->table('users')->where('name', 'John')->first();
```
