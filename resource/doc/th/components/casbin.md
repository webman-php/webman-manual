# Casbin

## คำอธิบาย

Casbin เป็นกรอบการควบคุมการเข้าถึงอย่างมีประสิทธิภาพและมีพลัง ซึ่งระบบการจัดการสิทธิ์ของมันสนับสนุนรูปแบบการควบคุมการเข้าถึงหลายแบบ

## ที่อยู่โปรเจค

https://github.com/teamones-open/casbin

## การติดตั้ง
 
  ```php
  composer require teamones/casbin
  ```

## เว็บไซต์ Casbin

สำหรับการใช้งานอธิบายอย่างละเอียดสามารถดูได้ที่เว็บไซต์ทางการภาษาจีน ที่นี่จะอธิบายว่าจะกำหนดค่าและใช้งานใน webman อย่างไร

https://casbin.org/docs/zh-CN/overview

## โครงสร้างโฟลเดอร์

``` 
.
├── config                        โฟลเดอร์การกำหนดค่า
│   ├── casbin-restful-model.conf ไฟล์กำหนดค่าโมเดลสิทธิ์ที่ใช้
│   ├── casbin.php                การกำหนดค่า casbin
......
├── database                      ไฟล์ฐานข้อมูล
│   ├── migrations                ไฟล์เคลื่อนย้าย
│   │   └── 20210218074218_create_rule_table.php
......
```

## ไฟล์เคลื่อนย้ายของฐานข้อมูล

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'ตารางกฎ']);

        //เพิ่มฟิลด์ข้อมูล
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'ไอดีหลัก'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'ประเภทกฎ'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        //ทำการสร้าง
        $table->create();
    }
}

```

## การกำหนดค่า casbin

สัญลักษณ์กฎสิทธิ์ ตัวอย่างของการกำหนดค่าดูได้ที่ : https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // ไฟล์กำหนดค่าโมเดลสิทธิ์
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model หรือ adapter
            'class' => \app\model\Rule::class,
        ],
    ],
    // สามารถกำหนดค่า model สิทธิ์ได้หลายตัว
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // ไฟล์กำหนดค่าโมเดลสิทธิ์
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model หรือ adapter
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### Adapter

ในการจัดการผ่าน Composer นี้ มันสามารถทำงานกับวิธีการของ think-orm ซึ่งอื่น ๆ โมร์ โปรดดูที่ vendor/teamones/src/adapters/DatabaseAdapter.php

และเปลี่ยนแปลงการกำหนดค่า

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // ไฟล์กำหนดค่าโมเดลสิทธิ์
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // จะกำหนดประเภทเป็นโหมดที่เข้ากันได้
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## วิธีใช้

### นำเข้า

```php
# การนำเข้า
use teamones\casbin\Enforcer;
```

### การใช้งาน 2 แบบ

```php
# 1. ใช้ค่าปรับแต่ง default 
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 2. ใช้ค่าปรับแต่ง rbac เอง
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### API ที่ใช้บ่อย

ตาราง API สำหรับความจัดการ: https://casbin.org/docs/zh-CN/management-api
ตาราง RBAC API: https://casbin.org/docs/zh-CN/rbac-api

```php
# เพิ่มสิทธิ์ที่แต่งตั้งให้กับผู้ใช้

Enforcer::addPermissionForUser('user1', '/user', 'read');

# ลบสิทธิ์ของผู้ใช้

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# รับข้อมูลสิทธิ์ทั้งหมดของผู้ใช้

Enforcer::getPermissionsForUser('user1'); 

# เพิ่มบทบาทให้กับผู้ใช้

Enforcer::addRoleForUser('user1', 'role1');

# เพิ่มสิทธิ์ให้กับบทบาท

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# รับข้อมูลของบทบาททั้งหมด

Enforcer::getAllRoles();

# รับข้อมูลของบทบาททั้งหมดของผู้ใช้

Enforcer::getRolesForUser('user1');

# รับข้อมูลของผู้ใช้ทั้งหมดของบทบาทที่กำหนด

Enforcer::getUsersForRole('role1');

# ตรวจสอบว่าผู้ใช้มีบทบาทหรือเปล่า

Enforcer::hasRoleForUser('use1', 'role1');

# ลบบทบาทของผู้ใช้

Enforcer::deleteRoleForUser('use1', 'role1');

# ลบบทบาตทั้งหมดของผู้ใช้

Enforcer::deleteRolesForUser('use1');

# ลบบทบาท

Enforcer::deleteRole('role1');

# ลบสิทธ์

Enforcer::deletePermission('/user', 'read');

# ลบสิทธ์ทั้งหมดของผู้ใช้หรือบทบาท

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# ตรวจสอบสิทธิ์ และคืนค่าเป็น true หรือ false

Enforcer::enforce("user1", "/user", "edit");
```
