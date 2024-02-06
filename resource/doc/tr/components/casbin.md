# Casbin

## Açıklama

Casbin, güçlü ve verimli bir açık kaynak erişim kontrol çerçevesidir ve yetkilendirme mekanizması çeşitli erişim kontrol modellerini destekler.

## Proje Linki

https://github.com/teamones-open/casbin

## Kurulum
 
  ```php
  composer require teamones/casbin
  ```

## Casbin Resmi Websitesi

Detaylı kullanım için resmi Çince belgelere bakabilirsiniz, burada sadece webman'de nasıl yapılandırılacağını anlatacağız.

https://casbin.org/docs/zh-CN/overview

## Klasör Yapısı

```
.
├── config                        Konfigürasyon klasörü
│   ├── casbin-restful-model.conf Kullanılan izin modeli yapılandırma dosyası
│   ├── casbin.php                casbin yapılandırması
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
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Kural tablosu']);

        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'Primary key ID'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'Kural tipi'])
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

## Casbin Yapılandırması

İzin kuralı modeli yapılandırma diline buradan bakabilirsiniz: https://casbin.org/docs/zh-CN/syntax-for-models

```php
<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // İzin kuralı modeli yapılandırma dosyası
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
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // İzin kuralı modeli yapılandırma dosyası
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

Mevcut composer kılıfının bir parçası olarak think-orm'in model yöntemine uyum sağlar, diğer orm'ler için vendor/teamones/src/adapters/DatabaseAdapter.php'ye bakabilirsiniz

Daha sonra yapılandırmayı değiştirin

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // İzin kuralı modeli yapılandırma dosyası
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // Burada tipi adaptör moduna ayarlayın
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## Kullanım Talimatları

### İçe Aktarma

```php
# İçe Aktarma
use teamones\casbin\Enforcer;
```

### İki Kullanım Şekli

```php
# 1. Varsayılan olarak default yapılandırmayı kullanma
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 2. Özel rbac yapılandırmasını kullanma
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### Sık Kullanılan API'lerin Tanıtımı

Daha fazla API kullanımı için resmi dökümanlara bakabilirsiniz

- Yönetim API'si: https://casbin.org/docs/zh-CN/management-api
- RBAC API'si: https://casbin.org/docs/zh-CN/rbac-api

```php
# Kullanıcıya izin ekleme

Enforcer::addPermissionForUser('user1', '/user', 'read');

# Bir kullanıcının iznini silme

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# Kullanıcının tüm izinlerini alma

Enforcer::getPermissionsForUser('user1'); 

# Kullanıcıya rol ekleme

Enforcer::addRoleForUser('user1', 'role1');

# Role izni ekleme

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# Tüm rolleri alma

Enforcer::getAllRoles();

# Kullanıcının tüm rollerini alma

Enforcer::getRolesForUser('user1');

# Role göre kullanıcıları alma

Enforcer::getUsersForRole('role1');

# Bir kullanıcının bir role ait olup olmadığını kontrol etme

Enforcer::hasRoleForUser('use1', 'role1');

# Kullanıcı rollerini silme

Enforcer::deleteRoleForUser('use1', 'role1');

# Kullanıcının tüm rollerini silme

Enforcer::deleteRolesForUser('use1');

# Role silme

Enforcer::deleteRole('role1');

# Izin silme

Enforcer::deletePermission('/user', 'read');

# Kullanıcının veya rolün tüm izinlerini silme

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# İzni kontrol etme, true veya false döner

Enforcer::enforce("user1", "/user", "edit");
```
