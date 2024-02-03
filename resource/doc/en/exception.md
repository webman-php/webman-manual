# Exception Handling

## Configuration
`config/exception.php`
```php
return [
    // Configure the exception handling class here
    '' => support\exception\Handler::class,
];
```
In multi-application mode, you can configure the exception handling class separately for each application. See [Multi-Application](multiapp.md) for more information.

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
	public function render(Request $request, Throwable $e) : Response;
}
```


## Rendering Response
The `render` method in the exception handling class is used to render the response.

If the `debug` value in the configuration file `config/app.php` is set to `true` (hereinafter referred to as `app.debug=true`), detailed exception information will be returned; otherwise, concise exception information will be returned.

If the request expects a JSON response, the exception information will be returned in JSON format, for example:
```json
{
	"code": "500",
	"msg": "Exception message"
}
```
If `app.debug=true`, the JSON data will include an additional `trace` field to return a detailed call stack.

You can write your own exception handling class to change the default exception handling logic.

# Business Exception BusinessException
Sometimes, we may want to terminate a request within a nested function and return an error message to the client. In this case, we can achieve this by throwing a `BusinessException`. For example:

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
            throw new BusinessException('Parameter error', 3000);
        }
    }
}
```

The above example will return:
```json
{"code": 3000, "msg": "Parameter error"}
```

> **Note**
> Business exceptions (BusinessException) do not need to be caught by the business, as the framework will automatically catch them and return the appropriate output based on the type of request.

## Custom Business Exception
If the above response does not meet your requirements, for example, if you want to change the `msg` to `message`, you can create a custom `MyBusinessException`.

Create `app/exception/MyBusinessException.php` with the following content:
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

Now when the business calls:
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Parameter error', 3000);
```
JSON requests will receive a JSON response similar to the following:
```json
{"code": 3000, "message": "Parameter error"}
```

> **Tip**
> Because BusinessException is a type of business exception (e.g., user input parameter error) that is predictable, the framework does not consider it a fatal error and does not log it.

## Summary
Consider using `BusinessException` whenever you want to interrupt the current request and return information to the client.