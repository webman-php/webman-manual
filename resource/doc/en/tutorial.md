# Simple example

## Return String
**New Controller**

Create a new file `app/controller/UserController.php` as follows

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Get the name parameter from the get request, or return it if no name parameter is passed$default_name
        $name = $request->get('name', $default_name);
        // Return string to browser
        return response('hello ' . $name);
    }
}
```

**Access**

Access in browser `http://127.0.0.1:8787/user/hello?name=tom`

The browser will return `hello tom`

## Returnjson
Change the file `app/controller/UserController.php` as follows

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

Access in browser `http://127.0.0.1:8787/user/hello?name=tom`

The browser will return `{"code":0,"msg":"ok","data":"tom""}`

Using the json helper function to return data will automatically add a header header `Content-Type: application/json`

## Returnxml
Similarly, using the helper function `xml($xml)` will return an `xml` response with a `Content-Type: text/xml` header。

where the `$xml` parameter can be either an `xml` string or a `SimpleXMLElement` object

## Returnjsonp
Synonym，We just need to slightly `jsonp($data, $callback_name = 'callback')` 将Returna`jsonp`response。

## Return View
Change the file `app/controller/UserController.php` as follows

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

Create a new file `app/view/user/hello.html` as follows

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

Access in browser `http://127.0.0.1:8787/user/hello?name=tom`
Will return an html page with the content `hello tom`。

note：webmanExcept for the constructorphpNative syntax as template。If you want to use other views see[view](view.md)。


