# রেডিস
রেডিস ব্যবহারকারীরা ডাটাবেইসের মতো ব্যবহার করা যায়, উদাহরণস্বরূপ `plugin/foo/config/redis.php`
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
ব্যবহার করার সময়
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

একইভাবে, যদি প্রধান প্রকল্পের রেডিস কনফিগারেশন পুনর্ব্যবহার করতে চান
```php
use support\Redis;
Redis::get('key');
// মনে করুন প্রধান প্রকল্পে "cache" নামে একটি সংযোগ কনফিগার করা আছে
Redis::connection('cache')->get('key');
```
