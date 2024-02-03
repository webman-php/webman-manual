## 自定義404
webman在404時會自動返回`public/404.html`裡面的內容，所以開發者可以直接更改`public/404.html`文件。

如果你想動態控制404的內容時，例如在ajax請求時返回json數據 `{"code:"404", "msg":"404 not found"}`，頁面請求時返回`app/view/404.html`模版，請參考如下示例

> 以下以php原生模版為例，其它模版`twig` `blade` `think-tmplate` 原理類似

**創建文件`app/view/404.html`**
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

**在`config/route.php`中加入如下代碼：**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // ajax請求時返回json
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // 頁面請求返回404.html模版
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## 自定義500
**新建`app/view/500.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
自定義錯誤模版：
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**新建**app/exception/Handler.php**(如目錄不存在請自行創建)**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * 渲染返回
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
        // 頁面請求返回500.html模版
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