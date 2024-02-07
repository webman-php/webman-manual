# மாங்கோட்ப்

webman இயல்புநிலையில் [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) ஐ மாங்கோட்பு கூறுகளாக பயன்படுத்துகிறது. இது லாரவேல் திட்டத்திலிருந்து வெளியீடு செய்யப்பட்டுள்ளது, பயன்பாடு லாரவேல் போன்று செயல்படும்.

`jenssegers/mongodb` -ஐ பயன்படுத்துவதற்கு முன் `php-cli` -க்கு முதன்முதலில் மாங்கோட்பு விரிவாக்கத்தை நிறுவ வேண்டும்.

> `php-cli` -யில் மாங்கோட்பு விரிவாக்கத்தைக் காண கொடுக்கும் `php -m | grep mongodb` கட்டத்தைப் பயன்படுத்துக. குறிப்பாக: நீங்கள் `php-fpm` -இல் மாங்கோட்பு விரிவாக்கத்தை நிறுவிய போது, அது `php-cli` -யைப் பயன்படுத்துவதற்கு அரசியலானாலும், `php-cli` மற்றும் `php-fpm` ஒரே பயன்பாட்டில் அல்ல, அவற்றின் பொருட்கள் பயன்படும்`php.ini` கட்டப்பட்டுள்ளது. உங்கள் `php-cli` எப்படி கட்டப்பட்ட உங்கள் `php.ini` கட்டத்தைப் பார்க்க கூடிய `php --ini` கட்டத்தை பயன்படுத்துக.

## நிறுவுவது

PHP>7.2 இலவசமாக
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2 இல்
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

நிறுவப்பட்ட பின் restart புதுப்பிக்க வேண்டும் (reload தெரியாது)

## கட்டமைப்பு
கோள் வடிவில் `config/database.php` இல் `mongodb` இணைப்பைச் சேர்க்கவும், போல் உரையும்:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...இதைவிட மேலும் அமைப்புகள் விடுக...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // here you can pass more settings to the Mongo Driver Manager
                // see https://www.php.net/manual/en/mongodb-driver-manager.construct.php under "Uri Options" for a list of complete parameters that you can use

                'appname' => 'homestead'
            ],
        ],
    ],
];
```
## மாதிரி
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        Db::connection('mongodb')->collection('test')->insert([1,2,3]);
        return json(Db::connection('mongodb')->collection('test')->get());
    }
}
```

## மேலும் விவரங்களுக்கு வருகை

https://github.com/jenssegers/laravel-mongodb
