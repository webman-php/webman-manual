# ライフサイクル

## プロセスライフサイクル
- 各プロセスには非常に長いライフサイクルがあります。
- 各プロセスは独立して動作し、互いに干渉しないようになっています。
- 各プロセスはそのライフサイクル内で複数のリクエストを処理できます。
- プロセスは`stop` `reload` `restart`コマンドを受信すると、終了し、そのライフサイクルを終了します。

> **ヒント**
> 各プロセスは独立しており、お互いに干渉しないため、それぞれが自身のリソース、変数、クラスのインスタンスなどを管理しています。これは、各プロセスが独自のデータベース接続を持っていることを意味し、いくつかのシングルトンは各プロセスで一度だけ初期化されるため、複数のプロセスで複数回初期化されることになります。

## リクエストライフサイクル
- 各リクエストは`$request`オブジェクトを生成します。
- `$request`オブジェクトはリクエストの処理が完了すると回収されます。

## コントローラーライフサイクル
- 各コントローラーは各プロセスで一度だけインスタンス化されますが、複数のプロセスでは複数回インスタンス化されます（コントローラーの再利用を除く詳細は[Controller Lifecycle](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F)を参照）。
- コントローラーのインスタンスは現在のプロセス内で複数のリクエストで共有されます（コントローラーの再利用を除く）。
- コントローラーライフサイクルはプロセスが終了すると終了します（コントローラーの再利用を除く）。

## 変数のライフサイクルについて
webmanはPHPベースのため、完全にPHPの変数の回収メカニズムに従います。ビジネスロジックで生成される一時的な変数、および`new`キーワードで作成されたクラスのインスタンスは、関数やメソッドが終了した後に自動的に回収されます。`unset`を手動で解放する必要はありません。つまり、webmanの開発は従来のフレームワークの開発とほぼ同様の経験です。たとえば、以下の例では、`$foo`インスタンスは`index`メソッドの実行が終了すると自動的に解放されます：
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // ここでFooクラスがあると仮定します
        return response($foo->sayHello());
    }
}
```
特定のクラスのインスタンスを再利用したい場合は、クラスをクラスの静的プロパティまたは長寿命オブジェクト（例：コントローラー）のプロパティに保存するか、Containerコンテナの`get`メソッドを使用してクラスのインスタンスを初期化することができます。たとえば：
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Container;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = Container::get(Foo::class);
        return response($foo->sayHello());
    }
}
```

`Container::get()`メソッドはクラスのインスタンスを作成および保存し、同じ引数で再度呼び出されると以前に作成されたクラスのインスタンスを返します。

> **注意**
> `Container::get()`は、コンストラクタパラメータのないインスタンスを初期化できます。 `Container::make()`はコンストラクタ引数を持つインスタンスを作成できますが、`Container::get()`とは異なり、`Container::make()`はインスタンスを再利用しません。つまり、同じ引数であっても常に新しいインスタンスを返します。

# メモリーリークについて
ほとんどの場合、ビジネスコードでメモリーリークは発生しません（ほとんどの場合、ユーザーフィードバックによるメモリーリークの報告はほとんどありません）。長寿命の配列データが無限に拡大しないようにそれをわずかに気をつけるだけです。以下のコードをご覧ください：
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    // 配列プロパティ
    public $data = [];
    
    public function index(Request $request)
    {
        $this->data[] = time();
        return response('hello index');
    }

    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```
コントローラーはデフォルトで長寿命です（コントローラーの再利用を除く）、同様にコントローラーの`$data`配列プロパティも長寿命です。`foo/index`リクエストが増えるにつれて、`$data`配列の要素が増え続け、メモリーリークが発生します。

詳細については、[メモリーリーク](./memory-leak.md)を参照してください。
