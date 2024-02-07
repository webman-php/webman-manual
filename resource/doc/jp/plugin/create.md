# 基本プラグインの生成および公開フロー

## 原理
1. クロスドメインプラグインを例に取ると、プラグインは3つの部分に分かれます。1つはクロスドメインミドルウェアプログラムファイル、もう1つはミドルウェア設定ファイルmiddleware.php、そしてInstall.phpというコマンドによって自動生成されるファイルです。
2. コマンドを使用して、これら3つのファイルをパッケージ化し、composerにリリースします。
3. ユーザーがcomposerを使用してクロスドメインプラグインをインストールすると、Install.phpの中のクロスドメインミドルウェアプログラムファイルおよび設定ファイルが `{主项目}/config/plugin` にコピーされ、webmanによって読み込まれます。これにより、クロスドメインミドルウェアファイルの自動設定が有効になります。
4. ユーザーがcomposerを使用してこのプラグインを削除すると、Install.phpが対応するクロスドメインミドルウェアプログラムファイルと設定ファイルを削除し、プラグインの自動アンインストールを実現します。

## 規格
1. プラグイン名は `ベンダー` と `プラグイン名` の2つの部分で構成されます。 例: `webman/push` で、これはcomposerパッケージ名に対応しています。
2. プラグインの設定ファイルは統一して `config/plugin/ベンダー/プラグイン名/` に置かれます (consoleコマンドが自動的に設定ディレクトリを作成します)。プラグインに設定が不要な場合は、自動的に作成される設定ディレクトリを削除する必要があります。
3. プラグインの設定ディレクトリはapp.php (プラグインのメイン設定)、bootstrap.php (プロセス起動設定)、route.php (ルート設定)、middleware.php (ミドルウェア設定)、process.php (カスタムプロセス設定)、database.php (データベース設定)、redis.php (redis設定)、thinkorm.php (thinkorm設定)をサポートしています。これらの設定はwebmanに自動的に認識されます。
4. プラグインは以下の方法で設定を取得します: `config('plugin.ベンダー.プラグイン名.設定ファイル.具体的な設定項目');` 例: `config('plugin.webman.push.app.app_key')`
5. プラグインが独自のデータベース設定を持つ場合、`illuminate/database` の場合は `Db::connection('plugin.ベンダー.プラグイン名.具体的な接続')` 、`thinkorm` の場合は `Db::connct('plugin.ベンダー.プラグイン名.具体的な接続')` を使用します。
6. プラグインが `app/` ディレクトリにビジネスファイルを配置する必要がある場合、ユーザープロジェクトや他のプラグインとの競合を避けるために、注意して配置する必要があります。
7. プラグインは、設定ファイル以外のファイルやディレクトリを主プロジェクトにコピーすることを避けるべきです。例えば、クロスドメインプラグインは、設定ファイル以外に関係ないため、ミドルウェアファイルは `vendor/webman/cros/src` に配置すればよく、主プロジェクトにはコピーする必要はありません。
8. プラグインの名前空間は大文字を使用することが推奨されます。例: `Webman/Console`。

## サンプル

**`webman/console`コマンドラインのインストール**

`composer require webman/console`

#### プラグインの作成

プラグインの名前を `foo/admin` としましょう (名称は後にcomposerでリリースするプロジェクト名であり、小文字である必要があります)。次のコマンドを実行します。
`php webman plugin:create --name=foo/admin`

プラグインを作成すると、プラグイン関連のファイルを格納するための `vendor/foo/admin` ディレクトリと、プラグイン関連の設定を格納するための `config/plugin/foo/admin` ディレクトリが生成されます。

> 注意
> `config/plugin/foo/admin`では、app.php (プラグインのメイン設定)、bootstrap.php (プロセス起動設定)、route.php (ルート設定)、middleware.php (ミドルウェア設定)、process.php (カスタムプロセス設定)、database.php (データベース設定)、redis.php (redis設定)、thinkorm.php (thinkorm設定)などの設定がサポートされています。これらの設定はwebmanによって自動的に認識され、設定にマージされます。
`plugin` を接頭辞として使用してアクセスします。例えば、`config('plugin.foo.admin.app');`

#### プラグインのエクスポート

プラグインの開発が完了したら、次のコマンドを実行してプラグインをエクスポートします。
`php webman plugin:export --name=foo/admin`
エクスポート

> 説明
> エクスポート後、config/plugin/foo/adminディレクトリがvendor/foo/admin/srcにコピーされ、同時にInstall.phpが自動的に生成されます。Install.phpは、自動インストールおよび自動アンインストール時にいくつかの操作を実行するために使用されます。
デフォルトのインストール操作は、vendor/foo/admin/srcにある設定を現在のプロジェクトconfig/pluginにコピーすることです。
削除時のデフォルトの操作は、現在のプロジェクトconfig/pluginにある設定ファイルを削除することです。
Install.phpを編集して、インストールやアンインストール時に独自の操作を行うことができます。

#### プラグインの提出
* 既に [github](https://github.com) および [packagist](https://packagist.org) のアカウントを持っていると仮定します。
* [github](https://github.com)で「admin」プロジェクトを作成し、コードをアップロードします。プロジェクトのURLは `https://github.com/yourusername/admin` とします。
* `https://github.com/yourusername/admin/releases/new` にアクセスして、「v1.0.0」などのバージョンをリリースします。
* [packagist](https://packagist.org)にアクセスし、ナビゲーションの`Submit`をクリックし、githubプロジェクトのURL `https://github.com/yourusername/admin` を提出してプラグインの提出を完了します。

> **ヒント**
> `packagist` でプラグインを提出すると名前の衝突が表示される場合は、ベンダー名を変更することができます。例: `foo/admin` を `myfoo/admin` に変更します。

プラグインプロジェクトのコードが更新されたら、コードをgithubに同期し、再度 `https://github.com/yourusername/admin/releases/new` にアクセスしてバージョンを更新し、「https://packagist.org/packages/foo/admin」ページにアクセスして `Update` ボタンをクリックしてバージョンを更新してください。
## プラグインにコマンドを追加する
時々、プラグインにはいくつかのカスタムコマンドが必要で、補助機能を提供するためのものです。例えば、`webman/redis-queue` プラグインをインストールすると、プロジェクトに `redis-queue:consumer` コマンドが自動的に追加されます。ユーザーは `php webman redis-queue:consumer send-mail` を実行するだけで、プロジェクトに SendMail.php のコンシューマークラスが作成され、素早く開発するのに役立ちます。

`foo/admin` プラグインに `foo-admin:add` コマンドを追加する必要があるとします。以下は手順の参考です。

#### コマンドを作成する

**`vendor/foo/admin/src/FooAdminAddCommand.php` という新しいコマンドファイルを作成します**

```php
<?php

namespace Foo\Admin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FooAdminAddCommand extends Command
{
    protected static $defaultName = 'foo-admin:add';
    protected static $defaultDescription = 'ここにコマンドの説明が入ります';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Add name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln("Admin add $name");
        return self::SUCCESS;
    }

}
```

> **注意**
> プラグイン間でのコマンドの衝突を避けるために、コマンドのフォーマットは `vendor-プラグイン名:具体的なコマンド` が推奨されます。例えば `foo/admin` プラグインのすべてのコマンドは `foo-admin:` をプレフィックスとして使用するべきです。例：`foo-admin:add`。

#### 設定を追加する
**新しい設定ファイル `config/plugin/foo/admin/command.php` を作成します**

```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....他の設定を追加できます...
];
```

> **ヒント**
> `command.php` ファイルは、プラグインにカスタムコマンドを設定するためのもので、配列の各要素はコマンドクラスファイルに対応し、各クラスファイルは各コマンドに対応します。ユーザーがコマンドラインを実行すると、`webman/console` は自動的に各プラグインの `command.php` で設定されたカスタムコマンドをロードします。コマンドラインに関する詳細は [コンソール](console.md) を参照してください。

#### エクスポートを実行する
`php webman plugin:export --name=foo/admin` を実行して、プラグインをエクスポートし、`packagist` に提出します。これにより、ユーザーが`foo/admin` プラグインをインストールすると、`foo-admin:add` コマンドが追加されます。`php webman foo-admin:add jerry` を実行すると、`Admin add jerry` が出力されます。
