# データベース移行ツール Phinx

## 説明

Phinxを使用すると、開発者はデータベースを簡潔に変更および維持できます。人為的なSQLステートメントの手書きを回避し、強力なPHP APIを使用してデータベース移行を管理します。開発者はバージョン管理を使用してデータベース移行を管理できます。 Phinxを使用すると、異なるデータベース間でデータを簡単に移行できます。また、実行された移行スクリプトを追跡し、開発者はデータベースの状態を心配する必要がなく、より良いシステムを構築することに集中できます。

## プロジェクトURL

https://github.com/cakephp/phinx

## インストール

```php
composer require robmorgan/phinx
```

## 公式中国語ドキュメントのURL

詳細な使用方法については公式中国語ドキュメントを参照してください。ここでは、webmanでの設定と使用方法について説明します。

https://tsy12321.gitbooks.io/phinx-doc/content/

## 移行ファイルのディレクトリ構造

```
.
├── app                           アプリケーションディレクトリ
│   ├── controller                コントローラディレクトリ
│   │   └── Index.php             コントローラ
│   ├── model                     モデルディレクトリ
......
├── database                      データベースファイル
│   ├── migrations                移行ファイル
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     テストデータ
│   │   └── UserSeeder.php
......
```

## phinx.phpの設定

プロジェクトのルートディレクトリにphinx.phpファイルを作成します。

```php
<?php
return [
    "paths" => [
        "migrations" => "database/migrations",
        "seeds"      => "database/seeds"
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database"        => "dev",
        "default_environment"     => "dev",
        "dev" => [
            "adapter" => "DB_CONNECTION",
            "host"    => "DB_HOST",
            "name"    => "DB_DATABASE",
            "user"    => "DB_USERNAME",
            "pass"    => "DB_PASSWORD",
            "port"    => "DB_PORT",
            "charset" => "utf8"
        ]
    ]
];
```

## 使用上の注意

移行ファイルはマージ後に再度変更することはできず、問題が発生した場合は新しい変更または削除操作ファイルを作成する必要があります。

#### データテーブル作成操作ファイルの命名規則

`{time（自動作成）}_create_{表名の英小文字}`

#### データテーブル変更操作ファイルの命名規則

`{time（自動作成）}_modify_{表名の英小文字+具体的な変更項目の英小文字}`

### データテーブル削除操作ファイルの命名規則

`{time（自動作成）}_delete_{表名の英小文字+具体的な変更項目の英小文字}`

### データの追加ファイルの命名規則

`{time（自動作成）}_fill_{表名の英小文字+具体的な変更項目の英小文字}`
