# 코루틴

> **코루틴 요구 사항**
> PHP >= 8.1, workerman >= 5.0, webman-framework >= 1.5, revol/event-loop > 1.0.0
> webman 업그레이드 명령: `composer require workerman/webman-framework ^1.5.0`
> workerman 업그레이드 명령: `composer require workerman/workerman ^5.0.0`
> Fiber 코루틴은 `composer require revolt/event-loop ^1.0.0`로 설치해야 합니다.

# 예제
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
        // 1.5초 동안 대기
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()`는 PHP의 `sleep()` 함수와 유사하지만, `Timer::sleep()`는 프로세스를 차단하지 않습니다.


### HTTP 요청 보내기

> **주의**
> `composer require workerman/http-client ^2.0.0`를 설치해야 합니다.

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
        $response = $client->get('http://example.com'); // 비동기 요청을 동기적으로 실행
        return $response->getBody()->getContents();
    }
}
```
동일한 `$client->get('http://example.com')` 요청은 차단되지 않으며, 이는 webman에서 비차단적인 HTTP 요청을 시작하여 응용 프로그램 성능을 향상시킬 수 있습니다.

자세한 내용은 [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)를 참조하세요.

### support\Context 클래스 추가

`support\Context` 클래스는 요청 컨텍스트 데이터를 저장하는 데 사용되며, 요청이 완료되면 해당 컨텍스트 데이터는 자동으로 삭제됩니다. 즉, 컨텍스트 데이터 수명은 요청 수명에 따라 지정됩니다. `support\Context`는 Fiber, Swoole, Swow 코루틴 환경을 지원합니다.

### Swoole 코루틴

swoole 확장 기능을 설치한 후 (swoole >= 5.0) `config/server.php` 파일을 구성하여 swoole 코루틴을 활성화합니다.
```php
'event_loop' => \Workerman\Events\Swoole::class,
```
더 많은 정보는 [workerman 이벤트 드라이버](https://www.workerman.net/doc/workerman/appendices/event.html)를 참조하세요.

### 전역 변수 오염

코루틴 환경에서는 **요청 관련** 상태 정보를 전역 변수나 정적 변수에 저장하는 것을 금지합니다. 이는 전역 변수 오염을 유발할 수 있기 때문입니다. 예를 들어,

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
프로세스 수를 1로 설정하고, 두 번의 연속적인 요청을 보낼 때  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
우리는 두 요청 결과가 각각 `lilei`와 `hanmeimei`여야 한다고 기대하지만 실제로는 둘 다 `hanmeimei`가 반환됩니다.
두 번째 요청이 정적 변수 `$name`을 덮어쓰기 때문에 첫 번째 요청이 대기를 마치고 반환할 때 정적 변수 `$name`은 이미 `hanmeimei`로 변경되어 있기 때문입니다.

**올바른 방법은 요청 상태 데이터를 컨텍스트에 저장하는 것입니다.**
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

**지역 변수는 데이터 오염을 일으키지 않습니다.**
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
지역 변수는 코루틴 간에 서로 접근할 수 없기 때문에 지역 변수를 사용하면 안전합니다.

# 코루틴에 대해

코루틴은 마법처럼 작동하지 않습니다. 코루틴을 도입하면 전역 변수/정적 변수 오염 문제를 주의해야 하며, 컨텍스트를 설정해야 합니다. 또한 코루틴 환경에서 버그를 디버깅하는 것은 차단식 프로그래밍보다 더 복잡할 수 있습니다.

webman 차단식 프로그래밍은 실제로 충분히 빠릅니다. [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2)에서 최근 3년 동안 3차례에 걸친 벤치마크 데이터를 보면, webman 차단식 프로그래밍은 데이터베이스 업무가 포함된 경우 go 웹 프레임워크인 gin, echo 등의 성능보다 거의 2배 높고, 전통적인 프레임워크인 laravel보다 거의 40배 높습니다.
![](../../assets/img/benchemarks-go-sw.png?)


데이터베이스, 레디스 등이 내부 네트워크에 있는 경우, 다중 프로세스 차단식 프로그래밍의 성능이 종종 코루틴을 도입하는 것보다 높을 수 있습니다. 그 이유는 데이터베이스, 레디스 등이 충분히 빨라서 코루틴을 생성, 스케줄링, 파괴하는 비용이 프로세스 전환 비용보다 크기 때문입니다. 그러므로 코루틴을 도입해도 성능을 현저하게 향상시키지 못할 수 있습니다.

# 언제 코루틴을 사용해야 하는가
업무에 지연이 발생하는 경우, 예를 들어 업무가 제3자 API에 액세스해야 하는 경우, [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)를 사용하여 비순차적인 방법으로 HTTP 호출을 시작하여 응용 프로그램의 동시성 능력을 향상시킬 수 있습니다.
