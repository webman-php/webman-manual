# アプリケーションプラグインの作成

## ユニークな識別子

各プラグインにはユニークなアプリケーション識別子があります。開発者は開発する前に識別子を決めて、その識別子が他で使用されていないかを確認する必要があります。
確認アドレス：[アプリ識別子の確認](https://www.workerman.net/app/check)

## 作成

`composer require webman/console` を実行して、webmanコマンドラインをインストールします。

次に、`php webman app-plugin:create {プラグインの識別子}` コマンドを使用して、ローカルにアプリケーションプラグインを作成できます。

例： `php webman app-plugin:create foo`

webmanを再起動します。

`http://127.0.0.1:8787/app/foo` を訪れて、コンテンツが返されれば作成に成功したことを意味します。
