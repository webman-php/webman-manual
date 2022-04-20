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
 
 
## 生命周期
 - 控制器仅在被需要的时候才会被实例化。
 - 控制器一旦实例化后遍会常驻内存直到进程销毁。
 - 由于控制器实例常驻内存，所以不会每个请求都会初始化一次控制器。

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
 
## 控制器钩子 `beforeAction()` `afterAction()`
在传统框架中，每个请求都会实例化一次控制器，所以很多开发者`__construct()`方法中做一些请求前的准备工作。

而webman由于控制器常驻内存，无法在`__construct()`里做这些工作，不过webman提供了更好的解决方案`beforeAction()` `afterAction()`，它不仅让开发者可以介入到请求前的流程中，而且还可以介入到请求后的处理流程中。

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

## 生命周期
 - 控制器仅在被需要的时候才会被实例化。
 - 控制器一旦实例化后遍会常驻内存直到进程销毁。
 - 由于控制器实例常驻内存，所以不会每个请求都会初始化一次控制器。
 
## 控制器钩子 `beforeAction()` `afterAction()`
在传统框架中，每个请求都会实例化一次控制器，所以很多开发者`__construct()`方法中做一些请求前的准备工作。

而webman由于控制器常驻内存，无法在`__construct()`里做这些工作，不过webman提供了更好的解决方案`beforeAction()` `afterAction()`，它不仅让开发者可以介入到请求前的流程中，而且还可以介入到请求后的处理流程中。

### 安装action-hook插件
`composer require webman/action-hook`

> **注意**
> 插件需要webman>=1.2，如果你的版本低于1.2可以参考这里[手动配置action-hook](https://www.workerman.net/a/1303)

### 使用 `beforeAction()` `afterAction()`
```php
<?php
namespace app\controller;
use support\Request;
class Index
{
    /**
     * 该方法会在请求前调用 
     */
    public function beforeAction(Request $request)
    {
        echo 'beforeAction';
        // 若果想终止执行Action就直接返回Response对象，不想终止则无需return
        // return response('终止执行Action');
    }

    /**
     * 该方法会在请求后调用
     */
    public function afterAction(Request $request, $response)
    {
        echo 'afterAction';
        // 如果想串改请求结果，可以直接返回一个新的Response对象
        // return response('afterAction'); 
    }

    public function index(Request $request)
    {
        return response('index');
    }
}
```

**`beforeAction`说明：**
 - 在当前控制器被执行前调用
 - 框架会传递一个`Request`对象给`beforeAction`，开发者可以从中获得用户输入
 - 如需终止执行当前控制器，则只需要在`beforeAction`里返回一个`Response`对象，比如`return redirect('/user/login');`
 - 无需终止执行当前控制器时，不要返回任何数据
 
**`afterAction`说明：**
 - 在当前控制器被执行后调用
 - 框架会传递`Request`对象以及`Response`对象给`afterAction`，开发者可以从中获得用户输入以及控制器执行后返回的响应结果
 - 开发者可以通过`$response->rawBody()`获得响应内容
 - 开发者可以通过`$response->getHeader()`获得响应的header头
 - 开发者可以通过`$response->getStatusCode()`获得响应的http状态码
 - 开发者可利用`$response->withBody()` `$response->header()` `$response->withStatus()`串改响应，也可以创建并返回一个新的`Response`对象替代原响应
 
> **提示**
> 你可以创建一个控制器基类，这个基类实现`beforeAction()` `afterAction()`方法。其他控制器继承这个基类，这样就不必每个控制器都实现一遍beforeAction()` `afterAction()`方法。
 

