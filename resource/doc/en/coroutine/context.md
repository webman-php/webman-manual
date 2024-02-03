# Context

The `support\Context` class is used to store request context data, which is automatically deleted when the request is completed. In other words, the context data has the same lifecycle as the request. `support\Context` supports Fiber, Swoole, and Swow coroutine environments.

For more information, refer to [webman Coroutine](./fiber.md).

## Interfaces

The context provides the following interfaces:

## Set context data

```php
Context::set(string $name, $mixed $value);
```

## Get context data

```php
Context::get(string $name = null);
```

## Delete context data

```php
Context::delete(string $name);
```

> **Note**
> The framework automatically calls the `Context::destroy()` interface to destroy context data after the request completes, and business logic should not manually call `Context::destroy()`.

## Example

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

## Caution

**When using coroutines**, do not store **request-specific state data** in global variables or static variables, as this may cause global data contamination. The correct approach is to use `Context` to store and retrieve them.