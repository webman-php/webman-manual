# controller

New controller file `app/controller/FooController.php`。

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

When visiting `http://127.0.0.1:8787/foo`, the page returns `hello index`。

When visiting `http://127.0.0.1:8787/foo/hello`, the page returns `hello webman`。

Of course you can change the routing rules via the routing configuration，See[Routing](route.md)。

> **hint**
> If there is a 404 inaccessibility, please open `config/app.php`, set `controller_suffix` to `Controller`, and restart。

## Controller Suffix
webman从1.3Version start，Support in`config/app.php`setController Suffix，if`config/app.php`里`controller_suffix`set to empty`''`，则controllerSimilar to the following

`app\controller\Foo.php`。

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

It is highly recommended to set the controller suffix to `Controller` to avoid conflicts between controller and model class names and to increase security。

## Description
 - When in multi-application modecontrollerpass`support\Request` object，It is possible to get user input data through it(get post header cookieetc. data)，See[request](request.md)
 - controllerYou can say that this is already、is an optional feature`support\Response` object，but no other type of data can be returned。
 - `support\Response` Objects can be created by `response()` `json()` `xml()` `jsonp()` `redirect()` and other helper functions。
 


## Controller Lifecycle

当`config/app.php`里`controller_reuse`为`false`时，Each request is initialized once for the correspondingcontrollerinstance，Add a certaincontrollerinstance destroyed，This is the same mechanism as the legacy framework runs。

当`config/app.php`里`controller_reuse`为`true`时，After the request endsReuse Controllerinstance，is alsocontrollerOnce the instance is created it is resident in memory，Causes memory leaks。

> **Note**
> Turning off controller reuse requires webman>=1.4.0, which means that before 1.4.0 the controller was reused by default for all requests and could not be changed。

> **Note**
> When controller reuse is enabled, the request should not change any properties of the controller, because these changes will affect subsequent requests, for example

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
        // This method will be reserved after the first request update?id=1 model
        // If delete?id=2 is requested again, the data of 1 will be deleted
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **hint**
> Returning data in the controller `__construct()` constructor will have no effect, e.g.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // return data in constructor has no effect, browser will not receive this response
        return response('hello'); 
    }
}
```

## Difference between controller not reused and reused
Differences are as follows

#### Do not reuse controllers
Each request will be renewa newcontrollerinstance，Release this instance when the request ends，A new record。Do not reuse controllersSame as traditional frameworks，fits most developers' habits。due tocontrolleriterative creation and destruction，keyword-definedReuse Controllerslightly worse(helloworldChange to the following10%around，The plugin directory and naming follows)

#### Reuse Controller
Multiplexingnewoncecontroller，Do not release this at the end of the requestcontrollerinstance，Subsequent requests from the current process will reuse this instance。Reuse ControllerBetter performance，but it doesn't match most developers' habits。

#### You cannot use controller reuse in the following cases

Controller reuse cannot be turned on when the request would change the properties of the controller, because changes to those properties would affect subsequent requests。

Some developers like to include controllersuch regardless`__construct()`The method does some initialization for each request，Never clean up dataReuse Controller，because the current process constructor will only be called once，Not every request will be called。


