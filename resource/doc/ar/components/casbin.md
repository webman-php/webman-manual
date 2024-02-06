# كاسبين

## الوصف

كاسبين هو إطار عمل قوي وفعال للسيطرة على الوصول مفتوح المصدر، وآلية إدارة الأذونات فيه يدعم مجموعة متنوعة من نماذج السيطرة على الوصول.

## عنوان المشروع

https://github.com/teamones-open/casbin

## التثبيت

  ```php
  composer require teamones/casbin
  ```

## موقع كاسبين

يمكنك الحصول على مزيد من التفاصيل من الاطلاع على الوثائق الرسمية باللغة الصينية، وهنا سوف نتحدث فقط عن كيفية تكوين الاستخدام في webman.

https://casbin.org/docs/zh-CN/overview

## هيكل الدليل

```
.
├── config                        الدليل لتكوينات
│   ├── casbin-restful-model.conf ملف تكوين نموذج الأذونات المستخدم
│   ├── casbin.php                تكوين كاسبين
......
├── database                      ملفات قاعدة البيانات
│   ├── migrations                ملفات الترحيل
│   │   └── 20210218074218_create_rule_table.php
......
```

## ملفات نقل قاعدة البيانات

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    /**
     * طريقة التغيير.
     *
     * اكتب الترحيلات القابلة للعكس باستخدام هذه الطريقة.
     *
     * ويمكن الحصول على مزيد من المعلومات حول كتابة الترحيلات من هنا:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * ويمكن استخدام الأوامر التالية في هذه الطريقة وسيقوم Phinx
     * بإعادتها تلقائيًا عند التراجع:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * سيؤدي أي تغييرات تدميرية أخرى إلى حدوث خطأ عند محاولة
     * التراجع عن الترحيل.
     *
     * تذكر أن تدعو إلى "create()" أو "update()" وليس "save()"
     * عند التعامل مع فئة الجدول.
     */
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'جدول القواعد']);

        // إضافة حقول البيانات
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'هوية رئيسية'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'نوع القاعدة'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        // تنفيذ الإنشاء
        $table->create();
    }
}

```

## تكوين كاسبين

يرجى الاطلاع على بناء النموذج الاذني لمزيد من المعلومات: https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // ملف تكوين نموذج الأذونات
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // النموذج أو المحول
            'class' => \app\model\Rule::class,
        ],
    ],
    // يمكن تكوين العديد من نماذج الأذونات
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // ملف تكوين نموذج الأذونات
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // النموذج أو المحول
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### المحول

المحول المعبأ حاليا في الcomposer يستخدم طريقة نموذج think-orm، يرجى الرجوع إلى مسار vendor/teamones/src/adapters/DatabaseAdapter.php لاستخدام طرق orm أخرى.

ثم قم بتعديل التكوين

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // ملف تكوين نموذج الأذونات
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // هنا يتم تكوين النوع كمحول
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## كيفية الاستخدام

### الاستيراد

```php
# الاستيراد
use teamones\casbin\Enforcer;
```

### طرق الاستخدام

```php
# 1. الاستخدام الافتراضي default configuration
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 1. استخدام تكوين الأذونات المخصص rbac
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### مقدمة لواجهة برمجة التطبيقات الشائعة

يرجى الانتقال لمزيد من استخدامات واجهة برمجة تطبيقات الإدارة إلى الموقع الرسمي

- إدارة wAPI: https://casbin.org/docs/zh-CN/management-api
- واجهة برمجة تطبيقات RBAC: https://casbin.org/docs/zh-CN/rbac-api

```php
# إضافة أذونات للمستخدم
Enforcer::addPermissionForUser('user1', '/user', 'read');

# حذف أذونة مستخدم
Enforcer::deletePermissionForUser('user1', '/user', 'read');

# الحصول على كافة أذونات المستخدم
Enforcer::getPermissionsForUser('user1'); 

# إضافة دور للمستخدم
Enforcer::addRoleForUser('user1', 'role1');

# إضافة أذونات للدور
Enforcer::addPermissionForUser('role1', '/user', 'edit');

# الحصول على كل الأدوار
Enforcer::getAllRoles();

# الحصول على كل الأدوار للمستخدم
Enforcer::getRolesForUser('user1');

# الحصول على المستخدمين بناء على الدور
Enforcer::getUsersForRole('role1');

# التحقق مما إذا كان المستخدم ينتمي إلى دور معين
Enforcer::hasRoleForUser('use1', 'role1');

# حذف دور المستخدم
Enforcer::deleteRoleForUser('use1', 'role1');

# حذف كل أدوار المستخدم
Enforcer::deleteRolesForUser('use1');

# حذف الدور
Enforcer::deleteRole('role1');

# حذف الأذونة
Enforcer::deletePermission('/user', 'read');

# حذف كل أذونات المستخدم أو الدور
Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# التحقق من الأذونة، إما صحيحة أو خاطئة
Enforcer::enforce("user1", "/user", "edit");
```
