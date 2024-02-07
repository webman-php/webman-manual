# セキュリティ

## 実行ユーザー
実行ユーザーは、nginxの実行ユーザーと同じような低い権限のユーザーに設定することが推奨されます。実行ユーザーは`config/server.php`の`user`および`group`で設定します。
同様に、カスタムプロセスのユーザーは`config/process.php`の`user`および`group`で指定されます。
監視プロセスには高い権限が必要なため、実行ユーザーを設定しないでください。

## コントローラーの規約
`controller`ディレクトリまたはそのサブディレクトリには、コントローラーファイルのみを配置することができます。それ以外のクラスファイルを配置することは禁止されており、そうしないと[コントローラーの拡張子](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)が有効になっていない場合、URLによる不正なアクセスが可能となり、予期せぬ結果をもたらす可能性があります。
例えば、`app/controller/model/User.php`は実際にはモデルクラスですが、誤って`controller`ディレクトリに配置されている場合、[コントローラーの拡張子](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)が有効になっていない場合、ユーザーは`/model/user/xxx`のようなURLで`User.php`内の任意のメソッドにアクセスできる可能性があります。
このような状況を完全に排除するために、どのファイルがコントローラーファイルであるかを明示的に示すために、[コントローラーの拡張子](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)の使用を強くお勧めいたします。

## XSSフィルタリング
汎用性を考慮して、webmanはリクエストにXSSエスケープを適用しません。
webmanはXSSエスケープはレンダリング時に行うことを強く推奨しており、データベースに入れる前にエスケープを行うことは推奨していません。
また、twig、blade、think-tmplateなどのテンプレートは自動的にXSSエスケープを実行し、手動でエスケープする必要はありません。

> **注意**
> データベースに入れる前にXSSエスケープを行うと、いくつかのアプリケーションプラグインの非互換性の問題が発生する可能性が高いです。

## SQLインジェクションの防止
SQLインジェクションを防ぐために、可能な限りORM（例：[illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html)、[think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html)）を使用し、自分でSQLを組み立てることを避けてください。

## nginxプロキシ
アプリケーションを外部ユーザーに公開する必要がある場合は、webmanの前にnginxプロキシを設置することを強くお勧めします。これにより、いくつかの不正なHTTPリクエストをフィルタリングし、セキュリティを向上させることができます。詳細については、[nginxプロキシ](nginx-proxy.md)を参照してください。
