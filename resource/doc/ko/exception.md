# 예외 처리

## 구성
`config/exception.php`
```php
return [
    // 여기에 예외 처리 클래스를 구성합니다
    '' => support\exception\Handler::class,
];
```
다중 애플리케이션 모드의 경우 각 애플리케이션에 대해 개별적으로 예외 처리 클래스를 구성할 수 있습니다. 자세한 내용은 [다중 애플리케이션](multiapp.md)을 참조하십시오.


## 기본 예외 처리 클래스
웹맨에서는 기본적으로 `support\exception\Handler` 클래스가 예외를 처리합니다. 기본 예외 처리 클래스를 변경하려면 구성 파일 `config/exception.php`을 수정해야 합니다. 예외 처리 클래스는 `Webman\Exception\ExceptionHandlerInterface` 인터페이스를 구현해야 합니다.
```php
interface ExceptionHandlerInterface
{
    /**
     * 로그 기록
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * 렌더링 응답
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```



## 응답 렌더링
예외 처리 클래스의 `render` 메서드는 응답을 렌더링하는 데 사용됩니다.

구성 파일 `config/app.php`에서 `debug` 값이 `true`인 경우(이하 `app.debug=true`로 약칭), 자세한 예외 정보가 반환되며, 그렇지 않으면 간략한 예외 정보가 반환됩니다.

요청이 JSON 응답을 기대하는 경우, 반환되는 예외 정보는 다음과 유사한 JSON 형식으로 반환됩니다.
```json
{
    "code": "500",
    "msg": "예외 정보"
}
```
`app.debug=true`인 경우 JSON 데이터에는 추가적으로 `trace` 필드가 포함되어 자세한 호출 스택이 반환됩니다.

기본 예외 처리 논리를 변경하려면 사용자 고유의 예외 처리 클래스를 작성할 수 있습니다.

# 비즈니스 예외 BusinessException
가끔 특정 중첩 함수에서 요청을 중지하고 클라이언트에게 오류 정보를 반환하고자 할 때는 `BusinessException`을 throw하여 이를 수행할 수 있습니다.
예를 들어:

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->chackInpout($request->post());
        return response('hello index');
    }
    
    protected function chackInpout($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('매개변수 오류', 3000);
        }
    }
}
```

위의 예제는 다음과 같은 JSON을 반환합니다.
```json
{"code": 3000, "msg": "매개변수 오류"}
```

> **참고:**
> 비즈니스 예외 BusinessException은 비즈니스에서 예상 가능하므로, 프레임워크가 치명적 오류로 간주하지 않으며 로그를 기록하지 않습니다.

## 사용자 정의 비즈니스 예외

위의 응답이 요구 사항에 부합하지 않는 경우, 예를 들어 `msg` 대신 `message`로 변경하려면 `MyBusinessException`을 사용자 정의할 수 있습니다.

다음 내용의 `app/exception/MyBusinessException.php`을 만듭니다.
```php
<?php

namespace app\exception;

use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class MyBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        // JSON 요청에는 JSON 데이터를 반환
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // JSON 이외의 요청에는 페이지를 반환
        return new Response(200, [], $this->getMessage());
    }
}
```

이제 비즈니스에서 다음을 호출하면
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('매개변수 오류', 3000);
```
JSON 요청이 다음과 유사한 JSON을 수신합니다.
```json
{"code": 3000, "message": "매개변수 오류"}
```

> **팁:**
> BusinessException 예외는 예측 가능한 비즈니스 예외(예: 사용자 입력 매개변수 오류)이므로 프레임워크가 치명적 오류로 간주하지 않고 로그를 기록하지 않습니다.

## 요약
현재 요청을 중지하고 클라이언트에게 정보를 반환하고 싶을 때는 `BusinessException`을 사용하는 것이 좋습니다.
