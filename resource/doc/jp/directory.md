# ディレクトリ構造
```
.
├── app                           アプリケーションディレクトリ
│   ├── controller                コントローラーディレクトリ
│   ├── model                     モデルディレクトリ
│   ├── view                      ビューディレクトリ
│   ├── middleware                ミドルウェアディレクトリ
│   │   └── StaticFile.php        組み込み静的ファイルミドルウェア
|   └── functions.php             ビジネスカスタム関数はこのファイルに書きます
|
├── config                        設定ディレクトリ
│   ├── app.php                   アプリケーションの設定
│   ├── autoload.php              ここに設定されたファイルは自動的にロードされます
│   ├── bootstrap.php             プロセスの起動時にonWorkerStartが実行されるコールバックの設定
│   ├── container.php             コンテナの設定
│   ├── dependence.php            コンテナ依存性の設定
│   ├── database.php              データベースの設定
│   ├── exception.php             例外の設定
│   ├── log.php                   ログの設定
│   ├── middleware.php            ミドルウェアの設定
│   ├── process.php               カスタムプロセスの設定
│   ├── redis.php                 Redisの設定
│   ├── route.php                 ルートの設定
│   ├── server.php                ポート、プロセス数などのサーバーの設定
│   ├── view.php                  ビューの設定
│   ├── static.php                静的ファイルのオンオフおよび静的ファイルミドルウェアの設定
│   ├── translation.php           複数言語の設定
│   └── session.php               セッションの設定
├── public                        静的リソースディレクトリ
├── process                       カスタムプロセスディレクトリ
├── runtime                       アプリケーションの実行時ディレクトリ。書き込み権限が必要です
├── start.php                     サービス起動ファイル
├── vendor                        composerでインストールされたサードパーティーライブラリディレクトリ
└── support                       ライブラリの適応(サードパーティーライブラリを含む)
    ├── Request.php               リクエストクラス
    ├── Response.php              レスポンスクラス
    ├── Plugin.php                プラグインのインストールおよびアンインストールスクリプト
    ├── helpers.php               ヘルパー関数(ビジネスカスタム関数はapp/functions.phpに書いてください)
    └── bootstrap.php             プロセス起動後の初期化スクリプト
```
