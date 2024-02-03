Webmanとは

webmanは、[workerman](https://www.workerman.net)をベースに開発された高性能HTTPサービスフレームワークです。webmanは従来のphp-fpmフレームワークに代わり、超高性能なスケーラブルなHTTPサービスを提供します。webmanを使用してウェブサイト、HTTPインターフェース、またはマイクロサービスを開発することができます。

さらに、webmanはカスタムプロセスをサポートし、websocketサービス、IoT、ゲーム、TCPサービス、UDPサービス、UNIXソケットサービスなど、workermanができることを可能にします。

webmanの理念
**最小のカーネルで最大の拡張性と最高のパフォーマンスを提供する。**

webmanは最も基本的な機能（ルーティング、ミドルウェア、セッション、カスタムプロセスインターフェース）のみを提供します。その他の機能はすべてComposerエコシステムを再利用しており、データベースの場合、開発者はLaravelの `illuminate/database` 、またはThinkPHPの `ThinkORM` 、さらには `Medoo` などのコンポーネントを使用することができます。これらをwebmanに統合することは非常に簡単です。

webmanの特徴
1. 高い安定性。webmanはworkermanに基づいているため、業界でバグが非常に少ない高い安定性のソケットフレームワークです。
2. 超高性能。webmanの性能は従来のphp-fpmフレームワークよりも10〜100倍高く、goのginやechoなどのフレームワークよりも約2倍高いです。
3. 高い再利用性。ほとんどのComposerコンポーネントやライブラリを変更せずに再利用できます。
4. 高い拡張性。カスタムプロセスをサポートし、workermanができることを可能にします。
5. 非常にシンプルで使いやすく、学習コストが非常に低く、コードの記述は従来のフレームワークとほとんど変わりません。
6. MITオープンソースライセンスを使用しており、親切で自由なライセンスです。

プロジェクトリンク
GitHub: https://github.com/walkor/webman **ぜひスターをお願いします**

Gitee: https://gitee.com/walkor/webman **ぜひスターをお願いします**

第三者の信頼できる負荷テストデータ

![](../assets/img/benchmark1.png)

データベースクエリビジネスを含む場合、単一のwebmanサーバーのスループットは39万QPSに達し、従来のphp-fpm構造のlaravelフレームワークよりも約80倍高い。

![](../assets/img/benchmarks-go.png)

データベースクエリビジネスを含む場合、webmanは同様のgo言語のwebフレームワークよりも約2倍の性能が高い。

上記のデータは[techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)から取得されています。
