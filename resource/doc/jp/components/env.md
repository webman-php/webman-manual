# vlucas/phpdotenv

## 説明
`vlucas/phpdotenv`は、異なる環境（開発環境、テスト環境など）の設定を区別するための環境変数の読み込みコンポーネントです。

## プロジェクトのリンク

https://github.com/vlucas/phpdotenv

## インストール

```php
composer require vlucas/phpdotenv
```
  
## 使い方

#### プロジェクトのルートディレクトリに`.env`ファイルを作成します
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### 設定ファイルを変更します
**config/database.php**
```php
return [
    // デフォルトのデータベース
    'default' => 'mysql',

    // 各種のデータベース設定
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DB_HOST'),
            'port'        => getenv('DB_PORT'),
            'database'    => getenv('DB_NAME'),
            'username'    => getenv('DB_USER'),
            'password'    => getenv('DB_PASSWORD'),
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
```

> **ヒント**
> `.env`ファイルを`.gitignore`リストに追加して、コードリポジトリにコミットしないようにします。プロジェクトで`.env.example`の設定例のファイルを追加し、プロジェクトをデプロイする際に `.env.example` を `.env` にコピーし、現在の環境に応じて `.env` の設定を変更することで、異なる環境で異なる設定を読み込むことができます。

> **注意**
> `vlucas/phpdotenv`はPHP TS（スレッドセーフバージョン）ではバグが発生する可能性があります。NTS（非スレッドセーフバージョン）を使用してください。
> 現在のPHPのバージョンは、`php -v` を実行して確認できます。

## その他の情報

https://github.com/vlucas/phpdotenv を参照してください。
