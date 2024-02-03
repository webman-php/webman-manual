# アプリケーションプラグインの作成

## ユニークな識別子

各プラグインにはユニークなアプリケーション識別子があります。開発者は開発前に識別子を考え、その識別子が既に使用されていないかを確認する必要があります。
確認アドレス：[アプリケーション識別子の確認](https://www.workerman.net/app/check)

## 作成

`composer require webman/console` を実行してwebmanコマンドラインをインストールします。

次に、コマンド `php webman app-plugin:create {プラグインの識別子}` を使用して、ローカルにアプリケーションプラグインを作成できます。

例えば、`php webman app-plugin:create foo` を実行します。

Webmanを再起動します。

`http://127.0.0.1:8787/app/foo` にアクセスして、コンテンツが返されれば、作成が成功したことを意味します。
