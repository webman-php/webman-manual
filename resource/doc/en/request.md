# Description

##  Get request object
webmanThe request object is automatically injected into the first parameter of the action method, for example


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
        // Get the name parameter from the get request, or return it if no name parameter is passed$default_name
        $name = $request->get('name', $default_name);
        // Return string to browser
        return response('hello ' . $name);
    }
}
```

With the `$request` object we can get any data related to the request。

**Sometimes we want to get the `$request` object of the current request in other classes, and then we just use the helper function `request()`.**;

## Get request parametersget

**Get the entire get array**
```php
$request->get();
```
Return an empty array if the request has no get parameters。

**Get a certain value of the get array**
```php
$request->get('name');
```
Return if the get array does not contain this valuenull。

You can also pass a default value to the second argument of the get method, and return the default value if the corresponding value is not found in the get array. For example：
```php
$request->get('name', 'tom');
```

## Get request parameterspost
**Get the whole post array**
```php
$request->post();
```
Returns an empty array if the request has no post parameters。

**Get a certain value of the post array**
```php
$request->post('name');
```
Return if the post array does not contain this valuenull。

Like the get method, you can also pass a default value to the second parameter of the post method and return the default value if the corresponding value is not found in the post array. For example, ：
```php
$request->post('name', 'tom');
```

## Get the original request post package body
```php
$post = $request->rawBody();
```
This function is similar to `php-fpm`in `file_get_contents("php://input");`operation。Used to gethttpdaemon model。this inGet非`application/x-www-form-urlencoded`formatpostUseful when requesting data。 


## Getheader
**Get the entire header array**
```php
$request->header();
```
Returns an empty array if the request does not have a header parameter. Note that all keys are lowercase。

**Get a value of the header array**
```php
$request->header('host');
```
Returns null if the header array does not contain this value. note that all keys are lowercase。

Like the get method, you can also pass a default value to the second parameter of the header method and return the default value if the corresponding value is not found in the header array. For example：
```php
$request->header('host', 'localhost');
```

## Getcookie
**Get the entire cookie array**
```php
$request->cookie();
```
Return an empty array if the request has no cookie parameters。

**Get a certain value of the cookie array**
```php
$request->cookie('name');
```
Return if the cookie array does not contain this valuenull。

As with the get method, you can pass a default value to the second parameter of the cookie method, and return the default value if the corresponding value is not found in the cookie array. For example：
```php
$request->cookie('name', 'tom');
```

## Get all inputs
Contains a collection of `post` `get`。
```php
$request->all();
```

## Get specified input value
Get a value from the set of `post` `get`。
```php
$request->input('name', $default_value);
```

## Get partial input data
Retrieve some data from the set of `post` `get`。
```php
// Get an array of username and password and ignore it if the corresponding key is not available, For example
$only = $request->only(['username', 'password']);
// Get all inputs except avatar and age
$except = $request->except(['avatar', 'age']);
```

## Get upload file
**Get the entire array of uploaded files**
```php
$request->file();
```

Form Similar:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

`$request->file()`Returned format is similar:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
He is a`webman\Http\UploadFile`Implemented syntactically。`webman\Http\UploadFile`Classes inherit from PHP built-in [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) 类，and provides a number of practical methods。

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // Is the file valid, for exampleture|false
            var_export($spl_file->getUploadExtension()); // Upload file suffix, e.g.'jpg'
            var_export($spl_file->getUploadMineType()); // Upload file mine type, for example 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Get the upload error code, e.g. UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Upload file name, e.g. 'my-test.jpg'
            var_export($spl_file->getSize()); // Get file size, e.g. 13364, in bytes
            var_export($spl_file->getPath()); // Get the uploaded directory, e.g. '/tmp'
            var_export($spl_file->getRealPath()); // Get temporary file path, e.g. `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Note：**

- The file will be named as a temporary file after being uploaded, similar to `/tmp/workerman.upload.SRliMu`
- The upload file size is subject to[defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html)restriction，default10M，Available in`config/server.php`Event data`max_package_size`Arrays of instances。
- Temporary files will be automatically cleared after the request ends
- If the request does not upload a file then `$request->file()` returns an empty array
- The uploaded file is not supported `move_uploaded_file()` Method，Please use `$file->move()`Method instead，Middleware sectionExample

### Get specific upload files
```php
$request->file('avatar');
```
Returns the corresponding file if it exists`webman\Http\UploadFile`instance，otherwise returnnull。

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

## Gethost
Get the host information of the request。
```php
$request->host();
```
If the request address is non-standard port 80 or 443, the host information may carry the port, e.g. `example.com:8080`. If the port is not needed the first parameter can be passed in`true`。

```php
$request->host(true);
```

## Get request method
```php
 $request->method();
```
The return value may be one of `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, `HEAD`。

## Get Requesturi
```php
$request->uri();
```
Return the uri of the request, including the path and queryString parts。

## Get request path

```php
$request->path();
```
Return the path part of the request。


## Get RequestqueryString

```php
$request->queryString();
```
Return the queryString part of the request。

## Get Requesturl
`url()`Method returns without the `Query` parameter URL。
```php
$request->url();
```
Return similar`//www.workerman.net/workerman-chat`

`fullUrl()`Method returns with `Query` parameter URL。
```php
$request->fullUrl();
```
Return similar`//www.workerman.net/workerman-chat?type=download`

> Note: `url()` and `fullUrl()` do not return the protocol part (no http or https)

## Get request HTTP version

```php
$request->protocolVersion();
```
Return string `1.1` or`1.0`。


## Get RequestsessionId

```php
$request->sessionId();
```
Returns a string, consisting of letters and numbers


## Get Request ClientIP
```php
$request->getRemoteIp();
```

## GetRequestClientPort
```php
$request->getRemotePort();
```

## Get request client realIP
```php
$request->getRealIp($safe_mode=true);
```

When a project uses a proxy (such as nginx), using `$request->getRemoteIp()` often yields a proxy server IP (something like `127.0.0.1` `192.168.x.x`) instead of the client's real IP. to get the client's real IP。

`$request->getRealIp();`The principle is: if the client IP is found to be an intranet IP, it tries to get the real IP from the `Client-Ip`, `X-Forwarded-For`, `X-Real-Ip`, `Client-Ip`, `Via` HTTP headers. if `$safe_mode` is false, it does not determine whether the client IP is intranet IP (not safe), directly try to read the client IP data from the above HTTP header. If the HTTP header does not have the above fields, the return value of `$request->getRemoteIp()` is used as the result to return 。

> Since HTTP headers are easily forged, the client IP obtained by this method is not 100% reliable, especially if `$safe_mode` is false. The most reliable way to get the real IP of a client through a proxy is to have a known safe proxy server IP and know exactly which HTTP header carries the real IP, if the IP returned by `$request->getRemoteIp()` is confirmed to be a known safe proxy server, then pass `$request->header('HTTP header carrying real IP HTTP header')` to get the realIP。


## Get ServerIP
```php
$request->getLocalIp();
```

## Get server port
```php
$request->getLocalPort();
```

## Determine if it is an ajax request
```php
$request->isAjax();
```

## Determine if it is a pjax request
```php
$request->isPjax();
```

## Determine if it is expecting a json return
```php
$request->expectsJson();
```

## Determine if the client accepts json returns
```php
$request->acceptJson();
```

## Get the requested plugin name
Return empty string for non-plugin requests`''`。
```php
$request->plugin;
```
> Required for this featurewebman>=1.4.0

## Get the requested application name
Single application always returns empty string`''`，[more applications](multiapp.md)when returning the application name
```php
$request->app;
```

> Because closure functions do not belong to any application, requests from closure routes `$request->app` always return the empty string`''`
> Component Usage Reference [Routing](route.md)

## Get the class name of the requested controller
Get the class name corresponding to the controller
```php
$request->controller;
```
Return similar `app\controller\IndexController`

> Since the closure function does not belong to any controller, requests from the closure route `$request->controller` always return the empty string`''`
> Component Usage Reference [Routing](route.md)

## Get the method name of the request
Get the name of the controller method corresponding to the request
```php
$request->action;
```
Return similar `index`

> Because the closure function does not belong to any controller, requests from the closure route `$request->action` always return the empty string`''`
> Component Usage Reference [Routing](route.md)



