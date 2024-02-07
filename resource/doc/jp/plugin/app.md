アプリケーションプラグインは、それぞれが完全なアプリケーションであり、ソースコードは`{メインプロジェクト}/plugin`ディレクトリに配置されています。

> **ヒント**
> `php webman app-plugin:create {プラグイン名}`コマンド（webman/console>=1.2.16が必要）を使用すると、ローカルにアプリケーションプラグインを作成できます。
> たとえば、 `php webman app-plugin:create cms` とすると、以下のディレクトリ構造が作成されます。

```plaintext
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

アプリケーションプラグインは、webmanと同じディレクトリ構造および構成ファイルを持っていることがわかります。実際には、アプリケーションプラグインを開発することは、基本的にwebmanプロジェクトを開発することと同じような体験であり、以下のいくつかの側面に注意するだけです。

## 名前空間
プラグインディレクトリおよび名前付けはPSR4規格に従います。プラグインはすべてpluginディレクトリに配置されているため、名前空間はすべてpluginから始まります。例えば `plugin\cms\app\controller\UserController`のようなものです。ここでcmsはプラグインのソースコードの主ディレクトリです。

## URLアクセス
アプリケーションプラグインのURLパスは常に`/app`で始まります。例えば`plugin\cms\app\controller\UserController`のURLは `http://127.0.0.1:8787/app/cms/user` です。

## 静的ファイル
静的ファイルは`plugin/{プラグイン}/public`に配置されます。例えば`http://127.0.0.1:8787/app/cms/avatar.png`にアクセスする場合は、実際には`plugin/cms/public/avatar.png`ファイルが取得されます。

## 設定ファイル
プラグインの設定は通常のwebmanプロジェクトと同じですが、プラグインの設定は通常、そのプラグインにのみ影響します。たとえば、`plugin.cms.app.controller_suffix`の値は通常、プラグインのコントローラのサフィックスにのみ影響し、メインプロジェクトには影響しません。
例えば、`plugin.cms.middleware`の値は通常、プラグインのミドルウェアにのみ影響し、メインプロジェクトには影響しません。
しかし、ルートはグローバルであるため、プラグインの設定されたルートも影響を受けます。

## 設定の取得
あるプラグインの設定を取得するには、`config('plugin.{プラグイン}.{具体的な設定}');`のようにします。たとえば、`plugin/cms/config/app.php`のすべての設定を取得するには`config('plugin.cms.app')`となります。
同様に、メインプロジェクトや他のプラグインも`config('plugin.cms.xxx')`を使用して、cmsプラグインの設定を取得できます。

## サポートされていない設定
アプリケーションプラグインでは`server.php`、`session.php`の設定はサポートされていません。`app.request_class`、`app.public_path`、`app.runtime_path`の設定もサポートされていません。

## データベース
プラグインは独自のデータベースを設定できます。たとえば、`plugin/cms/config/database.php`の内容は以下のようになります。
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysqlが接続名です
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'データベース名',
            'username'    => 'ユーザー名',
            'password'    => 'パスワード',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // adminが接続名です
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'データベース名',
            'username'    => 'ユーザー名',
            'password'    => 'パスワード',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
参照方法は`Db::connection('plugin.{プラグイン}.{接続名}');`です。例えば
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```
メインプロジェクトのデータベースを使用したい場合は、そのまま使用します。
```php
use support\Db;
Db::table('user')->first();
// 仮にメインプロジェクトにadmin接続がある場合
Db::connection('admin')->table('admin')->first();
```

> **ヒント**
> thinkormも同様の使い方です

## Redis
Redisの使用方法もデータベースと同様です。例えば `plugin/cms/config/redis.php`
```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ],
];
```
使用方法は次のようになります
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```
同様に、メインプロジェクトのRedis設定を再利用したい場合は、そのまま使用します。
```php
use support\Redis;
Redis::get('key');
// 仮にメインプロジェクトにcache接続がある場合
Redis::connection('cache')->get('key');
```

## ログ
ログクラスの使用方法もデータベースの使用方法と類似しています。
```php
use support\Log;
Log::channel('plugin.admin.default')->info('テスト');
```
メインプロジェクトのログ設定を再利用したい場合は、そのまま使用します。
```php
use support\Log;
Log::info('ログ内容');
// 仮にメインプロジェクトにテストログ設定がある場合
Log::channel('test')->info('ログ内容');
```

# アプリケーションプラグインのインストールとアンインストール
アプリケーションプラグインをインストールするには、プラグインディレクトリを`{メインプロジェクト}/plugin`ディレクトリにコピーするだけです。再読み込みまたは再起動が必要です。
アンインストールする際には、単純に`{メインプロジェクト}/plugin`ディレクトリから対応するプラグインディレクトリを削除するだけです。
