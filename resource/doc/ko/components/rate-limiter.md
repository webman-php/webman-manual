# 속도 제한기

webman 속도 제한기, 어노테이션 기반 제한 지원.
apcu, redis, memory 드라이버 지원.

## 소스 코드

https://github.com/webman-php/limiter

## 설치

```
composer require webman/limiter
```

## 사용

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\annotation\Limit;

class UserController
{

    #[Limit(limit: 10)]
    public function index(): string
    {
        // 기본은 IP 제한, 기본 단위 시간은 1초
        return 'IP당 초당 최대 10개 요청';
    }

    #[Limit(limit: 100, ttl: 60, key: Limit::UID)]
    public function search(): string
    {
        // key: Limit::UID, 사용자 ID 기준 제한, session('user.id') 비어있지 않아야 함
        return '사용자당 60초 최대 100회 검색';
    }

    #[Limit(limit: 1, ttl: 60, key: Limit::SID, message: '인당 분당 1통만 이메일 발송 가능')]
    public function sendMail(): string
    {
        // key: Limit::SID, session_id 기준 제한
        return '이메일 발송 성공';
    }

    #[Limit(limit: 100, ttl: 24*60*60, key: 'coupon', message: '오늘의 쿠폰은 소진되었습니다. 내일 다시 이용해 주세요')]
    #[Limit(limit: 1, ttl: 24*60*60, key: Limit::UID, message: '사용자당 하루 1회만 쿠폰 수령 가능')]
    public function coupon(): string
    {
        // key: 'coupon', 커스텀 key로 전역 제한, 하루 최대 100장 쿠폰
        // 사용자 ID 기준 제한, 사용자당 하루 1회만 쿠폰 수령 가능
        return '쿠폰 발송 성공';
    }

    #[Limit(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: '휴대폰 번호당 하루 최대 5통 SMS')]
    public function sendSms2(): string
    {
        // key가 변수일 때 [클래스, 정적 메서드]로 key 획득, 예: [UserController::class, 'getMobile']은 UserController::getMobile() 반환값을 key로 사용
        return 'SMS 발송 성공';
    }

    /**
     * 커스텀 key, 휴대폰 번호 획득, 정적 메서드여야 함
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[Limit(limit: 1, ttl: 10, key: Limit::IP, message: '속도 제한', exception: RuntimeException::class)]
    public function testException(): string
    {
        // 초과 시 기본 예외는 support\limiter\RateLimitException, exception 파라미터로 변경 가능
        return 'ok';
    }

}
```

**설명**

* 고정 윈도우 알고리즘 사용
* ttl 기본 단위 시간은 1초
* ttl로 단위 시간 설정, 예: `ttl:60`은 60초
* 기본 제한 차원은 IP (기본 `127.0.0.1`은 제한 없음, 아래 설정 참조)
* 내장: IP 제한, UID 제한 (`session('user.id')` 비어있지 않아야 함), SID 제한 (`session_id` 기준)
* nginx 프록시 사용 시 IP 제한에는 `X-Forwarded-For` 헤더 전달, [nginx 프록시](../others/nginx-proxy.md) 참조
* 초과 시 `support\limiter\RateLimitException` 트리거, `exception:xx`로 커스텀 예외 클래스 지정 가능
* 초과 시 기본 에러 메시지는 `Too Many Requests`, `message:xx`로 커스텀 메시지 지정 가능
* 기본 에러 메시지는 [다국어](translation.md)로도 수정 가능, Linux 참고:

```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Too Many Requests' => '请求频率受限'
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

## API

코드에서 직접 속도 제한기를 호출하는 예:

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // 여기서 mobile을 key로 사용
        Limiter::check($mobile, 5, 24*60*60, '휴대폰 번호당 하루 최대 5통 SMS');
        return 'SMS 발송 성공';
    }
}
```

## 설정

**config/plugin/webman/limiter/app.php**

```
<?php

use support\limiter\RateLimitException;

return [
    'enable' => true,
    'driver' => 'auto', // auto, apcu, memory, redis
    'stores' => [
        'redis' => [
            'connection' => 'default',
        ]
    ],
    // 이 IP들은 속도 제한 대상 아님 (key가 Limit::IP일 때만 유효)
    'ip_whitelist' => [
        '127.0.0.1',
    ],
    'exception' => RateLimitException::class
];
```

* **enable**: 속도 제한 활성화 여부
* **driver**: `auto`, `apcu`, `memory`, `redis` 중 하나, `auto`는 `apcu`(우선)와 `memory` 중 자동 선택
* **stores**: redis 설정, `connection`은 `config/redis.php`의 해당 key
* **ip_whitelist**: 화이트리스트 IP는 속도 제한 대상 아님 (key가 `Limit::IP`일 때만 유효)

## driver 선택

**memory**

* 소개
  확장 불필요, 최고 성능.

* 제한
  현재 프로세스에만 유효, 프로세스 간 데이터 공유 없음, 클러스터 제한 미지원.

* 적용 시나리오
  Windows 개발 환경; 엄격한 제한 불필요한 업무; CC 공격 방어.

**apcu**

* 확장 설치
  apcu 확장 필요, php.ini 설정:

```
apc.enabled=1
apc.enable_cli=1
```

php.ini 위치는 `php --ini`로 확인

* 소개
  우수한 성능, 다중 프로세스 공유 지원.

* 제한
  클러스터 미지원

* 적용 시나리오
  모든 개발 환경; 운영 단일 서버 제한; 클러스터에서 엄격한 제한 불필요; CC 공격 방어.

**redis**

* 의존
  redis 확장과 Redis 컴포넌트 필요, 설치:

```
composer require -W webman/redis illuminate/events
```

* 소개
  apcu보다 낮은 성능, 단일 서버 및 클러스터 정밀 제한 지원

* 적용 시나리오
  개발 환경; 운영 단일 서버; 클러스터 환경
