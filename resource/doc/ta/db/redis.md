# ரெடிஸ்

webman ஐப் பயன்படுத்தும் ரெடிஸ் உற்பத்திக்கு [illuminate/redis](https://github.com/illuminate/redis) உபயோகிக்கின்றது, அது லரவெல் ரெடிஸ் நூலகமாகும், பயன்பாடு laravel உபைதியாகும்.

`illuminate/redis` ஐப் பயன்படுத்த, `php-cli` க்கு முதலில் ரெடிஸ் நீங்கள் கொடுக்க வேண்டும்.

> **குறிப்பு**
> `php -m | grep redis` உபயோகிக்கி `php-cli` இல் ரெடிஸ் விளைவச் செய்திகள் உள்ளன என்று பார்க்கவும். குறிப்பாக: முதலில் `php-fpm` இல் ரெடிஸ் விளைவச் செய்திகள் உள்ளதாக இருந்தாலும், அதாவது நீங்கள் `php-cli` இல் அதை பயன்படுத்த முடியாது, ஏனைப்பால் `php-cli` மற்றும் `php-fpm` விஷயங்கள் வேறுபாடாக உள்ளன, இருந்தாலும் `php.ini` கட்டமைகள் வேறுபாடாக இருக்கின்றன. `php --ini` ஐப் பயன்படுத்தி உங்கள் `php-cli` எந்த `php.ini` கட்டமைகளை பயன்படுத்தி உள்ளன என்று பார்க்கவும்.

## நிறுவுக்கை

```php
composer require -W illuminate/redis illuminate/events
```

நிறைவில் செயல்படுத்திக்கு பிறகு மீண்டும் start/restart பொத்தானை எழுப்பவும் (reload செய்யக்கூடாது)


## கட்டமை

ரெடிஸ் கட்டமை கோப்பு `config/redis.php` இல் உள்ளது
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ]
];
```

## எடுத்துக்காட்டு
```php
<?php
namespace app\controller;

use support\Request;
use support\Redis;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Redis::set($key, rand());
        return response(Redis::get($key));
    }
}
```

## ரெடிஸ் இடைவார்த்தைகள்
```php
Redis::append($key, $value)
Redis::bitCount($key)
Redis::decr($key, $value)
Redis::decrBy($key, $value)
Redis::get($key)
Redis::getBit($key, $offset)
Redis::getRange($key, $start, $end)
Redis::getSet($key, $value)
Redis::incr($key, $value)
Redis::incrBy($key, $value)
Redis::incrByFloat($key, $value)
Redis::mGet(array $keys)
Redis::getMultiple(array $keys)
Redis::mSet($pairs)
Redis::mSetNx($pairs)
Redis::set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
Redis::setBit($key, $offset, $value)
Redis::setEx($key, $ttl, $value)
Redis::pSetEx($key, $ttl, $value)
Redis::setNx($key, $value)
Redis::setRange($key, $offset, $value)
Redis::strLen($key)
Redis::del(...$keys)
Redis::exists(...$keys)
Redis::expire($key, $ttl)
Redis::expireAt($key, $timestamp)
Redis::select($dbIndex)
```
ஒப்பீடு
```php
$redis = Redis::connection('default');
$redis->append($key, $value)
$redis->bitCount($key)
$redis->decr($key, $value)
$redis->decrBy($key, $value)
$redis->get($key)
$redis->getBit($key, $offset)
...
```

> **குறிப்பு**
> `Redis::select($db)` நெடுவரியில் எந்த கோரிக்கையிலும் உபயோகிக்கவும் என்றால் மதிப்பீடுகளை பின்னணி செய்யாது, விடாதும், webman ஒரு பெருநிலை நிலையான நுகர்நிலை ஆவணங்கள் ஏற்பட்டுக்கொள்ளும், ஒரு கோரிக்கை `Redis::select($db)` ஐப் பயன்படுத்தும் பிறகு பிற கோரிக்கைகள் குறிப்பில்லாதவைகள் விபரத்தில் பொருந்துகின்றன. அதனால், வேறுபட்ட `db` கட்டமைகளை வைத்து விவி஧ ரெடிஸ் இணைய கட்டமைகளுக்கு புதுப்பிக்கவும் உதவுகின்றது.

## பல ரெடிஸ் இணையங்களை பயன்படுத்துவது
உதாரணமாக, கட்டமை கோப்பு `config/redis.php`
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ],

    'cache' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 1,
    ],

]
```
இயல்பாக, நீங்கள் `default` கீழ் உள்ளதைப் பயன்படுத்துவது, உங்கள் `Redis::connection()` மெதாடுவைப் பயன்படுத்தி பயன்படுத்துவது.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## கூட்டம் கட்டமை
உங்கள் பயன்பாட்டில் ரெடிஸ் சேவைகள் குழுவை பயன்படுத்தில், நீங்கள் உங்கள் ரெடிஸ் அமைப்பின் கீழ் clusters வரைகூடிய கீழ் வரைகூடிய அளவு குழுமத்தை வீணாக்க வேண்டும்:
```php
return [
    'clusters' => [
        'default' => [
            [
                'host'     => 'localhost',
                'password' => null,
                'port'     => 6379,
                'database' => 0,
            ],
        ],
    ],

];
```
இடம்புகுசு, குழுமங்களின் மூல நோக்கம், வழக்கறிந்தது, தவறறொனி நோக்கத்தின் மூலம், அவைகள் அவற்ற்குஏக்கி அதை அமைக்கலாம். இந்த கடல்கூழ்மாய், அல்லாத ரெட்டிஸ் கூழம் முதலியனுக்கு உத்தம. அதாவது, இந்த அம்யாப்ளித்து ஒரெலபுத்துல பயன்படுத்துவது விஷயமாய். பிறகு, ரெடிஸ் உபயோங்கப் படுத்து  வழங்க பேசாமல், அவப்புக்கு ஆபடு படையாலய கூகப்பாக் வலைமமிச் சினா, ஸூறியலி சுறுசலி வழை உதத்.ாமை வீச்சுகலிதி `Redis::pipeline(function ($pipe) { for ($i = 0; $i < 1000; $i++) { $pipe->set("key:$i", $i); } });`
