# Exception Handling

## Configure
`config/exception.php`
```php
return [
    // Configure exception handling classes here
    '' => support\exception\Handler::class,
];
```
We see a，You can do this for each application individuallyConfigureException Handling类，See[more applications](multiapp.md)


## Default Exception Handling Class
webmanThe class is used internally `support\exception\Handler` hereinafter referred to as。modifiableConfigurefile`config/exception.php`to changeDefault Exception Handling Class。Exception HandlingSee below`Webman\Exception\ExceptionHandlerInterface` interface。
```php
interface ExceptionHandlerInterface
{
    /**
     * Record Log
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * Render Return
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```



## Render Response
The `render` method in the exception handling class is used to render the response。

ifConfigurefile`config/app.php`中`debug`value`true`(backend push`app.debug=true`)，Any of the methods in Exception Information，Otherwise, an abbreviatedException Information。

If the request expects a json return, the returned exception information will be returned in json format, similar to 
```json
{"code" : "500", "msg" : "Exception Information"}
```
If `app.debug=true`, an additional `trace` field will be added to the json data to return a detailed call stack。

You can write your own exception handling classes to change the default exception handling logic。

