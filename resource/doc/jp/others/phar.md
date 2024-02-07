# pharパッケージング

pharはPHPでJARに似たパッケージングファイルであり、webmanプロジェクトを単一のpharファイルにパッケージ化してデプロイすることができます。

**ここで、[fuzqing](https://github.com/fuzqing) さんのPRに非常に感謝します。**

> **注意**
> `php.ini`のphar構成オプションを閉じる必要があります。つまり、`phar.readonly = 0`を設定します。

## コマンドラインツールのインストール
`composer require webman/console`

## 設定の設定
`config/plugin/webman/console/app.php` ファイルを開き、`'exclude_pattern' => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`と設定して、いくつかの不要なディレクトリやファイルをパッケージ化する際に除外してパッケージのサイズを大きくするのを避けます。

## パッケージング
webmanプロジェクトのルートディレクトリで `php webman phar:pack` コマンドを実行します。
そうすると、buildディレクトリに`webman.phar`ファイルが生成されます。

> パッケージング関連の設定は `config/plugin/webman/console/app.php` にあります。

## 開始停止関連コマンド
**開始**
`php webman.phar start` または `php webman.phar start -d`

**停止**
`php webman.phar stop`

**状態の表示**
`php webman.phar status`

**接続状態の表示**
`php webman.phar connections`

**再起動**
`php webman.phar restart` または `php webman.phar restart -d`

## 説明
* webman.pharを実行すると、webman.pharが存在するディレクトリにruntimeディレクトリが生成され、ログなどの一時ファイルが格納されます。

* もしプロジェクトで.envファイルを使用している場合は、.envファイルをwebman.pharが存在するディレクトリに置く必要があります。

* もしビジネスでpublicディレクトリにファイルをアップロードする必要がある場合、publicディレクトリをwebman.pharが存在するディレクトリから切り離して配置する必要があります。その場合、`config/app.php` を設定する必要があります。
```'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',```
ビジネスは、`public_path()` ヘルパー関数を使って実際のpublicディレクトリの場所を見つけることができます。

* webman.pharはWindowsでカスタムプロセスを開始できません。
