# Controller

Create a new controller file `app/controller/FooController.php`.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

When accessing `http://127.0.0.1:8787/foo`, the page will return `hello index`.

When accessing `http://127.0.0.1:8787/foo/hello`, the page will return `hello webman`.

Of course, you can modify the route rules through route configuration, see [Routing](route.md).

> **Note**
> If a 404 error occurs, please open `config/app.php` and set `controller_suffix` to `Controller`, then reboot.

## Controller Suffix
Starting from webman version 1.3, you can set the controller suffix in `config/app.php`. If `controller_suffix` in `config/app.php` is set to an empty string `''`, the controller will look like this

`app\controller\Foo.php`.

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

It is strongly recommended to set the controller suffix to `Controller`, as this can avoid conflicts with model class names and increase security.

## Explanation
- The framework will automatically pass the `support\Request` object to the controller, through which you can access user input data (such as get, post, header, cookie, etc.), see [Request](request.md)
- Controllers can return numbers, strings, or `support\Response` objects, but cannot return any other type of data.
- The `support\Response` object can be created using helper functions such as `response()`, `json()`, `xml()`, `jsonp()`, `redirect()`, etc.

## Controller Lifecycle

When `controller_reuse` in `config/app.php` is set to `false`, each request will initialize a corresponding controller instance, and the controller instance will be destroyed after the request ends, similar to the operating mechanism of traditional frameworks.

When `controller_reuse` in `config/app.php` is set to `true`, all requests will reuse the controller instance, meaning that once the controller instance is created, it will reside in memory and be reused for all requests.

> **Note**
> Disabling controller reuse requires webman>=1.4.0, which means that before 1.4.0, all requests would reuse the controller instance by default, and could not be changed.

> **Note**
> When controller reuse is enabled, requests should not modify any properties of the controller, as these changes will affect subsequent requests, for example

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    protected $model;
    
    public function update(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->update();
        return response('ok');
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->delete();
        return response('ok');
    }
    
    protected function getModel($id)
    {
        // This method will retain the model after the first request, update?id=1
        // If a subsequent request, delete?id=2, is made, the data of 1 will be deleted
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **Note**
> Returning data in the `__construct()` constructor of the controller will have no effect, for example

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // Returning data in the constructor will have no effect, and the browser will not receive this response
        return response('hello'); 
    }
}
```

## Difference Between Non-reuse and Reuse of Controllers
The differences are as follows:

#### Non-reuse of controllers
Each request will create a new instance of the controller, which will be released and the memory will be reclaimed after the request ends. Non-reuse of controllers is similar to traditional frameworks, and conforms to the habits of most developers. Because of the repeated creation and destruction of controllers, the performance will be slightly lower than that of reusing controllers (10% lower performance in helloworld stress testing, but this can be almost negligible with actual business).

#### Reuse of controllers
If reusing, a controller will only be created once for each process, and the controller instance will not be released after the request ends, but instead will be reused for subsequent requests in the same process. Reusing controllers leads to better performance, but does not conform to the habits of most developers.

#### Cases where controller reuse cannot be used

When a request will change the properties of the controller, controller reuse cannot be enabled, because these changes to the properties will affect subsequent requests.

Some developers like to do some initialization for each request in the controller constructor `__construct()`, in which case controller reuse cannot be enabled, as the constructor for the current process will only be called once, not for every request.

