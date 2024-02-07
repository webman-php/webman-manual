# வங்கி (webman)

webman ஒரு [symfony/cache](https://github.com/symfony/cache) அடையாளம் பயன்படுத்தி வழங்கப்படுகின்றது.

> symfony/cache ஐப் பயன்படுத்துவதற்கு முன்பு php-cli க்கு Redis பேச்சு அமைப்பை முடக்க வேண்டும்.

## நிறுவுதல்
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

நிறுவுதல் பணியாளர் பிணைக்கிறது (மீண்டும் ஏமாற்றம் செய்யப்படும்)

## Redis உள்ளடக்கம்
Redis உள்ளடக்கம் `config/redis.php` க்கு இருக்கின்றது
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
## விண்ணப்பம்
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

> **குறிப்பு:**
> இந்த கோப்பு app\controller என்ற namespace இல் UserController என்பதின் மூலம் பயன்படுத்தப்படுகிறது.
>
> இந்த முறையில், ஒரு எழுத்தை (key) test_key என்பதினால் பின்வருமாறும் மொத்த எண்ணை (rand()) சேமிக்கவும். பின்வரும், பதிலாக Cache இல் சேமிக்கப்பட்ட மொத்த எண்ணை (Cache::get($key)) மேல் திரும்பக் கொடுக்க வேண்டும்.
## webman என்பது ஒரு உயர் செயலிக்கும் PHP கட்டமைப்பானாகும், இப்போது உங்களுக்கு எழுத்துப் படங்களை மூடுகிறது. சரியான தகவலை மூடுகின்றனர் என்பதை உறுதிப்படுத்த முடியும்.
