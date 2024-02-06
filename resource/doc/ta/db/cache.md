# காசே

webmanல் இயக்கத்திற்கு [symfony/cache](https://github.com/symfony/cache) ஐக் காசே அமைப்பாக கொண்டுள்ளது.

> `symfony/cache` ஐ உபயோகிக்கும் முன் `php-cli` க்கு அது redis நீர்வழி நிறுவணியாக்க வேண்டும்.

## நிறுவுதல்
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

நிறுவிய பின் மீள்பதில் மீளவும் (மீள்டோடு செயபடாது)

## ரெடிஸ் அமைப்பு
ரெடிஸ் அமைப்பு கோப்பு `config/redis.php` இல் உள்ளது
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

## உதாரணம்
```php
<?php
namespace app\controller;

use support\Request;
use support\Cache;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Cache::set($key, rand());
        return response(Cache::get($key));
    }
}
```

> **குறிப்பு**
> மிகவும் பாரம்பரியமாக ஒரு முன் குறி சேர்க்க, மற்றும் வேறு எதாவது தொழில்நுட்பத் தொகுதியொன்றை தொடர்ந்து ஏற்றுக்கொள்ள விலக வேண்டும்

## மற்ற காசே அமைப்புகளை பயன்படுத்துவது

[ThinkCache](https://github.com/top-think/think-cache) காசே அமைப்பின் பயன்பாடு பார்வை [பிற தரவுத்தளங்கள்](others.md#ThinkCache)
