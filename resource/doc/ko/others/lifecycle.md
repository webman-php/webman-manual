# 라이프 사이클

## 프로세스 라이프 사이클
- 각 프로세스는 매우 긴 수명을 갖고 있다.
- 각 프로세스는 독립적으로 실행되며 서로 간섭하지 않는다.
- 각 프로세스는 자체 수명 내에서 여러 요청을 처리할 수 있다.
- 프로세스는 `stop`, `reload`, `restart` 명령을 받으면 종료하여 현재 수명을 끝내게 된다.

> **팁**
> 각 프로세스는 독립적이며 간섭하지 않는다. 이는 각 프로세스가 자신의 리소스, 변수 및 클래스 인스턴스를 유지하고 있다는 것을 의미한다. 각 프로세스는 자체 데이터베이스 연결을 가지며, 몇 가지 싱글톤은 각 프로세스마다 한 번 초기화된다. 그러므로 여러 프로세스는 여러 번 초기화될 것이다.

## 요청 라이프 사이클
- 각 요청은 `$request` 객체를 생성한다.
- `$request` 객체는 요청 처리가 완료되면 회수된다.

## 컨트롤러 라이프 사이클
- 각 컨트롤러는 각 프로세스에서 한 번만 인스턴스화되며, 여러 프로세스에서는 여러 번 인스턴스화된다(컨트롤러 재사용이 꺼져 있음. [컨트롤러 라이프 사이클](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F) 참고).
- 컨트롤러 인스턴스는 현재 프로세스 내에서 여러 요청에 공유된다(컨트롤러 재사용이 꺼져 있음).
- 컨트롤러 수명은 프로세스가 종료되면 끝난다(컨트롤러 재사용이 꺼져 있음).

## 변수 수명과 관련하여
webman은 PHP를 기반으로 하기 때문에 PHP의 변수 회수 메커니즘을 완전히 따른다. 비즈니스 로직에서 생성된 임시 변수 및 `new` 키워드로 생성된 클래스 인스턴스는 함수나 메소드가 종료될 때 자동으로 회수되며, `unset`으로 수동 해제할 필요가 없다. 즉, webman의 개발 경험은 전통적인 프레임워크 개발과 거의 동일하다.

예를 들어 아래 예제에서 `$foo` 인스턴스는 `index` 메소드가 실행되고 난 후 자동으로 해제된다:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // 가정으로 Foo 클래스가 있다고 가정
        return response($foo->sayHello());
    }
}
```
만약 특정 클래스의 인스턴스를 재사용하려면 해당 클래스를 정적 속성이나 장기간 객체(예: 컨트롤러)의 속성에 저장하거나, `Container` 컨테이너의 `get` 메서드를 사용하여 클래스의 인스턴스를 초기화할 수 있다. 예를 들어:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Container;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = Container::get(Foo::class);
        return response($foo->sayHello());
    }
}
```

`Container::get()` 메서드는 클래스의 인스턴스를 생성하고 저장하는 데 사용되며, 동일한 매개변수로 다시 호출하면 이전에 생성된 클래스 인스턴스가 반환된다.

> **참고**
> `Container::get()`은 생성자 인수가 없는 인스턴스만 초기화할 수 있다. `Container::make()`는 생성자 인자를 가진 인스턴스를 만들 수 있지만, `Container::get()`과는 달리 항상 새로운 인스턴스를 반환한다.

# 메모리 누수에 대해
대부분의 경우, 비즈니스 코드에서는 메모리 누수가 발생하지 않는다(사용자의 피드백으로 메모리 누수가 거의 없다고 한다). 장기간의 배열 데이터가 무한히 확장되지 않도록만 조심하면 된다. 아래 코드를 확인해보자:
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    // 배열 속성
    public $data = [];
    
    public function index(Request $request)
    {
        $this->data[] = time();
        return response('hello index');
    }

    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```
컨트롤러는 기본적으로 장기간의 수명을 가지고 있으며(컨트롤러 재사용이 꺼져 있음), 동일한 컨트롤러의 `$data` 배열 속성도 장기간에 걸쳐 존재한다. 그렇기 때문에 `foo/index` 요청이 계속 증가함에 따라 `$data` 배열 요소가 계속 증가하여 메모리 누수를 초래할 수 있다.

자세한 내용은 [메모리 누수](./memory-leak.md)를 참고하십시오.
