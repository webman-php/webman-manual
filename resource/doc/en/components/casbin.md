# Casbin

## Introduction

Casbin is a powerful and efficient open-source access control framework that supports multiple access control models for permission management.

## Project Address

https://github.com/teamones-open/casbin

## Installation

```php
composer require teamones/casbin
```

## Casbin Official Website

For detailed usage, please refer to the official documentation in Chinese. Here, we will only discuss how to configure and use Casbin in webman.

https://casbin.org/docs/zh-CN/overview

## Directory Structure

```plaintext
.
├── config                        Configuration directory
│   ├── casbin-restful-model.conf Configuration file for the permission model used
│   ├── casbin.php                Casbin configuration
......
├── database                      Database files
│   ├── migrations                Migration files
│   │   └── 20210218074218_create_rule_table.php
......
```

## Database Migration File

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

        // Add data fields
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'Primary Key ID'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'Rule Type'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        // Execute creation
        $table->create();
    }
}
```

## Casbin Configuration

Please refer to the official Casbin documentation for the syntax of the permission rule model configuration: https://casbin.org/docs/zh-CN/syntax-for-models

```php
<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Configuration file for the permission rule model
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model or adapter
            'class' => \app\model\Rule::class,
        ],
    ],
    // You can configure multiple permission models
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // Configuration file for the permission rule model
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model or adapter
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### Adapter

Currently, the composer package is compatible with the `think-orm` method. For other ORMs, please refer to `vendor/teamones/src/adapters/DatabaseAdapter.php`.

Then modify the configuration.

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Configuration file for the permission rule model
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // Set the type to adapter mode here
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## Usage Instructions

### Import

```php
# Import
use teamones\casbin\Enforcer;
```

### Two Usage Methods

```php
# 1. Use the default configuration
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 2. Use a custom rbac configuration
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### Introduction to Commonly Used APIs

For more API usages, please refer to the official documentation.

- Management APIs: https://casbin.org/docs/zh-CN/management-api
- RBAC APIs: https://casbin.org/docs/zh-CN/rbac-api

```php
# Add a permission for a user
Enforcer::addPermissionForUser('user1', '/user', 'read');

# Delete a permission for a user
Enforcer::deletePermissionForUser('user1', '/user', 'read');

# Get all permissions for a user
Enforcer::getPermissionsForUser('user1');

# Add a role for a user
Enforcer::addRoleForUser('user1', 'role1');

# Add a permission for a role
Enforcer::addPermissionForUser('role1', '/user', 'edit');

# Get all roles
Enforcer::getAllRoles();

# Get all roles for a user
Enforcer::getRolesForUser('user1');

# Get users for a role
Enforcer::getUsersForRole('role1');

# Check if a user belongs to a role
Enforcer::hasRoleForUser('user1', 'role1');

# Delete a role for a user
Enforcer::deleteRoleForUser('user1', 'role1');

# Delete all roles for a user
Enforcer::deleteRolesForUser('user1');

# Delete a role
Enforcer::deleteRole('role1');

# Delete a permission
Enforcer::deletePermission('/user', 'read');

# Delete all permissions for a user or role
Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# Check permissions and return true or false
Enforcer::enforce("user1", "/user", "edit");
```
