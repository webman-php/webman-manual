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

> **注意**
> 此特性需要webman>=1.3

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

## 控制器生命周期

当`config/app.php`里`controller_reuse`为`false`时，每个请求都会初始化一次对应的控制器实例，请求结束后控制器实例销毁，这与传统框架运行机制相同。

当`config/app.php`里`controller_reuse`为`true`时，所有请求将复用控制器实例，也就是控制器实例一旦创建遍常驻内存，所有请求复用。

> **注意**
> 关闭控制器复用需要webman>=1.4.0，也即是说在1.4.0之前控制器默认是所有请求复用的，切无法更改。

> **注意**
> 开启控制器复用时，请求不应该更改控制器的任何属性，因为这些更改将影响后续请求，例如

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    protected $model;
    
    public function update(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->update();
        return response('ok');
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->delete();
        return response('ok');
    }
    
    protected function getModel($id)
    {
        // 该方法将在第一次请求 update?id=1 后会保留下 model
        // 如果再次请求 delete?id=2 时，会删除 1 的数据
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **提示**
> 在控制器`__construct()`构造函数中return数据不会有任何效果，例如

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // 构造函数中return数据没有任何效果，浏览器不会收到此响应
        return response('hello'); 
    }
}
```

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
