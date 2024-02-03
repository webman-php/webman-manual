# Casbin

## 説明

Casbinは、強力で効率的なオープンソースのアクセス制御フレームワークであり、その権限管理メカニズムは複数のアクセス制御モデルをサポートしています。

## プロジェクトURL

https://github.com/teamones-open/casbin

## インストール

```php
composer require teamones/casbin
```

## Casbin公式ウェブサイト

詳しい使用方法については公式の中国語のドキュメントをご覧いただくか、ここではwebmanでの設定方法について説明します。

https://casbin.org/docs/zh-CN/overview

## ディレクトリ構造

```
.
├── config                        // 設定ディレクトリ
│   ├── casbin-restful-model.conf  // 使用する権限モデルの設定ファイル
│   ├── casbin.php                // casbinの設定
......
├── database                      // データベースファイル
│   ├── migrations                // マイグレーションファイル
│   │   └── 20210218074218_create_rule_table.php
......
```

## データベースのマイグレーションファイル

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'ルールテーブル']);

        // データフィールドの追加
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'プライマリキーID'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'ルールタイプ'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        // 作成を実行
        $table->create();
    }
}
```

## Casbinの設定

権限ルールモデルの構成構文については、https://casbin.org/docs/zh-CN/syntax-for-models を参照してください。

```php
<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // 権限ルールモデルの設定ファイル
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // modelまたはadapter
            'class' => \app\model\Rule::class,
        ],
    ],
    // 複数の権限modelを構成できます
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // 権限ルールモデルの設定ファイル
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // modelまたはadapter
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### アダプター

現在のcomposerラッパーは、think-ormのmodelメソッドに適応されています。他のORMについては、vendor/teamones/src/adapters/DatabaseAdapter.php を参照してください。

その後、設定を変更します。

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // 権限ルールモデルの設定ファイル
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // ここでタイプをアダプターモードに構成
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## 使用方法

### インポート

```php
# インポート
use teamones\casbin\Enforcer;
```

### 2つの使用方法

```php
# 1. デフォルトで default 設定を使用する
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 1. カスタムの rbac 設定を使用する
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### 一般的なAPIの紹介

さらにAPIの使用法については、公式サイトをご覧ください。

- 管理API：https://casbin.org/docs/zh-CN/management-api
- RBAC API：https://casbin.org/docs/zh-CN/rbac-api

```php
# ユーザーに権限を追加する

Enforcer::addPermissionForUser('user1', '/user', 'read');

# ユーザーの権限を削除する

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# ユーザーのすべての権限を取得する

Enforcer::getPermissionsForUser('user1');

# ユーザーに役割を追加する

Enforcer::addRoleForUser('user1', 'role1');

# 役割に権限を追加する

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# すべての役割を取得する

Enforcer::getAllRoles();

# ユーザーのすべての役割を取得する

Enforcer::getRolesForUser('user1');

# 役割からユーザーを取得する

Enforcer::getUsersForRole('role1');

# ユーザーが特定の役割に属しているかを確認する

Enforcer::hasRoleForUser('use1', 'role1');

# ユーザーの役割を削除する

Enforcer::deleteRoleForUser('use1', 'role1');

# ユーザーのすべての役割を削除する

Enforcer::deleteRolesForUser('use1');

# 役割を削除する

Enforcer::deleteRole('role1');

# 権限を削除する

Enforcer::deletePermission('/user', 'read');

# ユーザーまたは役割のすべての権限を削除する

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# 権限をチェックし、trueまたはfalseを返す

Enforcer::enforce("user1", "/user", "edit");
```
