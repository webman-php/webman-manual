# Redis
Redis 사용법은 데이터베이스와 유사하며, 예를 들어 `plugin/foo/config/redis.php` 파일에 설정합니다.
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

사용 방법은 다음과 같습니다.
```php
use support\Redis;
Redis::connection('plugin.foo.default')->get('key');
Redis::connection('plugin.foo.cache')->get('key');
```

동일하게, 메인 프로젝트의 Redis 설정을 재사용하려면 다음과 같이합니다.
```php
use support\Redis;
Redis::get('key');
// 가정하기로 메인 프로젝트에 cache 연결 또한 설정되어 있다면
Redis::connection('cache')->get('key');
```
