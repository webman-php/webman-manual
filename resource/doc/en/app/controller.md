# Controllers

According to the PSR4 specification, the namespace of a controller class starts with `plugin\{plugin_identifier}`, for example:

Create a new controller file `plugin/foo/app/controller/FooController.php`.

```php
<?php
namespace plugin\foo\app\controller;

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

When accessing `http://127.0.0.1:8787/app/foo/foo`, the page returns `hello index`.

When accessing `http://127.0.0.1:8787/app/foo/foo/hello`, the page returns `hello webman`.
## URL Access
The URL path for application plugin starts with `/app`, followed by the plugin identifier, and then the specific controller and method.

For example, the URL path for `plugin\foo\app\controller\UserController` is `http://127.0.0.1:8787/app/foo/user`.
