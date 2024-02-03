# 다국어

다국어 기능은 [symfony/translation](https://github.com/symfony/translation) 컴포넌트를 사용합니다.

## 설치
```
composer require symfony/translation
```

## 언어 팩 설정
webman은 기본적으로 언어 팩을 `resource/translations` 디렉터리에 저장합니다(없는 경우 직접 생성). 디렉터리를 변경하려면 `config/translation.php`에서 설정하십시오.
각 언어에 해당하는 하위 디렉터리가 있고, 언어 정의는 기본적으로 `messages.php`에 위치합니다. 아래는 예시입니다:
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

모든 언어 파일은 아래와 같이 배열을 반환합니다:
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
    // 폴백 언어, 해당 언어에서 번역을 찾을 수 없을 때 폴백 언어의 번역을 시도합니다
    'fallback_locale' => ['zh_CN', 'en'],
    // 언어 파일 저장 디렉터리
    'path' => base_path() . '/resource/translations',
];
```

## 번역

번역은 `trans()` 메소드를 사용합니다.

다음과 같이 언어 파일을 생성하십시오: `resource/translations/zh_CN/messages.php`
```php
return [
    'hello' => '안녕하세요 웹맨',
];
```

파일 생성 후 다음과 같이 진행할 수 있습니다: `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // 안녕하세요 웹맨
        return response($hello);
    }
}
```

`http://127.0.0.1:8787/user/get`에 접속하면 "안녕하세요 웹맨"이 반환됩니다.

## 기본 언어 변경

`locale()` 메소드를 사용하여 언어를 변경할 수 있습니다.

언어 파일을 추가해보죠: `resource/translations/en/messages.php`에 다음을 입력하십시오.
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
`http://127.0.0.1:8787/user/get`에 접속하면 "hello world!"가 반환됩니다.

또한 `trans()` 함수의 4번째 인자를 사용하여 임시로 언어를 변경할 수 있습니다. 위의 예제는 다음과 같습니다:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // 4번째 인자로 언어 변경
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## 각 요청에 명시적으로 언어 설정

translation은 하나의 인스턴스이므로 모든 요청이 이 인스턴스를 공유합니다. 따라서 어떤 요청이든 `locale()`를 사용하여 기본 언어를 설정하면 해당 프로세스의 모든 후속 요청에 영향을 줍니다. 따라서 각 요청에 대해 명시적으로 언어를 설정해야 합니다. 아래와 같은 미들웨어를 사용할 수 있습니다.

`app/middleware/Lang.php` 파일을 생성하세요(없는 경우 직접 생성) 다음 내용을 입력하세요:
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

`config/middleware.php`에 전역 미들웨어로 아래와 같이 추가하세요:
```php
return [
    // 전역 미들웨어
    '' => [
        // ... 이하 생략
        app\middleware\Lang::class,
    ]
];
```


## 플레이스홀더 사용

가끔씩 메시지에 번역되어야 하는 변수가 포함된 경우가 있습니다. 예를 들어
```php
trans('hello ' . $name);
```
이런 경우에는 플레이스홀더로 처리합니다.

다음과 같이 `resource/translations/zh_CN/messages.php`를 변경하십시오:
```php
return [
    'hello' => '안녕하세요 %name%!',
];
```
번역 시 두 번째 매개변수로 플레이스홀더에 해당하는 값을 전달합니다.
```php
trans('hello', ['%name%' => 'webman']); // 안녕하세요 webman!
```

## 복수 처리

일부 언어는 항목 수에 따라 다양한 문장 형식을 사용합니다. 예를 들어 `사과 %count%개`의 경우, `%count%`가 1일 때는 문장 형식이 맞지만 1보다 큰 경우에는 잘못된 문장 형식입니다.

이런 경우 **파이프**(`|`)를 사용하여 복수 형식을 나열합니다.

어린이언어 파일 `resource/translations/en/messages.php`에 `apple_count`를 추가하세요:
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

심지어 숫자 범위를 지정하여 더 복잡한 복수 규칙을 만들 수도 있습니다:
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## 특정 언어 파일 지정

언어 파일의 기본 이름은 `messages.php`입니다. 하지만 실제로는 다른 이름의 언어 파일을 생성할 수 있습니다.

다음과 같은 방법으로 `resource/translations/zh_CN/admin.php` 파일을 생성하세요:
```php
return [
    'hello_admin' => '안녕하세요 관리자!',
];
```

`trans()`의 세 번째 매개변수를 사용하여 언어 파일을 지정할 수 있습니다(확장자`.php`를 생략).
```php
trans('hello', [], 'admin', 'zh_CN'); // 안녕하세요 관리자!
```

## 더 많은 정보
[symfony/translation 매뉴얼](https://symfony.com/doc/current/translation.html)을 참조하세요
