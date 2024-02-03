# セキュリティ

## 実行ユーザー
実行ユーザーは、nginxと同じような権限の低いユーザーに設定することをお勧めします。実行ユーザーは `config/server.php` の `user` と `group` で設定されます。
同様に、カスタムプロセスのユーザーは `config/process.php` の `user` と `group` で指定されます。
ただし、モニタープロセスには実行ユーザーを設定しないでください。なぜなら、それには正常に動作するための高い権限が必要だからです。

## コントローラーの規則
`controller`ディレクトリまたはそのサブディレクトリにはコントローラーファイルのみを配置することができ、他のクラスファイルを配置することは禁止されています。さもないと、[コントローラー接尾辞](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)が有効でない場合、クラスファイルが不正なURLからアクセスされ、予測できない結果をもたらす可能性があります。
例えば `app/controller/model/User.php` は実際にはModelクラスですが、間違って `controller`ディレクトリに配置されています。[コントローラー接尾辞](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)が有効でない場合、ユーザーは `/model/user/xxx` のような形で`User.php`内の任意のメソッドにアクセスすることができます。
このような状況を完全に排除するために、どのファイルがコントローラーファイルであるかを明示的に示すために、[コントローラー接尾辞](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)の使用を強くお勧めします。

## XSSフィルタリング
汎用性を考慮して、webmanはリクエストに対してXSSエスケープを行いません。
webmanは、レンダリング時にXSSエスケープを行うことを強く推奨し、データベースに格納する前にエスケープを行うことは推奨しません。
また、twig、blade、think-tmplateなどのテンプレートは自動的にXSSエスケープを実行するため、手動でエスケープする必要はありません。

> **ヒント**
> データベースに格納する前にXSSエスケープを行うと、いくつかのアプリケーションのプラグインの互換性の問題が発生する可能性が高いです。

## SQLインジェクションの防止
SQLインジェクションを防ぐためには、できるだけORMを使用することをお勧めします。例えば、[illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html)、[think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html)などです。また、SQLを自分で組み立てないようにしてください。

## nginxプロキシ
アプリケーションを外部ユーザーに公開する必要がある場合は、webmanの前にnginxプロキシを追加することを強くお勧めします。これにより、不正なHTTPリクエストがいくつかフィルタリングされ、セキュリティが向上します。詳細は[nginxプロキシ](nginx-proxy.md)を参照してください。
