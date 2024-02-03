# 基本的なプラグインの生成と公開フロー

## 原則
1. クロスドメインプラグインを例に取ると、プラグインは3つの部分に分かれます。1つはクロスドメインのミドルウェアプログラムファイル、もう1つはミドルウェアの設定ファイルであるmiddleware.php、そして最後にコマンドで自動生成されるInstall.phpです。
2. 私たちはコマンドを使用して、これら3つのファイルをパッケージ化してcomposerに公開します。
3. ユーザーがcomposerを使用してクロスドメインプラグインをインストールすると、Install.phpによってクロスドメインのミドルウェアプログラムファイルと設定ファイルが`{主项目}/config/plugin`にコピーされ、webmanが読み込むことができるようになります。これにより、クロスドメインのミドルウェアファイルが自動的に構成されます。
4. ユーザーがcomposerを使用してプラグインを削除すると、Install.phpによって対応するクロスドメインのミドルウェアプログラムファイルと設定ファイルが削除され、プラグインが自動的にアンインストールされます。

## 規格
1. プラグイン名は`ベンダー`と`プラグイン名`の2つの部分で構成されており、例えば、`webman/push`の場合、これはcomposerパッケージ名と対応しています。
2. プラグインの設定ファイルは一貫して`config/plugin/ベンダー/プラグイン名/`に配置されます（consoleコマンドは自動的に設定ディレクトリを作成します）。プラグイン設定が不要な場合は、手動で作成された設定ディレクトリを削除する必要があります。
3. プラグイン設定ディレクトリでは、app.phpがプラグインのメイン設定、bootstrap.phpがプロセス起動設定、route.phpがルート設定、middleware.phpがミドルウェア設定、process.phpがカスタムプロセス設定、database.phpがデータベース設定、redis.phpがRedisの設定、thinkorm.phpがThinkORMの設定システムをサポートしています。これらの設定はwebmanに自動的に識別されます。
4. プラグインは、設定を取得するために`config('plugin.ベンダー.プラグイン名.設定ファイル.具体的な設定項目');`のメソッドを使用します。例えば`config('plugin.webman.push.app.app_key')`
5. プラグインが独自のデータベース設定を持っている場合、`illuminate/database`では`Db::connection('plugin.ベンダー.プラグイン名.具体的な接続')`、`thinkrom`では`Db::connct('plugin.ベンダー.プラグイン名.具体的な接続')`を使用してアクセスします。
6. プラグインが`app/`ディレクトリにビジネスファイルを配置する必要がある場合、ユーザープロジェクトや他のプラグインとの競合を避けるために注意する必要があります。
7. プラグインはできるだけ主要なプロジェクトにファイルやディレクトリをコピーすることを避けるべきです。たとえば、クロスドメインプラグインは、設定ファイル以外に主要プロジェクトにコピーする必要がある場合は、ミドルウェアファイルを`vendor/webman/cros/src`に置いて、主要プロジェクトにコピーする必要はありません。
8. プラグインの名前空間は大文字を使用することをお勧めします。例えばWebman/Consoleです。

## 例

**`webman/console`コマンドラインをインストール**

`composer require webman/console`

#### プラグインの作成

例として、プラグインの名前が `foo/admin` とする。
以下のコマンドを実行する。
`php webman plugin:create --name=foo/admin`

プラグインを作成すると、プラグイン関連のファイルを格納するための`vendor/foo/admin`ディレクトリと、プラグイン関連の設定を格納するための`config/plugin/foo/admin`ディレクトリが生成されます。

> 注意
> `config/plugin/foo/admin` は以下の設定をサポートしています。app.phpがプラグインのメイン設定、bootstrap.phpがプロセス起動設定、route.phpがルート設定、middleware.phpがミドルウェア設定、process.phpがカスタムプロセス設定、database.phpがデータベース設定、redis.phpがRedisの設定、thinkorm.phpがThinkORMの設定。設定形式はwebmanと同じですが、これらの設定はwebmanによって自動的に認識されます、設定にマージされます。
`config('plugin.foo.admin.app');` のように`plugin`をプレフィックスとして使用してアクセスします。

#### プラグインのエクスポート

プラグインの開発が完了したら、以下のコマンドを実行してプラグインをエクスポートします。
`php webman plugin:export --name=foo/admin`
エクスポート

> 説明
> エクスポート後、config/plugin/foo/adminディレクトリがvendor/foo/admin/srcにコピーされ、同時にInstall.phpが自動的に生成されます。Install.phpは、プラグインの自動的なインストールとアンインストール時に実行されるいくつかの操作を実行するために使用されます。
デフォルトの操作は、vendor/foo/admin/srcの設定を現在のプロジェクトのconfig/pluginにコピーすることです。
削除時のデフォルトの操作は、現在のプロジェクトのconfig/pluginにある設定ファイルを削除することです。
Install.phpを変更して、インストールおよびアンインストール時にカスタム操作を実行することができます。

#### プラグインの提出
* 既に [github](https://github.com) と [packagist](https://packagist.org) アカウントをお持ちの場合
* [github](https://github.com)でadminプロジェクトを作成し、コードをアップロードし、プロジェクトのアドレスが`https://github.com/your_username/admin`であるとします。
* `https://github.com/your_username/admin/releases/new`にアクセスし、`v1.0.0`などのリリースを公開します。
* [packagist](https://packagist.org)に行き、ナビゲーションで`Submit`をクリックし、あなたのgithubプロジェクトアドレス`https://github.com/your_username/admin`を提出することで、プラグインの提出が完了します。

> **ヒント**
> `packagist`でプラグインを提出しようとすると名前の衝突が表示される場合は、`foo/admin`を`myfoo/admin`のような別のベンダーの名前に変更することができます。

後続でプラグインプロジェクトのコードを更新する場合は、コードをgithubに同期し、再度`https://github.com/your_username/admin/releases/new`にアクセスしてリリースを再度公開し、その後`https://packagist.org/packages/foo/admin`ページにアクセスして`Update`ボタンをクリックしてバージョンを更新します。

## プラグインにコマンドを追加
時には、プラグインにはいくつかのカスタムコマンドを追加して、補助機能を提供する場合があります。例えば、`webman/redis-queue`プラグインをインストールすると、プロジェクトに`redis-queue:consumer`コマンドが自動的に追加され、ユーザーは`php webman redis-queue:consumer send-mail`を実行するだけでプロジェクトにSendMail.phpのコンシューマクラスが生成されますし、これにより高速な開発が可能になります。

例えば `foo/admin`プラグインに`foo-admin:add`コマンドを追加する必要がある場合は、次の手順を参照してください。

#### コマンドの新規作成

**コマンドファイル `vendor/foo/admin/src/FooAdminAddCommand.php`を作成**

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
    protected static $defaultDescription = 'コマンドの説明';

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
> プラグイン間のコマンドの競合を避けるために、コマンドのフォーマットは`ベンダー-プラグイン名:具体的なコマンド`とすることをお勧めします。 例:`foo/admin`プラグインのすべてのコマンドは`foo-admin:`をプレフィックスとして指定する必要があります。例:`foo-admin:add`。

#### 設定の追加
**設定ファイル `config/plugin/foo/admin/command.php`を作成**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....複数の設定を追加できます...
];
```

> **ヒント**
> `command.php`はプラグインにカスタムコマンドを設定するためのファイルであり、配列のそれぞれの要素は1つのコマンドクラスファイルに対応しています。ユーザーがコマンドラインを実行するとき、`webman/console`は各プラグインの`command.php`で設定されたカスタムコマンドを自動的に読み込みます。詳細なコマンドについては、[コマンドライン](console.md)を参照してください。

#### エクスポート実行
`php webman plugin:export --name=foo/admin`コマンドを実行して、プラグインをエクスポートし、`packagist`に提出します。これにより、`foo/admin`プラグインをインストールしたユーザーは、`foo-admin:add`コマンドが追加されます。 `php webman foo-admin:add jerry` を実行すると `Admin add jerry` が出力されます。
