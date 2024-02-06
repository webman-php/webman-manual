# الـ Redis

يتم استخدام الـ Redis بطريقة مشابهة لقاعدات البيانات، مثل: `plugin/foo/config/redis.php`
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

عند الاستخدام
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

بنفس الطريقة، إذا كنت تريد إعادة استخدام إعدادات الـ Redis للمشروع الرئيسي
```php
use support\Redis;
Redis::get('key');
// مفترض أن المشروع الرئيسي لديه أيضًا اتصال cache آخر
Redis::connection('cache')->get('key');
```
