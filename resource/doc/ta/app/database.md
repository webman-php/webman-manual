# தரவுகள்
பிளகின் தரவுகள் தங்களுடைய தரவுகளை கட்டமைக்கலாம், உதாரணமாக `plugin/foo/config/database.php` உள்ளடக்கத்தைப் பார்க்கவும்
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql இணைப்பது இணைப்புப் பெயர்
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'தரவுத்தளம்',
            'username'    => 'பயனர்பெயர்',
            'password'    => 'கடவுச்சொல்',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin இணைப்பது இணைப்புப் பெயர்
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'தரவுத்தளம்',
            'username'    => 'பயனர்பெயர்',
            'password'    => 'கடவுச்சொல்',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
மீண்டும் படுத்துதல் சுருக்கம் `Db::connection('plugin.{உருப்படியை}.{இணைப்புப் பெயர்}');`, உதாரணமாக
```php
புள்ளியை பயன்படுத்துக
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```
மாஸ்டர் முகாமில் உள்ள தரவுத்தளத்தைப் பயன்படுத்த விரும்பினால், சோதனை செய்து முடியும், உதாரணமாக
```php
புள்ளியை பயன்படுத்துக
use support\Db;
Db::table('user')->first();
// மாஸ்டர் பயன்பாடுகொண்டு உள்ள admin இணைப்பையும், உதாரணமாக
Db::connection('admin')->table('admin')->first();
```

## மாதிரிக்கையை தரவுத்தளம் ஒப்பிடுவதற்கான வழிகள்

நாங்கள் மாதிரிக்கையை தரவுத்தளம் செய்வதற்கு இயல்பாக ஒரு அடிப்படை வரையம் உருவாக்கலாம், அடிப்படையான வரையம் `$connection` மூலம் உருப்படியைக் குறிப்பிடுகிறது, உதாரணமாக
```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.foo.mysql';

}
```
இது போன்று பிளகின் எல்லா மாதிரிக்கைகளும் அடிப்படை வரையம் பயன்படுத்தினால், பிளகின் தரவுத்தளத்தைத் தானாகவும் பயன்படுத்தினால் அடிப்படை உபயோகப்படும்.

## தரவுத்தளம் கட்டமை மற்றும் மீண்டும் பயன்படுத்துதல்
மேலும், மேலதிக கட்டமையில் உள்ள தரவுத்தளத்தைப் புதுப்பிக்க முடியும், [வெப்மான்-செம்மியார்](https://www.workerman.net/plugin/82) ஒத்திவிட்டு உள்ளூர் தரவுத்தளத்தைக் குறிப்பிடுதலும் முடியும், உதாரணமாக
```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.admin.mysql';

}
```
