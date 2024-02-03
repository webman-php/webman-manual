# コントローラー

`app/controller/FooController.php`に新しいコントローラーファイルを作成します。

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

`http://127.0.0.1:8787/foo`にアクセスすると、ページは `hello index`を返します。

`http://127.0.0.1:8787/foo/hello`にアクセスすると、ページは `hello webman`を返します。

もちろん、ルートの構成を変更するためには、[ルート](route.md)を参照してください。

> **ヒント**
> 404エラーが発生した場合は、`config/app.php`を開き、`controller_suffix`を`Controller`に設定し、再起動してください。

## コントローラーのサフィックス
webman 1.3以降、`config/app.php`でコントローラーのサフィックスを設定できます。`config/app.php`で`controller_suffix`を設定せずに残すと、コントローラーは以下のようになります。

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

コントローラーのサフィックスを`Controller`に設定することを強くお勧めします。これにより、コントローラーとモデルのクラス名の競合を避け、セキュリティを高めることができます。

## 説明
 - フレームワークは自動的に`support\Request`オブジェクトをコントローラーに渡します。これにより、ユーザーの入力データ（GET、POST、ヘッダー、Cookieなど）を取得できます。[リクエスト](request.md)を参照してください。
 - コントローラー内では、数値、文字列、または`support\Response`オブジェクトを返すことができますが、他の種類のデータは返すことはできません。
 - `support\Response`オブジェクトは、`response()` `json()` `xml()` `jsonp()` `redirect()`などのヘルパー関数を使用して作成することができます。

## コントローラーのライフサイクル

`config/app.php`で`controller_reuse`が`false`の場合、各リクエストに対して対応するコントローラーのインスタンスが初期化され、リクエストの終了後にコントローラーのインスタンスが破棄されます。これは従来のフレームワークの動作と同じです。

`config/app.php`で`controller_reuse`が`true`の場合、すべてのリクエストでコントローラーのインスタンスが再利用されるため、コントローラーのインスタンスが一度作成されるとメモリ内に常駐します。

> **注意**
> コントローラーの再利用を無効にするには、webman>=1.4.0が必要です。つまり、1.4.0より前では、デフォルトですべてのリクエストでコントローラーが再利用され、変更することはできません。

> **注意**
> コントローラーの再利用を有効にすると、後続のリクエストに影響を与える可能性があるため、リクエストがコントローラーの属性を変更しないようにする必要があります。

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
        // このメソッドは最初のリクエスト update?id=1 の後にmodelを保持します。 もし delete?id=2 のリクエストが再度行われた場合、1のデータが削除されます。
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **ヒント**
> コントローラーの`__construct()`コンストラクタ内でデータを返すと、効果はありません。例えば

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // コンストラクタ内でデータを返しても、ブラウザはこの応答を受け取りません。
        return response('hello'); 
    }
}
```

## コントローラーの再利用と非再利用の違い
違いは次のとおりです。

#### コントローラーの非再利用
各リクエストで新しいコントローラーのインスタンスが新たに作成され、リクエストの終了後にこのインスタンスが解放され、メモリが解放されます。コントローラーの非再利用は、一般的なフレームワークと同様であり、大部分の開発者の習慣に合致します。コントローラーの再生成と破棄が繰り返されるため、性能はコントローラーを再利用する場合よりもわずかに劣ります（helloworldの負荷テストにおいて、セキュリティによるパフォーマンスの低下は10%程度であり、ビジネスを含めばほぼ無視できます）。

#### コントローラーの再利用
再利用する場合は、プロセスごとに1回だけコントローラーを新規作成し、リクエストの終了後にこのインスタンスを解放せず、プロセスの後続のリクエストでこのインスタンスが再利用されます。コントローラーの再利用は性能が向上しますが、一般的な開発者の習慣に合致しない場合があります。

#### コントローラーの再利用を使用できない場合
リクエストでコントローラーの属性が変更される場合には、コントローラーの再利用を有効にすることができません。なぜなら、これらの属性の変更は後続のリクエストに影響を与えるからです。

コントローラーのコンストラクタ`__construct()`内で、各リクエストごとに初期化処理を行う開発者もいますが、この場合もコントローラーを再利用できません。なぜなら、現在のプロセスでコンストラクタが1回しか呼び出されず、各リクエストで呼び出されるわけではないからです。
