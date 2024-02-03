# アプリケーションプラグイン
各アプリケーションプラグインは、完全なアプリケーションであり、ソースコードは`{プロジェクトルート}/plugin`ディレクトリに配置されています。

> **ヒント**
> `php webman app-plugin:create {プラグイン名}`コマンド（webman/console>=1.2.16が必要）を使用すると、ローカルでアプリケーションプラグインを作成できます。
> たとえば、`php webman app-plugin:create cms`を実行すると、以下のディレクトリ構造が作成されます。

```
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

アプリケーションプラグインは、webmanと同じディレクトリ構造と構成ファイルを持っていることがわかります。実際、アプリケーションプラグインを開発する体験は、基本的にwebmanプロジェクトを開発する体験とほとんど同じですが、次のいくつかの点に注意する必要があります。

## 名前空間
プラグインのディレクトリと名前付けはPSR4規則に従います。プラグインはすべて`plugin`ディレクトリに配置されているため、名前空間は通常`plugin`で始まります。例えば`plugin\cms\app\controller\UserController`のようになります。ここでcmsはプラグインのソースコードのメインディレクトリです。

## URLアクセス
アプリケーションプラグインのURLアドレスはすべて `/app` で始まります。例えば、`plugin\cms\app\controller\UserController`のURLアドレスは `http://127.0.0.1:8787/app/cms/user` になります。

## 静的ファイル
静的ファイルは `plugin/{プラグイン名}/public` に配置されます。例えば、`http://127.0.0.1:8787/app/cms/avatar.png` にアクセスすると、実際には `plugin/cms/public/avatar.png` ファイルが取得されます。

## 構成ファイル
プラグインの構成は通常のwebmanプロジェクトと同じですが、プラグインの構成は通常、現在のプラグインにのみ影響します。メインプロジェクトには通常影響しません。
例えば、`plugin.cms.app.controller_suffix`の値は通常、プラグインのコントローラのサフィックスにのみ影響し、メインプロジェクトには影響しません。
例えば、`plugin.cms.app.controller_reuse`の値は通常、プラグインのコントローラの再利用にのみ影響し、メインプロジェクトには影響しません。
例えば、`plugin.cms.middleware`の値は通常、プラグインのミドルウェアにのみ影響し、メインプロジェクトには影響しません。
例えば、`plugin.cms.view`の値は通常、プラグインが使用するビューにのみ影響し、メインプロジェクトには影響しません。
例えば、`plugin.cms.container`の値は通常、プラグインが使用するコンテナにのみ影響し、メインプロジェクトには影響しません。
例えば、`plugin.cms.exception`の値は通常、プラグインの例外処理クラスにのみ影響し、メインプロジェクトには影響しません。

ただし、ルートはグローバルなので、プラグインの設定したルートもグローバルに影響します。

## 設定の取得
特定のプラグインの設定を取得するには、`config('plugin.{プラグイン}.{具体的な設定}');`を使用します。たとえば、`plugin/cms/config/app.php`のすべての設定を取得するには`config('plugin.cms.app')`を使用します。
同様に、メインプロジェクトや他のプラグインも`config('plugin.cms.xxx')`を使用して、cmsプラグインの設定を取得できます。

## サポートされていない設定
アプリケーションプラグインでは、server.php、session.phpの構成はサポートされていません。`app.request_class`、`app.public_path`、`app.runtime_path`の設定もサポートされていません。

## データベース
プラグインは、独自のデータベースを構成することができます。たとえば、`plugin/cms/config/database.php`の内容は以下のようになります。
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysqlは接続名
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'データベース',
            'username'    => 'ユーザー名',
            'password'    => 'パスワード',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // adminは接続名
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'データベース',
            'username'    => 'ユーザー名',
            'password'    => 'パスワード',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
接続方法は`Db::connection('plugin.{プラグイン}.{接続名}');`です。例えば
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

メインプロジェクトのデータベースを使用したい場合は、そのまま使用できます。例えば
```php
use support\Db;
Db::table('user')->first();
// メインプロジェクトでadmin接続を構成したとする
Db::connection('admin')->table('admin')->first();
```

> **ヒント**
> thinkormも同様の使い方です。

## Redis
Redisの使用方法はデータベースと類似しています。たとえば、 `plugin/cms/config/redis.php`は以下のようになります。
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
使用方法は以下の通りです。
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```

同様に、メインプロジェクトのRedis構成を再利用したい場合は、次のようにします。
```php
use support\Redis;
Redis::get('key');
// メインプロジェクトでcache接続を構成したとする
Redis::connection('cache')->get('key');
```

## ロギング
ロギングクラスの使用方法もデータベースの使用方法と似ています。
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

メインプロジェクトのロギング設定を再利用したい場合は、直接使用します。
```php
use support\Log;
Log::info('ログの内容');
// メインプロジェクトにtestのロギング設定がある場合
Log::channel('test')->info('ログの内容');
```

# アプリケーションプラグインのインストールとアンインストール
アプリケーションプラグインをインストールするには、プラグインディレクトリを`{プロジェクトルート}/plugin`ディレクトリにコピーするだけです。効力を発揮するにはreloadまたはrestartが必要です。
アンインストール時は、対応するプラグインディレクトリを`{プロジェクトルート}/plugin`から直接削除します。
