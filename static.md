## 处理静态文件

webman底层当路由匹配不上时候会自动尝试匹配处理了静态文件访问问题。

### 1. 首先我们确定静态文件夹

官方案例默认是应用根目录 **public** 文件夹，自定义请修改 **helpers.php** 文件内 **public_path()** 助手函数

### 2. 配置 static.php

**enable** 选项必须设置为 **true** 才会起作用，可以在 **middleware** 选项中配置静态文件处理中间件对请求和响应做更多自定义的处理。

```
.
├── app                           应用目录
│   └── view                      视图目录
│       └── index                 子目录，一般与控制器对应
│           └── view.html         视图文件
├── config                        配置目录
│   └── static.php                static配置
├── public                        静态资源目录
│   └── css                       css文件
│           └── main.css          main.css
│   └── js                        js文件
│           └── jquery.min.js     jquery.js
```

## static.php 配置

```php
<?php
return [
    'enable'     => true, // 是否支持静态文件
    'middleware' => [     // 静态文件中间件
        support\middleware\StaticFile::class,
    ],
];
```

## StaticFile 中间件

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