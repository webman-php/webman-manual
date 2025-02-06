## 处理静态文件
webman支持静态文件访问，静态文件都放置于`public`目录下，例如访问 `http://127.0.0.8787/upload/avatar.png`实际上是访问`{主项目目录}/public/upload/avatar.png`。

> **注意**
> 以`/app/xx/文件名`开头的静态文件访问实际是访问应用插件的`public`目录，也就是说不支持 `{主项目目录}/public/app/`下的目录访问。
> 更多请参考[应用插件](./plugin/app.md)

### 关闭静态文件支持
如果不需要静态文件支持，打开`config/static.php`将`enable`选项改成false。关闭后所有静态文件的访问会返回404。

### 更改静态文件目录
webman默认使用public目录为静态文件目录。如需修改请更改`support/helpers.php`的中的`public_path()`助手函数。

### 静态文件中间件
webman自带一个静态文件中间件，位置`app/middleware/StaticFile.php`。
有时我们需要对静态文件做一些处理，例如给静态文件增加跨域http头，禁止访问以点(`.`)开头的文件可以使用这个中间件

`app/middleware/StaticFile.php` 内容类似如下：
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
        // 禁止访问.开头的隐藏文件
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // 增加跨域http头
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
如果需要此中间件时，需要到`config/static.php`中`middleware`选项中开启。
