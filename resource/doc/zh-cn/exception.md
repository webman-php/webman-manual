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

