# 예외 처리

## 설정
`config/exception.php`
```php
return [
    // 여기에는 예외 처리 클래스를 설정합니다
    '' => support\exception\Handler::class,
];
```
다중 애플리케이션 모드에서는 각 애플리케이션별로 예외 처리 클래스를 개별적으로 구성할 수 있습니다. 자세한 내용은 [다중 애플리케이션](multiapp.md)을 참조하세요.


## 기본 예외 처리 클래스
webman에서는 기본적으로 예외를 처리하는 데 `support\exception\Handler` 클래스를 사용합니다. 기본 예외 처리 클래스를 변경하려면 구성 파일 `config/exception.php`를 수정해야 합니다. 예외 처리 클래스는 `Webman\Exception\ExceptionHandlerInterface` 인터페이스를 구현해야 합니다.
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
     * 응답 랜더링
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```



## 응답 랜더링
예외 처리 클래스의 `render` 메서드는 응답을 랜더링하는 데 사용됩니다.

만약 구성 파일 `config/app.php`에서 `debug` 값이 `true`인 경우(이하 `app.debug=true`로 지칭함), 자세한 예외 정보가 반환되고, 그렇지 않으면 간단한 예외 정보가 반환됩니다.

요청이 JSON 응답을 기대하는 경우 예외 정보는 다음과 유사한 JSON 형식으로 반환됩니다.
```json
{
    "code": "500",
    "msg": "예외 정보"
}
```
`app.debug=true`인 경우 JSON 데이터에는 추가적으로 `trace` 필드가 포함되어 자세한 호출 스택이 반환됩니다.

기본 예외 처리 로직을 변경하려면 사용자 정의 예외 처리 클래스를 작성할 수 있습니다.

# 비즈니스 예외 BusinessException
가끔 특정 중첩 함수에서 요청을 중단하고 클라이언트에게 오류 메시지를 반환하고 싶을 때, 이를 위해 `BusinessException`을 throw하여 이를 수행할 수 있습니다.
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
            throw new BusinessException('인수 오류', 3000);
        }
    }
}
```

위 예제는 다음과 같은 결과를 반환합니다.
```json
{"code": 3000, "msg": "인수 오류"}
```

> **참고**
> 비즈니스 예외 BusinessException은 비즈니스 try-catch로 처리할 필요가 없으며, 프레임워크가 자동으로 캐치하고 요청 유형에 맞는 출력을 반환합니다.

## 사용자 정의 비즈니스 예외

위의 응답이 요구 사항에 부합하지 않는 경우, 예를 들어 `msg`를 `message`로 변경하려면 `MyBusinessException`을 사용자 정의할 수 있습니다.

`app/exception/MyBusinessException.php`를 다음과 같이 작성하십시오.
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
        // JSON 요청에는 JSON 데이터가 반환됨
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // JSON이 아닌 요청의 경우 페이지를 반환함
        return new Response(200, [], $this->getMessage());
    }
}
```

이제 비즈니스에서 다음을 호출하면
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('인수 오류', 3000);
```
JSON 요청은 다음과 유사한 JSON을 수신합니다.
```json
{"code": 3000, "message": "인수 오류"}
```

> **노트**
> BusinessException 예외는 사용자 입력 매개변수가 잘못된 경우와 같이 예측 가능한 비즈니스 예외이므로 프레임워크는 치명적인 오류로 간주하지 않으며 로그를 남기지 않습니다.

## 요약
현재 요청을 중단하고 클라이언트에게 정보를 반환하고 싶을 때는 `BusinessException`을 사용하는 것이 좋습니다.
