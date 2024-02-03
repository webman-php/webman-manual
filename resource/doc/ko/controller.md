# 컨트롤러

`app/controller/FooController.php` 에 새로운 컨트롤러 파일을 생성합니다.

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

`http://127.0.0.1:8787/foo`에 접속할 때, 페이지는 `hello index`을 반환합니다.

`http://127.0.0.1:8787/foo/hello`에 접속할 때, 페이지는 `hello webman`을 반환합니다.

물론, 라우트 구성을 통해 라우트 규칙을 변경할 수 있습니다. 자세한 내용은 [라우트](route.md)를 참조하십시오.

> **팁**
> 404 오류가 발생한 경우, `config/app.php`를 열고 `controller_suffix`를 `Controller`로 설정한 후 다시 시작하십시오.

## 컨트롤러 접미사
webman은 1.3 버전부터 `config/app.php`에 컨트롤러 접미사를 설정할 수 있습니다. `config/app.php`에서 `controller_suffix`를 빈 문자열 `''`로 설정한 경우, 컨트롤러는 다음과 같이 보일 것입니다.

`app\controller\Foo.php`입니다.

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

컨트롤러 접미사를 `Controller`로 설정하는 것이 좋습니다. 이렇게 하면 컨트롤러와 모델 클래스 이름의 충돌을 피할 수 있으며, 보안성을 높일 수 있습니다.

## 설명
 - 프레임워크는 자동으로 `support\Request` 객체를 컨트롤러로 전달하며, 이를 통해 사용자 입력 데이터(get, post, header, cookie 등)를 얻을 수 있습니다. 자세한 내용은 [요청](request.md)을 참조하십시오.
 - 컨트롤러에서 숫자, 문자열 또는 `support\Response` 객체를 반환할 수 있지만 다른 유형의 데이터는 반환할 수 없습니다.
 - `support\Response` 객체는 `response()`, `json()`, `xml()`, `jsonp()`, `redirect()` 등의 도우미 함수를 사용하여 생성할 수 있습니다.

## 컨트롤러 라이프사이클
`config/app.php`에서 `controller_reuse`가 `false`로 설정된 경우 매 요청마다 해당하는 컨트롤러 인스턴스가 초기화되며, 요청이 끝나면 컨트롤러 인스턴스가 파기되어 메모리가 회수됩니다. 이것은 전통적인 프레임워크 실행 메커니즘과 동일합니다.

`config/app.php`에서 `controller_reuse`가 `true`로 설정된 경우, 모든 요청은 컨트롤러 인스턴스를 재사용하며, 컨트롤러 인스턴스가 한 번 생성되면 메모리에 상주합니다.

> **주의**
> 컨트롤러 재사용을 중지하려면 webman>=1.4.0이 필요합니다. 즉, 1.4.0 이전에는 기본적으로 모든 요청에서 컨트롤러를 재사용하고 변경할 수 없습니다.

> **주의**
> 컨트롤러 재사용을 활성화하면 요청에서 컨트롤러 속성을 변경해서는 안 됩니다. 이러한 변경 사항은 후속 요청에 영향을 미칠 수 있습니다.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
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
}
```

> **팁**
> 컨트롤러의 `__construct()` 생성자 함수에서 데이터를 반환하면 브라우저는 이 응답을 수신하지 않습니다.

## 컨트롤러 재사용 및 미사용의 차이
다음은 컨트롤러를 재사용하지 않을 때와 재사용할 때의 차이점입니다.

#### 컨트롤러 재사용하지 않을 때
매 요청마다 컨트롤러 인스턴스를 다시 생성하고, 요청이 끝나면 해당 인스턴스를 해제하여 메모리를 회수합니다. 컨트롤러 계속해서 생성 및 파기되기 때문에 성능이 재사용 컨트롤러보다 약간 떨어집니다(헬로월드 부하 테스트 성능이 약 10% 낮아지며, 비즈니스를 포함할 경우 기본적으로 무시할 수 있습니다).

#### 컨트롤러 재사용할 때
프로세스당 한 번만 컨트롤러를 생성하고, 요청이 끝난 후 해당 컨트롤러 인스턴스를 해제하지 않고 현재 프로세스의 후속 요청에서 이를 재사용합니다. 컨트롤러를 재사용하는 것이 성능면에서 더 우수하지만, 대부분의 개발자 습관과는 일치하지 않습니다.

#### 컨트롤러 재사용이 불가능한 경우
요청이 컨트롤러의 속성을 변경하는 경우에 컨트롤러를 재사용할 수 없습니다. 이러한 속성 변경은 후속 요청에 영향을 미칠 수 있습니다.

어떤 개발자들은 각 요청마다 초기화를 위해 컨트롤러 생성자 `__construct()`에서 작업을 수행하는 것을 좋아합니다. 이 경우 컨트롤러를 재사용할 수 없으며, 현재 프로세스에서 생성자는 한 번만 호출됩니다.
