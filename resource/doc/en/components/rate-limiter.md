# Rate Limiter

webman rate limiter with annotation-based limiting support.
Supports apcu, redis, and memory drivers.

## Source Repository

https://github.com/webman-php/limiter

## Installation

```
composer require webman/limiter
```

## Usage

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
        // Default is IP-based rate limiting, default time window is 1 second
        return 'Maximum 10 requests per IP per second';
    }

    #[Limit(limit: 100, ttl: 60, key: Limit::UID)]
    public function search(): string
    {
        // key: Limit::UID, rate limit by user ID, requires session('user.id') to be non-empty
        return 'Maximum 100 searches per user per 60 seconds';
    }

    #[Limit(limit: 1, ttl: 60, key: Limit::SID, message: 'Only 1 email per person per minute')]
    public function sendMail(): string
    {
        // key: Limit::SID, rate limit by session_id
        return 'Email sent successfully';
    }

    #[Limit(limit: 100, ttl: 24*60*60, key: 'coupon', message: 'Today\'s coupons are sold out, please try again tomorrow')]
    #[Limit(limit: 1, ttl: 24*60*60, key: Limit::UID, message: 'Each user can only claim one coupon per day')]
    public function coupon(): string
    {
        // key: 'coupon', custom key for global rate limiting, max 100 coupons per day
        // Also rate limit by user ID, each user can only claim one coupon per day
        return 'Coupon sent successfully';
    }

    #[Limit(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: 'Maximum 5 SMS per phone number per day')]
    public function sendSms2(): string
    {
        // When key is a variable, use [class, static_method] to get the key, e.g. [UserController::class, 'getMobile'] calls UserController::getMobile() return value as key
        return 'SMS sent successfully';
    }

    /**
     * Custom key, get mobile number, must be a static method
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[Limit(limit: 1, ttl: 10, key: Limit::IP, message: 'Rate limited', exception: RuntimeException::class)]
    public function testException(): string
    {
        // Default exception on limit exceeded is support\limiter\RateLimitException, can be changed via exception parameter
        return 'ok';
    }

}
```

**Notes**
* Uses fixed window algorithm
* Default ttl time window is 1 second
* Set time window via ttl, e.g. `ttl:60` for 60 seconds
* Default rate limit dimension is IP (default `127.0.0.1` is not limited, see configuration section below)
* Built-in IP, UID (requires `session('user.id')` non-empty), and SID (by `session_id`) rate limiting
* When using nginx proxy, pass `X-Forwarded-For` header for IP-based limiting, see [nginx proxy](../others/nginx-proxy.md)
* Triggers `support\limiter\RateLimitException` when limit exceeded, custom exception class via `exception:xx`
* Default error message on limit exceeded is `Too Many Requests`, custom message via `message:xx`
* Default error message can also be modified via [translation](translation.md), Linux reference:
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

Sometimes developers want to call the rate limiter directly in code, see the following example:

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // mobile is used as key here
        Limiter::check($mobile, 5, 24*60*60, 'Maximum 5 SMS per phone number per day');
        return 'SMS sent successfully';
    }
}
```

## Configuration

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
    // These IPs are not rate limited (only effective when key is Limit::IP)
    'ip_whitelist' => [
        '127.0.0.1',
    ],
    'exception' => RateLimitException::class
];
```
* **enable**: Whether to enable rate limiting
* **driver**: One of `auto`, `apcu`, `memory`, `redis`; `auto` automatically selects between `apcu` (preferred) and `memory`
* **stores**: Redis configuration, `connection` corresponds to the key in `config/redis.php`
* **ip_whitelist**: Whitelisted IPs are not rate limited (only effective when key is `Limit::IP`)

## Driver Selection

**memory**

* Introduction
  No extensions required, best performance.

* Limitations
  Rate limiting is effective only for the current process, data is not shared across processes, cluster rate limiting not supported.

* Use Cases
  Windows development environment; business scenarios that do not require strict rate limiting; defending against CC attacks.


**apcu**

* Extension Installation
  Requires apcu extension, and php.ini settings:
```
apc.enabled=1
apc.enable_cli=1
```
To find php.ini location, run `php --ini`

* Introduction
  Excellent performance, supports multi-process shared rate limit data.

* Limitations
  Cluster not supported

* Use Cases
  Any development environment; single-server production rate limiting; cluster scenarios that do not require strict rate limiting; defending against CC attacks.

**redis**

* Dependencies
  Requires redis extension and Redis component, install with:
```
composer require -W webman/redis illuminate/events
```

* Introduction
  Lower performance than apcu, supports both single-server and cluster precise rate limiting

* Use Cases
  Development environment; single-server production; cluster environment
