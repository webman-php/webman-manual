# Lifecycle

## Process Lifecycle
- Each process has a long lifecycle
- Each process runs independently without interference
- Each process can handle multiple requests within its lifecycle
- When a process receives the `stop`, `reload`, or `restart` command, it will exit and end the current lifecycle

> **Note**
> Each process is independent and does not interfere with others, meaning each process maintains its own resources, variables, class instances, etc. This means each process has its own database connection, and certain singletons are initialized once for each process, resulting in multiple initializations for multiple processes.

## Request Lifecycle
- Each request generates a `$request` object
- The `$request` object is recycled after the request is processed

## Controller Lifecycle
- Each controller is instantiated only once per process, but multiple times across processes (except when controller reuse is disabled, see [Controller Lifecycle](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F))
- Controller instances are shared among multiple requests within the current process (except when controller reuse is disabled)
- Controller lifecycle ends when the process exits (except when controller reuse is disabled)

## Variable Lifecycle
Since webman is based on PHP, it fully adheres to PHP's variable recycling mechanism. Temporary variables generated in business logic, including instances of classes created using the `new` keyword, are automatically recycled after the end of a function or method and do not require manual `unset` release. This means that the development experience with webman is basically the same as with traditional frameworks. For example, the `$foo` instance in the following example will be automatically released after the execution of the index method:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // Assuming there is a Foo class
        return response($foo->sayHello());
    }
}
```
If you want an instance of a class to be reused, you can save the class to a static property of the class or to a property of a long-lived object (such as a controller), or use the `Container` container's `get` method to initialize the class instance, for example:
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

The `Container::get()` method is used to create and save class instances, and when called with the same parameters again, it will return the previously created class instance.

> **Note**
> `Container::get()` can only initialize instances without constructor parameters. `Container::make()` can create instances with constructor parameters, but unlike `Container::get()`, `Container::make()` does not reuse instances, meaning it always returns a new instance even when called with the same parameters.

# About Memory Leaks
In the vast majority of cases, our business logic does not result in memory leaks (few users have reported experiencing memory leaks). We just need to be mindful of not allowing long-lived array data to expand infinitely. Please consider the following code:
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
By default, controllers have a long lifecycle (except when controller reuse is disabled), and similarly, the `$data` array property of the controller also has a long lifecycle. As the `foo/index` request continues to add data to the `$data` array, the increasing number of elements in the array can lead to memory leaks.

For more information, please refer to [Memory Leaks](./memory-leak.md)