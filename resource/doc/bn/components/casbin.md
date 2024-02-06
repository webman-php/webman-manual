# কাসবিন

## বর্ণনা

Casbin একটি শক্তিশালী, দক্ষ ওপেন সোর্স অ্যাক্সেস কন্ট্রোল ফ্রেমওয়ার্ক, যা অনেক ধরনের অ্যাক্সেস কন্ট্রোল মডেল সমর্থন করে।

## প্রকল্প ঠিকানা

https://github.com/teamones-open/casbin

## ইনস্টলেশন

```php
composer require teamones/casbin
```

## Casbin সাইট

বিস্তারিত ব্যবহার জানতে পারেন আপনি এখানে অফিসিয়াল চাইনিজ ডকুমেন্টে যাচ্ছেন, এখানে আমরা শুধুমাত্র webman এ কিভাবে কনফিগার করতে হয় শুনাব।

https://casbin.org/docs/zh-CN/overview

## ফোল্ডার স্ট্রাকচার

```
.
├── কনফিগ                        কনফিগারেশন ফোল্ডার
│   ├── casbin-restful-model.conf ব্যবহৃত অনুমতি মডেল কনফিগারেশন ফাইল
│   ├── casbin.php                casbin কনফিগারেশন
......
├── ডাটাবেস                   ডাটাবেস ফাইল
│   ├── migrations                ট্রান্সমিট ফাইল
│   │   └── 20210218074218_create_rule_table.php
......
```

## ডাটাবেস মাইগ্রেশন ফাইল

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    /**
     * চেঞ্জ মেথড।
     *
     * আপনার বিপর্যস্ত মাইগ্রেশনগুলি লিখুন এই পদ্ধতিতে।
     * 
     * এই পদ্ধতিতে মাইগ্রেটশন লিখার সম্পর্কে অধিক তথ্য এখানে উপলব্ধ।
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     * 
     * এই পদ্ধতিতে এই নির্দেশানাগুলি ব্যবহার করা যাবে এবং Phinx স্বয়ংক্রিয়ভাবে তাদের পিছনে ফিরিয়ে আনবে যখন রোলিংগ ব্যাক করা হবে:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * অন্যান্য ধ্বংসাত্মক পরিবর্তন যাবে না যখন মাইগ্রেশন রোলিং ব্যাক করার প্রয়াস করা হবে।
     *
     * টেবিল ক্লাস দিয়ে কাজের সাথে "তৈরি()" বা "আপডেট()" পদ্ধতিতে ডাক করতে মনে রাখবেন।
     */
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'রুল টেবিল']);

        //তথ্য ফিল্ডস যোগ করুন
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'প্রাথমিক আইডি'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'রুল টাইপ'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        //তৈরি সম্পাদন
        $table->create();
    }
}

```

## casbin কনফিগারেশন

অনুমতি নিয়ম মডেল কনফিগারেশন বাক্যবিন্যাসের জন্য দেখুন: https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // অনুমতি নিয়ম মডেল কনফিগারেশন ফাইল
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model or adapter
            'class' => \app\model\Rule::class,
        ],
    ],
    // একাধিক পূর্বনির্ধারিত অনুমতি মডেল কনফিগার করা যেতে পারে
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // অনুমতি নিয়ম মডেল কনফিগারেশন ফাইল
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model or adapter
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### এডাপ্টার

বর্তমান কম্পোজার পার্সেলে আপনি think-orm এর মডেল পদ্ধতিকে অ্যাডাপ্ট করেছেন, অন্যান্য orm দেখতে vendor/teamones/src/adapters/DatabaseAdapter.php

তারপর কনফিগারেশন পরিবর্তন করুন

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // অনুমতি নিয়ম মডেল কনফিগারেশন ফাইল
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // এখানে ধরনটিকে অ্যাডাপ্টার মোড হিসাবে কনফিগার করুন
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## ব্যবহারের নির্দেশিকা

### আমদানি

```php
# আমদানি
use teamones\casbin\Enforcer;
```

### দুইটি ব্যবহার পদ্ধতি

```php
# 1. ডিফল্ট কনফিগারেশন ব্যবহার করা
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 1. ব্যবহারকারী নির্ধারণ বোধগম্য rbac কনফিগারেশনের সাথে
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### সাধারণ API এর পরিচিতি

বিস্তারিত API ব্যবহারের জন্য অফিসিয়াল ডকুমেন্টে যান

- ম্যানেজমেন্ট এপিআই: https://casbin.org/docs/zh-CN/management-api
- RBAC এপিআই: https://casbin.org/docs/zh-CN/rbac-api

```php
# ব্যবহারকারীর জন্য অনুমতি যোগ করুন

Enforcer::addPermissionForUser('user1', '/user', 'read');

# একটি ব্যবহারকারীর অনুমতি মুছুন

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# ব্যবহারকারীর সমস্ত অনুমতি পেতে

Enforcer::getPermissionsForUser('user1'); 

# ব্যবহারকারীর জন্য ভূমিকা যোগ করুন

Enforcer::addRoleForUser('user1', 'role1');

# ভূমিকা জন্য অনুমতি যোগ করুন

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# সমস্ত ভূমিকা পেতে

Enforcer::getAllRoles();

# ব্যবহারকারীর সমস্ত ভূমিকা পেতে

Enforcer::getRolesForUser('user1');

# ভূমিকা থেকে ব্যবহারকারী পেতে

Enforcer::getUsersForRole('role1');

# ব্যবহারকারী কোন ভূমিকা ধারণ করে তা যাচাই করুন

Enforcer::hasRoleForUser('use1', 'role1');

# ব্যবহারকারীর ভূমিকা মুছুন

Enforcer::deleteRoleForUser('use1', 'role1');

# ব্যবহারকারীর সমস্ত ভূমিকা মুছুন

Enforcer::deleteRolesForUser('use1');

# ভূমিকা মুছুন

Enforcer::deleteRole('role1');

# অনুমতি মুছুন

Enforcer::deletePermission('/user', 'read');

# ব্যবহারকারী বা ভূমিকার সমস্ত অনুমতি মুছুন

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# অনুমতি পরীক্ষা, সত্য বা মিথ্যা ফিরে

Enforcer::enforce("user1", "/user", "edit");
```
