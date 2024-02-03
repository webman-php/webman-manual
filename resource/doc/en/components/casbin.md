# Casbin

## Introduction

Casbin is a powerful and efficient open-source access control framework, with support for multiple access control models in its permission management mechanism.

## Project Address

https://github.com/teamones-open/casbin

## Installation

```php
composer require teamones/casbin
```

## Casbin Official Website

For detailed usage, please refer to the official Chinese documentation. Here we only discuss how to configure and use Casbin in webman.

https://casbin.org/docs/zh-CN/overview

## Directory Structure

```
.
├── config                        Configuration directory
│   ├── casbin-restful-model.conf Configuration file for the used permission model
│   ├── casbin.php                Casbin configuration
......
├── database                      Database files
│   ├── migrations                Migration files
│   │   └── 20210218074218_create_rule_table.php
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
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Rule table']);

        // Add data fields
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'Primary key ID'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'Rule type'])
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

Please refer to the permission rule model configuration syntax: https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Configuration file for permission rule model
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
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // Configuration file for permission rule model
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

The currently encapsulated adapter in the Composer supports the model method of think-orm. For other ORMs, please refer to vendor/teamones/src/adapters/DatabaseAdapter.php and modify the configuration accordingly.

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Configuration file for permission rule model
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

# 2. Use the custom rbac configuration
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### Commonly Used API Introductions

For more API usage, please refer to the official documentation.

- Management API: https://casbin.org/docs/zh-CN/management-api
- RBAC API: https://casbin.org/docs/zh-CN/rbac-api

```php
# Add permission for a user

Enforcer::addPermissionForUser('user1', '/user', 'read');

# Delete a user's permission

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# Get all permissions for a user

Enforcer::getPermissionsForUser('user1'); 

# Add a role for a user

Enforcer::addRoleForUser('user1', 'role1');

# Add permission for a role

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# Get all roles

Enforcer::getAllRoles();

# Get all roles for a user

Enforcer::getRolesForUser('user1');

# Get users for a role

Enforcer::getUsersForRole('role1');

# Check if a user belongs to a role

Enforcer::hasRoleForUser('use1', 'role1');

# Delete a user's role

Enforcer::deleteRoleForUser('use1', 'role1');

# Delete all roles for a user

Enforcer::deleteRolesForUser('use1');

# Delete a role

Enforcer::deleteRole('role1');

# Delete permission

Enforcer::deletePermission('/user', 'read');

# Delete all permissions for a user or a role

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# Check permission, return true or false

Enforcer::enforce("user1", "/user", "edit");
```