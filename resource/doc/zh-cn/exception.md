# 异常处理

## 配置
`config/exception.php`
```php
return [
    // 这里配置异常处理类
    '' => support\exception\Handler::class,
];
```
多应用模式时，你可以为每个应用单独配置异常处理类，参见[多应用](multiapp.md)


## 默认异常处理类
webman中异常默认由 `support\exception\Handler` 类来处理。可修改配置文件`config/exception.php`来更改默认异常处理类。异常处理类必须实现`Webman\Exception\ExceptionHandlerInterface` 接口。
```php
interface ExceptionHandlerInterface
{
    /**
     * 记录日志
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * 渲染返回
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```



## 渲染响应
异常处理类中的`render`方法是用来渲染响应的。

如果配置文件`config/app.php`中`debug`值为`true`(以下简称`app.debug=true`)，将返回详细的异常信息，否则将返回简略的异常信息。

如果请求期待是json返回，则返回的异常信息将以json格式返回，类似
```json
{
    "code": "500",
    "msg": "异常信息"
}
```
如果`app.debug=true`，json数据里会额外增加一个`trace`字段返回详细的调用栈。

你可以编写自己的异常处理类来更改默认异常处理逻辑。

# 业务异常 BusinessException
有时候我们想在某个嵌套函数里终止请求并返回一个错误信息给客户端，这时可以通过抛出`BusinessException`来做到这点。
例如：

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->chackInpout($request->post());
        return response('hello index');
    }
    
    protected function chackInpout($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('参数错误', 3000);
        }
    }
}
```

以上示例会返回一个
```json
{"code": 3000, "msg": "参数错误"}
```

> **注意**
> 业务异常BusinessException不需要业务try捕获，框架会自动捕获并根据请求类型返回合适的输出。

## 自定义业务异常

如果以上响应不符合你的需求，例如想把`msg`要改为`message`，可以自定义一个`MyBusinessException`

新建 `app/exception/MyBusinessException.php` 内容如下
```php
<?php

namespace app\exception;

use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class MyBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        // json请求返回json数据
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // 非json请求则返回一个页面
        return new Response(200, [], $this->getMessage());
    }
}
```

这样当业务调用
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('参数错误', 3000);
```
json请求将收到一个类似如下的json返回
```json
{"code": 3000, "message": "参数错误"}
```

> **提示**
> 因为BusinessException异常属于业务异常(例如用户输入参数错误)，它是可预知的，所以框架并不会认为它是致命错误，并不会记录日志。

## 总结
在任何想中断当前请求并返回信息给客户端的时候可以考虑使用`BusinessException`异常。