## Custom404
webmanIn 404 will automatically return the contents of `public/404.html`, so the developer can directly change the `public/404.html` file。

If you want to control dynamically404Other languages，For example inajaxReturn on requestjsondata `{"code:"404", "msg":"404 not found"}`，pageReturn on request`app/view/404.html`Template，Please refer to the following examples

> The following is an example of a php native template, other templates `twig` `blade` `think-tmplate` have similar principles

**Create files`app/view/404.html`**
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

**Add the following code to `config/route.php`：**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // ajaxReturn on requestjson
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // The page request returns a 404.html template
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## Custom500
**New`app/view/500.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
Customize error templates：
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**New**app/exception/Handler.php**(Please create your own directory if it does not exist)**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * Render Return
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // ajaxRequest to return json data
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // Page request returns 500.html template
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**Configure`config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```
