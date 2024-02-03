# Casbin

## 說明

Casbin是一個強大的、高效的開源訪問控制框架，其權限管理機制支持多種訪問控制模型。

## 專案地址

https://github.com/teamones-open/casbin

## 安裝

```php
composer require teamones/casbin
```

## Casbin官網

詳細使用可以去看官方中文文檔，這裡只講怎麼在webman中配置使用

https://casbin.org/docs/zh-CN/overview

## 目錄結構

```
.
├── config                        配置目錄
│   ├── casbin-restful-model.conf 使用的權限模型配置文件
│   ├── casbin.php                casbin配置
......
├── database                      數據庫文件
│   ├── migrations                遷移文件
│   │   └── 20210218074218_create_rule_table.php
......
```

## 數據庫遷移文件

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '規則表']);

        //添加數據字段
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => '主鍵ID'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => '規則類型'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        //執行創建
        $table->create();
    }
}

```

## casbin 配置

權限規則模型配置語法請看：https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // 權限規則模型配置文件
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model or adapter
            'class' => \app\model\Rule::class,
        ],
    ],
    // 可以配置多個權限model
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // 權限規則模型配置文件
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model or adapter
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### 適配器

當前composer封裝中適配的是 think-orm 的model方法，其他 orm 請參考 vendor/teamones/src/adapters/DatabaseAdapter.php

然後修改配置

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // 權限規則模型配置文件
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // 這裡類型配置成適配器模式
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## 使用說明

### 引入

```php
# 引入
use teamones\casbin\Enforcer;
```

### 兩種用法

```php
# 1. 默認使用 default 配置
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 1. 使用自定義的 rbac 配置
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### 常用API介紹

更多API用法請去官方查看

- 管理API： https://casbin.org/docs/zh-CN/management-api
- RBAC API： https://casbin.org/docs/zh-CN/rbac-api

```php
# 為用戶添加權限

Enforcer::addPermissionForUser('user1', '/user', 'read');

# 刪除一個用戶的權限

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# 獲取用戶所有權限

Enforcer::getPermissionsForUser('user1'); 

# 為用戶添加角色

Enforcer::addRoleForUser('user1', 'role1');

# 為角色添加權限

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# 獲取所有角色

Enforcer::getAllRoles();

# 獲取用戶所有角色

Enforcer::getRolesForUser('user1');

# 根據角色獲取用戶

Enforcer::getUsersForRole('role1');

# 判斷用戶是否屬於一個角色

Enforcer::hasRoleForUser('use1', 'role1');

# 刪除用戶角色

Enforcer::deleteRoleForUser('use1', 'role1');

# 刪除用戶所有角色

Enforcer::deleteRolesForUser('use1');

# 刪除角色

Enforcer::deleteRole('role1');

# 刪除權限

Enforcer::deletePermission('/user', 'read');

# 刪除用戶或者角色的所有權限

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# 檢查權限，返回 true or false

Enforcer::enforce("user1", "/user", "edit");
```