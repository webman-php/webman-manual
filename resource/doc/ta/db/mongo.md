# MongoDB

webman இயல்புநிலையாக [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) ஐ இடைவெளியீடுக்கு பயன்படுத்துகிறது, இது laravel திட்டத்திலிருந்து எடுத்துக் கொள்ளப்பட்டுச் செயல்படுகிறது, மொழிபெயர்ப்புக்கு ஆனால் laravel ஆசிரியத்தினுடைய பயன்பாடுகளை அவதாரிப்பது.

`jenssegers/mongodb` -ஐப் பயன்படுத்துவதற்கு முன்பு, `php-cli` -க்கு mongodb நீட்டிப்பை நிறுத்த வேண்டும்.

> `php -m | grep mongodb` கட்டளையைப் பயன்படுத்தி `php-cli` இல் mongodb நீட்டிப்பு அமைந்திருக்கின்றதா என பார்க்கவும். குறிப்பாக: நீங்கள் `php-fpm` இல் mongodb நீட்டிப்பை நிறுத்தியும், அது `php-cli` -ல் அதை பயன்படுத்த முடியும் என்பதையும் உறுதிப்படுத்தவும், ஏனைய பயன்பாடுகளில், `php-cli` மற்றும் `php-fpm` என்றிருக்கும், வேறு வேறு `php.ini` கோணங்களைப் பயன்படுத்துகின்றன. உங்கள் `php-cli` -இல் யாரைப் பயன்படுத்துகின்றது என்பதை அறிந்துகொள்ள கட்டளை `php --ini` ஐப் பயன்படுத்தவும்.

## நிறுவுதல்

PHP>7.2 இலவா
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2 இலவா
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

நிறுவிய பின் மீண்டும் துவக்கிக்கொண்டு வைப்பது தேமுறை (மறைந்துவிடும்)

## உள்ளமை

`config/database.php` -இல் `mongodb` இணைப்பை உருவாக்கவும், கீழே காணப்படுகின்றது போல :
```php
return [

    'default' => 'mysql',

    'connections' => [

         ... இங்கே மற்ற உள்ளமை ...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // இங்கு நீங்கள் கூடுதல் அமைப்புகளை Mongo Driver Manager க்கு அனுப்பலாம்
                // "Uri Options" க்கு போக https://www.php.net/manual/en/mongodb-driver-manager.construct.php இல் உள்ளீடுகள் பட்டியலைப் பார்க்க
                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## எல்லாவற்றின் செயல்பாடு

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

## மேலும் தகவல்களுக்கு பார்வையாக

https://github.com/jenssegers/laravel-mongodb
