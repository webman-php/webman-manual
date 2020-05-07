# 控制器


新建控制器文件 `app\controller\Foo.php`。

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

当访问 `http://127.0.0.1:8787/foo` 时，页面返回 `hello index`。

当访问 `http://127.0.0.1:8787/foo/hello` 时，页面返回 `hello webman`。

当然你可以通过路由配置来更改路由规则，参见[路由](route.md)。

## 注意事项
 - 控制器里必须返回一个`support\Response` 对象，不要返回其它类型的数据。
 - `support\Response` 对象可以通过`response()` `json()` `xml()` `jsonp()` 等助手函数创建。
 - webman启动时会自动扫描加载app下的controller目录下的所有php文件。
 - webman会自动复用控制器实例，以减少反复初始化控制器带来的额外开销。

 

