# Response

The response is actually a `support\Response` object. To make it easier to create this object, webman provides some helper functions.

## Returning Any Response

**Example**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

The `response` function is implemented as follows:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

Alternatively, you can create an empty `response` object first and then use `$response->cookie()`, `$response->header()`, `$response->withHeaders()`, or `$response->withBody()` to set the response content at the appropriate location.
```php
public function hello(Request $request)
{
    // Create an object
    $response = response();
    
    // .... Business logic omitted
    
    // Set cookie
    $response->cookie('foo', 'value');
    
    // .... Business logic omitted
    
    // Set HTTP header
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... Business logic omitted

    // Set the data to be returned
    $response->withBody('Data to be returned');
    return $response;
}
```

## Returning JSON

**Example**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```
The `json` function is implemented as follows:
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```

## Returning XML

**Example**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        $xml = <<<XML
               <?xml version='1.0' standalone='yes'?>
               <values>
                   <truevalue>1</truevalue>
                   <falsevalue>0</falsevalue>
               </values>
               XML;
        return xml($xml);
    }
}
```
The `xml` function is implemented as follows:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## Returning a View
Create a new file `app/controller/FooController.php` as follows:

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return view('foo/hello', ['name' => 'webman']);
    }
}
```

Create a new file `app/view/foo/hello.html` as follows:

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

## Redirect
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return redirect('/user');
    }
}
```
The `redirect` function is implemented as follows:
```php
function redirect($location, $status = 302, $headers = [])
{
    $response = new Response($status, ['Location' => $location]);
    if (!empty($headers)) {
        $response->withHeaders($headers);
    }
    return $response;
}
```

## Setting Headers
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Header Value' 
        ]);
    }
}
```
You can also use the `header` and `withHeaders` methods to set headers individually or in batches.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'Header Value 1',
            'X-Header-Tow' => 'Header Value 2',
        ]);
    }
}
```
You can also set the header in advance and set the data to be returned at the end.
```php
public function hello(Request $request)
{
    // Create an object
    $response = response();
    
    // .... Business logic omitted
  
    // Set HTTP header
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... Business logic omitted

    // Set the data to be returned
    $response->withBody('Data to be returned');
    return $response;
}
```

## Setting Cookies
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->cookie('foo', 'value');
    }
}
```
Alternatively, you can set the cookie in advance and set the data to be returned at the end.
```php
public function hello(Request $request)
{
    // Create an object
    $response = response();
    
    // .... Business logic omitted
    
    // Set cookie
    $response->cookie('foo', 'value');
    
    // .... Business logic omitted

    // Set the data to be returned
    $response->withBody('Data to be returned');
    return $response;
}
```
The `cookie` method takes the following complete parameters:
`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## Returning File Stream
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->file(public_path() . '/favicon.ico');
    }
}
```

- webman supports sending very large files.
- For large files (over 2M), webman does not read the entire file into memory at once, but instead reads and sends the file in segments at the appropriate time.
- webman optimizes file read and send speed based on the client's receive speed, ensuring the fastest file transfer while minimizing memory usage.
- Data transmission is non-blocking and does not affect other request processing.
- The `file` method automatically adds the `if-modified-since` header and checks the `if-modified-since` header on the next request. If the file has not been modified, it returns 304 to save bandwidth.
- The file being sent is automatically sent to the browser with the appropriate `Content-Type` header.
- If the file does not exist, it automatically converts to a 404 response.

## Downloading a File
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'filename.ico');
    }
}
```
The `download` method is almost identical to the `file` method, except that:
1. After setting the download file name, the file will be downloaded instead of displayed in the browser.
2. The `download` method does not check the `if-modified-since` header.

## Getting Output
Some libraries directly print file content to standard output, which means the data is printed to the command line terminal and not sent to the browser. In this case, we need to capture the data into a variable using `ob_start();` `ob_get_clean();`, and then send the data to the browser. For example:

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // Create an image
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

        // Start capturing the output
        ob_start();
        // Output the image
        imagejpeg($im);
        // Get the image content
        $image = ob_get_clean();
        
        // Send the image
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```