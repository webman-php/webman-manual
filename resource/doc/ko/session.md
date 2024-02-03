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

`$request->session();`을 사용하여 `Workerman\Protocols\Http\Session` 인스턴스를 얻어와서 해당 인스턴스의 메소드를 사용하여 세션 데이터를 추가, 수정, 삭제합니다.

> 참고: 세션 객체가 소멸될 때 세션 데이터가 자동으로 저장되므로 `$request->session()`으로 반환된 객체를 전역 배열이나 클래스 멤버에 저장하여 세션을 저장할 수 없게 해야 합니다.

## 모든 세션 데이터 가져오기
```php
$session = $request->session();
$all = $session->all();
```
반환값은 배열입니다. 세션 데이터가 없는 경우 빈 배열이 반환됩니다.

## 세션에서 특정 값 가져오기
```php
$session = $request->session();
$name = $session->get('name');
```
데이터가 없는 경우 null이 반환됩니다.

또한 get 메소드에 두 번째 매개변수로 기본값을 전달할 수 있습니다. 세션 배열에서 해당 값이 찾을 수 없는 경우 기본값이 반환됩니다. 예:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## 세션 저장
특정 데이터를 저장할 때 set 메소드를 사용합니다.
```php
$session = $request->session();
$session->set('name', 'tom');
```
set 메소드는 반환값이 없으며, 세션 객체가 소멸될 때 세션은 자동으로 저장됩니다.

여러 값을 저장할 때는 put 메소드를 사용합니다.
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
put 메소드 역시 반환값이 없습니다.

## 세션 데이터 삭제
특정한 하나 또는 여러 세션 데이터를 삭제할 때는 `forget` 메소드를 사용합니다.
```php
$session = $request->session();
// 하나 삭제
$session->forget('name');
// 여러 개 삭제
$session->forget(['name', 'age']);
```

또한 시스템에서 delete 메소드를 제공하며, forget 메소드와의 차이점은 delete는 하나의 항목만 삭제할 수 있다는 것입니다.
```php
$session = $request->session();
// $session->forget('name');와 동일
$session->delete('name');
```

## 세션에서 값을 가져와 삭제
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
해당 세션이 존재하지 않을 경우 null이 반환됩니다.

## 모든 세션 데이터 삭제
```php
$request->session()->flush();
```
반환값이 없으며, 세션 객체가 소멸될 때 세션은 자동으로 저장소에서 삭제됩니다.

## 해당 세션 데이터가 존재하는지 확인
```php
$session = $request->session();
$has = $session->has('name');
```
해당 세션이 없거나 해당 세션 값이 null인 경우 false를 반환하며, 그 외의 경우 true를 반환합니다.

```
$session = $request->session();
$has = $session->exists('name');
```
위 코드는 세션 데이터가 존재하는지 확인하는데 사용되며, 차이는 해당 세션 항목 값이 null인 경우에도 true를 반환한다는 것입니다.

## Helper 함수 session()
> 2020-12-09 추가

webman은 동일한 기능을 수행하기 위해 Helper 함수 'session()'를 제공합니다.
```php
// 세션 인스턴스 가져오기
$session = session();
// 동일한 표현
$session = $request->session();

// 특정 값 가져오기
$value = session('key', 'default');
// 다음과 동일
$value = session()->get('key', 'default');
// 다음과 동일
$value = $request->session()->get('key', 'default');

// 세션에 값 할당하기
session(['key1'=>'value1', 'key2' => 'value2']);
// 동일한
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// 동일한
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
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
    
    // handler가 FileSessionHandler::class일 경우 값은 file,
    // handler가 RedisSessionHandler::class일 경우 값은 redis
    // handler가 RedisClusterSessionHandler::class일 경우 값은 redis_cluster (레디스 클러스터)
    'type'    => 'file',

    // 각 핸들러에 따른 다른 설정
    'config' => [
        // type이 file일 때의 설정
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // type이 redis일 때의 설정
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
    
    // === webman-framework>=1.3.14 workerman>=4.0.37이 필요한 설정 ===
    'auto_update_timestamp' => false,  // 세션 자동 갱신 여부, 기본적으로 비활성화
    'lifetime' => 7*24*60*60,          // 세션 유효 기간
    'cookie_lifetime' => 365*24*60*60, // 세션 ID를 저장하는 쿠키의 유효 기간
    'cookie_path' => '/',              // 세션 ID를 저장하는 쿠키의 경로
    'domain' => '',                    // 세션 ID를 저장하는 쿠키의 도메인
    'http_only' => true,               // httpOnly 사용 유무, 기본적으로 활성화됨
    'secure' => false,                 // 세션 ID를 오직 HTTPS에서만 사용할지 여부, 기본적으로 비활성화됨
    'same_site' => '',                 // CSRF 공격과 사용자 추적을 방지하기 위한 값, strict/lax/none 중에서 선택 가능
    'gc_probability' => [1, 1000],     // 세션 가비지 수거 확률
];
```

> **주의** 
> webman 1.4.0부터 SessionHandler의 네임스페이스가 변경되었습니다.  
> 기존:
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> 변경:
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  

## 유효기간 설정
webman-framework 버전이 1.3.14보다 낮은 버전의 경우, webman에서 세션 유효 시간은 `php.ini`에서 설정해야 합니다.

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

유효기간을 1440초로 설정하는 경우 다음과 같이 구성합니다.
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **팁**
> `php --ini` 명령을 사용하여 `php.ini`의 위치를 확인할 수 있습니다.
