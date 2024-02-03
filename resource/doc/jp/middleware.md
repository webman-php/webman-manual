# ミドルウェア

ミドルウェアは、通常、リクエストやレスポンスをインターセプトするために使用されます。たとえば、コントローラーを実行する前にユーザーの身元を統一的に検証し、ユーザーがログインしていない場合はログインページにリダイレクトする、またはレスポンスに特定のヘッダーを追加する、特定のURIリクエストの割合を集計するなどです。

## ミドルウェアのオニオンモデル

``` 
                              
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── リクエスト ───────────────────────> コントローラー ─ レスポンス ───────────────────────────> クライアント
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```

ミドルウェアとコントローラーは、クラシックなオニオンモデルを構成しており、ミドルウェアはオニオンの皮のようにレイヤーを形成し、コントローラーがオニオンの芯となります。図に示すように、リクエストは矢印のようにミドルウェア1、2、3を通過してコントローラーに到達し、コントローラーがレスポンスを返し、その後レスポンスは3、2、1の順序でミドルウェアを通過して最終的にクライアントに返されます。つまり、各ミドルウェア内ではリクエストを取得することも、レスポンスを取得することもできます。

## リクエストのインターセプト

時には、あるリクエストがコントローラーレイヤーに到達しないようにしたいことがあります。たとえば、特定の認証ミドルウェアで現在のユーザーがログインしていないことがわかった場合、リクエストを直接インターセプトし、ログインレスポンスを返します。このプロセスは次のようになります。

```
                              
            ┌───────────────────────────────────────────────────────┐
            │                     middleware1                       │ 
            │     ┌───────────────────────────────────────────┐     │
            │     │          　 　 認証ミドルウェア                │     │
            │     │      ┌──────────────────────────────┐     │     │
            │     │      │         middleware3          │     │     │       
            │     │      │     ┌──────────────────┐     │     │     │
            │     │      │     │                  │     │     │     │
 　── リクエスト ───────────―  │       コントローラ―   │     │     │
            │     │ レスポンス　│                  │     │     │     │
   <─────────────────┘   │     └──────────────────┘     │     │     │
            │     │      │                              │     │     │
            │     │      └──────────────────────────────┘     │     │
            │     │                                           │     │
            │     └───────────────────────────────────────────┘     │
            │                                                       │
            └───────────────────────────────────────────────────────┘
```

上記のように、認証ミドルウェアにリクエストが到達すると、ログインレスポンスが生成され、そのレスポンスが認証ミドルウェアを通過し、ブラウザに返されます。

## ミドルウェアインターフェース

ミドルウェアは `Webman\MiddlewareInterface` インターフェースを実装する必要があります。
```php
interface MiddlewareInterface
{
    /**
     * インカミングサーバーリクエストを処理します。
     *
     * インカミングサーバーリクエストを処理して、レスポンスを生成します。
     * レスポンスを生成できない場合は、提供されたリクエストハンドラに委任することができます。
     */
    public function process(Request $request, callable $handler): Response;
}
```
つまり、`process` メソッドを実装する必要があり、`process` メソッドは `support\Response` オブジェクトを返さなければなりません。通常、このオブジェクトは `$handler($request)` によって生成されます（リクエストはオニオンの芯に向かって続行されます）。または、`response()`、`json()`、`xml()`、`redirect()` などのヘルパー関数によって生成されるレスポンス（リクエストはオニオンの芯に向かって停止します）。

## ミドルウェア内でのリクエストとレスポンスの取得

ミドルウェア内ではリクエストを取得することも、コントローラー後のレスポンスを取得することもできます。したがって、ミドルウェアは次の3つの部分に分けることができます。
1. リクエストを通過する段階、つまりリクエスト処理前の段階
2. コントローラーがリクエストを処理する段階、つまりリクエスト処理の段階
3. レスポンスが通過する段階、つまりリクエスト処理後の段階

ミドルウェア内でのこれらの段階の表現は次のようになります
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Test implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        echo 'ここはリクエストを通過する段階であり、リクエスト処理前です';
        
        $response = $handler($request); // オニオンの芯に向かって続行し、コントローラーがレスポンスを取得します
        
        echo 'ここはレスポンスが通過する段階であり、リクエスト処理後です';
        
        return $response;
    }
}
```
## サンプル：認証ミドルウェア
ファイル`app/middleware/AuthCheckTest.php`を作成してください（ディレクトリが存在しない場合は作成してください）：

```php
<?php
namespace app\middleware;

use ReflectionClass;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AuthCheckTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        if (session('user')) {
            // ログイン済み、リクエストの処理を継続
            return $handler($request);
        }

        // 反射を使用してコントローラーのログインが不要なメソッドを取得
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // アクセスするメソッドにはログインが必要
        if (!in_array($request->action, $noNeedLogin)) {
            // リクエストをリダイレクトレスポンスに変更し、リクエストの処理を停止
            return redirect('/user/login');
        }

        // ログイン不要、リクエストの処理を継続
        return $handler($request);
    }
}
```

コントローラー`app/controller/UserController.php`を新規作成してください：

```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * ログインが不要なメソッド
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request)
    {
        $request->session()->set('user', ['id' => 10, 'name' => 'webman']);
        return json(['code' => 0, 'msg' => 'ログイン成功']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'ok', 'data' => session('user')]);
    }
}
```

`config/middleware.php`に以下のようにグローバルミドルウェアを追加してください：

```php
return [
    // グローバルミドルウェア
    '' => [
        // ... その他のミドルウェアをここに省略
        app\middleware\AuthCheckTest::class,
    ]
];
```

認証ミドルウェアを使うことで、コントローラーレイヤーでビジネスコードを書く際に、ユーザーがログインしているかどうかを気にする必要がなくなります。

## サンプル：クロスドメインリクエストミドルウェア
以下のように`app/middleware/AccessControlTest.php`を作成してください（ディレクトリが存在しない場合は作成してください）：

```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AccessControlTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // オプションリクエストの場合は空のレスポンスを返し、それ以外の場合はリクエストの処理を継続してレスポンスを得る
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // レスポンスにクロスドメイン関連のHTTPヘッダーを追加
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', '*'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
        ]);
        
        return $response;
    }
}
```

`config/middleware.php`に以下のようにグローバルミドルウェアを追加してください：

```php
return [
    // グローバルミドルウェア
    '' => [
        // ... その他のミドルウェアをここに省略
        app\middleware\AccessControlTest::class,
    ]
];
```

> **注意**
> Ajaxリクエストでヘッダーをカスタマイズした場合、ミドルウェアで `Access-Control-Allow-Headers` フィールドにこのカスタムヘッダーを追加する必要があります。そうしないと、`Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.` エラーが発生します。

## 説明
- ミドルウェアは、グローバルミドルウェア、アプリケーションミドルウェア（マルチアプリモードでのみ有効、[マルチアプリ](multiapp.md)を参照）およびルートミドルウェアに分かれます。
- 現時点では、単一のコントローラーのミドルウェアはサポートされていません（ただし、ミドルウェア内で`$request->controller`を確認することで、コントローラーミドルウェアのような機能を実現できます）。
- ミドルウェアの設定ファイルは `config/middleware.php` にあります。
- グローバルミドルウェアの設定は `''` のキーの下にあります。
- アプリケーションミドルウェアの設定は、例えば以下のようにそれぞれのアプリ名に対応する箇所にあります。

```php
return [
    // グローバルミドルウェア
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // APIアプリケーションのミドルウェア（マルチアプリモードでのみ有効）
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## ルートミドルウェア

1つのあるいは複数のルートに対してミドルウェアを設定することができます。
たとえば`config/route.php`に以下のように設定できます:

```php
<?php
use support\Request;
use Webman\Route;

Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($r, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

## ミドルウェアのコンストラクタへのパラメータの指定

> **注**
> この機能は、webman-framework >= 1.4.8が必要です

1.4.8以降では、設定ファイルでミドルウェアを直接インスタンス化したり、無名関数を使用することで、ミドルウェアにコンストラクタパラメータを簡単に渡すことができます。
たとえば、`config/middleware.php`では次のように設定できます。

```php
return [
    // グローバルミドルウェア
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // APIアプリケーションのミドルウェア（マルチアプリモードでのみ有効）
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

同様に、ルートミドルウェアでもコンストラクタを介してパラメータを渡すことができます。例えば、`config/route.php`では次のように設定できます。

```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## ミドルウェアの実行順序
- ミドルウェアの実行順序は `グローバルミドルウェア` -> `アプリケーションミドルウェア` -> `ルートミドルウェア` です。
- 複数のグローバルミドルウェアがある場合、実際のミドルウェアの設定順に従います（アプリケーションミドルウェア、ルートミドルウェアも同様）。
- 404リクエストはどのミドルウェアもトリガーしません。

## ルートからミドルウェアへのパラメータ渡し（route->setParams）

**ルート設定 `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**ミドルウェア（例えばグローバルミドルウェア）**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // デフォルトのルート $request->route はnullなので、ルートが空でないかどうかをチェックする必要があります
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## ミドルウェアからコントローラーへのパラメータ渡し

時にミドルウェアで生成されたデータをコントローラーで使用する必要がある場合、ミドルウェアで`$request`オブジェクトにプロパティを追加することでコントローラーにパラメータを渡すことができます。例えば：

**ミドルウェア**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $request->data = 'some value';
        return $handler($request);
    }
}
```

**コントローラー：**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response($request->data);
    }
}
```

## ミドルウェアで現在のリクエストルート情報を取得する
> **注意**
> webman-framework >= 1.3.2 が必要です。

`$request->route` を使ってルートオブジェクトを取得し、対応するメソッドを呼び出すことで情報を取得できます。

**ルートの設定**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**ミドルウェア**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $route = $request->route;
        // リクエストが（デフォルトのルートを除いて）どのルートにも一致しない場合、$request->route は null になります
        // 例えば、ブラウザが /user/111 のアドレスにアクセスした場合、以下の情報が出力されます
        if ($route) {
            var_export($route->getPath());       // /user/{uid}
            var_export($route->getMethods());    // ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD','OPTIONS']
            var_export($route->getName());       // user_view
            var_export($route->getMiddleware()); // []
            var_export($route->getCallback());   // ['app\\controller\\UserController', 'view']
            var_export($route->param());         // ['uid'=>111]
            var_export($route->param('uid'));    // 111 
        }
        return $handler($request);
    }
}
```

> **注意**
> `$route->param()` メソッドを使用するには、webman-framework >= 1.3.16 が必要です。


## ミドルウェアで例外を取得する
> **注意**
> webman-framework >= 1.3.15 が必要です。

ビジネス処理中に例外が発生することがあります。その際、ミドルウェア内で `$response->exception()` を使用して例外を取得できます。

**ルートの設定**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('exception test');
});
```

**ミドルウェア：**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $response = $handler($request);
        $exception = $response->exception();
        if ($exception) {
            echo $exception->getMessage();
        }
        return $response;
    }
}
```

## スーパーグローバルミドルウェア

> **注意**
> この機能にはwebman-framework >= 1.5.16 が必要です。

メインプロジェクトのグローバルミドルウェアは、メインプロジェクトにのみ影響を与え、[アプリケーションプラグイン](app/app.md)には影響しません。しかし、すべてのプラグインに影響を与えるグローバルミドルウェアを追加したい場合、スーパーグローバルミドルウェアを使用できます。

`config/middleware.php` で以下のように設定します：
```php
return [
    '@' => [ // メインプロジェクトとすべてのプラグインにグローバルミドルウェアを追加
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // メインプロジェクトにだけグローバルミドルウェアを追加
];
```

> **ヒント**
> `@` スーパーグローバルミドルウェアは、メインプロジェクトのみでなく、特定のプラグインで設定することもできます。例えば、`plugin/ai/config/middleware.php`内で`@`スーパーグローバルミドルウェアを設定すると、メインプロジェクトおよびすべてのプラグインに影響します。

## 特定のプラグインにミドルウェアを追加する

> **注意**
> この機能にはwebman-framework >= 1.5.16 が必要です。

ある[アプリケーションプラグイン](app/app.md)にミドルウェアを追加したいが、そのままプラグインのコードを変更したくない（アップデートが上書きされる可能性があるため）場合、メインプロジェクトでミドルウェアを設定することができます。

`config/middleware.php` で以下のように設定します：
```php
return [
    'plugin.ai' => [], // aiプラグインにミドルウェアを追加
    'plugin.ai.admin' => [], // aiプラグインのadminモジュールにミドルウェアを追加
];
```

> **ヒント**
> もちろん、特定のプラグインに影響を与えるために、同様の設定を別のプラグインに追加することもできます。例えば、`plugin/foo/config/middleware.php` に上記の設定を追加すると、aiプラグインに影響を与えます。
