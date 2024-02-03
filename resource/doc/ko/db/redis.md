# 레디스

웹맨의 레디스 구성요소는 기본적으로 [illuminate/redis](https://github.com/illuminate/redis)를 사용합니다. 이 라이브러리는 라라벨의 레디스 라이브러리와 동일한 사용법을 가지고 있습니다.

`illuminate/redis`를 사용하기 전에는 먼저 `php-cli`에 레디스 확장 기능을 설치해야 합니다.

> **주의**
> `php-cli`에 레디스 확장이 설치되었는지 확인하려면 `php -m | grep redis` 명령어를 사용하십시오. 참고: 레디스 확장을 `php-fpm`에 설치했더라도 'php-cli`에서 사용할 수 있는 확장이라는 것을 의미하지 않습니다. 왜냐하면 `php-cli`와 `php-fpm`은 서로 다른 애플리케이션이며 서로 다른 `php.ini` 설정을 사용할 수 있습니다. 사용 중인 `php-cli`에 사용되는 `php.ini` 설정 파일을 확인하려면 `php --ini` 명령어를 사용하십시오.

## 설치

```php
composer require -W illuminate/redis illuminate/events
```

설치 후에는 재시작(restart)이 필요합니다(reload는 적용되지 않음).


## 구성
레디스 설정 파일은 `config/redis.php` 위치합니다.
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

## 레디스 인터페이스
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
동일한 의미를 가지는 것은 다음과 같습니다.
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
> `Redis::select($db)` 인터페이스를 사용할 때 주의해야 합니다. 웹맨은 지속적인 메모리 프레임워크이기 때문에 특정 요청이 `Redis::select($db)`를 사용하여 데이터베이스를 전환하면 후속 요청에 영향을 미칠 수 있습니다. 여러 데이터베이스를 사용하려면 서로 다른 `$db`를 갖는 레디스 연결 구성으로 설정하는 것이 좋습니다.

## 여러 개의 레디스 연결 사용
예를 들어 설정 파일 `config/redis.php`는 다음과 같습니다.
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
기본적으로 `default`로 설정된 연결을 사용하고, `Redis::connection()` 메서드를 사용하여 어떤 레디스 연결을 사용할지 선택할 수 있습니다.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## 클러스터 구성
응용 프로그램이 레디스 서버 클러스터를 사용하는 경우, 클러스터를 정의하기 위해 Redis 구성 파일에서 clusters 키를 사용해야 합니다:
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

기본적으로 클러스터는 노드에서 클라이언트 샤딩을 수행할 수 있으며, 네트워크 풀 및 대량의 사용 가능한 메모리를 생성할 수 있습니다. 여기서 주의할 점은 클라이언트 공유가 실패 상황을 처리하지 않으므로 주로 다른 주 데이터베이스에서 캐시 데이터를 가져오는데 사용됩니다. Redis 고유의 클러스터를 사용하려면 구성 파일의 options 키에서 다음을 지정해야 합니다:

```php
return [
    'options' => [
        'cluster' => 'redis',
    ],

    'clusters' => [
        // ...
    ],
];
```

## 파이프라인 명령
한 번에 많은 명령을 서버로 보내야 할 때 파이프라인 명령을 사용하는 것이 좋습니다. `pipeline` 메서드는 Redis 인스턴스의 클로저를 인수로 받습니다. Redis 인스턴스로 모든 명령을 보낼 수 있으며, 이러한 명령은 한 번에 실행됩니다:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
