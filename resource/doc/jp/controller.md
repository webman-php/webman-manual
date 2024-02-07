# コントローラー

新しいコントローラーファイル `app/controller/FooController.php` を作成します。

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

`http://127.0.0.1:8787/foo` にアクセスすると、ページは `hello index` を返します。

`http://127.0.0.1:8787/foo/hello` にアクセスすると、ページは `hello webman` を返します。

もちろん、ルート構成を変更するには、[ルート](route.md)を参照してください。

> **ヒント**
> 404エラーが発生する場合は、`config/app.php` を開いて`controller_suffix` を`Controller` に設定して再起動してください。

## コントローラーのサフィックス
Webman 1.3から、`config/app.php` でコントローラーのサフィックスを設定できます。`config/app.php` で`controller_suffix` を空文字`''` に設定すると、コントローラーは以下のようになります。

`app\controller\Foo.php`。

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

コントローラーのサフィックスを`Controller`に設定することを強くお勧めします。これにより、コントローラーがモデルクラス名と競合するのを避けることができ、セキュリティが向上します。

## 説明
- フレームワークは自動的に`support\Request`オブジェクトをコントローラーに渡し、それを通じてユーザー入力データ(get post header cookieなど)を取得できます。[リクエスト](request.md)を参照してください。
- コントローラーでは、数字、文字列、または`support\Response`オブジェクトを返すことができますが、他の種類のデータを返すことはできません。
- `support\Response`オブジェクトは、`response()` `json()` `xml()` `jsonp()` `redirect()`などのヘルパー関数を使用して作成できます。

## コントローラーのライフサイクル
`config/app.php`で`controller_reuse`が`false`の場合、各リクエストで対応するコントローラーインスタンスが初期化され、リクエストの終了後にコントローラーインスタンスが破棄されます。これは従来のフレームワークの実行メカニズムと同様です。

`config/app.php`で`controller_reuse`が`true`の場合、すべてのリクエストでコントローラーインスタンスが再利用されます。つまり、一度コントローラーインスタンスが作成されると、すべてのリクエストでそのインスタンスが使用されます。

> **注意**
> コントローラー再利用を無効にするには、webman>=1.4.0が必要です。つまり、1.4.0より前ではコントローラーはデフォルトですべてのリクエストで再利用されます。

> **注意**
> コントローラー再利用を有効にすると、リクエスト中にコントローラーのプロパティを変更しないようにする必要があります。なぜなら、これらの変更は後続のリクエストに影響を与えるからです。

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    protected $model;
    
    public function update(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->update();
        return response('ok');
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->delete();
        return response('ok');
    }
    
    protected function getModel($id)
    {
        // このメソッドは最初のリクエスト update?id=1 の後にモデルを保持します。
        // もう一度リクエスト delete?id=2 を行うと、1 のデータが削除されます。
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **ヒント**
> コントローラーの`__construct()`コンストラクタ内でのデータの返却は何の効果もありません。例えば

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // コンストラクタでのデータの返却は効果がありません。ブラウザはこの応答を受け取りません
        return response('hello'); 
    }
}
```

## コントローラーの再利用と非再利用の違い
以下に違いを示します

#### コントローラーの再利用しない場合
すべてのリクエストで新しいコントローラーインスタンスが作成され、リクエスト終了後にそのインスタンスが解放され、メモリが解放されます。コントローラーの再利用は伝統的なフレームワークと同様であり、ほとんどの開発者の習慣に合致します。コントローラーが繰り返し作成および破棄されるため、性能は再利用するよりもやや低下します（helloworldのパフォーマンスは約10%低下し、ビジネスを含めるとほぼ無視できます）。

#### コントローラーの再利用する場合
一度プロセスでコントローラーを作成し、リクエスト終了後にそのコントローラーインスタンスを解放しない場合、現在のプロセスの後続のリクエストでこのインスタンスを再利用します。コントローラーの再利用はパフォーマンスが良くなりますが、ほとんどの開発者の習慣には合わないです。

#### コントローラーの再利用できない場合
リクエスト中にコントローラーのプロパティが変更される場合、コントローラーを再利用することはできません。なぜなら、これらのプロパティの変更は後続のリクエストに影響するからです。

コントローラーのコンストラクタ`__construct()`で各リクエストに対して初期化を行いたい開発者がいるときには、コントローラーの再利用を有効にすることはできません。なぜなら、現在のプロセスのコンストラクタは一度だけ呼び出され、すべてのリクエストでそれが呼び出されるわけではないからです。

