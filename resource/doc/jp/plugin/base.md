# 基本プラグイン

基本プラグインは通常、汎用コンポーネントであり、一般的にはcomposerを使用してインストールし、コードはvendorディレクトリに配置されます。インストール時には、いくつかのカスタム設定（ミドルウェア、プロセス、ルートなどの設定）を自動的に`{プロジェクト}config/plugin`ディレクトリにコピーすることができます。webmanはこのディレクトリの設定を自動的に識別し、メインの設定にマージしてプラグインがwebmanの任意のライフサイクルに介入できるようにします。

詳細は[基本プラグインの作成](create.md)を参照してください。
