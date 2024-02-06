# Thư viện kiểm soát truy cập Casbin webman-permission

## Giới thiệu

Nó dựa trên [PHP-Casbin](https://github.com/php-casbin/php-casbin), một framework kiểm soát truy cập mã nguồn mở mạnh mẽ và hiệu quả, hỗ trợ các mô hình kiểm soát truy cập như `ACL`, `RBAC`, `ABAC`, v.v.

## Liên kết dự án

https://github.com/Tinywan/webman-permission

## Cài đặt

```php
composer require tinywan/webman-permission
```
> Extension này yêu cầu PHP 7.1+ và [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998), xem tài liệu chính thức: https://www.workerman.net/doc/webman#/db/others

## Cấu hình

### Đăng ký dịch vụ
Tạo tệp cấu hình mới `config/bootstrap.php` với nội dung tương tự sau:

```php
    // ...
    webman\permission\Permission::class,
```

### Tệp cấu hình Model

Tạo tệp cấu hình mới `config/casbin-basic-model.conf` với nội dung tương tự sau:

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

### Tệp cấu hình Policy

Tạo tệp cấu hình mới `config/permission.php` với nội dung tương tự sau:

```php
<?php

return [
    /*
     * Mặc định Phân quyền
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * Cấu hình Model
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // Bộ đề ádapter
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * Cài đặt cơ sở dữ liệu
            */
            'database' => [
                // Tên kết nối cơ sở dữ liệu, nếu không điền sẽ sử dụng cấu hình mặc định.
                'connection' => '',
                // Tên bảng quy tắc (không bao gồm tiền tố bảng)
                'rules_name' => 'rule',
                // Tên đầy đủ của bảng quy tắc.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## Bắt đầu nhanh chóng

```php
use webman\permission\Permission;

//Thêm quyền cho người dùng
Permission::addPermissionForUser('eve', 'articles', 'read');
//Thêm vai trò cho người dùng
Permission::addRoleForUser('eve', 'writer');
//Thêm quyền cho quy tắc
Permission::addPolicy('writer', 'articles','edit');
```

Bạn có thể kiểm tra xem người dùng có quyền như vậy hay không

```php
if (Permission::enforce("eve", "articles", "edit")) {
    //Cho phép eve chỉnh sửa bài viết
} else {
    //Từ chối yêu cầu, hiển thị lỗi
}
````

## Middleware ủy quyền

Tạo tệp `app/middleware/AuthorizationMiddleware.php` (nếu thư mục không tồn tại, hãy tạo thủ công) như sau:

```php
<?php

/**
 * Middleware ủy quyền
 * Tác giả ShaoBo Wan (Tinywan)
 * Ngày giờ 7/9/2021 14:15
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
				throw new \Exception('Xin lỗi, bạn không có quyền truy cập vào giao diện này');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('Lỗi ủy quyền' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

Thêm middleware toàn cầu trong `config/middleware.php`:

```php
return [
    // Middleware toàn cầu
    '' => [
        // ... Bỏ qua các middleware khác ở đây
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## Cảm ơn

[Casbin](https://github.com/php-casbin/php-casbin), bạn có thể xem toàn bộ tài liệu trên trang [web](https://casbin.org/) của họ.

## Giấy phép

Dự án này được cấp giấy phép theo [giấy phép Apache 2.0](LICENSE).
