# webman/console コマンドラインプラグイン

`webman/console` は `symfony/console` に基づいています。

> プラグインにはwebman>=1.2.2およびwebman-framework>=1.2.1が必要です。

## インストール

```sh
composer require webman/console
```

## サポートされているコマンド
**使用法**  
`php webman コマンド` または `php webman コマンド`。
例： `php webman version` または `php webman version`

## サポートされているコマンド
### バージョン
**webmanのバージョンを表示します**

### route:list
**現在のルート構成を表示します**

### make:controller
**コントローラーファイルを作成します** 
例： `php webman make:controller admin` は `app/controller/AdminController.php` を作成します。
例： `php webman make:controller api/user` は `app/api/controller/UserController.php` を作成します。

### make:model
**モデルファイルを作成します**
例： `php webman make:model admin` は `app/model/Admin.php` を作成します。
例： `php webman make:model api/user` は `app/api/model/User.php` を作成します。

### make:middleware
**ミドルウェアファイルを作成します**
例： `php webman make:middleware Auth` は `app/middleware/Auth.php` を作成します。

### make:command
**カスタムコマンドファイルを作成します**
例： `php webman make:command db:config` は `app\command\DbConfigCommand.php` を作成します。

### plugin:create
**基本プラグインを作成します**
例： `php webman plugin:create --name=foo/admin` は `config/plugin/foo/admin` と `vendor/foo/admin` という2つのディレクトリを作成します
[基本プラグインの作成](/doc/webman/plugin/create.html)を参照してください。

### plugin:export
**基本プラグインをエクスポートします**
例： `php webman plugin:export --name=foo/admin` 
[基本プラグインの作成](/doc/webman/plugin/create.html)を参照してください。

### plugin:export
**アプリケーションプラグインをエクスポートします**
例： `php webman plugin:export shop` 
[アプリケーションプラグイン](/doc/webman/plugin/app.html)を参照してください。

### phar:pack
**webmanプロジェクトをpharファイルにパッケージ化します**
[pharパッケージ化](/doc/webman/others/phar.html)を参照してください。
> この機能にはwebman>=1.2.4、webman-framework>=1.2.4、webman\console>=1.0.5が必要です。

## カスタムコマンド
ユーザーは独自のコマンドを定義することができます。以下はデータベース構成を表示するコマンドの例です。

* `php webman make:command config:mysql` を実行します。
* `app/command/ConfigMySQLCommand.php`を開いて、以下のように変更します。

```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigMySQLCommand extends Command
{
    protected static $defaultName = 'config:mysql';
    protected static $defaultDescription = '現在のMySQLサーバー構成を表示します';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('MySQL構成は次のとおりです：');
        $config = config('database');
        $headers = ['name', 'default', 'driver', 'host', 'port', 'database', 'username', 'password', 'unix_socket', 'charset', 'collation', 'prefix', 'strict', 'engine', 'schema', 'sslmode'];
        $rows = [];
        foreach ($config['connections'] as $name => $db_config) {
            $row = [];
            foreach ($headers as $key) {
                switch ($key) {
                    case 'name':
                        $row[] = $name;
                        break;
                    case 'default':
                        $row[] = $config['default'] == $name ? 'true' : 'false';
                        break;
                    default:
                        $row[] = $db_config[$key] ?? '';
                }
            }
            if ($config['default'] == $name) {
                array_unshift($rows, $row);
            } else {
                $rows[] = $row;
            }
        }
        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();
        return self::SUCCESS;
    }
}
```

## テスト

コマンドラインで `php webman config:mysql` を実行します。

以下のような結果が得られます：
```plaintext
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| name  | default | driver | host      | port | database | username | password | unix_socket | charset | collation       | prefix | strict | engine | schema | sslmode |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
| mysql | true    | mysql  | 127.0.0.1 | 3306 | mysql    | root     | ******   |             | utf8    | utf8_unicode_ci |        | 1      |        |        |         |
+-------+---------+--------+-----------+------+----------+----------+----------+-------------+---------+-----------------+--------+--------+--------+--------+---------+
```

## 追加リソース
http://www.symfonychina.com/doc/current/components/console.html
