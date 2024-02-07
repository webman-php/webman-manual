# 다국어

다국어 기능은 [symfony/translation](https://github.com/symfony/translation) 구성 요소를 사용합니다.

## 설치
```composer require symfony/translation```

## 언어 패키지 설정
webman은 기본적으로 언어 패키지를 `resource/translations` 디렉토리에 배치합니다(없는 경우 직접 생성). 경로를 바꾸려면 `config/translation.php`에서 설정해야 합니다.
각 언어에 해당하는 하위 폴더가 있으며, 언어 정의는 기본적으로 `messages.php`에 저장됩니다. 아래는 예시입니다:
```resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

모든 언어 파일은 다음과 같이 배열을 반환합니다.
```php
// resource/translations/en/messages.php
return [
    'hello' => 'Hello webman',
];
```

## 설정

`config/translation.php` 
```php
return [
    // 기본 언어
    'locale' => 'zh_CN',
    // 폴백 언어, 현재 언어에서 번역을 찾을 수 없는 경우 폴백 언어의 번역을 시도함
    'fallback_locale' => ['zh_CN', 'en'],
    // 언어 파일이 저장된 폴더
    'path' => base_path() . '/resource/translations',
];
```

## 번역

번역은 `trans()` 메서드를 사용합니다.

다음과 같이 언어 파일을 작성하세요: `resource/translations/zh_CN/messages.php`
```php
return [
    'hello' => '안녕 웹맨!',
];
```

파일을 생성한 후 `app/controller/UserController.php`에 다음을 추가하세요.
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // 안녕 웹맨!
        return response($hello);
    }
}
```

`http://127.0.0.1:8787/user/get`에 액세스하면 "안녕 웹맨!"이 반환됩니다.

## 기본 언어 변경

언어 변경은 `locale()` 메서드를 사용합니다.

다음과 같이 새 언어 파일을 추가하세요: `resource/translations/en/messages.php`
```php
return [
    'hello' => 'hello world!',
];
```

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // 언어 변경
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
`http://127.0.0.1:8787/user/get`에 액세스하면 "hello world!"가 반환됩니다.

또한 `trans()` 함수의 4번째 매개변수를 사용하여 언어를 임시로 변경할 수도 있습니다. 위 예제와 아래 예제는 동일합니다.
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // 4번째 매개변수로 언어 변경
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## 각 요청에 대한 명시적 언어 설정

translation은 싱글톤이기 때문에 모든 요청이 이 인스턴스를 공유합니다. 특정 요청에서 `locale()`를 사용하여 기본 언어를 설정하면 해당 프로세스의 후속 모든 요청에 영향을 미칩니다. 따라서 각 요청에 대해 명시적으로 언어를 설정해야 합니다. 다음과 같은 미들웨어를 사용할 수 있습니다.

`app/middleware/Lang.php`를 만들어봅시다(없다면 직접 만들어 주세요) 
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        locale(session('lang', 'zh_CN'));
        return $handler($request);
    }
}
```

`config/middleware.php`에서 다음과 같이 전역 미들웨어를 추가하세요.
```php
return [
    // 전역 미들웨어
    '' => [
        // ... 다른 미들웨어는 여기에 생략합니다
        app\middleware\Lang::class,
    ]
];
```


## 자리 표시자 사용

때로는 번역해야 할 변수를 포함하는 메시지가 있습니다. 예를 들어
```php
trans('hello ' . $name);
```
이러한 경우 플레이스홀더를 사용하여 처리합니다.

`resource/translations/zh_CN/messages.php`를 다음과 같이 변경하세요.
```php
return [
    'hello' => '안녕 %name%!',
```
번역할 때 데이터를 두 번째 매개변수를 통해 플레이스홀더에 해당하는 값으로 전달하세요.
```php
trans('hello', ['%name%' => '웹맨']); // 안녕 웹맨!
```

## 복수 처리

일부 언어에서는 물건 수에 따라 문장의 형식이 달라질 수 있습니다. 예를 들어 `There is %count% apple`은 `%count%`가 1일 때는 올바른 형식이지만 1보다 큰 경우에는 옳지 않습니다.

이러한 경우 파이프(`|`)를 사용하여 복수 형식을 나열합니다.

언어 파일 `resource/translations/en/messages.php`에 `apple_count`를 추가하세요.
```php
return [
    // ...
    'apple_count' => 'One apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

심지어 숫자 범위를 지정하여 더 복잡한 복수 규칙을 만들 수 있습니다.
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples',
];

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## 특정 언어 파일 지정

언어 파일의 기본 이름은 `messages.php`입니다. 그러나 실제로 다른 이름의 언어 파일을 만들 수 있습니다.

`resource/translations/zh_CN/admin.php`를 다음과 같이 생성하세요.
```php
return [
    'hello_admin' => '안녕, 관리자!',
];
```

`trans()`의 세 번째 매개변수를 사용하여(확장자 `.php`를 생략) 언어 파일을 지정할 수 있습니다.
```php
trans('hello', [], 'admin', 'zh_CN'); // 안녕, 관리자!
```

## 더 알아보기

[symfony/translation 매뉴얼](https://symfony.com/doc/current/translation.html)을 참조하세요.
