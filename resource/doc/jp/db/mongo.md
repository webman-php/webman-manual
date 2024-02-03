# MongoDB

webmanはデフォルトで[jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb)をMongoDBコンポーネントとして使用します。これは、Laravelプロジェクトから切り出されたものであり、Laravelと同じように使用されます。

`jenssegers/mongodb`を使用する前に、`php-cli`にMongoDB拡張機能をインストールする必要があります。

> `php -m | grep mongodb`コマンドを使用して、`php-cli`にMongoDB拡張機能がインストールされているかどうかを確認します。注意：`php-fpm`でMongoDB拡張機能をインストールしていても、`php-cli`で使用できるとは限りません。なぜなら、`php-cli`と`php-fpm`は異なるアプリケーションであり、異なる`php.ini`構成を使用する可能性があるからです。`php --ini`コマンドを使用して、あなたの`php-cli`がどの`php.ini`構成ファイルを使用しているかを確認します。

## インストール

PHP>7.2の場合
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2の場合
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

インストール後は、再起動する必要があります（reloadは無効です）。

## 設定
`config/database.php`に`mongodb`接続を追加します。以下は例です：
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
                // ここでは、Mongo Driver Managerにさらなる設定を渡すことができます
                // 使用可能な完全なパラメータのリストについては、https://www.php.net/manual/en/mongodb-driver-manager.construct.php の「Uri Options」を参照してください

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## サンプル
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

## その他の情報は以下をご覧ください

https://github.com/jenssegers/laravel-mongodb
