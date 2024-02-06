# கட்சியம்
illuminate/database ஆவணம் மற்றும் புதியது ஆதாரவாரிகளை கொண்டு, கீழே உள்ளது:
 - மைஎஸ்கியூஎல் 5.6+ 
 - போஸ்ட்க்விஸ்க்யூஎல 9.4+ 
 - ஸ்க்வாலைட் 3.8.8+
 - எஸ்கியெல் சர்வேர் 2017+
 
 தரவுத்தள அமைப்பு கோப்பு இருக்கின்றது `config/database.php`.

 ```php
 return [
     // இயல்பு தரவுத்தளம்
     'default' => 'mysql',
     // வெளியீடுகளாக தரவுத்தளங்கள்
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
 
 ## பல உள்ளடக்க தரவுத்தளங்களை பயன்படுத்துவதன் மூலம்
`Db::connection('அமைப்புப் பெயர்')` உபயோகப்படுத்தி யாதோர் ஒன்றைத் தரவுத்தளத்தை பயன்படுத்த வேண்டும், `கட்சிப் பெயர்` அவ்விசேஷம் `மூலம் அமைக்கப்பட்ட அமைப்புப்` கோப்பில் இருந்து `தரவுத்தளங்கள்` மற்றும் `கீ` ஐ பயன்படுத்திக்கொள்ளும். 

உதாரணமாக பின்வரும் தரவுத்தள உரையாடல் உள்ளது:

```php
 return [
     // இயல்பு தரவுத்தளங்கள்
     'default' => 'mysql',
     // வெளியீடுகள் வேறு தரவுத்தள உரையாடங்கள்
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

போதுமாக பல தரவுத்தளங்கள் பயன்படுத்தி வெளியீடு அமைக்கலாம்.
```php
// இயல்பு தரவுத்தளம் பயன்படுத்தி, Db::connection('mysql')->table('users')->where('name', 'John')->first(); உடன் சமம்
$users = Db::table('பயனர்கள்')->where('பெயர்', 'ஜான்')->first(); 
// மைஎஸ்கியூ2 பயன்படுத்த
$users = Db::connection('mysql2')->table('பயனர்கள்')->where('பெயர்', 'ஜான்')->first();
// போஸ்க்யூஎல் பயன்படுத்த
$users = Db::connection('pgsql')->table('பயனர்கள்')->where('பெயர்', 'ஜான்')->first();
```
