# Casbin

## 説明

Casbinは、強力で効率的なオープンソースのアクセス制御フレームワークであり、その権限管理メカニズムは複数のアクセス制御モデルをサポートしています。

## プロジェクトのURL

https://github.com/teamones-open/casbin

## インストール

  ```php
  composer require teamones/casbin
  ```

## Casbin公式ウェブサイト

詳しい使用方法は公式の中国語のドキュメントを参照し、ここではwebmanでの設定と使用方法についてのみ説明します。

https://casbin.org/docs/zh-CN/overview

## ディレクトリ構造

```php
.
├── config                        設定ディレクトリ
│   ├── casbin-restful-model.conf 使用する権限モデルの設定ファイル
│   ├── casbin.php                casbinの設定
......
├── database                      データベースファイル
│   ├── migrations                マイグレーションファイル
│   │   └── 20210218074218_create_rule_table.php
......
```

## データベースのマイグレーションファイル

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    // 省略
}
```

## casbinの設定

権限ルールモデルの設定構文についてはこちらを参照してください：https://casbin.org/docs/zh-CN/syntax-for-models

```php
<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // 権限規則モデルの設定ファイル
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // modelまたはadapter
            'class' => \app\model\Rule::class,
        ],
    ],
    // 複数の権限モデルを設定できる
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // 権限規則モデルの設定ファイル
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

Composerパッケージには現在、think-ormのmodelメソッドに対応しており、他のORMについてはvendor/teamones/src/adapters/DatabaseAdapter.phpを参照してください。

その後、設定を変更します。

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // 権限規則モデルの設定ファイル
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // ここでタイプをアダプターモードに設定
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

### 2つの使用法

```php
# 1. デフォルトでdefault構成を使用する
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 1. カスタムのrbac構成を使用する
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### 一般的なAPIの紹介

より多くのAPIの使用法については、公式サイトをご覧ください。

- 管理API：https://casbin.org/docs/zh-CN/management-api
- RBAC API：https://casbin.org/docs/zh-CN/rbac-api

```php
# ユーザーに権限を追加

Enforcer::addPermissionForUser('user1', '/user', 'read');

# ユーザーの権限を削除

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# ユーザーのすべての権限を取得

Enforcer::getPermissionsForUser('user1');

# ユーザーにロールを追加

Enforcer::addRoleForUser('user1', 'role1');

# ロールに権限を追加

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# すべてのロールを取得

Enforcer::getAllRoles();

# ユーザーのすべてのロールを取得

Enforcer::getRolesForUser('user1');

# ロールからユーザーを取得する

Enforcer::getUsersForRole('role1');

# ユーザーが特定のロールに属しているかどうかを判断する

Enforcer::hasRoleForUser('user1', 'role1');

# ユーザーのロールを削除

Enforcer::deleteRoleForUser('user1', 'role1');

# ユーザーのすべてのロールを削除

Enforcer::deleteRolesForUser('user1');

# ロールの削除

Enforcer::deleteRole('role1');

# 権限の削除

Enforcer::deletePermission('/user', 'read');

# ユーザーまたはロールのすべての権限を削除

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# 権限のチェック、trueまたはfalseを返す

Enforcer::enforce("user1", "/user", "edit");
```
