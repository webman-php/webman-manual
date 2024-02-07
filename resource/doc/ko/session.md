# 세션 관리

## 예시
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('hello ' . $session->get('name'));
    }
}
```

`$request->session();`을 통해 `Workerman\Protocols\Http\Session` 인스턴스를 가져와서 인스턴스의 메소드를 사용하여 세션 데이터를 추가, 수정, 삭제합니다.

> 참고: 세션 객체가 파괴될 때 세션 데이터가 자동으로 저장되므로 `$request->session()`로 반환된 객체를 전역 배열이나 클래스 멤버에 저장하여 세션 데이터를 저장하지 않도록 주의하세요.

## 모든 세션 데이터 가져오기
```php
$session = $request->session();
$all = $session->all();
```
배열이 반환됩니다. 세션 데이터가 없는 경우 빈 배열이 반환됩니다.

## 세션에서 값 가져오기
```php
$session = $request->session();
$name = $session->get('name');
```
데이터가 존재하지 않는 경우 null을 반환합니다.

또한 get 메소드에 두 번째 인수로 기본값을 전달하여 세션 배열에서 해당 값이 없는 경우 기본값을 반환할 수 있습니다. 예:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## 세션 저장
set 메소드를 사용하여 특정 데이터를 저장합니다.
```php
$session = $request->session();
$session->set('name', 'tom');
```
set은 반환값이 없으며, 세션 객체가 파괴될 때 세션이 자동으로 저장됩니다.

여러 값 저장시 put 메소드를 사용합니다.
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
마찬가지로 put도 반환값이 없습니다.

## 세션 데이터 삭제
특정 세션 데이터 또는 여러 세션 데이터를 삭제하기 위해 `forget` 메소드를 사용합니다.
```php
$session = $request->session();
// 항목 하나 삭제
$session->forget('name');
// 여러 항목 삭제
$session->forget(['name', 'age']);
```
또한 시스템은 forget 메소드 대신 delete 메소드를 제공하며, delete는 하나의 항목만 삭제할 수 있습니다.
```php
$session = $request->session();
// $session->forget('name');와 동일
$session->delete('name');
```

## 값 가져와서 세션에서 삭제
```php
$session = $request->session();
$name = $session->pull('name');
```
아래 코드와 동일한 효과가 있습니다.
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
해당 세션이 존재하지 않는 경우 null을 반환합니다.

## 모든 세션 데이터 삭제
```php
$request->session()->flush();
```
반환값이 없으며, 세션 객체가 파괴될 때 세션은 자동으로 저장소에서 삭제됩니다.

## 해당 세션 데이터의 존재 여부 판단
```php
$session = $request->session();
$has = $session->has('name');
```
해당 세션이 존재하지 않거나 세션 값이 null인 경우에는 false를 반환하며, 그 외에는 true를 반환합니다.

```php
$session = $request->session();
$has = $session->exists('name');
```
위 코드도 세션 데이터의 존재 여부를 판단하기 위한 것으로, 해당 세션이 존재하지만 값을 null인 경우에도 true를 반환합니다.

## Helper 함수 session()
> 2020-12-09 추가

웹맨은 동일한 기능을 수행하는 `session()` Helper 함수를 제공합니다.
```php
// 세션 인스턴스 가져오기
$session = session();
// $request->session()과 동일

// 특정 값 가져오기
$value = session('key', 'default');
// session()->get('key', 'default')와 동일
// $request->session()->get('key', 'default')와 동일

// 세션에 값 할당
session(['key1'=>'value1', 'key2' => 'value2']);
// session()->put(['key1'=>'value1', 'key2' => 'value2'])와 동일
// $request->session()->put(['key1'=>'value1', 'key2' => 'value2'])와 동일
```

## 설정 파일
세션 설정 파일은 `config/session.php`에 있으며, 내용은 다음과 유사합니다.
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class 또는 RedisSessionHandler::class 또는 RedisClusterSessionHandler::class
    'handler' => FileSessionHandler::class,
    
    // handler가 FileSessionHandler::class인 경우 file,
    // handler가 RedisSessionHandler::class인 경우 redis
    // handler가 RedisClusterSessionHandler::class인 경우 redis_cluster 또는 redis 클러스터
    'type'    => 'file',

    // 다른 handler에 따라 다른 설정 사용
    'config' => [
        // file 유형인 경우의 설정
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // redis 유형인 경우의 설정
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
        
    ],

    'session_name' => 'PHPSID', // 세션 ID를 저장하는 쿠키 이름
    
    // === webman-framework>=1.3.14 workerman>=4.0.37 이상의 설정 ===
    'auto_update_timestamp' => false,  // 세션 자동 업데이트 여부, 기본적으로 비활성화
    'lifetime' => 7*24*60*60,          // 세션 만료 시간
    'cookie_lifetime' => 365*24*60*60, // 세션 ID를 저장하는 쿠키 만료 시간
    'cookie_path' => '/',              // 세션 ID를 저장하는 쿠키 경로
    'domain' => '',                    // 세션 ID를 저장하는 쿠키 도메인
    'http_only' => true,               // httpOnly 활성화 여부, 기본적으로 활성화
    'secure' => false,                 // https에서만 세션 활성화 여부, 기본적으로 비활성화
    'same_site' => '',                 // CSRF 공격 및 사용자 추적 방지에 사용, strict/lax/none 선택 가능
    'gc_probability' => [1, 1000],     // 세션 소멸 확률
];
```

> **주의** 
> 웹맨은 1.4.0부터 SessionHandler 네임스페이스를 원래의
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> 아래와 같이 변경하였습니다.  
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  

## 만료 기간 설정
webman-framework 1.3.14 미만의 경우, webman에서 세션 만료 시간을 `php.ini`에서 설정해야합니다.

```ini
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

만료 기간을 1440초로 설정한다면 다음과 같이 설정합니다.
```ini
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **참고** 
> `php.ini`의 위치를 찾기 위해 `php --ini` 명령을 사용할 수 있습니다.
