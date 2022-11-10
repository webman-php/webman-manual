# Casbin

## Description

Casbinis a powerful and efficient open source access control framework with a permission management mechanism that supports multiple access control models。
  
## Project address

https://github.com/teamones-open/casbin

## Install
 
  ```php
  composer require teamones/casbin
  ```

## CasbinOfficial Site

You can read the official Chinese documentation for details, here is only how to configure the use in webman

https://casbin.org/docs/zh-CN/overview

## directory structure

```
.
├── config                        Configure Catalog
│   ├── casbin-restful-model.conf Permission model profile to use
│   ├── casbin.php                casbinConfigure
......
├── database                      Database file
│   ├── migrations                Migration files
│   │   └── 20210218074218_create_rule_table.php
......
```

## Database migration file

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
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Rule Table']);

        //Add data fields
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'Primary KeyID'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'Rule Type'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        //Execute Create
        $table->create();
    }
}

```

## casbin Configure

Permission rule model configuration syntax see：https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Permission Rules Model Configuration File
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model or adapter
            'class' => \app\model\Rule::class,
        ],
    ],
    // Multiple permissions can be configuredmodel
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // Permission Rules Model Configuration File
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model or adapter
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### adapter

The current composer package is adapted to think-orm's model method, for other orm please refer to vendor/teamones/src/adapters/DatabaseAdapter.php

Then modify the configuration

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Permission Rules Model Configuration File
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // Here the type is configured to adapter mode
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## Usage Notes

### Introduction

```php
# Introduction
use teamones\casbin\Enforcer;
```

### Two Uses

```php
# 1. Default use default configuration
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 1. Use custom rbac configuration
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### Introduction to Common APIs

For more API usage, please go to the official page

- ManageAPI： https://casbin.org/docs/zh-CN/management-api
- RBAC API： https://casbin.org/docs/zh-CN/rbac-api

```php
# Add permissions for users

Enforcer::addPermissionForUser('user1', '/user', 'read');

# Delete a user's permissions

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# Get all user permissions

Enforcer::getPermissionsForUser('user1'); 

# Add role for user

Enforcer::addRoleForUser('user1', 'role1');

# Adding permissions to roles

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# Get all roles

Enforcer::getAllRoles();

# Get all roles of the user

Enforcer::getRolesForUser('user1');

# Get users by role

Enforcer::getUsersForRole('role1');

# Determine if a user belongs to a role

Enforcer::hasRoleForUser('use1', 'role1');

# Delete user role

Enforcer::deleteRoleForUser('use1', 'role1');

# Delete all roles of a user

Enforcer::deleteRolesForUser('use1');

# Delete Role

Enforcer::deleteRole('role1');

# Delete Permissions

Enforcer::deletePermission('/user', 'read');

# Remove all permissions for a user or role

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# Check Permissions, Return true or false

Enforcer::enforce("user1", "/user", "edit");
```


