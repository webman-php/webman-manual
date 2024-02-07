# 라이프사이클

## 프로세스 라이프사이클
- 각 프로세스는 매우 긴 라이프사이클을 갖고 있습니다.
- 각 프로세스는 독립적으로 실행되며 서로 간섭하지 않습니다.
- 각 프로세스는 라이프사이클 내에서 여러 요청을 처리할 수 있습니다.
- 프로세스는 `stop`, `reload`, `restart` 명령을 받으면 종료하여 현재 라이프사이클을 마칩니다.

> **팁**
> 각 프로세스가 독립적이고 서로 간섭하지 않는다는 것은 각 프로세스가 자체 리소스, 변수 및 클래스 인스턴스를 유지한다는 것을 의미합니다. 이는 각 프로세스가 자체 데이터베이스 연결을 유지하고, 여러 프로세스가 여러 번 초기화하여 싱글톤이 각 프로세스에 대해 여러 번 초기화된다는 것을 의미합니다.

## 요청 라이프사이클
- 각 요청은 `$request` 객체를 생성합니다.
- `$request` 객체는 요청 처리가 완료되면 해제됩니다.

## 컨트롤러 라이프사이클
- 각 컨트롤러는 각 프로세스에서 한 번만 인스턴스화되며, 여러 프로세스에서 여러 번 인스턴스화됩니다(컨트롤러 재사용을 닫을 경우 참조 [컨트롤러 라이프사이클](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F))
- 컨트롤러 인스턴스는 현재 프로세스 내에서 여러 요청에 공유됩니다(컨트롤러 재사용을 닫을 경우)
- 컨트롤러 라이프사이클은 프로세스 종료 후에 종료됩니다(컨트롤러 재사용을 닫을 경우)

## 변수 라이프사이클에 대해
webman은 PHP 기반으로 개발되었으며, 따라서 PHP 변수 회수 메커니즘을 완전히 준수합니다. 비즈니스 로직에서 생성된 임시 변수 및 함수 또는 메소드 종료 후 자동으로 회수되므로 수동으로 `unset`할 필요가 없습니다. 즉, webman 개발은 전통적인 프레임워크 개발 경험과 기본적으로 동일합니다. 아래 예에서 `$foo` 인스턴스는 `index` 메소드가 실행될 때 자동으로 해제됩니다.

```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // 여기에 Foo 클래스가 있다고 가정합니다.
        return response($foo->sayHello());
    }
}
```

특정 클래스의 인스턴스가 재사용되길 원한다면, 클래스를 클래스의 정적 속성이나 장기간의 수명 객체(예: 컨트롤러) 속성에 저장하거나 Container 컨테이너의 `get` 메소드를 사용하여 클래스 인스턴스를 초기화할 수 있습니다.

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

`Container::get()` 메소드는 클래스의 인스턴스를 만들고 저장하는 데 사용되며, 동일한 매개변수로 다시 호출될 때 이전에 생성된 클래스 인스턴스를 반환합니다.

> **주의**
> `Container::get()`은 매개변수를 가져오지 않는 인스턴스만을 초기화할 수 있습니다. `Container::make()`는 생성자 인수가 있는 인스턴스를 만들 수 있지만, `Container::get()`와는 달리 고정된 인스턴스를 반환하지 않습니다.

## 메모리 누수에 대해
대부분의 경우 비즈니스 코드에서 메모리 누수가 발생하지 않습니다(매우 드물게 사용자로부터 메모리 누수에 대한 피드백을 받습니다). 장기간 데이터 배열이 무한히 확장되지 않도록 조심하기만 하면 됩니다. 아래 코드를 참고하세요.

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

컨트롤러는 기본적으로 장기간 생명주기를 가지며(컨트롤러 재사용을 닫을 경우), 동일한 컨트롤러의 `$data` 배열 속성도 장기간 생명주기를 갖습니다. `foo/index` 요청이 계속적으로 더해지면, `$data` 배열 요소 수가 계속 증가하여 메모리 누수가 발생합니다.

더 많은 관련 정보는 [메모리 누수](./memory-leak.md)를 참조하십시오.
