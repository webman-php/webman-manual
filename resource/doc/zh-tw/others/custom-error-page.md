## 自訂404錯誤頁面
當webman發生404錯誤時，會自動返回`public/404.html`檔案中的內容，因此開發者可以直接修改`public/404.html`文件。

如果您想要動態控制404錯誤頁面的內容，例如在ajax請求時返回json數據 `{"code:"404", "msg":"404 not found"}`，在頁面請求時返回`app/view/404.html`模板，請參考以下示例

> 以下以php原生模板為例，其他模板`twig` `blade` `think-tmplate`原理類似

**建立文件`app/view/404.html`**
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

**在`config/route.php`中加入以下代碼：**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // ajax請求時返回json
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // 頁面請求返回404.html模板
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## 自訂500錯誤頁面
**新建`app/view/500.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
自訂錯誤模板：
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**新建`app/exception/Handler.php`(如路徑不存在請自行創建)**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * Render 返回
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // ajax請求返回json數據
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // 頁面請求返回500.html模板
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**配置`config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```
