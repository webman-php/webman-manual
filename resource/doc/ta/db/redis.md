# ரெடிஸ்

webman ரெடிஸ் பொருள் இயங்கும் போது [illuminate/redis](https://github.com/illuminate/redis) ஐ போல பயன்படுத்துகிறது, அது லரவல் ரெடிஸ் நூலையாகும், அதன் பயன்பாடு லரவல் போன்றவைக்கு அதமும்.

`illuminate/redis` ஐ பயன்படுத்துவதற்கு, `php-cli` க்கு முன்பே ரெடிஸ் விரிதியை நிறுத்தி கொடுக்க வேண்டும்.

> **குறிப்பு**
> `php -m | grep redis` என்ன பயன்படுத்தி `php-cli` ஐ ரெடிஸ் பக்கத்தையும் காண்பி. குறிப்பு: உங்கள் `php-fpm` உங்கள் `php-cli` ஐ அதிகரித்துதடும், அதாவது `php-cli` உங்கள் `php.ini` உபயோகப்படுத்துகிறது,  அது `php-fpm` போலும் அல்ல, சேர்க்கின்ற ஒரு வேளை `php --ini` என்ன பயன்படுத்தி உங்கள் `php-cli` எந்த `php.ini` உபயோகப்படுத்துகிறது என்று பார்க்கவும்.

## நிறுவும்

```php
composer require -W illuminate/redis illuminate/events
```

நிறுவும் பின்னர் restart உபயோகிக்கப்படும் (reload பயன்படுத்தக்கூடாது)


## அமைப்பு
ரெடிஸ் அமைப்பு கோப்பு `config/redis.php`
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

## ரெடிஸ் இணைப்பு
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
ஒருபோலவே
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
> `Redis::select($db)` கொடுங்கால் அதிவகை பாராட்டம், எனவே webman நிவாஸ நெஞ்சமிருந்துஉள்ள ஒரு போன்ற போக்கிஷ்டக், ஒரு கோர்டு போதியில் `Redis::select($db)` என்ன குறைபீடு உடனே உங்களுக்கு அகப்படாது. பல தரவுத்தளங்கள் வேற்கொள்ளத்தகும்இஉள்ள ஒரு `db` கோர்டுமறைக்கு வேறு வேனாலும் உங்களுக்கு அகப்படாது.

## பல ரெடிஸ் இணைப்புகளை பயன்படுத்துகின்ற ஒரு ரெடிஸ் இணைப்பு பாய்வு மாற்றத்திற்கான அதிக இணைப்புகளை பயன்படுத்துகின்ற மன்றம் உள்ளதோ, அதவை `Redis::connection()` முறையில் எந்த ரெடிஸ் இணைப்பை பயன்படுத்துவதற்கு அதிகரித்துக்கொள்கின்றது.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## கூடுதல் அமைப்பு
உங்கள் பயின்பாய்வு மாற்றமில்லா இருந்தும், நீங்கள் பயன்பாடு முன் ரெடிஸ் சேவையகங்களை அமைப்புகளின மூன்று வெவ்வேறு [`config/redis.php`](config/redis.php) உள்ள உருவங்களைப் பயன்படுத்தி வெவ்வேறு ரெடிஸ் இணைப்புகளை முன்னிடுக்கலான நாங்கள் select ஏனைய உள்ஈணைந்த கொள்கின்றது.
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
இடங்கள்ினா `clusters` இணைப்புகள் முக்கியமாக்கற்க,  தோரை இப்போது உங்களுக்கு ஒரு தரவு அமைப்பில் இருந்து அழைப்புகளகத்தைப் பெற வேண்டும்.
```php
return [
    'options' => [
        'cluster' => 'redis',
    ],

    'clusters' => [
        // ...
    ],
];
```

## செயல கட்டளை
நீங்கள் ஒவ்வொரு உறுதியான அதிகரித்து பயன்படுத்தினீர்கள்காலான செயல்பாடுகளைக் பயன்படுத்து அனைத்து  குறிக்கேயார உங்களைப் பெற்றீர்களகும் அத் கோட்ட நீங்கள்  கட்டளைத் தடையையுளவும் செயல்களானதற்க்கு பெற்றீர்முன்னபிழீர்ப்பனடைய இது முக்கியமாக யாது நீங்கள் வேண்டன.்வேண்டன்
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
