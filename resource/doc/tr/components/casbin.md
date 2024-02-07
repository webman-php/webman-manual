# Webman

## Tanım

Webman, yüksek performanslı bir PHP çatısı olan Workerman üzerine geliştirilmiş bir PHP çatısıdır. Ağ uygulamaları geliştirmek için kullanılan bu çatı, yüksek verimlilik ve hız sunar.

## Proje Adresi

https://github.com/walkor/webman

## Kurulum

```php
composer require teamones/casbin
```

## Webman Resmi Web Sitesi

Detaylı kullanım için resmi web sitesi üzerindeki Türkçe belgelendirmeyi inceleyebilirsiniz. Burada sadece webman üzerinde nasıl yapılandırılacağını anlatacağız.

https://casbin.org/docs/zh-CN/overview

## Klasör Yapısı

```plaintext
.
├── config                        Konfigürasyon klasörü
│   ├── casbin-restful-model.conf Kullanılan izin modeli yapılandırma dosyası
│   ├── casbin.php                Casbin konfigürasyonu
......
├── database                      Veritabanı dosyaları
│   ├── migrations                Göç dosyaları
│   │   └── 20210218074218_create_rule_table.php
......
```

## Veritabanı Göç Dosyası

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Rules Table']);

        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'Primary Key ID'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'Rule Type'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        $table->create();
    }
}
```

## Casbin Konfigürasyonu

İzin kuralları modeli yapılandırma dilbilgisini görmek için: https://casbin.org/docs/zh-CN/syntax-for-models

```php
<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Izin kuralları modeli yapılandırma dosyası
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model veya adapter
            'class' => \app\model\Rule::class,
        ],
    ],
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // Izin kuralları modeli yapılandırma dosyası
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model veya adapter
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### Adaptör

Mevcut composer paketinde think-orm'ın model yöntemi için uyarlama yapılmıştır, diğer ORM'ler için lütfen vendor/teamones/src/adapters/DatabaseAdapter.php dosyasına bakınız.

Daha sonra yapılandırmayı güncelleyin.

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Izin kuralları modeli yapılandırma dosyası
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // Burada tip uyarlama moduna konfigüre edildi
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```
## Kullanım Kılavuzu

### Dahil Etme

```php
# Dahil etme
use teamones\casbin\Enforcer;
```

### İki Kullanım Şekli

```php
# 1. Varsayılan olarak default yapılandırmayı kullanma
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 2. Özel rbac yapılandırmasını kullanma
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### Yaygın API'lerin Tanıtımı

Daha fazla API kullanımı için resmi belgelere bakın

- Yönetim API'si: https://casbin.org/docs/zh-CN/management-api
- RBAC API'si: https://casbin.org/docs/zh-CN/rbac-api

```php
# Kullanıcıya izin ekleme

Enforcer::addPermissionForUser('user1', '/user', 'read');

# Bir kullanıcının iznini silme

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# Kullanıcının tüm izinlerini al

Enforcer::getPermissionsForUser('user1'); 

# Kullanıcıya rol ekleme

Enforcer::addRoleForUser('user1', 'role1');

# Role izni ekleme

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# Tüm rolleri al

Enforcer::getAllRoles();

# Kullanıcının tüm rollerini al

Enforcer::getRolesForUser('user1');

# Role göre kullanıcıyı al

Enforcer::getUsersForRole('role1');

# Kullanıcının belirli bir role ait olup olmadığını kontrol etme

Enforcer::hasRoleForUser('use1', 'role1');

# Kullanıcı rolünü silme

Enforcer::deleteRoleForUser('use1', 'role1');

# Kullanıcının tüm rollerini silme

Enforcer::deleteRolesForUser('use1');

# Rolü silme

Enforcer::deleteRole('role1');

# İzni silme

Enforcer::deletePermission('/user', 'read');

# Kullanıcının veya rolün tüm izinlerini silme

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# İzni kontrol etme, true veya false döner

Enforcer::enforce("user1", "/user", "edit");
```
