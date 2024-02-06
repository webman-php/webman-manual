# vlucas/phpdotenv

## விளக்கம்
`vlucas/phpdotenv` ஒரு சூழல் மாறியை ஏற்றுமதிக்கும் பொருட்டாகும், வெவ்வேறு சூழல்களை(பள்ளிக்காலமாக, சேர் பள்ளிக்காலமாக) வேறுபாடு வழங்கும் உள்ளடக்கங்களை பொருட்டாகும்.

## திட்டம் இருப்பு

https://github.com/vlucas/phpdotenv
  
## நிறுவு

```php
composer require vlucas/phpdotenv
 ```
  
## பயன்பாடு

#### திட்டம் மூலம் `.env` கோப்புவடிவம் உருவாக்குங்கள்
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### உள்ளடக்க கோப்புவடிவத்தை மாற்று
**config/database.php**
```php
return [
    // இயல்புநிலை தரவுத்தளம்
    'default' => 'mysql',

    // வேறுபாட்டு தரவுத்தள உள்ளடக்கம்
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

> **குறித்து**
> `.env` கோப்பை `.gitignore` பட்டியலில் சேர்க்க, குறிப்பிட்டபடியாக கோட் களத்தில் சமர்ப்பிக்க தயாராக்க உதவுகின்றது. திட்டத்தில் `.env.example` கட்டமைக்கப்பட்டு சேர்க்க, பிரகாரம் திட்டத்தை ஏனைய சூழலில் மாற்ற, அதன் கட்ட பிரித்து, இதன் மூலம் திட்டத்தில் வேறுபாடு உள்ளடக்கம் ஏற்பாடம் குறிப்பு.

> **குறித்து**
> `vlucas/phpdotenv`உள்ள PHP டிஎஸ் பதிப்பு (சுற்று பெற்ற பதிப்பு) ல் பிழை இருப்பது இருமற்ற கனவில் (குமிழ்சாவுகூட இல்லா) பயன்பாடு செய்யவும்.
> தற்போதைய PHP எது பதிப்ப் `php -v` இயலாமைக்குச் சார்வணியப்படும்.

## மேலும் உள்ளடக்கம்

https://github.com/vlucas/phpdotenv
