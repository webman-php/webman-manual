# コマンドライン

Webman コマンドラインコンポーネント

## インストール
```
composer require webman/console
```

## 目次

### コード生成
- [make:controller](#make-controller) - コントローラクラスを生成
- [make:model](#make-model) - データベーステーブルからモデルクラスを生成
- [make:crud](#make-crud) - 完全な CRUD（モデル + コントローラ + バリデーター）を生成
- [make:middleware](#make-middleware) - ミドルウェアクラスを生成
- [make:command](#make-command) - コンソールコマンドクラスを生成
- [make:bootstrap](#make-bootstrap) - 起動初期化クラスを生成
- [make:process](#make-process) - カスタムプロセスクラスを生成

### ビルドとデプロイ
- [build:phar](#build-phar) - プロジェクトを PHAR アーカイブにパッケージ化
- [build:bin](#build-bin) - プロジェクトをスタンドアロン実行ファイルにパッケージ化
- [install](#install) - Webman インストールスクリプトを実行

### ユーティリティコマンド
- [version](#version) - Webman フレームワークのバージョンを表示
- [fix-disable-functions](#fix-disable-functions) - php.ini の無効化関数を修正
- [route:list](#route-list) - 登録済みルートをすべて表示

### アプリプラグイン管理 (app-plugin:*)
- [app-plugin:create](#app-plugin-create) - 新規アプリプラグインを作成
- [app-plugin:install](#app-plugin-install) - アプリプラグインをインストール
- [app-plugin:uninstall](#app-plugin-uninstall) - アプリプラグインをアンインストール
- [app-plugin:update](#app-plugin-update) - アプリプラグインを更新
- [app-plugin:zip](#app-plugin-zip) - アプリプラグインを ZIP にパッケージ化

### プラグイン管理 (plugin:*)
- [plugin:create](#plugin-create) - 新規 Webman プラグインを作成
- [plugin:install](#plugin-install) - Webman プラグインをインストール
- [plugin:uninstall](#plugin-uninstall) - Webman プラグインをアンインストール
- [plugin:enable](#plugin-enable) - Webman プラグインを有効化
- [plugin:disable](#plugin-disable) - Webman プラグインを無効化
- [plugin:export](#plugin-export) - プラグインのソースコードをエクスポート

### サービス管理
- [start](#start) - Webman ワーカープロセスを起動
- [stop](#stop) - Webman ワーカープロセスを停止
- [restart](#restart) - Webman ワーカープロセスを再起動
- [reload](#reload) - ダウンタイムなしでコードを再読み込み
- [status](#status) - ワーカープロセスの状態を確認
- [connections](#connections) - ワーカープロセスの接続情報を取得

## コード生成

<a name="make-controller"></a>
### make:controller

コントローラクラスを生成します。

**使用方法：**
```bash
php webman make:controller <名称>
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 必須 | コントローラ名（サフィックスなし） |

**オプション：**

| オプション | 省略形 | 説明 |
|--------|----------|-------------|
| `--plugin` | `-p` | 対応するプラグインディレクトリにコントローラを生成 |
| `--path` | `-P` | カスタムコントローラパス |
| `--force` | `-f` | ファイルが存在する場合は上書き |
| `--no-suffix` | | 「Controller」サフィックスを付加しない |

**例：**
```bash
# app/controller に UserController を作成
php webman make:controller User

# プラグイン内に作成
php webman make:controller AdminUser -p admin

# カスタムパス
php webman make:controller User -P app/api/controller

# 既存ファイルを上書き
php webman make:controller User -f

# 「Controller」サフィックスなしで作成
php webman make:controller UserHandler --no-suffix
```

**生成されるファイル構造：**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function index(Request $request)
    {
        return response('hello user');
    }
}
```

**説明：**
- コントローラはデフォルトで `app/controller/` ディレクトリに配置されます
- 設定されたコントローラサフィックスが自動的に付加されます
- ファイルが存在する場合は上書きするかどうか確認されます（以下同様）

<a name="make-model"></a>
### make:model

データベーステーブルからモデルクラスを生成します。Laravel ORM と ThinkORM をサポートします。

**使用方法：**
```bash
php webman make:model [名称]
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 任意 | モデルクラス名、対話モードでは省略可能 |

**オプション：**

| オプション | 省略形 | 説明 |
|--------|----------|-------------|
| `--plugin` | `-p` | 対応するプラグインディレクトリにモデルを生成 |
| `--path` | `-P` | 出力先ディレクトリ（プロジェクトルートからの相対パス） |
| `--table` | `-t` | テーブル名を指定、命名規則に合わない場合は明示的に指定を推奨 |
| `--orm` | `-o` | ORM を選択：`laravel` または `thinkorm` |
| `--database` | `-d` | データベース接続名を指定 |
| `--force` | `-f` | 既存ファイルを上書き |

**パス説明：**
- デフォルト：`app/model/`（メインアプリ）または `plugin/<プラグイン>/app/model/`（プラグイン）
- `--path` はプロジェクトルートからの相対パス、例：`plugin/admin/app/model`
- `--plugin` と `--path` を同時に使用する場合、両者は同一ディレクトリを指す必要があります

**例：**
```bash
# app/model に User モデルを作成
php webman make:model User

# テーブル名と ORM を指定
php webman make:model User -t wa_users -o laravel

# プラグイン内に作成
php webman make:model AdminUser -p admin

# カスタムパス
php webman make:model User -P plugin/admin/app/model
```

**対話モード：** 名称を指定しない場合は対話フローに入ります：テーブル選択 → モデル名入力 → パス入力。サポート：Enter で詳細表示、`0` で空モデル作成、`/キーワード` でテーブルフィルタ。

**生成されるファイル構造：**
```php
<?php
namespace app\model;

use support\Model;

/**
 * @property integer $id (主キー)
 * @property string $name
 */
class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;
}
```

テーブル構造に基づいて `@property` 注釈を自動生成します。MySQL、PostgreSQL をサポートします。

<a name="make-crud"></a>
### make:crud

データベーステーブルに基づいて、モデル、コントローラ、バリデーターを一括生成し、完全な CRUD 機能を構築します。

**使用方法：**
```bash
php webman make:crud
```

**オプション：**

| オプション | 省略形 | 説明 |
|--------|----------|-------------|
| `--table` | `-t` | テーブル名を指定 |
| `--model` | `-m` | モデルクラス名 |
| `--model-path` | `-M` | モデルディレクトリ（プロジェクトルートからの相対パス） |
| `--controller` | `-c` | コントローラクラス名 |
| `--controller-path` | `-C` | コントローラディレクトリ |
| `--validator` | | バリデータークラス名（`webman/validation` に依存） |
| `--validator-path` | | バリデーターディレクトリ（`webman/validation` に依存） |
| `--plugin` | `-p` | 対応するプラグインディレクトリにファイルを生成 |
| `--orm` | `-o` | ORM：`laravel` または `thinkorm` |
| `--database` | `-d` | データベース接続名 |
| `--force` | `-f` | 既存ファイルを上書き |
| `--no-validator` | | バリデーターを生成しない |
| `--no-interaction` | `-n` | 非対話モード、デフォルト値を使用 |

**実行フロー：** `--table` を指定しない場合は対話でテーブル選択；モデル名はデフォルトでテーブル名から推論；コントローラ名はデフォルトでモデル名 + コントローラサフィックス；バリデーター名はデフォルトでコントローラ名からサフィックスを除いたもの + `Validator`。各パスのデフォルト：モデル `app/model/`、コントローラ `app/controller/`、バリデーター `app/validation/`；プラグインの場合は `plugin/<プラグイン>/app/` の対応サブディレクトリ。

**例：**
```bash
# 対話式で生成（テーブル選択後に段階的に確認）
php webman make:crud

# テーブル名を指定
php webman make:crud --table=users

# テーブル名とプラグインを指定
php webman make:crud --table=users --plugin=admin

# 各パスを指定
php webman make:crud --table=users --model-path=app/model --controller-path=app/controller

# バリデーターを生成しない
php webman make:crud --table=users --no-validator

# 非対話 + 上書き
php webman make:crud --table=users --no-interaction --force
```

**生成されるファイル構造：**

モデル（`app/model/User.php`）：
```php
<?php

namespace app\model;

use support\Model;

class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
}
```

コントローラ（`app/controller/UserController.php`）：
```php
<?php

namespace app\controller;

use support\Request;
use support\Response;
use app\model\User;
use app\validation\UserValidator;
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(validator: UserValidator::class, scene: 'create', in: ['body'])]
    public function create(Request $request): Response
    {
        $data = $request->post();
        $model = new User();
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'update', in: ['body'])]
    public function update(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $data = $request->post();
        unset($data['id']);
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'delete', in: ['body'])]
    public function delete(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $model->delete();
        return json(['code' => 0, 'msg' => 'ok']);
    }

    #[Validate(validator: UserValidator::class, scene: 'detail')]
    public function detail(Request $request): Response
    {
        if (!$model = User::find($request->input('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }
}
```

バリデーター（`app/validation/UserValidator.php`）：
```php
<?php
declare(strict_types=1);

namespace app\validation;

use support\validation\Validator;

class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:0',
        'username' => 'required|string|max:32'
    ];

    protected array $messages = [];

    protected array $attributes = [
        'id' => '主キー',
        'username' => 'ユーザー名'
    ];

    protected array $scenes = [
        'create' => ['username', 'nickname'],
        'update' => ['id', 'username'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
```

**説明：**
- `webman/validation` がインストールされていないか有効になっていない場合は、バリデーターの生成を自動的にスキップします（インストール方法：`composer require webman/validation`）。
- バリデーターの `attributes` はデータベースフィールドのコメントに基づいて自動生成され、コメントがない場合は `attributes` を生成しません。
- バリデーターのエラーメッセージは多言語に対応し、言語は `config('translation.locale')` に基づいて自動選択されます。

<a name="make-middleware"></a>
### make:middleware

ミドルウェアクラスを生成し、`config/middleware.php`（プラグインの場合は `plugin/<プラグイン>/config/middleware.php`）に自動登録します。

**使用方法：**
```bash
php webman make:middleware <名称>
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 必須 | ミドルウェア名 |

**オプション：**

| オプション | 省略形 | 説明 |
|--------|----------|-------------|
| `--plugin` | `-p` | 対応するプラグインディレクトリにミドルウェアを生成 |
| `--path` | `-P` | 出力先ディレクトリ（プロジェクトルートからの相対パス） |
| `--force` | `-f` | 既存ファイルを上書き |

**例：**
```bash
# app/middleware に Auth ミドルウェアを作成
php webman make:middleware Auth

# プラグイン内に作成
php webman make:middleware Auth -p admin

# カスタムパス
php webman make:middleware Auth -P plugin/admin/app/middleware
```

**生成されるファイル構造：**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Auth implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        return $handler($request);
    }
}
```

**説明：**
- デフォルトで `app/middleware/` ディレクトリに配置されます
- 作成後、対応する middleware 設定ファイルにクラス名が自動的に追加され、有効化されます

<a name="make-command"></a>
### make:command

コンソールコマンドクラスを生成します。

**使用方法：**
```bash
php webman make:command <命令名>
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `命令名` | 必須 | コマンド名、形式は `group:action`（例：`user:list`） |

**オプション：**

| オプション | 省略形 | 説明 |
|--------|----------|-------------|
| `--plugin` | `-p` | 対応するプラグインディレクトリにコマンドを生成 |
| `--path` | `-P` | 出力先ディレクトリ（プロジェクトルートからの相対パス） |
| `--force` | `-f` | 既存ファイルを上書き |

**例：**
```bash
# app/command に user:list コマンドを作成
php webman make:command user:list

# プラグイン内に作成
php webman make:command user:list -p admin

# カスタムパス
php webman make:command user:list -P plugin/admin/app/command

# 既存ファイルを上書き
php webman make:command user:list -f
```

**生成されるファイル構造：**
```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('user:list', 'user list')]
class UserList extends Command
{
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Hello</info> <comment>' . $this->getName() . '</comment>');
        return self::SUCCESS;
    }
}
```

**説明：**
- デフォルトで `app/command/` ディレクトリに配置されます

<a name="make-bootstrap"></a>
### make:bootstrap

起動初期化クラス（Bootstrap）を生成します。プロセス起動時にクラスの start メソッドが自動的に呼び出され、プロセス起動時のグローバル初期化処理に使用します。

**使用方法：**
```bash
php webman make:bootstrap <名称> 
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 必須 | Bootstrap クラス名 |

**オプション：**

| オプション | 省略形 | 説明 |
|--------|----------|-------------|
| `--plugin` | `-p` | 対応するプラグインディレクトリに生成 |
| `--path` | `-P` | 出力先ディレクトリ（プロジェクトルートからの相対パス） |
| `--force` | `-f` | 既存ファイルを上書き |

**例：**
```bash
# app/bootstrap に MyBootstrap を作成
php webman make:bootstrap MyBootstrap

# 作成するが自動有効化しない
php webman make:bootstrap MyBootstrap no

# プラグイン内に作成
php webman make:bootstrap MyBootstrap -p admin

# カスタムパス
php webman make:bootstrap MyBootstrap -P plugin/admin/app/bootstrap

# 既存ファイルを上書き
php webman make:bootstrap MyBootstrap -f
```

**生成されるファイル構造：**
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MyBootstrap implements Bootstrap
{
    public static function start($worker)
    {
        $is_console = !$worker;
        if ($is_console) {
            return;
        }
        // ...
    }
}
```

**説明：**
- デフォルトで `app/bootstrap/` ディレクトリに配置されます
- 有効化時はクラスが `config/bootstrap.php`（プラグインの場合は `plugin/<プラグイン>/config/bootstrap.php`）に追加されます

<a name="make-process"></a>
### make:process

カスタムプロセスクラスを生成し、`config/process.php` に設定を書き込んで自動起動します。

**使用方法：**
```bash
php webman make:process <名称>
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 必須 | プロセスクラス名（例：MyTcp、MyWebsocket） |

**オプション：**

| オプション | 省略形 | 説明 |
|--------|----------|-------------|
| `--plugin` | `-p` | 対応するプラグインディレクトリに生成 |
| `--path` | `-P` | 出力先ディレクトリ（プロジェクトルートからの相対パス） |
| `--force` | `-f` | 既存ファイルを上書き |

**例：**
```bash
# app/process に作成
php webman make:process MyTcp

# プラグイン内に作成
php webman make:process MyProcess -p admin

# カスタムパス
php webman make:process MyProcess -P plugin/admin/app/process

# 既存ファイルを上書き
php webman make:process MyProcess -f
```

**対話フロー：** 実行後に順に質問：ポートをリッスンするか → プロトコルタイプ（websocket/http/tcp/udp/unixsocket）→ リッスンアドレス（IP+ポートまたは unix socket パス）→ プロセス数。HTTP プロトコルの場合は組み込みモードまたはカスタムモードの選択も質問されます。

**生成されるファイル構造：**

非リッスンプロセス（`onWorkerStart` のみ）：
```php
<?php
namespace app\process;

use Workerman\Worker;

class MyProcess
{
    public function onWorkerStart(Worker $worker)
    {
        // TODO: Write your business logic here.
    }
}
```

TCP/WebSocket などのリッスンプロセスでは、対応する `onConnect`、`onMessage`、`onClose` などのコールバックテンプレートが生成されます。

**説明：**
- デフォルトで `app/process/` ディレクトリに配置され、プロセス設定は `config/process.php` に書き込まれます
- 設定キーはクラス名の snake_case で、既に存在する場合は失敗します
- HTTP 組み込みモードは `app\process\Http` プロセスファイルを再利用し、新規ファイルは生成しません
- サポートプロトコル：websocket、http、tcp、udp、unixsocket

## ビルドとデプロイ

<a name="build-phar"></a>
### build:phar

プロジェクトを PHAR アーカイブにパッケージ化し、配布とデプロイを容易にします。

**使用方法：**
```bash
php webman build:phar
```

**起動：**

build ディレクトリに移動して実行

```bash
php webman.phar start
```

**注意事項**
* パッケージ化後のプロジェクトは reload をサポートしません。コード更新には restart で再起動が必要です

* パッケージファイルサイズが大きくなりすぎてメモリを消費しないよう、config/plugin/webman/console/app.php の exclude_pattern exclude_files オプションで不要なファイルを除外できます。

* webman.phar を実行すると、webman.phar のあるディレクトリに runtime ディレクトリが生成され、ログなどの一時ファイルが保存されます。

* プロジェクトで .env ファイルを使用している場合、.env ファイルを webman.phar と同じディレクトリに配置する必要があります。

* webman.phar は Windows ではカスタムプロセスをサポートしないことに注意してください

* ユーザーアップロードファイルを phar パッケージ内に保存しないでください。phar:// プロトコルでユーザーアップロードファイルを操作するのは非常に危険です（phar デシリアライゼーション脆弱性）。ユーザーアップロードファイルは phar パッケージ外のディスクに個別に保存する必要があります。以下を参照してください。

* 業務で public ディレクトリにファイルをアップロードする必要がある場合、public ディレクトリを独立させて webman.phar と同じディレクトリに配置する必要があり、その場合は config/app.php の設定が必要です。
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
業務ではヘルパー関数 public_path($ファイル相対位置) で実際の public ディレクトリの位置を取得できます。


<a name="build-bin"></a>
### build:bin

プロジェクトをスタンドアロン実行ファイルにパッケージ化します。PHP ランタイムを含むため、対象環境に PHP のインストールは不要です。

**使用方法：**
```bash
php webman build:bin [版本]
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `版本` | 任意 | PHP バージョン番号（例：8.1、8.2）、デフォルトは現在の PHP バージョン、最低 8.1 |

**例：**
```bash
# 現在の PHP バージョンを使用
php webman build:bin

# PHP 8.2 を指定
php webman build:bin 8.2
```

**起動：**

build ディレクトリに移動して実行

```bash
./webman.bin start
```

**注意事項：**
* ローカルの PHP バージョンとパッケージバージョンを一致させることを強く推奨します。例：ローカルが PHP 8.1 の場合は 8.1 でパッケージ化し、互換性問題を避けてください
* パッケージ化では PHP 8 のソースコードをダウンロードしますが、ローカルにはインストールされず、ローカルの PHP 環境には影響しません
* webman.bin は現在 x86_64 アーキテクチャの Linux システムでのみ動作をサポートし、mac システムでは動作しません
* パッケージ化後のプロジェクトは reload をサポートしません。コード更新には restart で再起動が必要です
* デフォルトでは env ファイルをパッケージ化しません（config/plugin/webman/console/app.php の exclude_files で制御）。起動時は env ファイルを webman.bin と同じディレクトリに配置してください
* 実行中は webman.bin のあるディレクトリに runtime ディレクトリが生成され、ログファイルが保存されます
* 現在 webman.bin は外部 php.ini ファイルを読み込みません。カスタム php.ini が必要な場合は、/config/plugin/webman/console/app.php の custom_ini で設定してください
* 不要なファイルは config/plugin/webman/console/app.php で除外して、パッケージ化後のファイルが大きくなりすぎないようにできます
* バイナリパッケージは swoole コルーチンの使用をサポートしません
* ユーザーアップロードファイルをバイナリパッケージ内に保存しないでください。phar:// プロトコルでユーザーアップロードファイルを操作するのは非常に危険です（phar デシリアライゼーション脆弱性）。ユーザーアップロードファイルはパッケージ外のディスクに個別に保存する必要があります。
* 業務で public ディレクトリにファイルをアップロードする必要がある場合、public ディレクトリを独立させて webman.bin と同じディレクトリに配置する必要があり、その場合は config/app.php を以下のように設定して再パッケージ化してください。
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

Webman フレームワークのインストールスクリプト（`\Webman\Install::install()` を呼び出し）を実行し、プロジェクトの初期化に使用します。

**使用方法：**
```bash
php webman install
```

## ユーティリティコマンド

<a name="version"></a>
### version

workerman/webman-framework のバージョンを表示します。

**使用方法：**
```bash
php webman version
```

**説明：** `vendor/composer/installed.php` からバージョンを読み取り、読み取れない場合は失敗を返します。

<a name="fix-disable-functions"></a>
### fix-disable-functions

php.ini の `disable_functions` を修正し、Webman の実行に必要な関数を削除します。

**使用方法：**
```bash
php webman fix-disable-functions
```

**説明：** `disable_functions` から以下の関数（およびそのプレフィックス一致）を削除します：`stream_socket_server`、`stream_socket_accept`、`stream_socket_client`、`pcntl_*`、`posix_*`、`proc_*`、`shell_exec`、`exec`。php.ini が見つからないか `disable_functions` が空の場合はスキップします。**php.ini ファイルを直接変更します**。事前にバックアップすることを推奨します。

<a name="route-list"></a>
### route:list

登録済みのすべてのルートを表形式で一覧表示します。

**使用方法：**
```bash
php webman route:list
```

**出力例：**
```
+-------+--------+-----------------------------------------------+------------+------+
| URI   | メソッド | コールバック                                    | ミドルウェア | 名前 |
+-------+--------+-----------------------------------------------+------------+------+
| /user | GET    | ["app\\controller\\UserController","index"] | null       |      |
| /api  | POST   | クロージャ                                      | ["Auth"]   | api  |
+-------+--------+-----------------------------------------------+------------+------+
```

**出力列：** URI、メソッド、コールバック、ミドルウェア、名前。クロージャコールバックは「クロージャ」と表示されます。

## アプリプラグイン管理 (app-plugin:*)

<a name="app-plugin-create"></a>
### app-plugin:create

新規アプリプラグインを作成し、`plugin/<名称>` 配下に完全なディレクトリ構造と基本ファイルを生成します。

**使用方法：**
```bash
php webman app-plugin:create <名称>
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 必須 | プラグイン名、`[a-zA-Z0-9][a-zA-Z0-9_-]*` に準拠、`/` または `\` を含めない |

**例：**
```bash
# foo という名前のアプリプラグインを作成
php webman app-plugin:create foo

# ハイフン付きのプラグインを作成
php webman app-plugin:create my-app
```

**生成されるディレクトリ構造：**
```
plugin/<名称>/
├── app/
│   ├── controller/IndexController.php
│   ├── model/
│   ├── middleware/
│   ├── view/index/index.html
│   └── functions.php
├── config/          # app.php、route.php、menu.php など
├── api/Install.php  # インストール/アンインストール/更新フック
├── public/
└── install.sql
```

**説明：**
- プラグインは `plugin/<名称>/` 配下に作成され、ディレクトリが既に存在する場合は失敗します

<a name="app-plugin-install"></a>
### app-plugin:install

アプリプラグインをインストールし、`plugin/<名称>/api/Install::install($version)` を実行します。

**使用方法：**
```bash
php webman app-plugin:install <名称>
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 必須 | プラグイン名、`[a-zA-Z0-9][a-zA-Z0-9_-]*` に準拠 |

**例：**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

アプリプラグインをアンインストールし、`plugin/<名称>/api/Install::uninstall($version)` を実行します。

**使用方法：**
```bash
php webman app-plugin:uninstall <名称>
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 必須 | プラグイン名 |

**オプション：**

| オプション | 省略形 | 説明 |
|--------|----------|-------------|
| `--yes` | `-y` | 確認をスキップして直接実行 |

**例：**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

アプリプラグインを更新し、`Install::beforeUpdate($from, $to)` と `Install::update($from, $to, $context)` を順に実行します。

**使用方法：**
```bash
php webman app-plugin:update <名称>
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 必須 | プラグイン名 |

**オプション：**

| オプション | 省略形 | 説明 |
|--------|----------|-------------|
| `--from` | `-f` | 開始バージョン、デフォルトは現在のバージョン |
| `--to` | `-t` | 目標バージョン、デフォルトは現在のバージョン |

**例：**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

アプリプラグインを ZIP ファイルにパッケージ化し、`plugin/<名称>.zip` に出力します。

**使用方法：**
```bash
php webman app-plugin:zip <名称>
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 必須 | プラグイン名 |

**例：**
```bash
php webman app-plugin:zip foo
```

**説明：**
- `node_modules`、`.git`、`.idea`、`.vscode`、`__pycache__` などのディレクトリを自動的に除外します

## プラグイン管理 (plugin:*)

<a name="plugin-create"></a>
### plugin:create

新規 Webman プラグイン（Composer パッケージ形式）を作成し、`config/plugin/<名称>` 設定ディレクトリと `vendor/<名称>` プラグインソースディレクトリを生成します。

**使用方法：**
```bash
php webman plugin:create <名称>
php webman plugin:create --name <名称>
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 必須 | プラグインパッケージ名、形式は `vendor/package`（例：`foo/my-admin`）、Composer パッケージ名規約に準拠 |

**例：**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**生成される構造：**
- `config/plugin/<名称>/app.php`：プラグイン設定（`enable` スイッチを含む）
- `vendor/<名称>/composer.json`：プラグインパッケージ定義
- `vendor/<名称>/src/`：プラグインソースディレクトリ
- プロジェクトルートの `composer.json` に PSR-4 マッピングを自動追加
- `composer dumpautoload` を実行してオートロードを更新

**説明：**
- 名称は `vendor/package` 形式である必要があり、小文字、数字、`-`、`_`、`.` のみ、かつ `/` を1つ含む必要があります
- `config/plugin/<名称>` または `vendor/<名称>` が既に存在する場合は失敗します
- 引数と `--name` を同時に指定し、値が異なる場合はエラーになります

<a name="plugin-install"></a>
### plugin:install

プラグインのインストールスクリプト（`Install::install()`）を実行し、プラグインリソースをプロジェクトディレクトリにコピーします。

**使用方法：**
```bash
php webman plugin:install <名称>
php webman plugin:install --name <名称>
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 必須 | プラグインパッケージ名、形式は `vendor/package`（例：`foo/my-admin`） |

**オプション：**

| オプション | 説明 |
|--------|-------------|
| `--name` | オプション形式でプラグイン名を指定、引数とどちらか一方 |

**例：**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

プラグインのアンインストールスクリプト（`Install::uninstall()`）を実行し、プラグインがプロジェクトにコピーしたリソースを削除します。

**使用方法：**
```bash
php webman plugin:uninstall <名称>
php webman plugin:uninstall --name <名称>
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 必須 | プラグインパッケージ名、形式は `vendor/package` |

**オプション：**

| オプション | 説明 |
|--------|-------------|
| `--name` | オプション形式でプラグイン名を指定、引数とどちらか一方 |

**例：**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

プラグインを有効化し、`config/plugin/<名称>/app.php` の `enable` を `true` に設定します。

**使用方法：**
```bash
php webman plugin:enable <名称>
php webman plugin:enable --name <名称>
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 必須 | プラグインパッケージ名、形式は `vendor/package` |

**オプション：**

| オプション | 説明 |
|--------|-------------|
| `--name` | オプション形式でプラグイン名を指定、引数とどちらか一方 |

**例：**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

プラグインを無効化し、`config/plugin/<名称>/app.php` の `enable` を `false` に設定します。

**使用方法：**
```bash
php webman plugin:disable <名称>
php webman plugin:disable --name <名称>
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 必須 | プラグインパッケージ名、形式は `vendor/package` |

**オプション：**

| オプション | 説明 |
|--------|-------------|
| `--name` | オプション形式でプラグイン名を指定、引数とどちらか一方 |

**例：**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

プロジェクト内のプラグイン設定と指定ディレクトリを `vendor/<名称>/src/` にエクスポートし、`Install.php` を生成してパッケージ公開を容易にします。

**使用方法：**
```bash
php webman plugin:export <名称> [--source=路径]...
php webman plugin:export --name <名称> [--source=路径]...
```

**引数：**

| 引数 | 必須 | 説明 |
|----------|----------|-------------|
| `名称` | 必須 | プラグインパッケージ名、形式は `vendor/package` |

**オプション：**

| オプション | 省略形 | 説明 |
|--------|----------|-------------|
| `--name` | | オプション形式でプラグイン名を指定、引数とどちらか一方 |
| `--source` | `-s` | エクスポートするパス（プロジェクトルートからの相対パス）、複数指定可能 |

**例：**
```bash
# プラグインをエクスポート、デフォルトで config/plugin/<名称> を含む
php webman plugin:export foo/my-admin

# app、config などのディレクトリを追加でエクスポート
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**説明：**
- プラグイン名は Composer パッケージ名規約（`vendor/package`）に準拠する必要があります
- `config/plugin/<名称>` が存在し、`--source` に含まれていない場合は、自動的にエクスポートリストに追加されます
- エクスポートされる `Install.php` には `pathRelation` が含まれ、`plugin:install` / `plugin:uninstall` で使用されます
- `plugin:install`、`plugin:uninstall` はプラグインが `vendor/<名称>` に存在し、`Install` クラスと `WEBMAN_PLUGIN` 定数が存在する必要があります

## サービス管理

<a name="start"></a>
### start

Webman ワーカープロセスを起動します。デフォルトは DEBUG モード（フォアグラウンド実行）です。

**使用方法：**
```bash
php webman start
```

**オプション：**

| オプション | 省略形 | 説明 |
|--------|----------|-------------|
| `--daemon` | `-d` | DAEMON モードで起動（バックグラウンド実行） |

<a name="stop"></a>
### stop

Webman ワーカープロセスを停止します。

**使用方法：**
```bash
php webman stop
```

**オプション：**

| オプション | 省略形 | 説明 |
|--------|----------|-------------|
| `--graceful` | `-g` | グレースフル停止、現在のリクエスト処理完了後に終了 |

<a name="restart"></a>
### restart

Webman ワーカープロセスを再起動します。

**使用方法：**
```bash
php webman restart
```

**オプション：**

| オプション | 省略形 | 説明 |
|--------|----------|-------------|
| `--daemon` | `-d` | 再起動後 DAEMON モードで実行 |
| `--graceful` | `-g` | グレースフル停止後に再起動 |

<a name="reload"></a>
### reload

ダウンタイムなしでコードを再読み込みします。コード更新後のホットリロードに適しています。

**使用方法：**
```bash
php webman reload
```

**オプション：**

| オプション | 省略形 | 説明 |
|--------|----------|-------------|
| `--graceful` | `-g` | グレースフルリロード、現在のリクエスト処理完了後に再読み込み |

<a name="status"></a>
### status

ワーカープロセスの実行状態を確認します。

**使用方法：**
```bash
php webman status
```

**オプション：**

| オプション | 省略形 | 説明 |
|--------|----------|-------------|
| `--live` | `-d` | 詳細を表示（リアルタイム状態） |

<a name="connections"></a>
### connections

ワーカープロセスの接続情報を取得します。

**使用方法：**
```bash
php webman connections
```
