# Casbin 액세스 제어 라이브러리 webman-permission

## 설명

이 라이브러리는 [PHP-Casbin](https://github.com/php-casbin/php-casbin)에 기반하며, ACL, RBAC, ABAC 등 다양한 액세스 제어 모델을 지원하는 강력하고 효율적인 오픈 소스 액세스 제어 프레임워크입니다.

## 프로젝트 주소

https://github.com/Tinywan/webman-permission

## 설치

```php
composer require tinywan/webman-permission
```
> 이 확장은 PHP 7.1+ 및 [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998)이 필요하며, 공식 설명서는 https://www.workerman.net/doc/webman#/db/others 에서 볼 수 있습니다.

## 설정

### 서비스 등록
`config/bootstrap.php` 파일을 만들고 다음과 같이 내용을 입력하세요.

```php
    // ...
    webman\permission\Permission::class,
```
### 모델 구성 파일

`config/casbin-basic-model.conf` 파일을 만들고 다음과 같이 내용을 입력하세요.

```conf
[request_definition]
r = sub, obj, act

[policy_definition]
p = sub, obj, act

[role_definition]
g = _, _

[policy_effect]
e = some(where (p.eft == allow))

[matchers]
m = g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act
```
### 정책 설정 파일

`config/permission.php` 파일을 만들고 다음과 같이 내용을 입력하세요.

```php
<?php

return [
    /*
     *Default  Permission
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * Model 설정
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // 适配器 .
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * 데이터베이스 설정.
            */
            'database' => [
                // 데이터베이스 연결 이름, 기본 설정으로 비워 둡니다.
                'connection' => '',
                // 정책 테이블 이름(접두사 없음)
                'rules_name' => 'rule',
                // 정책 테이블 전체 이름.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```
## 빠른 시작

```php
use webman\permission\Permission;

// 사용자에게 권한 추가
Permission::addPermissionForUser('eve', 'articles', 'read');
// 사용자에게 역할 추가
Permission::addRoleForUser('eve', 'writer');
// 정책에 권한 추가
Permission::addPolicy('writer', 'articles','edit');
```

사용자가 해당 권한을 가지고 있는지 확인할 수 있습니다.

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // eve에게 article 편집 권한 부여
} else {
    // 요청 거부, 오류 표시
}
````

## 인가 미들웨어

`app/middleware/AuthorizationMiddleware.php` 파일을 생성하고 아래와 같이 작성하세요. (디렉토리가 없는 경우 직접 만들어주세요)

```php
<?php

/**
 * 인가 미들웨어
 * 작성자: ShaoBo Wan (Tinywan)
 * 날짜: 2021/09/07 14:15
 */

declare(strict_types=1);

namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use Casbin\Exceptions\CasbinException;
use webman\permission\Permission;

class AuthorizationMiddleware implements MiddlewareInterface
{
	public function process(Request $request, callable $next): Response
	{
		$uri = $request->path();
		try {
			$userId = 10086;
			$action = $request->method();
			if (!Permission::enforce((string) $userId, $uri, strtoupper($action))) {
				throw new \Exception('죄송합니다. 해당 API에 대한 액세스 권한이 없습니다.');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('인가 예외' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

`config/middleware.php` 파일에 전역 미들웨어를 추가하세요.

```php
return [
    // 전역 미들웨어
    '' => [
        // ... 여기에 다른 미들웨어를 생략했습니다.
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## 감사의 글

[Casbin](https://github.com/php-casbin/php-casbin)은 [공식 웹 사이트](https://casbin.org/)에서 모든 문서를 확인할 수 있습니다.

## 라이센스

이 프로젝트는 [Apache 2.0 라이센스](LICENSE)에 따라 라이센스가 부여됩니다.
