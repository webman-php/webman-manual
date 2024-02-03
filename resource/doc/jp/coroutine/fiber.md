# コルーチン

> **コルーチンの要件**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> webmanのアップグレードコマンド `composer require workerman/webman-framework ^1.5.0`
> workermanのアップグレードコマンド `composer require workerman/workerman ^5.0.0`
> Fiberのコルーチンをインストールするには `composer require revolt/event-loop ^1.0.0` を実行します。

# 例
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
`Timer::sleep()` はPHPの`sleep()`関数に似ていますが、異なるのは `Timer::sleep()` はプロセスをブロックしません。


### HTTPリクエストの送信

> **注意**
> 以下のコマンドで `composer require workerman/http-client ^2.0.0` をインストールする必要があります。

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
        $response = $client->get('http://example.com'); // 非同期リクエストを送信する同期メソッド
        return $response->getBody()->getContents();
    }
}
```
同様に `$client->get('http://example.com')` リクエストは非ブロッキングで、これを使用してwebmanで非同期HTTPリクエストを送信することができます。これによりアプリケーションの性能が向上します。

詳細については、[workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) を参照してください。

### support\Context クラスの追加

`support\Context`クラスはリクエストのコンテキストデータを保存するために使用され、リクエストが完了すると対応するコンテキストデータは自動的に削除されます。つまり、コンテキストデータのライフサイクルはリクエストのライフサイクルに従います。`support\Context` はFiber、Swoole、Swowのコルーチン環境をサポートしています。


### Swooleコルーチン
swoole拡張機能をインストールした後(swoole>=5.0が必要)、config/server.php設定ファイルでSwooleコルーチンを有効にすることができます。
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

詳細については、[workermanイベント駆動](https://www.workerman.net/doc/workerman/appendices/event.html) を参照してください。

### グローバル変数の汚染

コルーチン環境では、**リクエストに関連する**ステータス情報をグローバル変数や静的変数に保存することは禁止されています。なぜならこれによってグローバル変数が汚染される可能性があるからです。たとえば

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

プロセス数を1に設定して、連続して2つのリクエストを送信すると、
http://127.0.0.1:8787/test?name=lilei
http://127.0.0.1:8787/test?name=hanmeimei
のように期待している結果は、それぞれ `lilei` と`hanmeimei` を返すことですが、実際には両方のリクエストが`hanmeimei`を返します。
これは、2番目のリクエストが静的変数`$name`を上書きしたためです。最初のリクエストがスリープを終了して返ってくると、静的変数`$name`はすでに`hanmeimei`になっているからです。

**正しい方法は、コンテキストを使用してリクエストの状態データを保存することです**
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

**ローカル変数はデータの汚染を起こしません**
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
`$name`はローカル変数なので、コルーチン間でローカル変数を相互に参照することはできません。そのため、ローカル変数の使用はコルーチンセーフです。

# コルーチンの使用について
コルーチンは魔法の弾ではありません。コルーチンを導入することは、グローバル変数/静的変数の汚染問題に注意する必要があり、コンテキストを設定することを忘れないでください。また、コルーチン環境でのバグデバッグはブロッキングプログラミングよりも複雑です。

webmanのブロッキングプログラミングは実際には十分に速く、[techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) の過去3年間のベンチマークデータを見ると、webmanのブロッキングプログラミングはデータベースビジネスを含む場合、goのwebフレームワークgin、echoなどの性能よりも約1倍高く、伝統的なフレームワークlaravelよりも約40倍高いです。

データベースやRedisがすべて内部ネットワークにある場合、多プロセスとブロッキングプログラミングのパフォーマンスはコルーチン導入後よりも高い可能性があります。なぜなら、データベースやRedisなどが十分に高速である場合、コルーチンの作成、スケジューリング、破棄にかかるコストがプロセスの切り替えのコストよりも大きくなる可能性があるからです。そのため、コルーチンの導入が性能を著しく向上させる保証はありません。

# いつコルーチンを使用すべきか
ビジネスに遅いリクエストがある場合、たとえば第三者のAPIにアクセスする必要がある場合、[workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) を使用して非同期HTTP呼び出しを行うコルーチンの方法でアプリケーションの並行性を高めることができます。
