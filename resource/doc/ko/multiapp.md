# 다중 애플리케이션
프로젝트는 때때로 여러 하위 프로젝트로 구성될 수 있습니다. 예를 들어 쇼핑몰은 쇼핑몰 메인 프로젝트, 쇼핑몰 API 인터페이스, 쇼핑몰 관리자 대시보드와 같이 3개의 하위 프로젝트로 구성될 수 있습니다. 이러한 각각의 하위 프로젝트는 동일한 데이터베이스 구성을 사용합니다.

webman에는 다음과 같이 app 디렉토리를 계획할 수 있습니다.
```plaintext
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
`http://127.0.0.1:8787/shop/{controller}/{method}`를 방문할 때 `app/shop/controller` 디렉토리에 있는 컨트롤러 및 메소드가 방문됩니다.

`http://127.0.0.1:8787/api/{controller}/{method}`를 방문할 때 `app/api/controller` 디렉토리에 있는 컨트롤러 및 메소드가 방문됩니다.

`http://127.0.0.1:8787/admin/{controller}/{method}`를 방문할 때 `app/admin/controller` 디렉토리에 있는 컨트롤러 및 메소드가 방문됩니다.

또한 webman에서는 다음과 같이 app 디렉토리를 계획할 수 있습니다.
```plaintext
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
이렇게 하면 `http://127.0.0.1:8787/{controller}/{method}`를 방문할 때 `app/controller` 디렉토리에 있는 컨트롤러 및 메소드가 방문됩니다. 경로가 api 또는 admin으로 시작하는 경우에는 해당 디렉토리에 있는 컨트롤러 및 메소드가 방문됩니다.

다중 애플리케이션의 경우 클래스 네임스페이스는 `psr4`을 준수해야 합니다. 예를 들어 `app/api/controller/FooController.php` 파일은 아래와 유사한 클래스를 가져야 합니다.
```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## 다중 애플리케이션 미들웨어 구성
때로는 각 애플리케이션에 대해 다른 미들웨어를 구성하고 싶은 경우가 있습니다. 예를 들어 `api` 애플리케이션에는 CORS 미들웨어가 필요할 수 있고, `admin`에는 관리자 로그인을 확인하는 미들웨어가 필요할 수 있습니다. 그렇다면 미들웨어를 구성하는 `config/midlleware.php` 파일은 다음과 유사할 것입니다.
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
> 위의 미들웨어가 실제로는 존재하지 않을 수 있으며, 여기서는 예시로 각 애플리케이션에 대한 미들웨어를 어떻게 구성하는지 설명한 것입니다.

미들웨어 실행 순서는 `전역 미들웨어`->`애플리케이션 미들웨어`입니다.

미들웨어 개발은 [미들웨어 섹션](middleware.md)을 참조하세요.

## 다중 애플리케이션 예외 처리 구성
마찬가지로 각 애플리케이션에 대해 다른 예외 처리 클래스를 구성하고 싶은 경우가 있습니다. 예를 들어 `shop` 애플리케이션에서 예외가 발생할 경우 사용자 친화적인 안내 페이지를 제공하고, `api` 애플리케이션에서 예외가 발생할 경우 페이지가 아닌 JSON 문자열을 반환하고 싶을 것입니다. 각 애플리케이션에 대해 다른 예외 처리 클래스를 구성하는 `config/exception.php` 파일은 다음과 유사할 것입니다.
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> 미들웨어와 다르게 각 애플리케이션에 대해 예외 처리 클래스는 하나만 구성할 수 있습니다.

> 위의 예외 처리 클래스가 실제로는 존재하지 않을 수 있으며, 여기서는 각 애플리케이션에 대해 어떻게 예외 처리 클래스를 구성하는지에 대한 예시를 설명한 것입니다.

예외 처리 개발은 [예외 처리 섹션](exception.md)을 참조하세요.
