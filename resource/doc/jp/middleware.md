# ミドルウェア
ミドルウェアは通常、リクエストまたはレスポンスをインターセプトするために使用されます。たとえば、コントローラーを実行する前にユーザーの身元を統一的に検証する場合、ユーザーがログインしていない場合はログインページにリダイレクトするなど、レスポンスに特定のヘッダーを追加する場合、特定のURIリクエストの割合を集計する場合などがあります。

## ミドルウェアオニオンモデル

```                          
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
   ── リクエスト ──────────────────> コントローラー ─── レスポンス ───────────────────────────> クライアント
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
ミドルウェアとコントローラーは、クラシックなオニオンモデルを構成しており、ミドルウェアは一層一層のオニオンの皮のように、コントローラーはオニオンの中心部のようです。この図に示すように、リクエストは矢印のようにミドルウェア1、2、3を通ってコントローラーに到達し、コントローラーがレスポンスを返し、その後、レスポンスは3、2、1の順番でミドルウェアを通って最終的にクライアントに返されます。つまり、各ミドルウェア内でリクエストを取得し、またレスポンスを取得することができます。

## リクエストのインターセプト
時には、特定のリクエストをコントローラーレイヤーに到達させたくない場合があります。たとえば、middleware2で現在のユーザーがログインしていないことが分かった場合、直接リクエストをインターセプトし、ログインレスポンスを返すことができます。このようなフローは次のようになります。

```                          
            ┌────────────────────────────────────────────────────────────┐
            │                         middleware1                        │ 
            │     ┌────────────────────────────────────────────────┐     │
            │     │                   middleware2                  │     │
            │     │          ┌──────────────────────────────┐      │     │
            │     │          │        middleware3           │      │     │       
            │     │          │    ┌──────────────────┐      │      │     │
            │     │          │    │                  │      │      │     │
   ── リクエスト ────────┐     │    │    コントローラー  │      │      │     │
            │     │ レスポンス │    │                  │      │      │     │
   <───────────────────┘     │    └──────────────────┘      │      │     │
            │     │          │                              │      │     │
            │     │          └──────────────────────────────┘      │     │
            │     │                                                │     │
            │     └────────────────────────────────────────────────┘     │
            │                                                            │
            └────────────────────────────────────────────────────────────┘
```
図のように、リクエストはmiddleware2に到達した後、ログインレスポンスが生成され、そのレスポンスがmiddleware2を通ってmiddleware1に返され、クライアントに返されます。

## ミドルウェアインターフェース
ミドルウェアは`Webman\MiddlewareInterface` インターフェースを実装する必要があります。

```php
interface MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, callable $handler): Response;
}
```

つまり、`process`メソッドを実装する必要があり、`process`メソッドは`support\Response`オブジェクトを返さなければならず、デフォルトではこのオブジェクトは`$handler($request)`によって生成されます（リクエストはオニオンの中心部を通り抜け続けます）、または`response()` `json()` `xml()` `redirect()`などのヘルパー関数によって生成されたレスポンス（リクエストはオニオンの中心部を通り抜けます）でも構いません。
中間層でリクエストとレスポンスを取得する
中間層では、リクエストを取得し、またはコントローラーの実行後のレスポンスを取得することができます。したがって、中間層には3つの部分があります。
1. リクエスト処理前のリクエストの処理段階
2. コントローラーがリクエストを処理する段階
3. レスポンスの処理後のレスポンスの処理段階

中間層でのこれらの段階の具体的な表現は以下のようになります：

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
        echo 'これはリクエスト処理前の段階、つまりリクエスト処理前です';
        
        $response = $handler($request); // コントローラーが実行され、レスポンスが得られるまで、次の段階へ進む
        
        echo 'これはレスポンス処理後の段階、つまりリクエスト処理後です';
        
        return $response;
    }
}
```

## サンプル：認証ミドルウェア
ファイル`app/middleware/AuthCheckTest.php`を作成します（ディレクトリが存在しない場合は作成してください）：

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
            // ログイン済みの場合、リクエストを次の段階へ進める
            return $handler($request);
        }

        // リフレクションを使用して、コントローラーのどのメソッドがログインを必要としないかを取得する
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // アクセスしようとしているメソッドがログインを必要とする場合
        if (!in_array($request->action, $noNeedLogin)) {
            // リクエストを横断しないようにリダイレクトレスポンスを返す
            return redirect('/user/login');
        }

        // ログインを必要としない場合、リクエストを次の段階へ進める
        return $handler($request);
    }
}
```

新しいコントローラー`app/controller/UserController.php`を作成します：

```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * ログインが必要ないメソッド
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request)
    {
        $request->session()->set('user', ['id' => 10, 'name' => 'webman']);
        return json(['code' => 0, 'msg' => 'login ok']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'ok', 'data' => session('user')]);
    }
}
```

`config/middleware.php`に以下のようにグローバルミドルウェアを追加します：

```php
return [
    // グローバルミドルウェア
    '' => [
        // ... 他のミドルウェアを省略
        app\middleware\AuthCheckTest::class,
    ]
];
```

認証ミドルウェアがあることで、コントローラーレベルでのログインの確認について心配する必要なく、コントローラーレイヤーでビジネスコードを書くことができます。

## サンプル：クロスドメインリクエストミドルウェア
ファイル`app/middleware/AccessControlTest.php`を作成します（ディレクトリが存在しない場合は作成してください）：

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
        // オプションリクエストであれば空のレスポンスを返し、それ以外は次の段階へ進めてレスポンスを得る
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // レスポンスにクロスドメイン関連のHTTPヘッダーを追加する
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

`config/middleware.php`に以下のようにグローバルミドルウェアを追加します：

```php
return [
    // グローバルミドルウェア
    '' => [
        // ... 他のミドルウェアを省略
        app\middleware\AccessControlTest::class,
    ]
];
```

> **注意**
> Ajaxリクエストでヘッダーをカスタムした場合、中間層で`Access-Control-Allow-Headers`フィールドにそのカスタムヘッダーを追加する必要があります。そうしないと`Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.` というエラーが発生する可能性があります。

## 説明

- ミドルウェアにはグローバルミドルウェア、アプリケーションミドルウェア（複数のアプリケーションモードでのみ有効）、ルートミドルウェアの3つの種類があります。
- 現在、単一のコントローラーのミドルウェアはサポートされていません（ただし、中間層で`$request->controller`を判断することで、コントローラーのミドルウェアのような機能を実現することができます）。
- ミドルウェアの設定ファイルの場所は `config/middleware.php` です。
- グローバルミドルウェアの設定は、`''` のキー下にあります。
- アプリケーションミドルウェアの設定は、特定のアプリケーション名の下にあります。例えば：

```php
return [
    // グローバルミドルウェア
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // APIアプリケーションミドルウェア（複数のアプリケーションモードでのみ有効）
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## ルートミドルウェア

特定の1つまたは複数のルートにミドルウェアを設定することができます。
例えば`config/route.php`に以下のように設定を追加します：

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
中間層構築関数のパラメーター

> 注意
> この機能はwebman-framework >= 1.4.8が必要です

1.4.8バージョン以降、設定ファイルは直接ミドルウェアをインスタンス化するか、無名関数を使用して、構築関数を介してミドルウェアにパラメーターを渡すことができます。
例えば、`config/middleware.php`では以下のように構成できます。
```php
return [
    // グローバルミドルウェア
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // APIアプリケーションミドルウェア（複数のアプリケーションモードのみ有効）
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

同様に、ルートミドルウェアも構築関数を介してパラメーターを渡すことができます。例えば`config/route.php`では以下のようになります。
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## ミドルウェアの実行順序
- ミドルウェアの実行順序は `グローバルミドルウェア`->`アプリケーションミドルウェア`->`ルートミドルウェア`です。
- 複数のグローバルミドルウェアがある場合、ミドルウェアの実際の構成順に従って実行されます（アプリケーションミドルウェア、ルートミドルウェアも同様です）。
- 404リクエストはグローバルミドルウェアを含む、いかなるミドルウェアもトリガーしません。

## ルートがミドルウェアにパラメータを渡す（route->setParams）

**ルート構成 `config/route.php`**
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
        // デフォルトルート $request->route はnullなので、$request->route が空かどうかを確認する必要があります。
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## ミドルウェアがコントローラにパラメータを渡す

時にはコントローラでミドルウェア内で生成されたデータを使用したい場合があります。その場合、`$request`オブジェクトにプロパティを追加してコントローラに渡すことができます。例えば：

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

**コントローラ：**
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

## ミドルウェアが現在のリクエストルート情報を取得する

> 注
> webman-framework >= 1.3.2が必要です

`$request->route`を使用してルートオブジェクトを取得し、対応するメソッドを呼び出すことで、現在の情報を取得できます。

**ルートの構成**
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
        // リクエストがいかなるルートにも一致しない場合（デフォルトのルートを除く），$request->route はnullです。
        // たとえば、ブラウザで /user/111 にアクセスした場合、以下の情報が表示されます。
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

> 注意
> `$route->param()`メソッドには webman-framework >= 1.3.16が必要です。

## ミドルウェアが例外を取得する

> 注意
> webman-framework >= 1.3.15が必要です

ビジネスプロセス中に例外が発生する場合、ミドルウェア内で`$response->exception()`を使用して例外を取得できます。

**ルートの構成**
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

## 超大域ミドルウェア

> 注意
> この機能にはwebman-framework >= 1.5.16が必要です

メインプロジェクトのグローバルミドルウェアはメインプロジェクトにのみ影響を与え、[アプリケーションプラグイン](app/app.md)に影響を与えません。場合によっては、全てのプラグインに影響を与えるミドルウェアを追加したいと思う場合、超大域ミドルウェアを使用できます。

`config/middleware.php`に以下のように設定します。
```php
return [
    '@' => [ // メインプロジェクトと全てのプラグインにグローバルミドルウェアを追加
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // メインプロジェクトにのみグローバルミドルウェアを追加
];
```

> ヒント
>`@`超全域ミドルウェアは、メインプロジェクトのみでなく、プラグインの指定でも構成でき、例えば`plugin/ai/config/middleware.php`で`@`超大域ミドルウェアを構成すると、メインプロジェクトと全てのプラグインに影響を与えます。

## 特定のプラグインにミドルウェアを追加する

> 注意
> この機能にはwebman-framework >= 1.5.16が必要です

時には特定の[アプリケーションプラグイン](app/app.md)にミドルウェアを追加したい場合がありますが、プラグインのコードを変更したくない（アップグレードされると上書きされるため）場合、メインプロジェクトでそれにミドルウェアを構成することができます。

`config/middleware.php`に以下のように設定します。
```php
return [
    'plugin.ai' => [], // aiプラグインにミドルウェアを追加
    'plugin.ai.admin' => [], // aiプラグインのadminモジュールにミドルウェアを追加
];
```

> ヒント
> もちろん、他のプラグインに影響を与えるために、同様の設定をプラグインに追加することもできます。例えば、`plugin/foo/config/middleware.php`に上記の構成を加えると、aiプラグインに影響を与えます。
