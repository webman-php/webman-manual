# vlucas/phpdotenv

## 説明
`vlucas/phpdotenv`は、異なる環境（開発環境、テスト環境など）の構成を区別するための環境変数ローダーコンポーネントです。

## プロジェクトのアドレス

https://github.com/vlucas/phpdotenv
  
## インストール
 
```php
composer require vlucas/phpdotenv
 ```

## 使用法

#### プロジェクトのルートディレクトリに`.env`ファイルを作成します
**.env**
```php
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### 構成ファイルを変更します
**config/database.php**
```php
return [
    // デフォルトデータベース
    'default' => 'mysql',

    // 各種データベースの構成
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
> `.env`ファイルを`.gitignore`リストに追加して、リポジトリに送信しないでください。プロジェクトに`.env.example`構成例ファイルを追加し、プロジェクトの展開時に`.env.example`を`.env`にコピーして、現在の環境に合わせて`.env`の設定を変更すると、プロジェクトが異なる環境で異なる設定をロードできるようになります。

> **注意**
> `vlucas/phpdotenv`は、PHP TSバージョン（スレッドセーフバージョン）ではバグが発生する可能性がありますので、NTSバージョン（非スレッドセーフバージョン）をご利用ください。
> 現在のPHPのバージョンは`php -v`を実行して確認できます。

## その他

https://github.com/vlucas/phpdotenv をご覧ください。
