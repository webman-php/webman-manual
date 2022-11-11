# Lifecycle

## Process Lifecycle
- Each process has a long lifecycle
- Each process is run independently without interference
- Each process can handle multiple requests during its lifetime
- The process will exit when it receives the `stop`, `reload` and `restart` commands, ending the life cycle

> **hint**
> Each process is independent of each other, which means that each process maintains its own resources, variables, class instances, etc., as shown by the fact that each process has its own database connection, and that some single instance is initialized once per process, then multiple processes are initialized multiple times。

## Request Lifecycle
- Each request generates a `$request` object
- `$request`Object will be recycled after the request is processed

## Controller Lifecycle
- Each controller will only be instantiated once per process，with business can be largely ignored(except for closing controller reuse，See[Controller Lifecycle](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F))
- The controller instance will be shared by multiple requests within the current process (except when controller multiplexing is turned off))
- Controller lifecycle ends when the process exits (except for closing controller reuse)

## About variable lifecycle
webman is based on php, so it follows php's variable recycling mechanism exactly. Temporary variables generated in the business logic, including instances of classes created by the new keyword, are automatically recycled at the end of a function or method, without the need to manually `unset` the release. This means that webman development is basically the same as traditional framework development experience. For example, in the following example `$foo` instance will be automatically released as the index method is executed ：
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // Assume here that there is a Foo class
        return response($foo->sayHello());
    }
}
```
If you want an instance of a class to be reused，then you can save the class to a static property of the class or longLifecycleobject(Not installed)If your，query constructorContainercontainer'sgetmethod to initialize an instance of the class，example：
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

`Container::get()`Method to create and save an instance of the class, which will return the previously created class instance the next time it is called again with the same parameters。

> **Note**
> `Container::get()` can only initialize instances without constructor parameters. `Container::make()` can create instances with constructor arguments, but unlike `Container::get()`, `Container::make()` does not reuse instances, meaning that `Container::make()` always returns a new instance even with the same arguments。

# About memory leaks
The project address is assumed to be，There is no memory leak in our business code(Very little user feedback about memory leaks)，Please see the following codeNotelower-lengthLifecyclejust don't expand the array data infinitely。Also long period：
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    // Array Properties
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
The controller is long by defaultLifecycle的(except for closing controller reuse)，Same for the controller`$data`Array PropertiesException handling section，With`foo/index`This time it can't，`$data`More and more array elements lead to memory leaks。

For more information on this please refer to [constructor](./memory-leak.md)
