# pharパッケージ

pharは、PHPのJARに似たパッケージングファイルで、webmanプロジェクトを単一のpharファイルにまとめることができ、デプロイが容易になります。

**ここで [fuzqing](https://github.com/fuzqing) にPRしていただいたことに感謝いたします。**

> **注意**
> `php.ini`のphar構成オプションを閉じる必要があります。つまり、`phar.readonly = 0` を設定します。

## コマンドラインツールのインストール
`composer require webman/console`

## 設定
`config/plugin/webman/console/app.php` ファイルを開き、`'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'` を設定し、ユーザーが不要なディレクトリやファイルを除外してパッケージングし、パッケージサイズが大きくなりすぎるのを避けます。

## パッケージング
webmanプロジェクトのルートディレクトリで `php webman phar:pack` コマンドを実行すると、`build`ディレクトリに`webman.phar`ファイルが生成されます。

> パッケージングに関する設定は `config/plugin/webman/console/app.php` にあります。

## 関連コマンドの起動と停止
**起動**
`php webman.phar start` または `php webman.phar start -d`

**停止**
`php webman.phar stop`

**状態の確認**
`php webman.phar status`

**接続状態の確認**
`php webman.phar connections`

**再起動**
`php webman.phar restart` または `php webman.phar restart -d`

## 説明
* webman.pharを実行すると、webman.pharが存在するディレクトリに`runtime`ディレクトリが生成され、ログなどの一時ファイルが保存されます。

* もしプロジェクトで.envファイルを使用している場合、.envファイルをwebman.pharが存在するディレクトリに置く必要があります。

* もしビジネスがファイルをpublicディレクトリにアップロードする必要がある場合、publicディレクトリをwebman.pharが存在するディレクトリから切り離して配置する必要があり、この時は`config/app.php`を設定する必要があります。
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
ビジネスはヘルパー関数 `public_path()` を使用して実際のpublicディレクトリの場所を見つけることができます。

* webman.pharはWindowsでカスタムプロセスを有効にすることはサポートされていません。
