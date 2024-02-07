`support\Context` 클래스는 요청 컨텍스트 데이터를 저장하는 데 사용되며, 요청이 완료되면 해당 컨텍스트 데이터가 자동으로 삭제됩니다. 즉, 컨텍스트 데이터의 수명은 요청 수명을 따릅니다. `support\Context`는 Fiber, Swoole, Swow 코루틴 환경을 지원합니다.

더 많은 정보는 [webman 코루틴](./fiber.md)를 참조하세요.

# 인터페이스
다음과 같은 인터페이스가 제공됩니다.

## 컨텍스트 데이터 설정
```php
Context::set(string $name, mixed $value);
```

## 컨텍스트 데이터 가져오기
```php
Context::get(string $name = null);
```

## 컨텍스트 데이터 삭제
```php
Context::delete(string $name);
```

> **주의사항**
> 프레임워크는 요청이 완료된 후에 자동으로 Context::destroy() 인터페이스를 호출하여 컨텍스트 데이터를 소멸시킵니다. 비즈니스 로직에서는 Context::destroy()를 수동으로 호출해서는 안 됩니다.

# 예시
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        return Context::get('name');
    }
}
```

# 주의사항
**코루틴을 사용할 때**는 **요청과 관련된 상태 데이터**를 전역 변수나 정적 변수에 저장해서는 안 됩니다. 이렇게 하면 전역 데이터 오염이 발생할 수 있습니다. 올바른 방법은 Context를 사용하여 이러한 데이터를 저장하고 검색하는 것입니다.
