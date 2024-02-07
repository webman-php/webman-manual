# Thư viện kiểm soát truy cập Casbin cho webman-permission

## Giới thiệu

Nó dựa trên [PHP-Casbin](https://github.com/php-casbin/php-casbin), một framework kiểm soát truy cập mã nguồn mở mạnh mẽ và hiệu quả, hỗ trợ các mô hình kiểm soát truy cập như `ACL`, `RBAC`, `ABAC`, v.v.

## Địa chỉ dự án

https://github.com/Tinywan/webman-permission

## Cài đặt

```php
composer require tinywan/webman-permission
```

> Tiện ích này yêu cầu PHP 7.1+ và [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998). Xem tài liệu chính thức tại: https://www.workerman.net/doc/webman#/db/others

## Cấu hình

### Đăng ký dịch vụ
Tạo tệp cấu hình mới `config/bootstrap.php` với nội dung tương tự như sau:

```php
    // ...
    webman\permission\Permission::class,
```

### Cấu hình Model

Tạo tệp cấu hình mới `config/casbin-basic-model.conf` với nội dung tương tự như sau:
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

### Cấu hình Policy

Tạo tệp cấu hình mới `config/permission.php` với nội dung tương tự như sau:
```php
<?php

return [
    /*
     *Permission mặc định
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

            // Trình điều adapter .
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * Cấu hình cơ sở dữ liệu.
            */
            'database' => [
                // Tên kết nối cơ sở dữ liệu, nếu không điền thì mặc định.
                'connection' => '',
                // Tên bảng chính sách (không bao gồm tiền tố bảng)
                'rules_name' => 'rule',
                // Tên đầy đủ của bảng chính sách
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## Bắt đầu nhanh

```php
use webman\permission\Permission;

// Thêm quyền cho một người dùng
Permission::addPermissionForUser('eve', 'articles', 'read');
// Thêm một vai trò cho người dùng.
Permission::addRoleForUser('eve', 'writer');
// Thêm quyền cho một quy tắc
Permission::addPolicy('writer', 'articles','edit');
```

Bạn có thể kiểm tra xem người dùng có quyền như vậy hay không

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // cho phép eve chỉnh sửa bài viết
} else {
    // từ chối yêu cầu, hiển thị lỗi
}
````

## Middleware ủy quyền

Tạo tệp `app/middleware/AuthorizationMiddleware.php` (nếu thư mục không tồn tại, tự tạo) như sau:

```php
<?php

/**
 * Middleware ủy quyền
 * Tác giả: ShaoBo Wan (Tinywan)
 * Ngày giờ: 2021/09/07 14:15
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

Thêm middleware toàn cầu trong `config/middleware.php` như sau:

```php
return [
    // Toàn cầu middleware
    '' => [
        // ... Bỏ qua middleware khác
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## Xin cảm ơn

[Casbin](https://github.com/php-casbin/php-casbin), bạn có thể xem tất cả tài liệu trên [trang web chính thức](https://casbin.org/).

## Giấy phép

Dự án này được cấp phép theo [giấy phép Apache 2.0](LICENSE).
