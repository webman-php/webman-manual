# データベース移行ツール Phinx

## 説明

Phinxは開発者がデータベースを簡単に変更および管理できるようにします。手動でSQL文を記述する必要がなく、強力なPHP APIを使用してデータベースの移行を管理します。開発者はバージョン管理を使用してデータベースの移行を管理できます。 Phinxを使用すると、異なるデータベース間でのデータ移行が簡単に行えます。また、実行された移行スクリプトを追跡することができ、開発者はデータベースの状態を心配する必要がなくなり、システムの改善により集中できます。

## プロジェクトのアドレス

https://github.com/cakephp/phinx

## インストール

```php
composer require robmorgan/phinx
```

## 公式ドキュメント（中国語）

詳細な使用方法については公式の中国語ドキュメントをご覧ください。ここではwebmanでの構成と使用方法について説明します。

https://tsy12321.gitbooks.io/phinx-doc/content/

## 移行ファイルのディレクトリ構造

``` 
.
├── app                           アプリケーションディレクトリ
│   ├── controller                コントローラーディレクトリ
│   │   └── Index.php             コントローラー
│   ├── model                     モデルディレクトリ
......
├── database                      データベースファイル
│   ├── migrations                移行ファイル
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     テストデータ
│   │   └── UserSeeder.php
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

## 使用の提案

移行ファイルは一度コードがマージされると再度変更することはできません。問題が発生した場合は、新しい変更を作成または削除操作ファイルを処理する必要があります。

#### データテーブルの作成操作ファイル名の規則

`{time(自動作成)}_create_{テーブル名の小文字の英語}`

#### データテーブルの変更操作ファイル名の規則

`{time(自動作成)}_modify_{テーブル名の英語の小文字+具体的な変更項目の英語の小文字}`

### データテーブルの削除操作ファイル名の規則

`{time(自動作成)}_delete_{テーブル名の英語の小文字+具体的な変更項目の英語の小文字}`

### データの埋め込みファイル名の規則

`{time(自動作成)}_fill_{テーブル名の英語の小文字+具体的な変更項目の英語の小文字}`
