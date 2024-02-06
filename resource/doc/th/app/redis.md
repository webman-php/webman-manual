# Redis
การใช้ Redis คล้ายกับการใช้ฐานข้อมูล อย่างเช่น `plugin/foo/config/redis.php`
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
ในการใช้งาน
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

เช่นเดียวกัน หากต้องการใช้การกำหนดค่า Redis ของโปรเจคหลัก
```php
use support\Redis;
Redis::get('key');
// สมมติว่าโปรเจคหลักได้กำหนดการเชื่อมต่อ cache ไว้ด้วย
Redis::connection('cache')->get('key');
```
