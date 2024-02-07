# Context

The `support\Context` class is used to store request context data. When a request is completed, the corresponding context data is automatically deleted. This means that the lifecycle of context data is tied to the lifecycle of the request. `support\Context` supports the Fiber, Swoole, and Swow coroutine environments.

For more information, refer to [webman coroutine](./fiber.md).

# Interfaces
The context provides the following interfaces.

## Set Context Data
```php
Context::set(string $name, $mixed $value);
```

## Get Context Data
```php
Context::get(string $name = null);
```

## Delete Context Data
```php
Context::delete(string $name);
```

> **Note**
> The framework automatically calls the `Context::destroy()` interface to destroy context data after the request is completed. The business cannot manually call `Context::destroy()`.

# Examples
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        return Context::get('name');
    }
}
```

# Note
When using coroutines, you should not store **request-related state data** in global variables or static variables, as this may cause global data contamination. The correct approach is to use Context to store and retrieve them.
