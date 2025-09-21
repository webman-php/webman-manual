# 控制器

根据PSR4规范，控制器类命名空间以`plugin\{插件标识}`开头，例如

新建控制器文件 `plugin/foo/app/controller/FooController.php`。

```php
<?php
namespace plugin\foo\app\controller;

use support\Request;

class FooController
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

当访问 `http://127.0.0.1:8787/app/foo/foo` 时，页面返回 `hello index`

当访问 `http://127.0.0.1:8787/app/foo/foo/hello` 时，页面返回 `hello webman`


## url访问
应用插件url地址路径都以`/app`开头，后面接插件标识，然后是具体的控制器及方法。
例如`plugin\foo\app\controller\UserController`url地址是 `http://127.0.0.1:8787/app/foo/user`
