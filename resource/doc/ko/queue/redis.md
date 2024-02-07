## Redis 큐

Redis를 기반으로 하는 메시지 큐는 메시지 지연 처리를 지원합니다.

## 설치
`composer require webman/redis-queue`

## 구성 파일
Redis 구성 파일은 `config/plugin/webman/redis-queue/redis.php` 경로에 자동으로 생성됩니다. 내용은 다음과 유사합니다:
```php
<?php
return [
    'default' => [
        'host' => 'redis://127.0.0.1:6379',
        'options' => [
            'auth' => '',         // 옵션 선택 사항으로 비밀번호
            'db' => 0,            // 데이터베이스
            'max_attempts'  => 5, // 소비 실패 후 재시도 횟수
            'retry_seconds' => 5, // 재시도 간격, 초 단위
        ]
    ],
];
```

### 소비 실패 재시도
소비 실패(예외 발생)시 메시지는 지연 대기열에 넣어져 다음 재시도를 기다립니다. 재시도 횟수는 `max_attempts` 매개변수로 제어되며, 재시도 간격은 `retry_seconds`와 `max_attempts` 둘 다에 의해 제어됩니다. 예를 들어 `max_attempts`가 5이고, `retry_seconds`가 10이면 1차 재시도 간격은 `1*10`초이며, 2차 재시도 간격은 `2*10`초, 3차 재시도 간격은 `3*10`초 등으로 5번의 재시도까지 계속됩니다. `max_attempts`로 설정된 재시도 횟수를 초과하면 메시지는`{redis-queue}-failed`라는 실패 큐로 이동합니다.

## 메시지 전달 (동기)
> **참고**
> webman/redis >= 1.2.0 필요, Redis 확장 의존

```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Redis;

class Index
{
    public function queue(Request $request)
    {
        // 큐 이름
        $queue = 'send-mail';
        // 배열 직접 전달 가능, 직렬화가 필요하지 않음
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // 메시지 전달
        Redis::send($queue, $data);
        // 메시지 지연 전달, 메시지는 60초 후 처리됨
        Redis::send($queue, $data, 60);

        return response('redis queue test');
    }

}
```
`Redis::send()`가 성공적으로 전달되면 true를 반환하고, 그렇지 않으면 false를 반환하거나 예외를 throw합니다.

> **팁**
> 메시지 지연 대기열의 소비 시간은 지연을 유발할 수 있습니다. 예를 들어 소비 속도가 생산 속도보다 느려지면 대기열이 쌓여 지연이 발생할 수 있으며, 이로 인해 소비가 지연될 수 있습니다. 해결 방법은 더 많은 소비 프로세스를 여는 것입니다.

## 메시지 전달 (비동기)
```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Client;

class Index
{
    public function queue(Request $request)
    {
        // 큐 이름
        $queue = 'send-mail';
        // 배열 직접 전달 가능, 직렬화가 필요하지 않음
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // 메시지 전달
        Client::send($queue, $data);
        // 메시지 지연 전달, 메시지는 60초 후 처리됨
        Client::send($queue, $data, 60);

        return response('redis queue test');
    }

}
```
`Client::send()`는 반환 값이 없으며, 이는 비동기 전달로써 메시지가 100% Redis로 전달되지 않을 수 있다는 것을 의미합니다.

> **팁**
> `Client::send()`는 지역 메모리에 메모리 대기열을 만들고 메시지를 Redis로 비동기적으로 동기화하는 방식으로 작동합니다(동기화 속도는 매우 빠르며 대략 1만 건의 메시지가 1초당 동기화됩니다). 프로세스가 다시 시작될 때, 지역 메모리 대기열에 아직 동기화되지 않은 데이터가 있는 경우 메시지가 손실될 수 있습니다. `Client::send()` 비동기 전달은 중요하지 않은 메시지에 적합합니다.

> **팁**
> `Client::send()`는 비동기적으로 작동하므로 workerman 실행 환경에서만 사용할 수 있으며, 명령 줄 스크립트에서는 동기적 인터페이스 `Redis::send()`를 사용해야 합니다.

## 다른 프로젝트에서 메시지 전달
때로는 다른 프로젝트에서 메시지를 전달해야 하지만 `webman\redis-queue`를 사용할 수 없는 경우 아래 함수를 참고하여 큐에 메시지를 전달할 수 있습니다.

```php
function redis_queue_send($redis, $queue, $data, $delay = 0) {
    $queue_waiting = '{redis-queue}-waiting';
    $queue_delay = '{redis-queue}-delayed';
    $now = time();
    $package_str = json_encode([
        'id'       => rand(),
        'time'     => $now,
        'delay'    => $delay,
        'attempts' => 0,
        'queue'    => $queue,
        'data'     => $data
    ]);
    if ($delay) {
        return $redis->zAdd($queue_delay, $now + $delay, $package_str);
    }
    return $redis->lPush($queue_waiting.$queue, $package_str);
}
```

여기서 매개변수 `$redis`는 Redis 인스턴스입니다. 예를 들어, Redis 확장 사용 방법은 다음과 같습니다:
```php
$redis = new Redis;
$redis->connect('127.0.0.1', 6379);
$queue = 'user-1';
$data= ['some', 'data'];
redis_queue_send($redis, $queue, $data);
````

## 소비
소비 프로세스 구성 파일은 `config/plugin/webman/redis-queue/process.php`에 있습니다.
소비자 디렉토리는 `app/queue/redis/`에 있습니다.

`php webman redis-queue:consumer my-send-mail` 명령을 실행하면 `app/queue/redis/MyMailSend.php` 파일이 생성됩니다.

> **참고**
> 명령이 없으면 수동으로 생성할 수 있습니다.

```php
<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class MyMailSend implements Consumer
{
    // 소비할 큐 이름
    public $queue = 'send-mail';

    // 연결 이름, plugin/webman/redis-queue/redis.php에있는 연결에 해당
    public $connection = 'default';

    // 소비
    public function consume($data)
    {
        // 역직렬화 할 필요 없음
        var_export($data); // ['to' => 'tom@gmail.com', 'content' => 'hello'] 출력
    }
}
```

> **주의**
> 소비 중 예외나 오류가 발생하지 않았을 경우 소비 성공으로 간주되며, 그렇지 않으면 소비 실패로 간주되어 재시도 대기열로 이동합니다. redis-queue에는 ack 메커니즘이 없습니다. 이를 자동 ack(예외나 오류 없음)으로 간주할 수 있습니다. 메시지 소비 중 현재 메시지를 처리하지 않았다고 표시하려면 수동으로 예외를 throw하여 현재 메시지가 재시도 대기열로 이동하도록 할 수 있습니다. 이것은 실제로 ack 메커니즘과 별 다를 바 없습니다.

> **팁**
> 소비자는 여러 서버 및 프로세스를 지원하며, 동일한 메시지는 **다시** 소비되지 않습니다. 소비한 메시지는 자동으로 큐에서 제거되므로 수동으로 삭제할 필요가 없습니다.

> **팁**
> 소비 프로세스는 여러 종류의 다른 큐를 동시에 소비하고, 구성 파일인 `process.php`를 수정할 필요 없이 새로운 큐를 추가할 때는 `app/queue/redis`에 해당하는 `Consumer` 클래스를 추가하고 속성 `$queue`를 사용하여 소비할 큐 이름을 지정하면 됩니다.

> **팁**
> Windows 사용자는 php windows.php를 실행하여 webman을 시작해야합니다. 그렇지 않으면 소비 프로세스가 시작되지 않습니다.
## 다른 대기열에 대해 다른 소비 프로세스 설정
기본적으로 모든 소비자가 동일한 소비 프로세스를 공유합니다. 그러나 때로는 일부 대기열을 독립적으로 처리해야 할 필요가 있습니다. 예를 들어 처리 속도가 느린 업무를 하나의 그룹으로 처리하고, 처리 속도가 빠른 업무를 다른 그룹으로 처리해야 하는 경우가 있습니다. 이를 위해 소비자를 두 개의 디렉터리로 분리할 수 있습니다. 예를 들어 `app_path() . '/queue/redis/fast'` 및 `app_path() . '/queue/redis/slow'`와 같이 소비 클래스의 네임스페이스를 동일하게 바꾸어 주어야 합니다. 그러면 다음과 같이 설정할 수 있습니다:
```php
return [
    ...기타 설정은 생략...
    
    'redis_consumer_fast'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // 소비자 클래스 디렉터리
            'consumer_dir' => app_path() . '/queue/redis/fast'
        ]
    ],
    'redis_consumer_slow'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // 소비자 클래스 디렉터리
            'consumer_dir' => app_path() . '/queue/redis/slow'
        ]
    ]
];
```

디렉터리 분류 및 해당 구성을 통해 다른 소비자에 대해 다른 소비 프로세스를 쉽게 설정할 수 있습니다.

## 다중 redis 구성
#### 구성
`config/plugin/webman/redis-queue/redis.php`
```php
<?php
return [
    'default' => [
        'host' => 'redis://192.168.0.1:6379',
        'options' => [
            'auth' => null,       // 패스워드, 문자열 형식, 선택 사항
            'db' => 0,            // 데이터베이스
            'max_attempts'  => 5, // 소비 실패 후 재시도 횟수
            'retry_seconds' => 5, // 재시도 간격, 초 단위
        ]
    ],
    'other' => [
        'host' => 'redis://192.168.0.2:6379',
        'options' => [
            'auth' => null,       // 패스워드, 문자열 형식, 선택 사항
            'db' => 0,             // 데이터베이스
            'max_attempts'  => 5, // 소비 실패 후 재시도 횟수
            'retry_seconds' => 5, // 재시도 간격, 초 단위
        ]
    ],
];
```

`other`를 키로한 새로운 redis 구성이 추가되었음에 유의하세요.

####  다중 redis로 메시지 전달

```php
// `default`를 키로한 대기열로 메시지를 보냄
Client::connection('default')->send($queue, $data);
Redis::connection('default')->send($queue, $data);
//  다음과 같음
Client::send($queue, $data);
Redis::send($queue, $data);

// `other`를 키로한 대기열로 메시지를 보냄
Client::connection('other')->send($queue, $data);
Redis::connection('other')->send($queue, $data);
```

#### 다중 redis로 소비
`other`를 키로한 대기열의 메시지를 소비하는 설정
```php
namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class SendMail implements Consumer
{
    // 소비할 대기열 이름
    public $queue = 'send-mail';

    // === 이 부분에서 other로 설정함으로써 설정에서 other를 키로한 대기열을 소비함을 나타냄 ===
    public $connection = 'other';

    // 소비
    public function consume($data)
    {
        // 역직렬화할 필요 없음
        var_export($data);
    }
}
```

## 일반적인 문제

**'Workerman\Redis\Exception: Workerman Redis Wait Timeout (600 seconds)' 오류가 발생하는 이유는 무엇인가요?**

이 오류는 비동기 전송 인터페이스 `Client::send()`에서만 발생합니다. 비동기 전송은 먼저 메시지를 로컬 메모리에 저장한 다음 프로세스가 redis로 메시지를 전송합니다. 만약 redis가 메시지 수신 속도가 메시지 생성 속도보다 느리거나, 프로세스가 다른 업무로 바빠서 메모리의 메시지를 redis로 충분히 동기화하지 못할 경우 메시지가 쌓여서 발생합니다. 메시지가 600초 이상 쌓일 경우 이 오류가 발생합니다.

해결책: 메시지 전송은 동기적인 `Redis::send()` 인터페이스를 사용합니다.
