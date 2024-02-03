# 例外処理

## 設定
`config/exception.php`
```php
return [
    // ここに例外処理クラスを設定します
    '' => support\exception\Handler::class,
];
```
複数のアプリケーションモードの場合、それぞれのアプリケーションに対して例外処理クラスを個別に設定することができます。[Multiapp](multiapp.md)を参照してください。


## デフォルトの例外処理クラス
webmanでは、例外はデフォルトで `support\exception\Handler` クラスによって処理されます。デフォルトの例外処理クラスを変更するには、設定ファイル`config/exception.php`を変更する必要があります。例外処理クラスは`Webman\Exception\ExceptionHandlerInterface` インターフェースを実装する必要があります。
```php
interface ExceptionHandlerInterface
{
    /**
     * ログの記録
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * レンダリングレスポンス
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```



## レスポンスのレンダリング
例外処理クラスの`render`メソッドは、レスポンスのレンダリングに使用されます。

`config/app.php`ファイルで`debug`の値が`true`の場合（以下、`app.debug=true`とします）、詳細な例外情報が返され、それ以外の場合は簡潔な例外情報が返されます。

JSONレスポンスが期待される場合、例外情報はJSON形式で返されます。以下のようになります。
```json
{
    "code": "500",
    "msg": "例外情報"
}
```
`app.debug=true`の場合、JSONデータには詳細な呼び出しスタックを返すための`trace`フィールドが追加されます。

デフォルトの例外処理ロジックを変更するために、独自の例外処理クラスを作成することができます。

# ビジネス例外 BusinessException
時には、ネストされた関数内でリクエストを中断してクライアントにエラーメッセージを返したいと思うことがあります。その場合は`BusinessException`をスローすることでそれができます。
例：

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->chackInpout($request->post());
        return response('hello index');
    }
    
    protected function chackInpout($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('パラメータエラー', 3000);
        }
    }
}
```

上記の例では、以下のようなレスポンスが返されます。
```json
{"code": 3000, "msg": "パラメータエラー"}
```

> **注意点**
> ビジネス例外BusinessExceptionはビジネスのtry/catchでキャッチする必要はありません。フレームワークは自動的にキャッチし、リクエストタイプに応じた適切な出力を行います。

## カスタムビジネス例外

上記のレスポンスが要件を満たさない場合、例えば`msg`を`message`に変更したい場合は、`MyBusinessException`をカスタマイズすることができます。

次の内容で `app/exception/MyBusinessException.php` を作成します。
```php
<?php

namespace app\exception;

use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class MyBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        // JSONリクエストはJSONデータを返す
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // JSON以外のリクエストはページを返す
        return new Response(200, [], $this->getMessage());
    }
}
```
これで、ビジネスで下記のようにコールすると
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('パラメータエラー', 3000);
```
JSONリクエストであれば、以下のようなJSONが返されます。
```json
{"code": 3000, "message": "パラメータエラー"}
```

> **ヒント**
> BusinessExceptionは予測可能なビジネス例外（例：ユーザー入力パラメータエラーなど）なので、フレームワークは致命的なエラーとは見なしませんし、ログを記録しません。

## まとめ
現在のリクエストを中断してクライアントに情報を返す際には、`BusinessException`を使用することを検討してください。
