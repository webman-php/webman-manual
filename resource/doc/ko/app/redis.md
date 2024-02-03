# Redis
Redis 사용법은 데이터베이스와 유사합니다. 예를 들어 `plugin/foo/config/redis.php` 파일입니다.
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
사용하는 방법은 다음과 같습니다.
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

마찬가지로, 주 프로젝트의 Redis 구성을 재사용하려는 경우
```php
use support\Redis;
Redis::get('key');
// 가정: 주 프로젝트에 'cache' 연결이 구성되어 있다고 가정합니다.
Redis::connection('cache')->get('key');
```
