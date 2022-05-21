# 自定义404 500错误页面

## 自定义404
webman在404时会自动返回`public/404.html`里面的内容，所以开发者可以直接更改`public/404.html`文件。

如果你想动态控制404的内容时，例如在ajax请求时返回json数据 `{"code:"404", "msg":"404 not found"}`，页面请求时返回`app/view/404.html`模版，请参考如下示例

**创建文件`app/view/404.html`**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 not found</title>
</head>
<body>
here <?=htmlspecialchars($some)?>
</body>
</html>
```

**在`config/route.php`中加入如下代码：**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // ajax请求时返回json
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // 页面请求返回404.html模版
    return view('404', ['some' => 'data']);
});
```

## 自定义500
**新建`app/view/500.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
自定义错误模版：
<?=$some?>
</body>
</html>
```

**新建**app/exception/Handler.php**(如目录不存在请自行创建)**
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
        // ajax请求返回json数据
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // 页面请求返回500.html模版
        return view('500', ['exception' => $exception]);
    }
}
```

**配置`config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```