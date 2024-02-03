# Casbin (Casbin)

## 설명

Casbin은 강력하고 효율적인 오픈 소스 액세스 제어 프레임워크로, 여러 종류의 액세스 제어 모델을 지원하는 권한 관리 메커니즘을 갖추고 있습니다.

## 프로젝트 주소

https://github.com/teamones-open/casbin

## 설치

```php
composer require teamones/casbin
```

## Casbin 공식 웹사이트

자세한 사용법은 공식 중문 문서를 참조하시고, 여기서는 webman에서의 구성 및 사용법에 대해서만 다루겠습니다.

https://casbin.org/docs/zh-CN/overview

## 디렉토리 구조

```
.
├── config                        구성 디렉토리
│   ├── casbin-restful-model.conf 사용하는 권한 모델 구성 파일
│   ├── casbin.php                casbin 구성
......
├── database                      데이터베이스 파일
│   ├── migrations                이주 파일
│   │   └── 20210218074218_create_rule_table.php
......
```

## 데이터베이스 이주 파일

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
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '규칙 테이블']);

        //데이터 필드 추가
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => '기본 키 ID'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => '규칙 유형'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        //생성 실행
        $table->create();
    }
}

```

## casbin 구성

권한 규칙 모델 구성 구문은 여기를 참조하십시오: https://casbin.org/docs/zh-CN/syntax-for-models

```php
<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // 권한 규칙 모델 구성 파일
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // 모델 또는 어댑터
            'class' => \app\model\Rule::class,
        ],
    ],
    // 다수의 권한 모델 설정이 가능합니다.
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // 권한 규칙 모델 설정 파일
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // 모델 또는 어댑터
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### 어댑터

현재 컴포저 랩핑에는 think-orm의 model 방식이 적용되었으며, 다른 orm은 vendor/teamones/src/adapters/DatabaseAdapter.php를 참조하십시오.

그런 다음 구성을 수정하십시오.

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // 권한 규칙 모델 구성 파일
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // 여기에서 유형을 어댑터 모드로 설정합니다.
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## 사용법

### 가져오기

```php
# 가져오기
use teamones\casbin\Enforcer;
```

### 두 가지 사용 방법

```php
# 1. 기본 default 구성 사용
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 2. 사용자 정의 rbac 구성 사용
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### 자주 사용되는 API 소개

더 많은 API 사용 방법은 공식 문서를 참조하십시오.

- 관리 API: https://casbin.org/docs/zh-CN/management-api
- RBAC API: https://casbin.org/docs/zh-CN/rbac-api

```php
# 사용자에게 권한 부여

Enforcer::addPermissionForUser('user1', '/user', 'read');

# 사용자의 권한 삭제

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# 사용자의 모든 권한 가져오기

Enforcer::getPermissionsForUser('user1');

# 사용자에게 역할 부여

Enforcer::addRoleForUser('user1', 'role1');

# 역할에 권한 부여

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# 모든 역할 가져오기

Enforcer::getAllRoles();

# 사용자의 모든 역할 가져오기

Enforcer::getRolesForUser('user1');

# 역할로부터 사용자 가져오기

Enforcer::getUsersForRole('role1');

# 사용자가 특정 역할에 속하는지 확인

Enforcer::hasRoleForUser('use1', 'role1');

# 사용자의 역할 삭제

Enforcer::deleteRoleForUser('use1', 'role1');

# 사용자의 모든 역할 삭제

Enforcer::deleteRolesForUser('use1');

# 역할 삭제

Enforcer::deleteRole('role1');

# 권한 삭제

Enforcer::deletePermission('/user', 'read');

# 사용자나 역할의 모든 권한 삭제

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# 권한 확인, true 또는 false 반환

Enforcer::enforce("user1", "/user", "edit");
```
