# Response
Responseclosure routing see`support\Response`object，To facilitate the creation of this object，webmanSome helper functions are provided。

## Return an arbitrary response

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

responseThe function is implemented as follows：
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

You can also create an empty one first`response`object，and then utilize it in the appropriate location`$response->cookie()` `$response->header()` `$response->withHeaders()` `$response->withBody()`SetupReturncontent。
```php
public function hello(Request $request)
{
    // Create an object
    $response = response();
    
    // .... Business Logic Omitted
    
    // Setupcookie
    $response->cookie('foo', 'value');
    
    // .... Business Logic Omitted
    
    // Set http header
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... Business Logic Omitted

    // Set the data to be returned
    $response->withBody('Returned data');
    return $response;
}
```

## Returnjson
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
jsonThe function is implemented as follows
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```


## Returnxml
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

xmlThe function is implemented as follows：
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## Return View
Create a new file `app/controller/FooController.php` as follows

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

Create a new file `app/view/foo/hello.html` as follows

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

redirectThe function is implemented as follows：
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

## headerSetup
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
You can also use the `header` and `withHeaders` methods to set them individually or in bulkheader。
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
You can also set the header in advance and set the data that will be returned at the end。
```php
public function hello(Request $request)
{
    // Create an object
    $response = response();
    
    // .... Business Logic Omitted
  
    // Set http header
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... Business Logic Omitted

    // Set the data to be returned
    $response->withBody('Returned data');
    return $response;
}
```

## Setupcookie

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

You can also set cookies in advance and set the data to be returned at the end。
```php
public function hello(Request $request)
{
    // Create an object
    $response = response();
    
    // .... Business Logic Omitted
    
    // Setupcookie
    $response->cookie('foo', 'value');
    
    // .... Business Logic Omitted

    // Set the data to be returned
    $response->withBody('Returned data');
    return $response;
}
```

cookieThe full parameters of the method are as follows：

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## Return File Stream
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

- webmanSupport sending oversized files
- For large files (over 2M), webman does not read the whole file into memory at once, but reads it in sections and sends it at the right time
- webmanOptimize the file reading and sending speed according to the client's receiving speed to ensure the fastest file delivery while minimizing memory usage
- Data sending is non-blocking and does not affect other request processing
- fileand `if-modified-since`header and detect it on the next request`if-modified-since`头，If the file is unmodified then it is straightforwardReturn304consistent behavior of methods
- The sent file will be automatically sent to the browser using the appropriate `Content-Type` hair
- If the file does not exist, it will automatically go to a 404 response


## Download file
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'Optional filename.ico');
    }
}
```
downloadMethods andfilein order to save bandwidthdownloadMethods are generally used to download and save files，has numerousSetupwill be automatically converted to。downloadrequest share, etc.`if-modified-since`头。Other withfileAlways remember to turn it on。


## Get Output
Some libraries print the file contents directly to the standard output, that is, the data will be printed in the command line terminal and not sent to the browser, then we need to capture the data into a variable via `ob_start();` `ob_get_clean();` and then send the data to the browser, for example：

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // Create image
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

        // Start getting output
        ob_start();
        // Output image
        imagejpeg($im);
        // Get image content
        $image = ob_get_clean();
        
        // Send image
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```