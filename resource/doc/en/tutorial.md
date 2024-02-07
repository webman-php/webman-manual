# Simple Example

## Return a String
**Create a Controller**

Create a new file `app/controller/UserController.php` as follows:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Get the 'name' parameter from the get request, and return $default_name if the 'name' parameter is not passed
        $name = $request->get('name', $default_name);
        // Return a string to the browser
        return response('hello ' . $name);
    }
}
```

**Access**

Access `http://127.0.0.1:8787/user/hello?name=tom` in the browser

The browser will return `hello tom`

## Return JSON
Modify the file `app/controller/UserController.php` as follows:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return json([
            'code' => 0, 
            'msg' => 'ok', 
            'data' => $name
        ]);
    }
}
```

**Access**

Access `http://127.0.0.1:8787/user/hello?name=tom` in the browser

The browser will return `{"code":0,"msg":"ok","data":"tom"}`

Using the json helper function to return data will automatically add a `Content-Type: application/json` header.

## Return XML
Similarly, using the helper function `xml($xml)` will return an `xml` response with the `Content-Type: text/xml` header.

The `$xml` parameter can be an `xml` string or a `SimpleXMLElement` object.

## Return JSONP
Similarly, using the helper function `jsonp($data, $callback_name = 'callback')` will return a `jsonp` response.

## Return View
Modify the file `app/controller/UserController.php` as follows:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return view('user/hello', ['name' => $name]);
    }
}
```

Create a new file `app/view/user/hello.html` as follows:

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

Access `http://127.0.0.1:8787/user/hello?name=tom` in the browser
It will return an html page with the content `hello tom`.

Note: By default, webman uses native PHP syntax as the template. If you want to use other views, see [Views](view.md).
