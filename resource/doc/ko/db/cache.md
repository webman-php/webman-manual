# 캐시

webman은 기본적으로 [symfony/cache](https://github.com/symfony/cache)를 캐시 구성 요소로 사용합니다.

> `symfony/cache`를 사용하기 전에 `php-cli`에 redis 확장을 설치해야 합니다.

## 설치
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

설치 후에는 재시작(reload는 작동하지 않음)이 필요합니다.

## Redis 구성
redis 구성 파일은 `config/redis.php`에 있습니다.
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

## 예시
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

> **주의**
> 다른 비즈니스에서 redis를 사용할 때 충돌을 피하기 위해 키에 접두어를 사용하는 것이 좋습니다.

## 다른 캐시 구성 요소 사용

[ThinkCache](https://github.com/top-think/think-cache) 구성 요소 사용법은 [다른 데이터베이스](others.md#ThinkCache)를 참조하세요.
