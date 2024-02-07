## Handling Static Files
webman supports access to static files, which are placed in the `public` directory. For example, accessing `http://127.0.0.8787/upload/avatar.png` actually accesses `{main project directory}/public/upload/avatar.png`.

> **Note**
> Starting from webman 1.4, it supports application plugins. Accessing static files starting with `/app/xx/filename` actually accesses the `public` directory of the application plugin. In other words, webman >=1.4.0 does not support accessing directories under `{main project directory}/public/app/`.
> For more information, please refer to [Application Plugins](./plugin/app.md).

### Disabling Static File Support
If static file support is not needed, open `config/static.php` and change the `enable` option to false. After disabling, accessing all static files will return a 404 error.

### Changing the Static File Directory
By default, webman uses the `public` directory as the static file directory. To modify this, please edit the `public_path()` helper function in `support/helpers.php`.

### Static File Middleware
webman comes with a static file middleware located at `app/middleware/StaticFile.php`.
Sometimes, we need to perform some processing on static files, such as adding cross-origin headers, or prohibiting access to files starting with a dot (`.`). This middleware can be used for such tasks.

The contents of `app/middleware/StaticFile.php` are similar to the following:
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // Prohibit access to hidden files starting with .
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Add cross-origin headers
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
If this middleware is required, it needs to be enabled in the `middleware` option in `config/static.php`.
