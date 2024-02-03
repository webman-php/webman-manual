# 다중 어플리케이션
가끔 한 프로젝트는 여러 하위 프로젝트로 분할될 수 있습니다. 예를 들어 상점은 상점 주요 프로젝트, 상점 API 인터페이스, 상점 관리자 목적의 3개 하위 프로젝트로 분할될 수 있으며, 이러한 프로젝트들은 모두 동일한 데이터베이스 구성을 사용합니다.

webman은 다음과 같은 방식으로 app 디렉토리를 구성할 수 있도록 합니다:
```
app
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
`http://127.0.0.1:8787/shop/{controller}/{method}` 주소에 접근할 때 `app/shop/controller`에 있는 컨트롤러 및 메서드에 접근됩니다.

`http://127.0.0.1:8787/api/{controller}/{method}` 주소에 접근할 때 `app/api/controller`에 있는 컨트롤러 및 메서드에 접근됩니다.

`http://127.0.0.1:8787/admin/{controller}/{method}` 주소에 접근할 때 `app/admin/controller`에 있는 컨트롤러 및 메서드에 접근됩니다.

또한 webman에서 app 디렉토리를 다음과 같이 구성할 수도 있습니다.
```
app
├── controller
├── model
├── view
│
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
이 경우 `http://127.0.0.1:8787/{controller}/{method}` 주소에 접근하면 `app/controller`에 있는 컨트롤러 및 메서드에 접근됩니다. 경로에 api 또는 admin으로 시작하면 해당 디렉토리에 있는 컨트롤러 및 메서드에 접근됩니다.

여러 애플리케이션의 경우 클래스 네임스페이스는 `psr4`를 준수해야 합니다. 예를 들어 `app/api/controller/FooController.php` 파일은 다음과 유사한 클래스를 가져야 합니다:
```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}
```

## 다중 어플리케이션 미들웨어 설정
때로는 각기 다른 애플리케이션에 대해 다른 미들웨어를 설정하고 싶을 수 있습니다. 예를 들어 `api` 애플리케이션은 CORS 미들웨어를 필요로 하고, `admin`은 관리자 로그인을 확인하는 미들웨어가 필요한 경우, `config/midlleware.php` 파일은 다음과 유사한 설정을 할 수 있습니다:
```php
return [
    // 전역 미들웨어
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // api 애플리케이션 미들웨어
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // admin 애플리케이션 미들웨어
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> 위 미들웨어는 실제로 존재하지 않을 수 있으며, 여기서는 단지 애플리케이션별로 미들웨어를 어떻게 설정하는지 설명하는 예시일 뿐입니다.

미들웨어 실행 순서는 `전역 미들웨어` -> `애플리케이션 미들웨어` 순서대로 실행됩니다.

미들웨어 개발은 [미들웨어 섹션](middleware.md)을 참고하세요.

## 다중 어플리케이션 예외 처리 설정
마찬가지로, 각 기존 애플리케이션에 대해 서로 다른 예외 처리 클래스를 설정하고 싶을 때, `shop` 애플리케이션에 예외가 발생하면 친숙한 안내 페이지를 제공하고 싶을 수도 있으며, `api` 애플리케이션에 예외가 발생하면 페이지가 아닌 JSON 문자열을 반환하고 싶을 수도 있습니다. 다른 애플리케이션에 대해 서로 다른 예외 처리 클래스를 설정하는 설정 파일인 `config/exception.php`은 다음과 유사한 설정을 할 수 있습니다:
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> 미들웨어와는 달리, 각 애플리케이션은 하나의 예외 처리 클래스만 설정할 수 있습니다.

> 위 예외 처리 클래스는 실제로 존재하지 않을 수 있으며, 여기서는 각 애플리케이션별로 예외 처리를 어떻게 설정하는지 설명하는 예시일 뿐입니다.

예외 처리 개발은 [예외 처리 섹션](exception.md)을 참고하세요.
