# コルーチン

> **コルーチンの要件**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> webmanのアップグレードコマンド `composer require workerman/webman-framework ^1.5.0`
> workermanのアップグレードコマンド `composer require workerman/workerman ^5.0.0`
> Fiberコルーチンのインストールには `composer require revolt/event-loop ^1.0.0` が必要です。

# サンプル
### 遅延レスポンス

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // 1.5秒スリープ
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` は PHPの`sleep()` 関数に似ていますが、`Timer::sleep()` はプロセスをブロックしません。


### HTTPリクエストの送信

> **注意**
> `composer require workerman/http-client ^2.0.0` をインストールする必要があります。

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // 非同期リクエストのための同期メソッドの呼び出し
        return $response->getBody()->getContents();
    }
}
```
同様に`$client->get('http://example.com')`のリクエストは非ブロッキングであり、これはwebmanで非同期HTTPリクエストを開始するために使用できます。アプリケーションの性能を向上させます。

詳細は [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) を参照してください。

### support\Context クラスの追加

`support\Context`クラスはリクエストのコンテキストデータを保存するために使用され、リクエストが完了すると、それに応じたコンテキストデータが自動的に削除されます。つまり、contextデータの寿命はリクエストの寿命に従います。`support\Context`はFiber、Swoole、Swowコルーチン環境をサポートしています。

### Swooleコルーチン
Swoole拡張機能（Swoole>=5.0が必要）をインストールした後、`config/server.php`を設定してSwooleコルーチンを有効にします。
```php
'event_loop' => \Workerman\Events\Swoole::class,
```
詳細は [workermanイベント駆動](https://www.workerman.net/doc/workerman/appendices/event.html) を参照してください。

### グローバル変数の汚染

コルーチン環境では、**リクエスト関連**の状態情報をグローバル変数や静的変数に保存しないでください。なぜなら、これによりグローバル変数の汚染が引き起こされる可能性があるからです。例えば、以下のコード：

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```

プロセス数を1に設定した場合、連続して2つのリクエストを送信すると、  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
期待される結果は、それぞれ `lilei` と `hanmeimei` を返すことですが、実際にはどちらも `hanmeimei` を返します。  
これは、2つ目のリクエストが静的変数`$name`を上書きし、最初のリクエストがスリープ終了時には静的変数`$name`がすでに `hanmeimei` になっているためです。

**正しい方法は、コンテキストにリクエスト状態のデータを保存することです。**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        Timer::sleep(5);
        return Context::get('name');
    }
}
```

**ローカル変数はデータの汚染を引き起こしません**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        Timer::sleep(5);
        return $name;
    }
}
```
`$name` はローカル変数であるため、コルーチン間でローカル変数にアクセスすることはできないため、ローカル変数の使用はコルーチンに安全です。

# コルーチンについて
コルーチンは銀の弾ではありません。コルーチンを導入すると、グローバル変数/静的変数の汚染問題に注意する必要があります。また、コルーチン環境でのバグデバッグはブロッキングプログラミングよりも複雑です。

実際に、webmanのブロッキングプログラミングは十分に速いです。[techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) 最近3年間の3回のベンチマークデータによると、webmanのブロッキングプログラミングはデータベースビジネスに対して、goのwebフレームワークgin、echoなどの性能を約1倍、従来のフレームワークlaravelよりも約40倍高くなっています。
![](../../assets/img/benchemarks-go-sw.png?)

データベース、redisなどがローカルネットワークにある場合、マルチプロセスのブロッキングプログラミングの性能は通常協程よりも高くなる場合があります。なぜなら、データベース、redisなどが十分に早い場合、協程の作成、スケジューリング、破棄の費用がプロセスの切り替えの費用よりも大きいため、協程の導入が性能を著しく向上させることはありません。

# いつコルーチンを使用するか
ビジネスに遅延アクセスがある場合、例えば、ビジネスがサードパーティAPIにアクセスする必要がある場合、[workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)を使用して非同期HTTP呼び出しを行うことで、アプリケーションの同時実行能力を向上させることができます。
