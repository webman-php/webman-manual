# Casbin

## Giới thiệu

Casbin là một framework quản lý truy cập mã nguồn mở mạnh mẽ và hiệu quả, cơ chế quản lý quyền hỗ trợ nhiều mô hình quản lý truy cập.

## Địa chỉ dự án

https://github.com/teamones-open/casbin

## Cài đặt

  ```php
  composer require teamones/casbin
  ```

## Trang web chính thức của Casbin

Bạn có thể xem hướng dẫn chi tiết trên tài liệu chính thức bằng tiếng Trung, ở đây chỉ hướng dẫn cách cấu hình và sử dụng trong webman.

https://casbin.org/docs/zh-CN/overview

## Cấu trúc thư mục

```
.
├── config                        Thư mục cấu hình
│   ├── casbin-restful-model.conf Tệp cấu hình mô hình quyền truy cập được sử dụng
│   ├── casbin.php                Cấu hình casbin
......
├── database                      Tệp cơ sở dữ liệu
│   ├── migrations                Tệp di cư
│   │   └── 20210218074218_create_rule_table.php
......
```

## Tệp di cư cơ sở dữ liệu

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    /**
     * ... (Các phương pháp khác có thể được sử dụng trong phương thức này và Phinx sẽ tự động đảo ngược chúng khi quay trở lại)
     */
    public function change()
    {
       //Vui lòng gọi "create()" hoặc "update()" và KHÔNG phải "save()" khi làm việc với lớp Bảng.
    }
}
```

## Cấu hình casbin

Về cú pháp cấu hình mô hình quyền truy cập, vui lòng xem: https://casbin.org/docs/zh-CN/syntax-for-models

```php
<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Tệp cấu hình mô hình quyền truy cập
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // mô hình hoặc bộ chuyển đổi
            'class' => \app\model\Rule::class,
        ],
    ],
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // Tệp cấu hình mô hình quyền truy cập
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // mô hình hoặc bộ chuyển đổi
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### Bộ chuyển đổi

Trong composer hiện tại, bộ chuyển đổi hỗ trợ phương pháp model của think-orm, với orm khác, vui lòng xem vendor/teamones/src/adapters/DatabaseAdapter.php

Sau đó, sửa cấu hình

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Tệp cấu hình mô hình quyền truy cập
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // Chuyển đổi loại thành chế độ bộ chuyển đổi
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## Hướng dẫn sử dụng

### Nhập

```php
# Nhập
use teamones\casbin\Enforcer;
```

### Hai cách sử dụng

```php
# 1. Sử dụng cấu hình mặc định default
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 1. Sử dụng cấu hình rbac tùy chỉnh
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### Giới thiệu về API phổ biến

Vui lòng xem hướng dẫn chính thức để biết thêm thông tin về cách sử dụng các API

- API quản lý: https://casbin.org/docs/zh-CN/management-api
- RBAC API: https://casbin.org/docs/zh-CN/rbac-api

```php
# Thêm quyền cho người dùng

Enforcer::addPermissionForUser('user1', '/user', 'read');

# Xóa quyền của một người dùng

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# Lấy tất cả quyền của người dùng

Enforcer::getPermissionsForUser('user1'); 

# Thêm vai trò cho người dùng

Enforcer::addRoleForUser('user1', 'role1');

# Thêm quyền cho vai trò

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# Lấy tất cả vai trò

Enforcer::getAllRoles();

# Lấy tất cả vai trò của người dùng

Enforcer::getRolesForUser('user1');

# Lấy người dùng dựa trên vai trò

Enforcer::getUsersForRole('role1');

# Kiểm tra xem người dùng có thuộc về một vai trò không

Enforcer::hasRoleForUser('use1', 'role1');

# Xóa vai trò của người dùng

Enforcer::deleteRoleForUser('use1', 'role1');

# Xóa tất cả vai trò của người dùng

Enforcer::deleteRolesForUser('use1');

# Xóa vai trò

Enforcer::deleteRole('role1');

# Xóa quyền

Enforcer::deletePermission('/user', 'read');

# Xóa tất cả quyền của người dùng hoặc vai trò

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# Kiểm tra quyền, trả về true hoặc false

Enforcer::enforce("user1", "/user", "edit");
```
