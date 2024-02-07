# Redis
ரெடிஸ் SQLite போல் பயன்படுத்தப்படுகின்றது, உதாரணமாக, `plugin/foo/config/redis.php`
```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ],
];
```

பயன்பாடு
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

அதே போல, பிரத்யேக பிராதமிக உதாரணத்தில் ரெடிஸ் கட்டமைப்பை மீட்புச் செய்ய விரும்பும் 
```php
use support\Redis;
Redis::get('key');
// உதாரணமாக தலை பிராஜெக்டும் ஒரு காஷே இணைவைக்கும்
Redis::connection('cache')->get('key');
```
