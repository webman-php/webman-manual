## カスタム404
webmanは404の際に自動的に`public/404.html`内の内容を返しますので、開発者は直接`public/404.html`ファイルを変更することができます。

404の内容を動的に制御したい場合、例えばajaxリクエスト時にはJSONデータ `{"code:"404", "msg":"404 not found"}` を返し、ページリクエスト時には`app/view/404.html`テンプレートを返したい場合は、以下の例を参考にしてください。

> 以下はPHPネイティブテンプレートを例に取っていますが、その他のテンプレート`twig` `blade` `think-template`についても同様の原理が適用されます。

**`app/view/404.html`ファイルを作成**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 not found</title>
</head>
<body>
<?=htmlspecialchars($error)?>
</body>
</html>
```

**`config/route.php`に以下のコードを追加:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // ajaxリクエスト時にはjsonを返す
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // ページリクエスト時には404.htmlテンプレートを返す
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## カスタム500
**`app/view/500.html`を作成**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
カスタムエラーテンプレート：
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**`app/exception/Handler.php`を新規作成**(ディレクトリが存在しない場合は自分で作成してください)
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * レンダリングと返却
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // ajaxリクエスト時にはjsonデータを返す
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // ページリクエスト時には500.htmlテンプレートを返す
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**`config/exception.php`を設定**
```php
return [
    '' => \app\exception\Handler::class,
];
```
