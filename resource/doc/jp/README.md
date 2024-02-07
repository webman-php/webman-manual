# webmanとは

webmanは、[workerman](https://www.workerman.net)に基づいて開発された高性能なHTTPサービスフレームワークです。webmanは従来のphp-fpmアーキテクチャを置き換え、超高性能かつ拡張可能なHTTPサービスを提供します。webmanを使用すると、ウェブサイトの開発やHTTPインターフェース、マイクロサービスの開発が可能です。

さらに、webmanはカスタムプロセスをサポートし、websocketサービス、IoT、ゲーム、TCPサービス、UDPサービス、Unixソケットサービスなど、workermanが行うことができるすべてのことを実行することができます。

# webmanのコンセプト
**最小のカーネルで最大の拡張性と最高のパフォーマンスを提供する。**

webmanは最も基本的な機能（ルーティング、ミドルウェア、セッション、カスタムプロセスインターフェース）だけを提供します。その他の機能はすべて、composerエコシステムを再利用します。つまり、webmanで最も馴染みのある機能コンポーネントを使用することができます。例えば、データベースの部分では、開発者はLaravelの`illuminate/database`、またはThinkPHPの`ThinkORM`、あるいは他のコンポーネント如`Medoo`を選択することができます。それらをwebmanに統合することは非常に簡単です。

# webmanの特徴

1. 高い安定性。webmanはworkermanに基づいており、業界でほとんどバグのない高い安定性のソケットフレームワークです。

2. 超高性能。webmanの性能は従来のphp-fpmフレームワークよりも10〜100倍高く、goのginやechoなどのフレームワークよりも約2倍高いです。

3. 高い再利用性。ほとんどのcomposerコンポーネントやライブラリを変更することなく再利用できます。

4. 高い拡張性。カスタムプロセスをサポートし、workermanができることは何でも行うことができます。

5. 非常にシンプルかつ使いやすく、学習コストが非常に低く、コードの書き方は従来のフレームワークと同じです。

6. 最も寛大で友好的なMITオープンソースライセンスを使用しています。

# プロジェクトのURL
GitHub: https://github.com/walkor/webman **ぜひ星をたくさんつけてくださいね**

码云: https://gitee.com/walkor/webman **ぜひ星をたくさんつけてくださいね**

# 第三者による信頼性のあるベンチマークデータ

![](../assets/img/benchmark1.png)

データベースクエリを含む業務において、webmanの単一サーバーのスループットは39万QPSに達し、従来のphp-fpmアーキテクチャのlaravelフレームワークよりも約80倍高いです。

![](../assets/img/benchmarks-go.png)

データベースクエリを含む業務において、同様のタイプのgo言語のwebフレームワークよりも、webmanの性能が約2倍高いです。

以上のデータは[techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)より引用されています。
