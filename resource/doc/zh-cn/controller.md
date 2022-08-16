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

## 说明
 - 框架会自动向控制器传递`support\Request` 对象，通过它可以获取用户输入数据(get post header cookie等数据)，参见[请求](request.md)
 - 控制器里可以返回数字、字符串或者`support\Response` 对象，但是不能返回其它类型的数据。
 - `support\Response` 对象可以通过`response()` `json()` `xml()` `jsonp()` `redirect()`等助手函数创建。
 

## 控制器后缀
webman支持设置控制器后缀，这可以避免控制器和模型命名会冲突。例如在config/app.php中设置`controller_suffix`为`Controller`时Foo控制器文件及内容类似如下(key `controller_suffix`不存在时请自行创建)。

**控制器文件 `app\controller\FooController.php`。**

```php
<?php
namespace app\controller;

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

> **注意**
> 此特性需要webman>=1.3

## 控制器生命周期

webman控制器一旦初始化便常驻内存，后面的请求会复用它。

当然我们可以选择不复用控制器，这时候我们需要给`config/app.php`增加一个`'controller_reuse' => false`的配置。

不复用控制器时，每个请求都会重新初始化一个新的控制器实例，开发和可以在控制器`__construct()`构造函数中做一些请求处理前的初始化工作。

> **注意**
> controller_reuse 配置需要 webman-framework >= 1.4.0
> 当 webman-framework < 1.4.0时，可以选择安装 [action-hook插件](https://www.workerman.net/plugin/30)，使用`beforeAction()`钩子做请求处理前初始化工作。

> **提示**
> 更多内容请参考[生命周期](./others/lifecycle.md)


## 资源型控制器
资源型路由规则，参见[路由](route.md)。
 ```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    /**
    * 首页/列表页只支持get访问 /foo
    * @param Request $request
    * @return \support\Response
    */
    public function index(Request $request)
    {
        return response('hello index');
    }
    /**
    * 新增只支持get访问 /foo/create
    * @param Request $request
    * @return \support\Response
    */
    public function create(Request $request)
    {
        return response('hello webman');
    }
    /**
    * 新增提交只支持post提交 /foo
    * @param Request $request
    * @return \support\Response
    */
    public function store(Request $request)
    {
        $params = $request->all();
        return response('hello webman');
    }
    /**
    * 获取详情只支持get访问 /foo/{id}
    * @param Request $request
    * @return \support\Response
    */
    public function show(Request $request,$id)
    {
        return response('hello webman');
    }
    /**
    * 编辑获取数据只支持get访问 /foo/{id}/edit
    * @param Request $request
    * @return \support\Response
    */
    public function edit(Request $request,$id)
    {
        return response('hello webman');
    }
    /**
    * 编辑提交只支持PUT提交 /foo/{id}
    * @param Request $request
    * @return \support\Response
    */
    public function update(Request $request,$id)
    {
        $params = $request->all();
        return response('hello webman');
    }
    /**
    * 删除只支持DELETE /foo/{id}
    * @param Request $request
    * @return \support\Response
    */
    public function destroy(Request $request,$id)
    {
         //获取id数组 
        $ids = is_array($id) ? $id : (is_string($id) ? explode(',', $id) : func_get_args());
        return response('hello webman');
    }
    /**
    * 恢复软删除只支持PUT /foo/{id}/recovery
    * @param Request $request
    * @return \support\Response
    */
    public function recovery(Request $request,$id)
    {
        //获取id数组 
        $ids = is_array($id) ? $id : (is_string($id) ? explode(',', $id) : func_get_args());
        $params = $request->all();
        return response('hello webman');
    }
}
```
