## 处理静态文件

webman支持静态文件访问。

### 关闭静态文件支持
如果不需要静态文件支持，打开`config/static.php`将`enable`选项改成false。

### 更改静态文件目录
webman默认使用public目录为静态文件目录。如需修改请更改`support/helpers.php`的中的`public_path()`助手函数。

### 静态文件中间件
webman自带一个静态文件中间件，位置``
有时我们需要对静态文件做一些处理，例如给静态文件增加跨域http头，禁止访问以点(`.`)开头的文件等等。


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

## 示例

```html
<!doctype html>
<html>
<head>
 <meta charset="utf-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="/favicon.ico" />
 <title>webman</title>
    <!--js文件示例-->
 <script type="text/javascript" src="/js/jquery.min.js"></script>
    <!--css文件示例-->
 <link rel="stylesheet" href="/css/main.css">
</head>
<body>
hello {$name}
</body>
</html>
```