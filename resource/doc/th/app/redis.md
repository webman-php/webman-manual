# Redis
การใช้ Redis คล้ายกับการใช้ฐานข้อมูล เช่น `plugin/foo/config/redis.php`
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
เมื่อใช้
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

เช่นเดียวกัน หากต้องการนำการกำหนดค่าตั้งต้นของ Redis ของโปรเจกต์หลักมาใช้
```php
use support\Redis;
Redis::get('key');
// ถ้าสมมติว่าโปรเจกต์หลักกำหนดการเชื่อมต่อ cache ไว้
Redis::connection('cache')->get('key');
```
