# ரெடிஸ்
ரெடிஸ் தரவுத்தளமும் பொருட்டம் போன்று, உதாரணமாக `plugin/foo/config/redis.php`
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
பயன்படுத்தும் போது
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

ஒப்பீனியாக, தலைமை பிராஜெக்டின் ரெடிஸ் உள்ளடக்கைப் பயன்படுத்த விரும்பும் போன்ற
```php
use support\Redis;
Redis::get('key');
// தலைமை பிராஜெக்டு ஒரு வெக்கம் கணினியையும் உள்ளடக்கு போன்ற
Redis::connection('cache')->get('key');
```
