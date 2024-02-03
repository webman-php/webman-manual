# Documentation

##  Obtain the Request Object
Webman automatically injects the request object into the first parameter of the action method. For example:

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
        // Get the name parameter from the GET request, and return $default_name if the name parameter is not passed
        $name = $request->get('name', $default_name);
        // Return a string to the browser
        return response('hello ' . $name);
    }
}
```

Through the `$request` object, we can get any data related to the request.

**Sometimes we may want to obtain the `$request` object in other classes, and in such cases, we can simply use the helper function `request()`**.

## Obtain GET Request Parameters

**Get the entire GET array**
```php
$request->get();
```
If the request does not have GET parameters, an empty array is returned.

**Get a value from the GET array**
```php
$request->get('name');
```
If the value is not found in the GET array, null is returned.

You can also pass a default value as the second parameter to the `get` method. If the value corresponding to the key is not found in the GET array, the default value is returned. For example:
```php
$request->get('name', 'tom');
```

## Obtain POST Request Parameters
**Get the entire POST array**
```php
$request->post();
```
If the request does not have POST parameters, an empty array is returned.

**Get a value from the POST array**
```php
$request->post('name');
```
If the value is not found in the POST array, null is returned.

Similar to the `get` method, you can pass a default value as the second parameter to the `post` method. If the value corresponding to the key is not found in the POST array, the default value is returned. For example:
```php
$request->post('name', 'tom');
```

## Obtain the Raw POST Body
```php
$post = $request->rawBody();
```
This feature is similar to the `file_get_contents("php://input")` operation in `php-fpm`. It is used to obtain the raw HTTP request body and is useful when retrieving non-`application/x-www-form-urlencoded` format POST request data.

## Obtain Headers
**Get the entire header array**
```php
$request->header();
```
If the request does not have header parameters, an empty array is returned. Note that all keys are in lowercase.

**Get a value from the header array**
```php
$request->header('host');
```
If the value is not found in the header array, null is returned. Note that all keys are in lowercase.

Similar to the `get` method, you can pass a default value as the second parameter to the `header` method. If the value corresponding to the key is not found in the header array, the default value is returned. For example:
```php
$request->header('host', 'localhost');
```

## Obtain Cookies
**Get the entire cookie array**
```php
$request->cookie();
```
If the request does not have cookie parameters, an empty array is returned.

**Get a value from the cookie array**
```php
$request->cookie('name');
```
If the value is not found in the cookie array, null is returned.

Similar to the `get` method, you can pass a default value as the second parameter to the `cookie` method. If the value corresponding to the key is not found in the cookie array, the default value is returned. For example:
```php
$request->cookie('name', 'tom');
```

## Obtain All Inputs
Includes the collection of both `post` and `get`.

```php
$request->all();
```

## Obtain a Specific Input Value
Get a value from the collection of `post` and `get` parameters.

```php
$request->input('name', $default_value);
```

## Obtain Partial Input Data
Get part of the data from the collection of `post` and `get` parameters.

```php
// Get an array consisting of username and password, ignoring the keys if not found
$only = $request->only(['username', 'password']);
// Get all inputs except for avatar and age
$except = $request->except(['avatar', 'age']);
```

## Obtain Uploaded Files
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

The format returned by `$request->file()` is similar to:

```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
It is an array of `webman\Http\UploadFile` instances. The `webman\Http\UploadFile` class extends the PHP built-in [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) class and provides some convenient methods.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // Check if the file is valid, returns true or false
            var_export($spl_file->getUploadExtension()); // Get the uploaded file extension, for example, 'jpg'
            var_export($spl_file->getUploadMimeType()); // Get the uploaded file MIME type, for example, 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Get the upload error code, for example, UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_NO_FILE, UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Get the uploaded file name, for example, 'my-test.jpg'
            var_export($spl_file->getSize()); // Get the file size, for example, 13364, in bytes
            var_export($spl_file->getPath()); // Get the upload directory, for example, '/tmp'
            var_export($spl_file->getRealPath()); // Get the temporary file path, for example, `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Note:**

- After the file is uploaded, it will be given a temporary file name, similar to `/tmp/workerman.upload.SRliMu`.
- The size of the uploaded file is subject to the [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html) limit, which is 10M by default and can be changed in the `config/server.php` file by modifying `max_package_size`.
- The temporary file will be automatically cleared after the request ends.
- If there are no uploaded files in the request, `$request->file()` returns an empty array.
- Uploaded files do not support the `move_uploaded_file()` method. Please use the `$file->move()` method instead, as shown in the following example.

### Obtain a Specific Uploaded File
```php
$request->file('avatar');
```
If the file exists, it returns the corresponding `webman\Http\UploadFile` instance for the file, otherwise it returns null.

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

## Obtain Host
Get the host information of the request.

```php
$request->host();
```
If the request address is non-standard, i.e., not on ports 80 or 443, the host information may include the port, for example, `example.com:8080`. If the port is not needed, the first parameter can be set to `true`.

```php
$request->host(true);
```

## Obtain the Request Method
```php
 $request->method();
```
The return value may be one of `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, or `HEAD`.

## Obtain the Request URI
```php
$request->uri();
```
Returns the URI of the request, including the path and query string parts.

## Obtain the Request Path
```php
$request->path();
```
Returns the path part of the request.

## Obtain the Request Query String
```php
$request->queryString();
```
Returns the query string part of the request.

## Obtain the Request URL
The `url()` method returns the URL without the `query` parameter.

```php
$request->url();
```
Returns a URL similar to `//www.workerman.net/workerman-chat`.

The `fullUrl()` method returns the URL with the `query` parameter.

```php
$request->fullUrl();
```
Returns a URL similar to `//www.workerman.net/workerman-chat?type=download`.

> **Note**
> `url()` and `fullUrl()` do not include the protocol part (i.e., it does not return `http` or `https`). This is because using a URL that starts with `//` in the browser will automatically detect the current site's protocol and automatically initiate the request using http or https.

If you are using an nginx proxy, add `proxy_set_header X-Forwarded-Proto $scheme;` to the nginx configuration, [refer to nginx proxy](others/nginx-proxy.md), so that you can use `$request->header('x-forwarded-proto')` to determine if it is http or https, for example:

```php
echo $request->header('x-forwarded-proto'); // Outputs http or https
```

## Obtain the Request HTTP Version
```php
$request->protocolVersion();
```
Returns the string `1.1` or `1.0`.

## Obtain the Request Session ID
```php
$request->sessionId();
```
Returns a string composed of letters and numbers.

## Obtain the Client's IP Address
```php
$request->getRemoteIp();
```

## Obtain the Client's Port
```php
$request->getRemotePort();
```

## Obtain the Real Client IP Address
```php
$request->getRealIp($safe_mode=true);
```

When the project uses a proxy (e.g., nginx), using `$request->getRemoteIp()` often returns the IP of the proxy server (such as `127.0.0.1` or `192.168.x.x`) instead of the actual client IP. In such cases, you can attempt to use `$request->getRealIp()` to obtain the actual client IP.

`$request->getRealIp()` tries to get the real IP from the `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, and `via` fields in the HTTP header.

> Since HTTP headers are easily spoofed, the IP obtained by this method is not 100% trustworthy, especially when `$safe_mode` is set to false. A more reliable method to obtain the real client IP when a proxy is used is to know the IP address of a secure proxy server and know which HTTP header carries the real IP. If the IP returned by `$request->getRemoteIp()` is confirmed as the IP of a known secure proxy server, then the actual IP can be obtained using `$request->header('Header carrying the real IP')`.

## Obtain the Server's IP Address
```php
$request->getLocalIp();
```

## Obtain the Server's Port
```php
$request->getLocalPort();
```

## Check if the Request is an AJAX Request
```php
$request->isAjax();
```

## Check if the Request is a PJAX Request
```php
$request->isPjax();
```

## Check if the Request Expects JSON Response
```php
$request->expectsJson();
```

## Check if the Client Accepts JSON Response
```php
$request->acceptJson();
```

## Obtain the Plugin Name of the Request
Returns an empty string `''` if it is not a plugin request.

```php
$request->plugin;
```
> This feature requires webman>=1.4.0

## Obtain the Application Name of the Request
Returns an empty string `''` in the case of a single application, and the application name in the case of [multiple applications](multiapp.md).

```php
$request->app;
```

> Because closure functions do not belong to any application, the request from a closure route will always return an empty string `''`.
> See [Route](route.md) for closure routes.

## Obtain the Controller Class Name of the Request
Gets the class name corresponding to the controller.

```php
$request->controller;
```
Returns something like `app\controller\IndexController`.

> Because closure functions do not belong to any controller, the request from a closure route will always return an empty string `''`.
> See [Route](route.md) for closure routes.

## Obtain the Method Name of the Request
Gets the controller method name corresponding to the request.

```php
$request->action;
```
Returns `index`, for instance.

> Because closure functions do not belong to any controller, the request from a closure route will always return an empty string `''`.
> See [Route](route.md) for closure routes.