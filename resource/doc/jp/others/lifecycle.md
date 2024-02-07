# ライフサイクル

## プロセスのライフサイクル
- 各プロセスには非常に長いライフサイクルがあります
- 各プロセスは独立して動作し、互いに干渉しません
- 各プロセスは、そのライフサイクル内で複数のリクエストを処理できます
- プロセスは`stop` `reload` `restart`コマンドを受信すると、終了して現在のライフサイクルを終了します

> **注意**
> 各プロセスは独立しており、それは各プロセスが自身のリソース、変数、およびクラスのインスタンスを維持していることを意味します。それは各プロセスが独自のデータベース接続を持っていることを表し、いくつかのシングルトンは各プロセスで1度だけ初期化されるため、複数のプロセスが複数回初期化されるということです。

## リクエストのライフサイクル
- 各リクエストは`$request`オブジェクトを生成します
- `$request`オブジェクトはリクエスト処理が完了すると回収されます

## コントローラーのライフサイクル
- 各コントローラーは各プロセスで1回だけインスタンス化されますが、複数のプロセスでは複数回インスタンス化されます（コントローラーの再利用を除く。詳細は[コントローラーのライフサイクル](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F)を参照）
- コントローラーのインスタンスは現在のプロセス内で複数のリクエストと共有されます（コントローラーの再利用を除く）
- コントローラーのライフサイクルは、プロセスが終了すると終了します（コントローラーの再利用を除く）

## 変数のライフサイクルについて
webmanはPHPベースのフレームワークであり、そのためPHPの変数のリサイクルメカニズムに完全に従います。関数やメソッドの終了後、newキーワードで作成されたクラスのインスタンスなど、ビジネスロジック内で発生する一時変数は自動的に解放され、`unset`を手動で行う必要はありません。つまり、webman開発と従来のフレームワークの開発体験はほとんど同じです。例えば、以下の例では、`$foo`インスタンスはindexメソッドの実行が完了すると自動的に解放されます：
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
あるクラスのインスタンスを再利用したい場合は、クラスを静的プロパティや長寿命オブジェクト（例：コントローラー）のプロパティに保存するか、Containerコンテナのgetメソッドを使用してクラスのインスタンスを初期化できます。たとえば：
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

`Container::get()`メソッドは、クラスのインスタンスを作成して保存し、同じ引数で再度呼び出されると以前に作成したクラスのインスタンスを返します。

> **注意**
> `Container::get()`は、コンストラクタパラメータを持たないインスタンスを初期化できます。`Container::make()`は、コンストラクタパラメータを持つインスタンスを作成できますが、`Container::get()`とは異なり、`Container::make()`はインスタンスを再利用せず、常に新しいインスタンスを返します。

## メモリリークについて
ほとんどの場合、ビジネスコードでメモリリークは発生しません（ほとんどの場合、ユーザーフィードバックによるメモリリークの報告はほとんどありません）。長寿命の配列データが無限に拡張しないように注意すれば大丈夫です。以下のコードをご覧ください：
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
コントローラーはデフォルトで長寿命です（コントローラーの再利用を除く）。同様に、コントローラーの`$data`配列プロパティも長ライフサイクルです。そして、`foo/index`リクエストが増えるにつれて、`$data`配列の要素が増え続けてメモリリークが発生します。

詳細については、[メモリリーク](./memory-leak.md)を参照してください。
