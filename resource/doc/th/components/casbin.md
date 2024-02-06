# Casbin

## คำอธิบาย

Casbin เป็นกรอบควบคุมการเข้าถึงที่เปิดตัวที่มีประสิทธิภาพและมีความแข็งแกร่ง และมีกลไกการจัดการสิทธิการเข้าถึงที่รองรับรูปแบบควบคุมการเข้าถึงที่หลากหลาย

## ที่อยู่โปรเจค

https://github.com/teamones-open/casbin

## การติดตั้ง

  ```php
  composer require teamones/casbin
  ```

## เว็บไซต์ Casbin

สามารถดูข้อมูลการใช้งานเพิ่มเติมได้ที่เว็บไซต์อย่างเป็นทางการ ที่นี่จะกล่าวถึงวิธีการกำหนดใช้งานใน webman เท่านั้น

https://casbin.org/docs/zh-CN/overview

## โครงสร้างไดเร็กทอรี

```
.
├── config                        ไดเร็กทอรีการกำหนดค่า
│   ├── casbin-restful-model.conf ไฟล์กำหนดค่าของโมเดลสิทธิการเข้าถึงที่ใช้
│   ├── casbin.php                การกำหนดค่า casbin
......
├── database                      ไฟล์ฐานข้อมูล
│   ├── migrations                ไฟล์โครงการย้าย
│   │   └── 20210218074218_create_rule_table.php
......
```

## ไฟล์การย้ายฐานข้อมูล

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
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'ตารางกฎ']);

        // เพิ่มฟิลด์ข้อมูล
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'ไอดีหลัก'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'ประเภทกฎ'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        // ทำการสร้าง
        $table->create();
    }
}

```

## การกำหนดค่า casbin

สัญญาณการกำหนดค่าโมเดลสิทธิการเข้าถึง โปรดดูที่นี่：https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // ไฟล์กำหนดค่าโมเดลสิทธิการเข้าถึง
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model หรือ adapter
            'class' => \app\model\Rule::class,
        ],
    ],
    // สามารถกำหนดค่าโมเดลสิทธิหลาย ๆ รูปแบบ
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // ไฟล์กำหนดค่าโมเดลสิทธิการเข้าถึง
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model หรือ adapter
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### อะแดปเตอร์

สำหรับขณะนี้ที่พากย์หน้าคอมโพเซอร์มีการฝังวิธีการของ think-orm หากต้องการใช้ orm อื่น ๆ โปรดดูที่ vendor/teamones/src/adapters/DatabaseAdapter.php

แล้วแก้ไขการกำหนดค่าดังนี้

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // ไฟล์กำหนดค่าโมเดลสิทธิการเข้าถึง
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // ที่นี่กำหนดเป็นโหมดอะแดปเตอร์
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## การใช้งาน

### การนำเข้า

```php
# การนำเข้า
use teamones\casbin\Enforcer;
```

### วิธีการใช้งาน 2 แบบ

```php
# 1. ใช้ค่าเริ่มต้น default
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 1. ใช้การกำหนดรูปแบบ rbac ที่กำหนดเอง
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### แนวทางการใช้งาน API

สำหรับข้อมูลการใช้งาน API โปรดดูที่เว็บไซต์อย่างเป็นทางการ

- การจัดการ API: https://casbin.org/docs/zh-CN/management-api
- RBAC API: https://casbin.org/docs/zh-CN/rbac-api

```php
# เพิ่มสิทธิให้กับผู้ใช้

Enforcer::addPermissionForUser('user1', '/user', 'read');

# ลบสิทธิของผู้ใช้หนึ่งคน

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# รับสิทธิทั้งหมดของผู้ใช้

Enforcer::getPermissionsForUser('user1'); 

# เพิ่มบทบาทให้กับผู้ใช้

Enforcer::addRoleForUser('user1', 'role1');

# เพิ่มสิทธิให้แก่บทบาท

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# รับข้อมูลบทบาททั้งหมด

Enforcer::getAllRoles();

# รับข้อมูลบทบาททั้งหมดของผู้ใช้

Enforcer::getRolesForUser('user1');

# รับข้อมูลผู้ใช้โดยใช้บทบาท

Enforcer::getUsersForRole('role1');

# ตรวจสอบว่าผู้ใช้ตรงกับบทบาทหรือไม่

Enforcer::hasRoleForUser('use1', 'role1');

# ลบบทบาทของผู้ใช้

Enforcer::deleteRoleForUser('use1', 'role1');

# ลบบทบาททั้งหมดของผู้ใช้

Enforcer::deleteRolesForUser('use1');

# ลบบทบาท

Enforcer::deleteRole('role1');

# ลบสิทธิ

Enforcer::deletePermission('/user', 'read');

# ลบสิทธิทั้งหมดของผู้ใช้หรือบทบาท

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# ตรวจสอบสิทธิ และคืนค่าเป็น true หรือ false

Enforcer::enforce("user1", "/user", "edit");
```
