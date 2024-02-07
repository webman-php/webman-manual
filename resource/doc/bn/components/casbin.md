# কাসবিন

## বর্ণনা

কাসবিন হল একটি শক্তিশালী, দক্ষ ওপেন সোর্স অ্যাক্সেস কন্ট্রোল ফ্রেমওয়ার্ক যা বহু ধরণের অ্যাক্সেস কন্ট্রোল মডেল সমর্থন করে।

## প্রজেক্ট ঠিকানা

https://github.com/teamones-open/casbin

## ইনস্টলেশন

```php
composer require teamones/casbin
```

## কাসবিন অফিসিয়াল ওয়েবসাইট

বিস্তারিত ব্যবহারের জন্য আপনি অফিসিয়াল চাইনিজ ডকুমেন্টে যেতে পারেন, এখানে আমি কেবল ওয়েবম্যানে কীভাবে কনফিগার করব তা বলবো।

https://casbin.org/docs/zh-CN/overview

## ডিরেক্টরি স্ট্রাকচার

``` 
.
├── config                        কনফিগারেশন ডিরেক্টরি
│   ├── casbin-restful-model.conf ব্যবহার করা অনুমতি মডেল কনফিগারেশন ফাইল
│   ├── casbin.php                casbin কনফিগারেশন
......
├── database                      ডেটাবেস ফাইল
│   ├── migrations                মাইগ্রেশন ফাইল
│   │   └── 20210218074218_create_rule_table.php
......
```

## ডেটাবেস মাইগ্রেশন ফাইল

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    // পরিবর্তন পদ্ধতি
    public function change()
    {
      // টেবিল তৈরি করুন
    }
}

```

## কাসবিন কনফিগারেশন

অনুমতি নিয়ামক কনফিগারেশন দয়া করে দেখুন: https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // অনুমতি নিয়ামক মডেল কনফিগারেশন ফাইল
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // মডেল অথবা অ্যাডাপ্টার
            'class' => \app\model\Rule::class,
        ],
    ],
    // একাধিক অনুমতি মডেল কনফিগারেশন করা যায়
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // অনুমতি নিয়ামক মডেল কনফিগারেশন ফাইল
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // মডেল অথবা অ্যাডাপ্টার
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### অ্যাডাপ্টার

বর্তমান কম্পোজার পৃষ্ঠাবলীতে think-orm এর মডেল পদ্ধতিকে অনুগত করা হয়েছে, অন্য এওয়ারএম দয়া করে vendor/teamones/src/adapters/DatabaseAdapter.php দেখুন

তারপর কনফিগারেশন পরিবর্তন করুন

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // অনুমতি নিয়ামক মডেল কনফিগারেশন ফাইল
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // এখানে টাইপটি অ্যাডাপ্টার মোডেল হিসেবে কনফিগার করুন
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## ব্যবহারের নির্দেশ

### আমন্ত্রণ

```php
# আমন্ত্রণ
use teamones\casbin\Enforcer;
```

### দুটি ব্যবহারের পদ্ধতি

```php
# 1. ডিফল্ট কনফিগারেশন ব্যবহার করুন
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 2. ব্যক্তিগত rbac কনফিগারেশন ব্যবহার করুন
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### সাধারণ API পরিচিতি

অধিক API ব্যবহারের তথ্যের জন্য দয়া করে অফিসিয়াল ডকুমেন্টে যান

- ম্যানেজমেন্ট API: https://casbin.org/docs/zh-CN/management-api
- আরবিএসি এপিআই: https://casbin.org/docs/zh-CN/rbac-api

```php
# ব্যবহারকারীকে অনুমতি যোগ করুন

Enforcer::addPermissionForUser('user1', '/user', 'read');

# একটি ব্যবহারকে আনুমতি মুছুন

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# ব্যবহারকে সমস্ত অনুমতি প্রাপ্তি করুন

Enforcer::getPermissionsForUser('user1');

# ব্যবহারকে ভূমিকা যোগ করুন

Enforcer::addRoleForUser('user1', 'role1');

# ভূমিকার জন্য অনুমতি যোগ করুন

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# সমস্ত ভূমিকা প্রাপ্ত করুন

Enforcer::getAllRoles();

# ব্যবহারকে সমস্ত ভূমিকা প্রাপ্ত করুন

Enforcer::getRolesForUser('user1');

# ভূমিকা অনুযায়ী ব্যবহার প্রাপ্ত করুন

Enforcer::getUsersForRole('role1');

# ব্যবহারকে ভূমিকা অনুযায়ী মধ্যে যে কোনও ভূমিকা হয় কি না তা বিচার করুন

Enforcer::hasRoleForUser('use1', 'role1');

# ব্যবহারকে ভূমিকা থেকে মুছুন

Enforcer::deleteRoleForUser('use1', 'role1');

# ব্যবহারের সমস্ত ভূমিকা মুছুন

Enforcer::deleteRolesForUser('use1');

# ভূমিকা মুছুন

Enforcer::deleteRole('role1');

# অনুমতি মুছুন

Enforcer::deletePermission('/user', 'read');

# ব্যবহারকে অথবা ভূমিকার সমস্ত অনুমতি মুছুন

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# অনুমতি পরীক্ষা করুন, true বা false ফিরে।

Enforcer::enforce("user1", "/user", "edit");

```
