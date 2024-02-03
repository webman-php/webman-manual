# 実行フロー

## プロセス起動フロー

php start.php startを実行した後、以下の手順に従います：

1. config/ディレクトリ内の設定を読み込む
2. Workerの関連する設定を設定する（例：`pid_file` `stdout_file` `log_file` `max_package_size` など）
3. webmanプロセスを作成し、ポート（デフォルトは8787）をリッスンする
4. 設定に基づいてカスタムプロセスを作成する
5. webmanプロセスとカスタムプロセスが起動した後、以下のロジックを実行する（これらはonWorkerStart内で実行されます）：
   ① `config/autoload.php` で設定されたファイルをロードする（例：`app/functions.php`）
   ② `config/middleware.php` をロードする（`config/plugin/*/*/middleware.php`も含む）で設定されたミドルウェア
   ③ `config/bootstrap.php` を実行する（`config/plugin/*/*/bootstrap.php`も含む）で設定されたクラスのstartメソッドを初期化するために使用し、例えばLaravelデータベースの初期接続などを行う
   ④ `config/route.php` をロードする（`config/plugin/*/*/route.php`も含む）で定義されたルート

## リクエスト処理フロー

1. リクエストのURLがpublicディレクトリ内の静的ファイルに一致するかどうかを判断し、一致する場合はファイルを返送して処理を終了し、一致しない場合は次に進む
2. URLに基づいて特定のルートに一致するかどうかを判断し、一致しない場合は次に進み、一致する場合は次に進む
3. デフォルトのルートが閉じられているかどうかを判断し、閉じられている場合は404を返送して処理を終了し、閉じられていない場合は次に進む
4. リクエストに対応するコントローラーのミドルウェアを見つけ、順番にミドルウェアの前処理（オニオンモデルのリクエスト段階）を実行し、コントローラーのビジネスロジックを実行し、ミドルウェアの後処理（オニオンモデルの応答段階）を実行し、リクエストを終了します。 （[ミドルウェアオニオンモデル](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B)を参照）
