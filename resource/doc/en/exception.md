# Exception Handling

## Configuration
`config/exception.php`
```php
return [
    // Configure the exception handling class here
    '' => support\exception\Handler::class,
];
```
In multi-app mode, you can configure an exception handling class for each application individually. Refer to [Multi-App](multiapp.md) for more details.


## Default Exception Handling Class
In webman, exceptions are handled by the `support\exception\Handler` class by default. You can modify the configuration file `config/exception.php` to change the default exception handling class. The exception handling class must implement the `Webman\Exception\ExceptionHandlerInterface` interface.
```php
interface ExceptionHandlerInterface
{
    /**
     * Log the exception
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * Render the response
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e): Response;
}
```

## Rendering Responses
The `render` method in the exception handling class is used to render the response.

If the `debug` value is set to `true` in the configuration file `config/app.php` (referred to as `app.debug=true` from now on), the detailed exception information will be returned. Otherwise, a simplified version of the exception information will be returned.

If the request expects a JSON response, the exception information will be returned in JSON format, like this:
```json
{
    "code": "500",
    "msg": "Exception message"
}
```
If `app.debug=true`, the JSON data will also include a `trace` field that contains detailed stack trace information.

You can write your own exception handling class to modify the default exception handling logic.

# BusinessException
Sometimes, we want to stop the request inside a nested function and return an error message to the client. In such cases, you can throw a `BusinessException`.

For example:
```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->checkInput($request->post());
        return response('hello index');
    }
    
    protected function checkInput($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('Parameter error', 3000);
        }
    }
}
```
In the above example, it will return:
```json
{"code": 3000, "msg": "Parameter error"}
```

> **Note**
> The BusinessException does not need to be caught by the business logic. It will be automatically caught by the framework and the appropriate output will be returned based on the request type.

## Custom Business Exception

If the above response does not meet your requirements, for example, if you want to change `msg` to `message`, you can define a custom `MyBusinessException`.

Create a new file `app/exception/MyBusinessException.php` with the following content:
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
        // Return JSON data for JSON requests
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // Return a page for non-JSON requests
        return new Response(200, [], $this->getMessage());
    }
}
```
Now, when you throw a `MyBusinessException` in your code like this:
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Parameter error', 3000);
```
A JSON request will receive a JSON response like this:
```json
{"code": 3000, "message": "Parameter error"}
```

> **Note**
> BusinessException exceptions are business exceptions (e.g., user input parameter errors) and are expected. Therefore, the framework does not consider them as fatal errors and does not log them.

## Summary
You can consider using `BusinessException` whenever you want to interrupt the current request and return information to the client.
