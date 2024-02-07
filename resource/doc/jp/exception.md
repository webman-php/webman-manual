# 例外処理

## 設定
`config/exception.php`
```php
return [
    // ここで例外処理クラスを設定します
    '' => support\exception\Handler::class,
];
```
複数のアプリケーションモードの場合、それぞれのアプリケーションに個別の例外処理クラスを設定することができます。詳細は[マルチアプリケーション](multiapp.md)を参照してください。

## デフォルトの例外処理クラス
webmanでは、例外はデフォルトで `support\exception\Handler` クラスによって処理されます。デフォルトの例外処理クラスを変更するには、`config/exception.php`の設定ファイルを変更する必要があります。例外処理クラスは `Webman\Exception\ExceptionHandlerInterface` インターフェースを実装する必要があります。

```php
interface ExceptionHandlerInterface
{
    /**
     * ログを記録する
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * レスポンスをレンダリングする
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```

## レスポンスのレンダリング
例外処理クラス内の `render` メソッドはレスポンスをレンダリングするために使用されます。

`config/app.php`の`debug`値が`true`の場合（以下では`app.debug=true`とします）、詳細な例外情報が返されます。それ以外の場合は、簡潔な例外情報が返されます。

JSONレスポンスが期待される場合、例外情報はJSON形式で返されます。例：
```json
{
    "code": "500",
    "msg": "例外情報"
}
```
`app.debug=true`の場合、JSONデータには詳細な呼び出しスタックが追加されます。

デフォルトの例外処理ロジックを変更するには、独自の例外処理クラスを作成することができます。

# ビジネス例外 BusinessException
時には、入れ子になった関数内でリクエストを中断し、クライアントにエラーメッセージを返したい場合があります。その場合は`BusinessException`をスローすることができます。例：

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
            throw new BusinessException('パラメーターエラー', 3000);
        }
    }
}
```
上記の例では、次のようなJSONが返されます。
```json
{"code": 3000, "msg": "パラメーターエラー"}
```

> **注意**
> ビジネス例外BusinessExceptionはtry-catchでキャッチする必要はありません。フレームワークが自動的にキャッチし、リクエストタイプに適した出力を返します。

## カスタムビジネス例外

上記の応答が要件に適合しない場合、`msg`を`message`に変更したい場合は、`MyBusinessException`をカスタマイズできます。

`app/exception/MyBusinessException.php`を新規作成し、以下のようにします。
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
        // JSONリクエストではJSONデータを返す
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // JSON以外のリクエストではページを返す
        return new Response(200, [], $this->getMessage());
    }
}
```
すると、ビジネスが以下のように呼び出された場合、
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('パラメーターエラー', 3000);
```
JSONリクエストは次のようなJSONを受け取ります。
```json
{"code": 3000, "message": "パラメーターエラー"}
```

> **注意**
> BusinessException例外はビジネス例外（たとえば、ユーザー入力パラメーターエラー）なので、予測可能であり、フレームワークは致命的なエラーとはみなさず、ログを記録しません。

## まとめ
現在のリクエストを中断し、クライアントに情報を返したい場合は、`BusinessException`を使用することを検討してください。
