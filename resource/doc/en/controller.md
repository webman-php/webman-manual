# Controllers

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

When accessing `http://127.0.0.1:8787/foo`, the page returns `hello index`.

When accessing `http://127.0.0.1:8787/foo/hello`, the page returns `hello webman`.

Of course, you can change the routing rules through the routing configuration, see [Routing](route.md).

> **Tips:**
> If a 404 error occurs, please open `config/app.php`, set `controller_suffix` to `Controller`, and restart.

## Controller Suffix
Starting from version 1.3, webman supports setting the controller suffix in `config/app.php`. If `config/app.php` has `controller_suffix` set to empty `''`, the controller will be like this:

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

It is strongly recommended to set the controller suffix to `Controller`, which can avoid conflicts between controller and model class names and improve security at the same time.

## Explanation
- The framework will automatically pass the `support\Request` object to the controller, which can be used to get user input data (such as get, post, header, cookie, and other data), see [Request](request.md).
- The controller can return numbers, strings, or `support\Response` objects, but cannot return other types of data.
- `support\Response` objects can be created using helper functions such as `response()`, `json()`, `xml()`, `jsonp()`, `redirect()`, etc.

## Controller Lifecycle

When `config/app.php` has `controller_reuse` set to `false`, each request will initialize the corresponding controller instance once, and the controller instance will be destroyed after the request is finished. This is the same as the running mechanism of traditional frameworks.

When `config/app.php` has `controller_reuse` set to `true`, all requests will reuse the controller instance, that is, the controller instance will reside in memory once it is created, and all requests will reuse it.

> **Note:**
> To disable controller reuse, webman >= 1.4.0 is required. In other words, before version 1.4.0, the controller is reused by default for all requests and cannot be changed.

> **Note:**
> When controller reuse is enabled, requests should not change any properties of the controller because these changes will affect subsequent requests. For example:

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
        // This method will retain the model after the first request "update?id=1".
        // If another request "delete?id=2" is made, the data of 1 will be deleted.
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **Tips:**
> Returning data in the constructor `__construct()` of a controller will have no effect. For example:

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // The response in the constructor has no effect, the browser will not receive this response.
        return response('hello'); 
    }
}
```

## Difference Between Controller Non-Reuse and Reuse
The differences are as follows:

#### Non-Reuse Controller
A new controller instance will be created for each request, and the instance will be released and memory will be reclaimed after the request is finished. Non-reuse controllers are similar to traditional frameworks and conform to the habits of most developers. Due to the repeated creation and destruction of controllers, the performance will be slightly worse than that of reuse controllers (the performance difference can be ignored for a simple hello world test, but may be noticeable for more complex tests).

#### Reuse Controller
If the controller is reused, a controller is only created once for each process, and the instance of this controller is not released after the request is finished. The subsequent requests of the current process will reuse this instance. Reuse controllers have better performance but do not conform to the habits of most developers.

#### The following cases cannot be used with controller reuse:

When a request will change the properties of the controller, controller reuse should not be enabled, because the changes to these properties will affect subsequent requests.

Some developers like to do some initialization for each request in the constructor `__construct()` of the controller. In this case, controller reuse cannot be used because the constructor of the current process will only be called once, not for each request.
