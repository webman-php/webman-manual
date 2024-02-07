# 控制器

根據PSR4規範，控制器類命名空間以`plugin\{插件標識}`開頭，例如

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

當訪問 `http://127.0.0.1:8787/app/foo/foo` 時，頁面返回 `hello index`

當訪問 `http://127.0.0.1:8787/app/foo/foo/hello` 時，頁面返回 `hello webman`


## url訪問
應用插件url地址路徑都以`/app`開頭，後面接插件標識，然後是具體的控制器及方法。
例如`plugin\foo\app\controller\UserController`url地址是 `http://127.0.0.1:8787/app/foo/user`
