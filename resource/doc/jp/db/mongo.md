# MongoDB

webmanは、[jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) をMongoDBコンポーネントとしてデフォルトで使用しています。これは、laravelプロジェクトから抽出されたもので、laravelと同様の使い方ができます。

`jenssegers/mongodb` を使用する前に、`php-cli`にMongoDB拡張をインストールする必要があります。

> `php -m | grep mongodb` コマンドを使用して、 `php-cli` にMongoDB拡張がインストールされているかどうかを確認します。注意：`php-fpm`にMongoDB拡張がインストールされていても、`php-cli`で使用できるとは限りません。なぜならば、`php-cli`と`php-fpm`は異なるアプリケーションであり、異なる`php.ini`構成を使用する可能性があるからです。使用している`php-cli`の`php.ini`構成ファイルを確認するには、`php --ini`コマンドを使用します。

## インストール

PHP>7.2の場合
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2の場合
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

インストール後は、再起動する必要があります（reloadでは無効です）。

## 設定
`config/database.php` に `mongodb` 接続を追加します。以下は例です：
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...他の設定は省略...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // ここではMongo Driver Managerにさらなる設定を渡すことができます
                // 使用可能な完全なパラメータのリストについては、https://www.php.net/manual/en/mongodb-driver-manager.construct.php の「Uri Options」を参照してください

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## 例
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        Db::connection('mongodb')->collection('test')->insert([1,2,3]);
        return json(Db::connection('mongodb')->collection('test')->get());
    }
}
```

## その他
詳細は以下をご覧ください：https://github.com/jenssegers/laravel-mongodb
