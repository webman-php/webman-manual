# Casbin

## 说明

Casbin是一个强大的、高效的开源访问控制框架，其权限管理机制支持多种访问控制模型。
  
## 项目地址

https://github.com/teamones-open/casbin

## 安装
 
  ```php
  composer require teamones/casbin
  ```

## Casbin官网

详细使用可以去看官方中文文档，这里只讲怎么在webman中配置使用

https://casbin.org/docs/zh-CN/overview

## 目录结构

```
.
├── config                        配置目录
│   ├── casbin-restful-model.conf 使用的权限模型配置文件
│   ├── casbin.php                casbin配置
......
├── database                      数据库文件
│   ├── migrations                迁移文件
│   │   └── 20210218074218_create_rule_table.php
......
```

## 数据库迁移文件

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
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '规则表']);

        //添加数据字段
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => '主键ID'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => '规则类型'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        //执行创建
        $table->create();
    }
}

```

## casbin 配置

权限规则模型配置语法请看：https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // 权限规则模型配置文件
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model or adapter
            'class' => \app\model\Rule::class,
        ],
    ],
    // 可以配置多个权限model
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // 权限规则模型配置文件
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model or adapter
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### 适配器

当前composer封装中适配的是 think-orm 的model方法，其他 orm 请参考 vendor/teamones/src/adapters/DatabaseAdapter.php

然后修改配置

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // 权限规则模型配置文件
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // 这里类型配置成适配器模式
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## 使用说明

### 引入

```php
# 引入
use teamones\casbin\Enforcer;
```

### 两种用法

```php
# 1. 默认使用 default 配置
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 1. 使用自定义的 rbac 配置
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### 常用API介绍

更多API用法请去官方查看

- 管理API： https://casbin.org/docs/zh-CN/management-api
- RBAC API： https://casbin.org/docs/zh-CN/rbac-api

```php
# 为用户添加权限

Enforcer::addPermissionForUser('user1', '/user', 'read');

# 删除一个用户的权限

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# 获取用户所有权限

Enforcer::getPermissionsForUser('user1'); 

# 为用户添加角色

Enforcer::addRoleForUser('user1', 'role1');

# 为角色添加权限

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# 获取所有角色

Enforcer::getAllRoles();

# 获取用户所有角色

Enforcer::getRolesForUser('user1');

# 根据角色获取用户

Enforcer::getUsersForRole('role1');

# 判断用户是否属于一个角色

Enforcer::hasRoleForUser('use1', 'role1');

# 删除用户角色

Enforcer::deleteRoleForUser('use1', 'role1');

# 删除用户所有角色

Enforcer::deleteRolesForUser('use1');

# 删除角色

Enforcer::deleteRole('role1');

# 删除权限

Enforcer::deletePermission('/user', 'read');

# 删除用户或者角色的所有权限

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# 检查权限，返回 true or false

Enforcer::enforce("user1", "/user", "edit");
```


