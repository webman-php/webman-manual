## Custom 404
webman automatically returns the content in `public/404.html` when a 404 error occurs, so developers can directly modify the `public/404.html` file.

If you want to dynamically control the content of the 404 error, for example, return JSON data `{"code:"404", "msg":"404 not found"}` for AJAX requests, and return the `app/view/404.html` template for page requests, please refer to the following example:

> The example below uses PHP native templates as an example. Other templates such as `twig`, `blade`, `think-template` have similar principles.

**Create the file `app/view/404.html`**
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

**Add the following code in `config/route.php`:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // Return JSON for AJAX requests
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // Return the 404.html template for page requests
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## Custom 500
**Create `app/view/500.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
Custom error template:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**Create app/exception/Handler.php** (create the directory if it does not exist)
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * Render the response
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // Return JSON data for AJAX requests
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // Return the 500.html template for page requests
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**Configure `config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```
