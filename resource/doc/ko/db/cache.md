# 캐시

webman은 기본적으로 [symfony/cache](https://github.com/symfony/cache)를 캐시 구성 요소로 사용합니다.

> `symfony/cache`를 사용하기 전에 `php-cli`에 Redis 확장 기능을 설치해야 합니다.

## 설치
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

설치 후에는 restart를 해야 합니다 (reload는 작동하지 않음)

## Redis 구성
Redis 구성 파일은 `config/redis.php`에 있습니다.
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

## 예제
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

> **참고**
> 가능한 경우 키에 접두사를 추가하여 다른 Redis를 사용하는 업무와의 충돌을 피하십시오.

## 다른 캐시 구성 요소 사용
[ThinkCache](https://github.com/top-think/think-cache) 구성 요소의 사용은 [다른 데이터베이스](others.md#ThinkCache) 참고.
