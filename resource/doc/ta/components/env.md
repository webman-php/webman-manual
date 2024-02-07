# vlucas/phpdotenv

## விளக்கம்
`vlucas/phpdotenv` ஒரு சூழல் மாறியம் ஏற்றுக்கொள்ளும் ஒரு ஒரேபிரத்தியைக் கொண்டுள்ளது, வேலைப் பந்தயங்கள் எப்ரவீராக (வளர்ச்சி அலுவலகம், சோதனை அலுவலகம் போன்ற) கட்டமைப்புகளை வேலைப்படுத்த உதவுகின்றது.

திட்டத்தின் முகவரி
https://github.com/vlucas/phpdotenv
  
## நிறுவுதல்
 
```php
composer require vlucas/phpdotenv
 ```
  
## பயன்படுத்துகின்றது

#### திட்ட முகநூலில் `.env`கோப்பை புதுப்பிப்பது
**.env**
```php
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### அமைப்பு கோப்பை மாற்று
**config/database.php**
```php
return [
    // இயல்புநிலை தரவுத்தளம்
    'default' => 'mysql',

    // வேரககள்வ தரவுத்தளங்கள்
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DB_HOST'),
            'port'        => getenv('DB_PORT'),
            'database'    => getenv('DB_NAME'),
            'username'    => getenv('DB_USER'),
            'password'    => getenv('DB_PASSWORD'),
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
```

> **குறிப்பு**
> போன்றின்றி`.env`கோப்பை`.gitignore`பட்டியலில் சேர்க்கவுள்ளது, குறிப்பதனாலும் குறிப்பிடப்படும் அளவு மாற்றும் போது`.env.example`கட்டமைப்பு நிலைகள் கோப்பைக் குறிக்காமல் மேல் சேர்க்கப்பட்டபோது, திட்டமிடும் தனிப்பட்ட நிலையில் `.env`கோப்பின் வகைப்படுத்த, மாற்ற brain`.env`கொண்ட பயன்பாட்டைக் கிடைக்கலாம்.

> **குறிப்பு**
> `vlucas/phpdotenv`, PHP TS பதிப்பில் (தொழில்பராமரக் கொள்கையான பதிப்பு) பிழைகள் ஏற்பட முடியும், NTS பதிப்பில் (தொழில்பராமரத்தைத் தேர்வுசெய்க வேண்டும் என்பதால் தரவிறக்கம் செய்யவும்).
> தற்போதைய பிஎச்பி எதாவது பதிப்புகளைப் பயன்படுத்தும் மூலமாக நீங்கள் `php -v`ஐச் செயல்படுத்தி அறிந்து கொள்ளலாம்

## மேலும் விவரங்கள்

அணுக https://github.com/vlucas/phpdotenv
