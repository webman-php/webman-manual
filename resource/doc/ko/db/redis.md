# Redis

웹맨의 Redis 구성 요소는 기본적으로 [illuminate/redis](https://github.com/illuminate/redis)를 사용합니다. 이것은 라라벨의 Redis 라이브러리이며 라라벨과 동일한 방식으로 사용됩니다.

`illuminate/redis`를 사용하기 전에 `php-cli`에 Redis 확장을 설치해야 합니다.

> **주의**
> `php-cli`에 Redis 확장이 설치되어 있는지 확인하려면 `php -m | grep redis` 명령을 사용하십시오. 참고: `php-fpm`에 Redis 확장을 설치했더라도 `php-cli`에서 사용할 수 있는 것은 아닙니다. 왜냐하면 `php-cli`와 `php-fpm`은 다른 응용프로그램이며 서로 다른 `php.ini` 설정을 사용할 수 있습니다. `php-cli`가 사용하는 `php.ini` 설정 파일을 확인하려면 `php --ini` 명령을 사용하십시오.

## 설치

```php
composer require -W illuminate/redis illuminate/events
```

설치 후에는 restart를 해야 합니다. (reload는 무효)

## 구성

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
use support\Redis;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Redis::set($key, rand());
        return response(Redis::get($key));
    }
}
```

## Redis 인터페이스
```php
Redis::append($key, $value)
Redis::bitCount($key)
Redis::decr($key, $value)
Redis::decrBy($key, $value)
Redis::get($key)
Redis::getBit($key, $offset)
Redis::getRange($key, $start, $end)
Redis::getSet($key, $value)
Redis::incr($key, $value)
Redis::incrBy($key, $value)
Redis::incrByFloat($key, $value)
Redis::mGet(array $keys)
Redis::getMultiple(array $keys)
Redis::mSet($pairs)
Redis::mSetNx($pairs)
Redis::set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
Redis::setBit($key, $offset, $value)
Redis::setEx($key, $ttl, $value)
Redis::pSetEx($key, $ttl, $value)
Redis::setNx($key, $value)
Redis::setRange($key, $offset, $value)
Redis::strLen($key)
Redis::del(...$keys)
Redis::exists(...$keys)
Redis::expire($key, $ttl)
Redis::expireAt($key, $timestamp)
Redis::select($dbIndex)
```
동등한 표현은 다음과 같습니다.
```php
$redis = Redis::connection('default');
$redis->append($key, $value)
$redis->bitCount($key)
$redis->decr($key, $value)
$redis->decrBy($key, $value)
$redis->get($key)
$redis->getBit($key, $offset)
...
```

> **주의**
> `Redis::select($db)` 인터페이스는 신중하게 사용해야 합니다. 웹맨은 메모리 상주형 프레임워크이기 때문에 한 요청이 `Redis::select($db)`를 사용하여 데이터베이스를 변경하면 이후 다른 요청에 영향을 미칠 수 있습니다. 다중 데이터베이스를 사용하는 경우 각각의 `$db`를 다른 Redis 연결 구성으로 설정하는 것이 좋습니다.

## 다중 Redis 연결 사용

예를 들어 구성 파일 `config/redis.php`은 다음과 같습니다.

```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ],

    'cache' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 1,
    ],

]
```

기본적으로는 `default`에 구성된 연결을 사용하지만, `Redis::connection()` 메서드를 사용하여 어떤 Redis 연결을 사용할지 선택할 수 있습니다.

```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## 클러스터 구성

애플리케이션이 Redis 서버 클러스터를 사용하는 경우, 클러스터를 정의하기 위해 Redis 구성 파일에 `clusters` 키를 사용해야 합니다.

```php
return [
    'clusters' => [
        'default' => [
            [
                'host'     => 'localhost',
                'password' => null,
                'port'     => 6379,
                'database' => 0,
            ],
        ],
    ],

];
```

기본적으로 클러스터는 클라이언트 샤딩을 노드에 구현하므로 노드 풀 및 대규모 인메모리를 생성할 수 있습니다. 그러나 클라이언트 공유는 오류 처리를 다루지 않으므로 주로 다른 메인 데이터베이스로부터 캐시 데이터를 가져오는 경우에 사용됩니다. Redis의 원래 클러스터를 사용하려면 `options` 키에 다음을 지정해야 합니다.

```php
return[
    'options' => [
        'cluster' => 'redis',
    ],

    'clusters' => [
        // ...
    ],
];
```

## 파이프라인 명령

서버에 여러 명령을 보내야 할 때 파이프라인 명령을 사용하는 것이 좋습니다. `pipeline` 메서드는 Redis 인스턴스의 익명함수를 받습니다. 모든 명령을 Redis 인스턴스에 보낼 수 있으며 한 번에 실행됩니다.

```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
