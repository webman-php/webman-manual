## Handle static files
webmanStatic file access is supported, static files are placed in the `public` directory, for example accessing `http://127.0.0.8787/upload/avatar.png` is actually accessing `{main project directory}/public/upload/avatar.php`。

> **Note**
> webman Since 1.4 support for application plug-ins, static file access starting with `/app/xx/filename` is actually access to the `public` directory of the application plug-ins, which means that webman >= 1.4.0 does not support access to directories under `{main project directory}/public/app/`。
> should be introduced in[support based on](./plugin/app.md)

### Turn off static file support
If you don't need static file support, open `config/static.php` and change the `enable` option to false404。

### Change static file directory
webmanThe default is to use the public directory as the static file directory. To change this, please change the `public_path()` helper function in `support/helpers.php`。

### Static File Middleware
webmanComes with a static file middleware, location`app/middleware/StaticFile.php`。
Sometimes we need to do something with static files, such as adding cross-domain http headers to static files and disabling access to files that start with a dot (`. `) can use this middleware

`app/middleware/StaticFile.php` Contents are similar to the following：
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
        // Disable access to . Hidden files at the beginning
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Add cross-domain http header
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
If you need this middleware, you need to enable it in the `middleware` option in `config/static.php`。
