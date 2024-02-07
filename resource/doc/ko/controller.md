# 컨트롤러

`app/controller/FooController.php`에 새로운 컨트롤러 파일을 생성합니다.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

`http://127.0.0.1:8787/foo`에 접속하면 페이지는 `hello index`를 반환합니다.

`http://127.0.0.1:8787/foo/hello`에 접속하면 페이지는 `hello webman`을 반환합니다.

물론 라우트 구성을 통해 라우트 규칙을 변경할 수 있으며 [라우트](route.md)를 참조하세요.

> **팁**  
> 404 오류가 발생하면 `config/app.php`를 열어 `controller_suffix`를 `Controller`로 설정하고 다시 시작하세요.

## 컨트롤러 접미사
webman 1.3부터 `config/app.php`에서 컨트롤러 접미사를 설정할 수 있습니다. `config/app.php`에서 `controller_suffix`가 빈 문자열 `''`로 설정되어 있다면, 컨트롤러는 다음과 같이 작성됩니다.

`app\controller\Foo.php`

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

컨트롤러 접미사를 `Controller`로 설정하는 것을 강력히 권장합니다. 이렇게 하면 컨트롤러 이름과 모델 클래스의 충돌을 피할 수 있으며 보안성을 높일 수 있습니다.

## 설명
- 프레임워크는 자동으로 `support\Request` 객체를 컨트롤러에 전달하며, 이를 통해 사용자 입력 데이터(get, post, header, cookie 등)를 얻을 수 있습니다. 자세한 내용은 [요청](request.md)을 참조하세요.
- 컨트롤러는 숫자, 문자열 또는 `support\Response` 객체를 반환할 수 있지만 다른 유형의 데이터는 반환할 수 없습니다.
- `support\Response` 객체는 `response()`, `json()`, `xml()`, `jsonp()`, `redirect()` 등의 도우미 함수를 통해 생성할 수 있습니다.

## 컨트롤러 라이프사이클

`config/app.php`에 있는 `controller_reuse`가 `false`로 설정되어 있을 때, 각 요청마다 해당하는 컨트롤러 인스턴스가 한 번씩 초기화되며, 요청을 마치면 컨트롤러 인스턴스는 파괴되어 메모리가 회수됩니다. 이것은 전통적인 프레임워크의 작동 메커니즘과 동일합니다.

`config/app.php`에 있는 `controller_reuse`가 `true`로 설정되어 있을 때, 모든 요청은 컨트롤러 인스턴스를 재사용하며, 한 번 생성된 컨트롤러 인스턴스는 메모리에 상주하게 됩니다.

> **참고**
> 컨트롤러 재사용을 비활성화하려면 webman>=1.4.0이 필요하며, 이는 즉, 1.4.0 이전에서는 컨트롤러가 기본적으로 모든 요청을 재사용하며 변경할 수 없습니다.

> **참고**
> 컨트롤러의 속성을 변경하면 해당 변경 사항이 후속 요청에 영향을 미치므로 컨트롤러 재사용을 비활성화해야 합니다.

```php
protected $model;

public function update(Request $request, $id)
{
    $model = $this->getModel($id);
    $model->update();
    return response('ok');
}

public function delete(Request $request, $id)
{
    $model = $this->getModel($id);
    $model->delete();
    return response('ok');
}

protected function getModel($id)
{
    if (!$this->model) {
        $this->model = Model::find($id);
    }
    return $this->model;
}
```

> **팁**  
> 컨트롤러의 `__construct()` 생성자에서는 데이터를 반환하더라도 브라우저에서 이 응답을 수신하지 않습니다.

```php
public function __construct()
{
    return response('hello');
}
```

## 컨트롤러 재사용과 미재사용의 차이
차이점은 다음과 같습니다.

#### 미재사용 컨트롤러
각 요청마다 새로운 컨트롤러 인스턴스를 생성하며, 요청을 마치면 해당 인스턴스를 해제하고 메모리를 회수합니다. 재사용되지 않는 컨트롤러는 전통적인 프레임워크와 같이 대부분의 개발자 습관에 부합합니다. 컨트롤러의 반복적 생성과 파괴로 인해 성능이 재사용 컨트롤러보다 약간 떨어집니다(프레임워크 없이 성능 차이는 약 10% 정도 악화되며 업무를 포함하면 대체로 무시할 수 있습니다).

#### 재사용 컨트롤러
한 프로세스에서 한 번만 컨트롤러를 생성하며, 요청을 마치면 해당 컨트롤러 인스턴스를 해제하지 않고 이후 요청에서 해당 인스턴스를 재사용합니다. 재사용 컨트롤러는 성능이 더 우수하지만 대부분의 개발자 습관에 부합하지 않습니다.

#### 컨트롤러 재사용이 불가능한 경우
요청에서 컨트롤러 속성을 변경하는 경우 컨트롤러 재사용을 활성화할 수 없습니다. 속성의 변경은 후속 요청에 영향을 미칩니다.

일부 개발자들은 각 요청에 대한 초기화를 수행하기 위해 컨트롤러 생성자 `__construct()`에서 초기화 작업을 수행하는 경우 컨트롤러 재사용을 활성화할 수 없습니다. 왜냐하면 현재 프로세스에서 생성자는 한 번만 호출되며 각 요청 마다 호출되지 않기 때문입니다.
