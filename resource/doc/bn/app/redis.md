# রেডিস
রেডিস ব্যবহার ডাটাবেসের মতো। উদাহরণস্বরূপ `plugin/foo/config/redis.php`
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
ব্যবহার করুন
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

একই রকমভাবে, যদি প্রধান প্রযুক্তিতে রেডিস কনফিগার পুনরাবৃত্তি করতে চান
```php
use support\Redis;
Redis::get('key');
// মনে করুন প্রধান প্রকল্প আরো একটি ক্যাশ কানেকশন কনফিগার করেছে
Redis::connection('cache')->get('key');
```
