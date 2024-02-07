# Documentation

## Obtaining the request object
Webman will automatically inject the request object into the first parameter of the action method, for example:

**Example**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Get the 'name' parameter from the get request, or return $default_name if the parameter is not passed
        $name = $request->get('name', $default_name);
        // Return a string to the browser
        return response('hello ' . $name);
    }
}
```

Through the `$request` object, we can obtain any data related to the request.

**Sometimes we may want to get the `$request` object in other classes, in which case we can use the helper function `request()`**

## Getting request parameters from get
**Get the entire get array**
```php
$request->get();
```
If the request does not contain get parameters, it returns an empty array.

**Get a value from the get array**
```php
$request->get('name');
```
If the get array does not contain this value, it returns null.

You can also pass a default value as the second parameter to the get method. If the corresponding value is not found in the get array, it will return the default value. For example:
```php
$request->get('name', 'tom');
```

## Getting request parameters from post
**Get the entire post array**
```php
$request->post();
```
If the request does not contain post parameters, it returns an empty array.

**Get a value from the post array**
```php
$request->post('name');
```
If the post array does not contain this value, it returns null.

Similar to the get method, you can also pass a default value as the second parameter to the post method. If the corresponding value is not found in the post array, it will return the default value. For example:
```php
$request->post('name', 'tom');
```

## Getting the raw request body from post
```php
$post = $request->rawBody();
```
This function is similar to the `file_get_contents("php://input")` operation in `php-fpm`. It is used to obtain the raw HTTP request body, which is very useful when obtaining post request data in a non-`application/x-www-form-urlencoded` format.

## Getting header
**Get the entire header array**
```php
$request->header();
```
If the request does not contain header parameters, it returns an empty array. Note that all keys are lowercase.

**Get a value from the header array**
```php
$request->header('host');
```
If the header array does not contain this value, it returns null. Note that all keys are lowercase.

Similar to the get method, you can also pass a default value as the second parameter to the header method. If the corresponding value is not found in the header array, it will return the default value. For example:
```php
$request->header('host', 'localhost');
```

## Getting cookie
**Get the entire cookie array**
```php
$request->cookie();
```
If the request does not contain cookie parameters, it returns an empty array.

**Get a value from the cookie array**
```php
$request->cookie('name');
```
If the cookie array does not contain this value, it returns null.

Similar to the get method, you can also pass a default value as the second parameter to the cookie method. If the corresponding value is not found in the cookie array, it will return the default value. For example:
```php
$request->cookie('name', 'tom');
```

## Getting all inputs
Includes the collection of `post` and `get`.
```php
$request->all();
```

## Getting a specific input value
Get a value from the collection of `post` and `get`.
```php
$request->input('name', $default_value);
```

## Getting partial input data
Get part of the data from the collection of `post` and `get`.
```php
// Get an array of username and password, ignoring the key if it does not exist
$only = $request->only(['username', 'password']);
// Get all inputs except 'avatar' and 'age'
$except = $request->except(['avatar', 'age']);
```

## Getting uploaded files
**Get the entire uploaded file array**
```php
$request->file();
```

Form example:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

The format returned by `$request->file()` is similar to the following:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
It is an array of instances of `webman\Http\UploadFile`. The `webman\Http\UploadFile` class inherits the native PHP [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) class and provides some practical methods.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // Whether the file is valid, for example true|false
            var_export($spl_file->getUploadExtension()); // Uploaded file extension, for example 'jpg'
            var_export($spl_file->getUploadMimeType()); // Uploaded file MIME type, for example 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Get the upload error code, for example UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Uploaded file name, for example 'my-test.jpg'
            var_export($spl_file->getSize()); // Get file size, for example 13364, in bytes
            var_export($spl_file->getPath()); // Get the upload directory, for example '/tmp'
            var_export($spl_file->getRealPath()); // Get the temporary file path, for example `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Note:**

- The file will be renamed to a temporary file after upload, similar to `/tmp/workerman.upload.SRliMu`
- The uploaded file size is limited by [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html), which is 10M by default and can be changed in the `config/server.php` file by modifying `max_package_size`.
- The temporary files will be automatically cleared after the request ends
- If there are no uploaded files in the request, `$request->file()` returns an empty array
- The uploaded files do not support the `move_uploaded_file()` method, please use the `$file->move()` method instead, as shown in the example below

### Getting a specific uploaded file
```php
$request->file('avatar');
```
If the file exists, it returns the corresponding file's instance of `webman\Http\UploadFile`, otherwise it returns null.

**Example**
```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function file(Request $request)
    {
        $file = $request->file('avatar');
        if ($file && $file->isValid()) {
            $file->move(public_path().'/files/myfile.'.$file->getUploadExtension());
            return json(['code' => 0, 'msg' => 'upload success']);
        }
        return json(['code' => 1, 'msg' => 'file not found']);
    }
}
```

## Getting host
Get the host information of the request.
```php
$request->host();
```
If the request address is non-standard, i.e., not on port 80 or 443, the host information may include the port, for example, `example.com:8080`. If the port information is not needed, the first parameter can be set to `true`.

```php
$request->host(true);
```

## Getting the request method
```php
 $request->method();
```
The return value may be one of `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, or `HEAD`.

## Getting the request URI
```php
$request->uri();
```
Returns the URI of the request, including the path and query string parts.

## Getting the request path
```php
$request->path();
```
Returns the path part of the request.

## Getting the request query string
```php
$request->queryString();
```
Returns the query string part of the request.
## Getting request URL

The `url()` method returns the URL without `Query` parameters.

```php
$request->url();
```

It returns something like `//www.workerman.net/workerman-chat`.

The `fullUrl()` method returns the URL with `Query` parameters.

```php
$request->fullUrl();
```

It returns something like `//www.workerman.net/workerman-chat?type=download`.

> **Note**
> `url()` and `fullUrl()` do not include the protocol part (neither `http` nor `https`). This is because using `//example.com` in the browser will automatically recognize the current site's protocol and send the request using either http or https.

If you are using an nginx proxy, add `proxy_set_header X-Forwarded-Proto $scheme;` to the nginx configuration, [refer to nginx proxy](others/nginx-proxy.md). This way, you can use `$request->header('x-forwarded-proto');` to determine whether it is http or https, for example:

```php
echo $request->header('x-forwarded-proto'); // outputs http or https
```

## Getting request HTTP version

```php
$request->protocolVersion();
```

Returns a string `1.1` or `1.0`.

## Getting request session ID

```php
$request->sessionId();
```

Returns a string consisting of letters and numbers.

## Getting client's IP address

```php
$request->getRemoteIp();
```

## Getting client's port

```php
$request->getRemotePort();
```

## Getting client's real IP address

```php
$request->getRealIp($safe_mode = true);
```

When the project is using a proxy (such as nginx), using `$request->getRemoteIp()` often returns the proxy server's IP (like `127.0.0.1` or `192.168.x.x`) instead of the client's real IP. In such cases, you can try using `$request->getRealIp()` to obtain the client's real IP.

`$request->getRealIp()` will attempt to obtain the real IP from the `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, and `via` fields in the HTTP header.

> Since HTTP headers can be easily spoofed, the client IP obtained from this method is not 100% reliable, especially when `$safe_mode` is set to `false`. A more reliable method for obtaining the client's real IP through a proxy is to know the secure proxy server IP and explicitly know which HTTP header carries the real IP. If the IP returned by `$request->getRemoteIp()` confirms a known secure proxy server, then use `$request->header('Header carrying the real IP')` to obtain the real IP.

## Getting server's IP address

```php
$request->getLocalIp();
```

## Getting server's port

```php
$request->getLocalPort();
```

## Checking if it is an AJAX request

```php
$request->isAjax();
```

## Checking if it is a PJAX request

```php
$request->isPjax();
```

## Checking if it expects JSON response

```php
$request->expectsJson();
```

## Checking if the client accepts JSON response

```php
$request->acceptJson();
```

## Getting the requested plugin name
Returns an empty string `''` for non-plugin requests.
```php
$request->plugin;
```
> This feature requires webman>=1.4.0

## Getting the requested application name
Returns an empty string `''` for single applications, and the application name for [multiple applications](multiapp.md).
```php
$request->app;
```
> Because closures do not belong to any application, the request from a closure route will always return an empty string `''`.
> Refer to [Route](route.md) for closure routes.

## Getting the requested controller class name
Gets the class name corresponding to the controller
```php
$request->controller;
```
Returns something like `app\controller\IndexController`.

> Because closures do not belong to any controller, the request from a closure route will always return an empty string `''`.
> Refer to [Route](route.md) for closure routes.

## Getting the requested method name
Gets the method name corresponding to the request's controller method
```php
$request->action;
```
Returns something like `index`.

> Because closures do not belong to any controller, the request from a closure route will always return an empty string `''`.
> Refer to [Route](route.md) for closure routes.
