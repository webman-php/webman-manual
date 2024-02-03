# 코루틴

> **코루틴 요구 사항**
> PHP>=8.1 workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> webman의 업그레이드 명령 `composer require workerman/webman-framework ^1.5.0`
> workerman의 업그레이드 명령 `composer require workerman/workerman ^5.0.0`
> Fiber 코루틴을 설치해야 함 `composer require revolt/event-loop ^1.0.0`

# 예시
### 지연 응답

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // 1.5초 동안 잠자기
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()`는 PHP 내장 `sleep()` 함수와 유사하지만, `Timer::sleep()`는 프로세스를 차단하지 않는다.

### HTTP 요청 보내기

> **주의**
> `composer require workerman/http-client ^2.0.0`를 설치해야 함

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // 동기식 방법으로 비동기 요청 보내기
        return $response->getBody()->getContents();
    }
}
```
`$client->get('http://example.com')`과 같은 요청은 차단되지 않으며, 이는 webman에서 비차단적인 HTTP 요청을 보내므로 응용 프로그램 성능을 향상시킬 수 있다.

더 많은 정보는 [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)를 참조하십시오.

### support\Context 클래스 추가

`support\Context` 클래스는 요청 컨텍스트 데이터를 저장하는 데 사용되며, 요청이 완료되면 해당 컨텍스트 데이터는 자동으로 삭제된다. 즉, 컨텍스트 데이터의 수명 주기는 요청 수명 주기를 따른다. `support\Context`는 Fiber, Swoole, Swow 코루틴 환경을 지원한다.

### Swoole 코루틴

swoole 확장을 설치한 후(버전은 5.0 이상이어야 함), config/server.php를 구성하여 swoole 코루틴을 사용할 수 있다.
```php
'event_loop' => \Workerman\Events\Swoole::class,
```
더 많은 정보는 [workerman 이벤트 드리븐](https://www.workerman.net/doc/workerman/appendices/event.html)를 참조하십시오.

### 전역 변수 오염

코루틴 환경에서는 **요청 관련** 상태 정보를 전역 변수나 정적 변수에 저장하는 것을 금지한다. 왜냐하면 이는 전역 변수 오염을 야기할 수 있기 때문이다. 예를 들어,

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```

프로세스 수를 1로 설정한 후에 두 번의 연속 요청을 보낼 때  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
우리는 두 요청의 반환 값이 각각 `lilei`와 `hanmeimei`가 되길 기대하지만, 실제로는 모두 `hanmeimei`가 반환된다.  
이는 두 번째 요청이 정적 변수 `$name`을 덮어썼기 때문이다. 첫 번째 요청의 슬립이 끝나 반환될 때 이미 정적 변수 `$name`은 `hanmeimei`가 되었기 때문이다.

**하지만 올바른 방법은 context를 사용하여 요청 상태 데이터를 저장하는 것이다.**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        Timer::sleep(5);
        return Context::get('name');
    }
}
```

**지역 변수는 데이터 오염을 일으키지 않는다.**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        Timer::sleep(5);
        return $name;
    }
}
```
지역 변수는 코루틴 간에 상호 접근할 수 없기 때문에 안전하다.

# 코루틴에 대하여
코루틴은 마법의 해결책이 아니다. 코루틴을 도입하면 전역 변수/정적 변수 오염 문제에 주의해야 하며 context를 설정해야 한다. 또한 코루틴 환경에서 버그를 디버그하는 것은 차단 프로그래밍과 비교하여 더 복잡하다.

webman의 차단 프로그래밍은 실제로 이미 충분히 빠르며, [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2)에서 최근 3년간 세 차례의 벤치 마크 결과를 보면, webman의 차단 프로그래밍은 데이터베이스 비즈니스를 포함할 때 go의 웹 프레임워크 gin, echo 등보다 성능이 거의 2배 높으며 전통적인 프레임워크인 laravel과 비교하여 성능이 거의 40배 높다.
![](../../assets/img/benchemarks-go-sw.png?)

데이터베이스 및 레디스가 내부 네트워크에 있을 때, 다중 프로세스의 차단 프로그래밍 성능은 종종 코루틴보다 더 높을 수 있다. 왜냐하면 데이터베이스, 레디스 등이 충분히 빠를 때, 코루틴 생성, 스케줄링, 파괴의 비용이 프로세스 전환의 비용보다 커질 수 있기 때문이다. 그래서 이러한 경우 코루틴을 도입해도 성능이 크게 향상되지 않을 수 있다.

# 언제 코루틴을 사용해야 하는가
비즈니스가 느린 요청이 있는 경우, 예를 들어 비즈니스가 서드 파티 API에 요청을 보내야 하는 경우, [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)를 사용하여 비동기적 HTTP 호출을 하여 응용 프로그램의 동시성 능력을 향상시킬 수 있다.
