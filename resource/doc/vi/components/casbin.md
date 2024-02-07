# Casbin

## Giới thiệu

Casbin là một framework quản lý truy cập nguồn mở mạnh mẽ và hiệu quả, cơ chế quản lý quyền hỗ trợ nhiều mô hình kiểm soát truy cập.

## Địa chỉ dự án

https://github.com/teamones-open/casbin

## Cài đặt

```php
composer require teamones/casbin
```

## Trang chủ của Casbin

Bạn có thể xem chi tiết sử dụng tại trang chủ thông qua tài liệu tiếng Trung, ở đây chỉ giải thích cách cấu hình và sử dụng trong webman.

https://casbin.org/docs/zh-CN/overview

## Cấu trúc thư mục

```plaintext
.
├── config                        Thư mục cấu hình
│   ├── casbin-restful-model.conf Tập tin cấu hình mô hình quyền hạn được sử dụng
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
     * Phương thức Thay đổi.
     *
     * Viết các di cư có thể hoán đổi của bạn bằng phương pháp này.
     *
     * Thêm thông tin về viết di cư có sẵn tại đây:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * Các lệnh sau có thể được sử dụng trong phương pháp này và Phinx sẽ
     * tự động hoán đổi chúng khi quay trở lại:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Mọi thay đổi phá hủy khác sẽ dẫn đến lỗi khi cố gắng
     * quay trở lại di cư.
     *
     * Nhớ gọi "create()" hoặc "update()" và KHÔNG PHẢI "save()" khi làm việc
     * với class Bảng.
     */
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Bảng quy tắc']);

        // Thêm cột dữ liệu
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'ID chính'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'Loại quy tắc'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        // Thực hiện tạo
        $table->create();
    }
}
```

## Cấu hình casbin

Về cú pháp cấu hình mô hình quyền hạn, vui lòng xem tại: https://casbin.org/docs/zh-CN/syntax-for-models

```php
<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Tập tin cấu hình mô hình quyền hạn
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model hoặc adapter
            'class' => \app\model\Rule::class,
        ],
    ],
    // Bạn có thể cấu hình nhiều mô hình quyền hạn
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // Tập tin cấu hình mô hình quyền hạn
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model hoặc adapter
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
``` 

### Adapter

Gói Composer hiện tại hỗ trợ phương thức model của think-orm, cho các orm khác, vui lòng xem tại vendor/teamones/src/adapters/DatabaseAdapter.php

Sau đó, chỉnh sửa cấu hình

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Tập tin cấu hình mô hình quyền hạn
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // Ở đây loại được cấu hình thành chế độ Adapter
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
# 1. Sử dụng cấu hình mặc định
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 2. Sử dụng cấu hình rbac tùy chỉnh
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### Giới thiệu API phổ biến

Để biết thêm về cách sử dụng API, vui lòng xem tại trang chính thức

- API quản lý: https://casbin.org/docs/zh-CN/management-api
- API RBAC: https://casbin.org/docs/zh-CN/rbac-api

```php
# Thêm quyền cho người dùng

Enforcer::addPermissionForUser('user1', '/user', 'read');

# Xóa quyền của người dùng

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

# Kiểm tra xem người dùng có thuộc một vai trò không

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
