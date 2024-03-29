# webmanのパフォーマンス

### 伝統的なフレームワークのリクエスト処理フロー

1. nginx/apache がリクエストを受け取る
2. nginx/apache がリクエストを php-fpm に渡す
3. php-fpm が環境を初期化し、変数リストを作成する
4. php-fpm が各拡張機能/モジュールの RINIT を呼び出す
5. php-fpm がディスクから PHP ファイルを読み込む (opcache を使用すると回避できる)
6. php-fpm がレキシカル解析、構文解析、opcode にコンパイルする (opcache を使用すると回避できる)
7. php-fpm がopcodeを実行する。これには8.9.10.11が含まれる
8. フレームワークの初期化、コンテナ、コントローラー、ルート、ミドルウェアなどのクラスのインスタンス化
9. フレームワークがデータベースに接続して権限を検証し、Redis に接続する
10. フレームワークがビジネスロジックを実行する
11. フレームワークがデータベースと Redis の接続を閉じる
12. php-fpm がリソースを解放し、すべてのクラス定義、インスタンス、シンボルテーブルなどを破棄する
13. php-fpm が各拡張機能/モジュールの RSHUTDOWN メソッドを順番に呼び出す
14. php-fpm が結果を nginx/apache に転送する
15. nginx/apache が結果をクライアントに返す

### webmanのリクエスト処理フロー
1. フレームワークがリクエストを受け取る
2. フレームワークがビジネスロジックを実行する
3. フレームワークが結果をクライアントに返す

そうです、nginx のリバースプロキシがない場合、フレームワークはこれらの3つのステップのみです。これはすでに PHP フレームワークの究極と言えるでしょう。これにより、webmanのパフォーマンスは伝統的なフレームワークの何倍も、数十倍にもなると言えます。

詳細は[パフォーマンステスト](benchmarks.md)を参照してください。
