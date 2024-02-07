# Lifecycle

## Process Lifecycle
- Each process has a long lifecycle.
- Each process runs independently, without interference.
- Each process can handle multiple requests within its lifecycle.
- When a process receives the `stop`, `reload`, or `restart` command, it will exit and end its current lifecycle.

> **Tip**
> Each process runs independently without interference, which means each process maintains its own resources, variables, class instances, etc. This is reflected in each process having its own database connection, and some singletons being initialized once for each process, leading to multiple initializations across processes.

## Request Lifecycle
- Each request generates a `$request` object.
- The `$request` object is recycled after the request is processed.

## Controller Lifecycle
- Each controller is instantiated only once per process, but multiple instantiations are possible across processes (unless controller reusing is disabled, see [Controller Lifecycle](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F)).
- Controller instances are shared among multiple requests within the current process (unless controller reusing is disabled).
- The controller's lifecycle ends when the process exits (unless controller reusing is disabled).

## Variable Lifecycle
webman is developed based on PHP, so it fully complies with PHP's variable recycling mechanism. Temporary variables created in business logic, including instances of classes created with the `new` keyword, are automatically recycled after a function or method ends, without the need for manual `unset` releases. This means that the development experience with webman is essentially the same as with traditional frameworks. For example, in the following example, the `$foo` instance will be automatically released when the `index` method finishes executing:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // Assuming there is a Foo class here
        return response($foo->sayHello());
    }
}
```
If you want an instance of a class to be reused, you can save the class to the class's static property or to a property of a long-lived object (such as a controller). You can also use the `Container` container's `get` method to initialize the class instance, for example:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Container;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = Container::get(Foo::class);
        return response($foo->sayHello());
    }
}
```
The `Container::get()` method is used to create and save class instances, so that the same parameters used in a subsequent call will return the previously created class instance.

> **Note**
> `Container::get()` can only initialize instances without constructor parameters. `Container::make()` can create instances with constructor parameters, but unlike `Container::get()`, `Container::make()` does not reuse instances, meaning that it always returns a new instance, even with the same parameters.

# About Memory Leaks
In most cases, our business code does not cause memory leaks (very few users have reported memory leaks). We just need to be mindful not to infinitely expand long-lived array data. Consider the following code:
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    // Array property
    public $data = [];
    
    public function index(Request $request)
    {
        $this->data[] = time();
        return response('hello index');
    }

    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```
By default, the controller has a long lifecycle (unless controller reusing is disabled), and similarly, the `$data` array property of the controller also has a long lifecycle. As the `foo/index` request continues to add elements to the `$data` array, it leads to a memory leak.

For more information, please refer to [Memory Leaks](./memory-leak.md).
