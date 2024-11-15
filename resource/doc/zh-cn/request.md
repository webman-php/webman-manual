# 说明

##  获得请求对象
webman会自动将请求对象注入到action方法第一个参数中，例如


**例子**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // 从get请求里获得name参数，如果没有传递name参数则返回$default_name
        $name = $request->get('name', $default_name);
        // 向浏览器返回字符串
        return response('hello ' . $name);
    }
}
```

通过`$request`对象我们能获取到请求相关的任何数据。

**有时候我们想在其它类中获取当前请求的`$request`对象，这时候我们只要使用助手函数`request()`即可**;

## 获得请求参数get

**获取整个get数组**
```php
$request->get();
```
如果请求没有get参数则返回一个空的数组。

**获取get数组的某一个值**
```php
$request->get('name');
```
如果get数组中不包含这个值则返回null。

你也可以给get方法第二个参数传递一个默认值，如果get数组中没找到对应值则返回默认值。例如：
```php
$request->get('name', 'tom');
```

## 获得请求参数post
**获取整个post数组**
```php
$request->post();
```
如果请求没有post参数则返回一个空的数组。

**获取post数组的某一个值**
```php
$request->post('name');
```
如果post数组中不包含这个值则返回null。

与get方法一样，你也可以给post方法第二个参数传递一个默认值，如果post数组中没找到对应值则返回默认值。例如：
```php
$request->post('name', 'tom');
```

## 获得原始请求post包体
```php
$post = $request->rawBody();
```
这个功能类似与 `php-fpm`里的 `file_get_contents("php://input");`操作。用于获得http原始请求包体。这在获取非`application/x-www-form-urlencoded`格式的post请求数据时很有用。 


## 获取header
**获取整个header数组**
```php
$request->header();
```
如果请求没有header参数则返回一个空的数组。注意所有key均为小写。

**获取header数组的某一个值**
```php
$request->header('host');
```
如果header数组中不包含这个值则返回null。注意所有key均为小写。

与get方法一样，你也可以给header方法第二个参数传递一个默认值，如果header数组中没找到对应值则返回默认值。例如：
```php
$request->header('host', 'localhost');
```

## 获取cookie
**获取整个cookie数组**
```php
$request->cookie();
```
如果请求没有cookie参数则返回一个空的数组。

**获取cookie数组的某一个值**
```php
$request->cookie('name');
```
如果cookie数组中不包含这个值则返回null。

与get方法一样，你也可以给cookie方法第二个参数传递一个默认值，如果cookie数组中没找到对应值则返回默认值。例如：
```php
$request->cookie('name', 'tom');
```

## 获得所有输入
包含了`post` `get` 的集合。
```php
$request->all();
```

## 获取指定输入值
从`post` `get` 的集合中获取某个值。
```php
$request->input('name', $default_value);
```

## 获取部分输入数据
从`post` `get`的集合中获取部分数据。
```php
// 获取 username 和 password 组成的数组，如果对应的key没有则忽略
$only = $request->only(['username', 'password']);
// 获得除了avatar 和 age 以外的所有输入
$except = $request->except(['avatar', 'age']);
```

## 通过控制器参数获得输入
> **注意**
> 此特性需要 webman-framework >= 1.6.0

```php
<?php
namespace app\controller;
use support\Response;

class UserController
{
    public function create(string $name, int $age = 18): Response
    {
        return json(['name' => $name, 'age' => $age]);
    }
}
```
代码逻辑类似于
```php
<?php
namespace app\controller;
use support\Request;
use support\Response;

class UserController
{
    public function create(Request $request): Response
    {
        $name = $request->input('name');
        $age = (int)$request->input('age', 18);
        return json(['name' => $name, 'age' => $age]);
    }
}
```
更多信息请参考[控制器参数绑定](controller.md#控制器参数绑定)

## 获取上传文件

> **提示**
> 上传文件需要使用 `multipart/form-data` 格式的表单

**获取整个上传文件数组**
```php
$request->file();
```

表单类似:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

`$request->file()`返回的格式类似:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
他是一个`webman\Http\UploadFile`实例的数组。`webman\Http\UploadFile`类继承了 PHP 内置的 [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) 类，并且提供了一些实用的方法。

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // 文件是否有效，例如ture|false
            var_export($spl_file->getUploadExtension()); // 上传文件后缀名，例如'jpg'
            var_export($spl_file->getUploadMimeType()); // 上传文件mine类型，例如 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // 获取上传错误码，例如 UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // 上传文件名，例如 'my-test.jpg'
            var_export($spl_file->getSize()); // 获得文件大小，例如 13364，单位字节
            var_export($spl_file->getPath()); // 获得上传的目录，例如 '/tmp'
            var_export($spl_file->getRealPath()); // 获得临时文件路径，例如 `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**注意：**

- 文件被上传后会被命名为一个临时文件，类似 `/tmp/workerman.upload.SRliMu`
- 上传文件大小受到[defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html)限制，默认10M，可在`config/server.php`文件中修改`max_package_size`更改默认值。
- 请求结束后临时文件将被自动清除
- 如果请求没有上传文件则`$request->file()`返回一个空的数组
- 上传的文件不支持 `move_uploaded_file()` 方法，请使用 `$file->move()`方法代替，参见下面的例子

### 获取特定上传文件
```php
$request->file('avatar');
```
如果文件存在的话则返回对应文件的`webman\Http\UploadFile`实例，否则返回null。

**例子**
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

## 获取host
获取请求的host信息。
```php
$request->host();
```
如果请求的地址是非标准的80或者443端口，host信息可能会携带端口，例如`example.com:8080`。如果不需要端口第一个参数可以传入`true`。

```php
$request->host(true);
```

## 获取请求方法
```php
 $request->method();
```
返回值可能是`GET`、`POST`、`PUT`、`DELETE`、`OPTIONS`、`HEAD`中的一个。

## 获取请求uri
```php
$request->uri();
```
返回请求的uri，包括path和queryString部分。

## 获取请求路径

```php
$request->path();
```
返回请求的path部分。


## 获取请求queryString

```php
$request->queryString();
```
返回请求的queryString部分。

## 获取请求url
`url()`方法返回不带有`Query` 参数 的 URL。
```php
$request->url();
```
返回类似`//www.workerman.net/workerman-chat`

`fullUrl()`方法返回带有`Query` 参数 的 URL。
```php
$request->fullUrl();
```
返回类似`//www.workerman.net/workerman-chat?type=download`

> **注意**
> `url()` 和 `fullUrl()` 没有返回协议部分(没有返回http或者https)。
> 因为浏览器里使用 `//example.com` 这样以`//`开头的地址会自动识别当前站点的协议，自动以http或https发起请求。

如果你使用了nginx代理，请将 `proxy_set_header X-Forwarded-Proto $scheme;` 加入到nginx配置中，[参考nginx代理](others/nginx-proxy.md)，
这样就可以用`$request->header('x-forwarded-proto');`来判断是http还是https，例如：
```php
echo $request->header('x-forwarded-proto'); // 输出 http 或 https
```

## 获取请求HTTP版本

```php
$request->protocolVersion();
```
返回字符串 `1.1` 或者`1.0`。


## 获取请求sessionId

```php
$request->sessionId();
```
返回字符串，由字母和数字组成


## 获取请求客户端IP
```php
$request->getRemoteIp();
```

## 获取请求客户端端口
```php
$request->getRemotePort();
```

## 获取请求客户端真实IP
```php
$request->getRealIp($safe_mode=true);
```

当项目使用代理(例如nginx)时，使用`$request->getRemoteIp()`得到的往往是代理服务器IP(类似`127.0.0.1` `192.168.x.x`)并非客户端真实IP。这时候可以尝试使用`$request->getRealIp()`获得客户端真实IP。

`$request->getRealIp()`会尝试从HTTP头的`x-real-ip`、`x-forwarded-for`、`client-ip`、`x-client-ip`、`via`字段中获取真实IP。

> 由于HTTP头很容伪造，所以此方法获得的客户端IP并非100%可信，尤其是`$safe_mode`为false时。透过代理获得客户端真实IP的比较可靠的方法是，已知安全的代理服务器IP，并且明确知道携带真实IP是哪个HTTP头，如果`$request->getRemoteIp()`返回的IP确认为已知的安全的代理服务器，然后通过`$request->header('携带真实IP的HTTP头')`获取真实IP。


## 获取服务端IP
```php
$request->getLocalIp();
```

## 获取服务端端口
```php
$request->getLocalPort();
```

## 判断是否是ajax请求
```php
$request->isAjax();
```

## 判断是否是pjax请求
```php
$request->isPjax();
```

## 判断是否是期待json返回
```php
$request->expectsJson();
```

## 判断客户端是否接受json返回
```php
$request->acceptJson();
```

## 获得请求的插件名
非插件请求返回空字符串`''`。
```php
$request->plugin;
```
> 此特性需要webman>=1.4.0

## 获得请求的应用名
单应用的时候始终返回空字符串`''`，[多应用](multiapp.md)的时候返回应用名
```php
$request->app;
```

> 因为闭包函数不属于任何应用，所以来自闭包路由的请求`$request->app`始终返回空字符串`''`
> 闭包路由参见 [路由](route.md)

## 获得请求的控制器类名
获得控制器对应的类名
```php
$request->controller;
```
返回类似 `app\controller\IndexController`

> 因为闭包函数不属于任何控制器，所以来自闭包路由的请求`$request->controller`始终返回空字符串`''`
> 闭包路由参见 [路由](route.md)

## 获得请求的方法名
获得请求对应的控制器方法名
```php
$request->action;
```
返回类似 `index`

> 因为闭包函数不属于任何控制器，所以来自闭包路由的请求`$request->action`始终返回空字符串`''`
> 闭包路由参见 [路由](route.md)

## 重写参数
有时候我们想重写请求的参数，例如将请求过滤，让后重新赋值给请求对象，这时候我们可以使用`setGet()` `setPost()` `setHeader()`方法。

> **提示**
> 此特性需要webman-framework>=1.6.0

#### 重写GET参数
```php
$request->get(); // 假设得到 ['name' => 'tom', 'age' => 18]
$request->setGet(['name' => 'tom']);
$request->get(); // 最终得到 ['name' => 'tom']
```

> **注意**
> 如例子所示，`setGet()`是重写所有GET参数，`setPost()` `setHeader()` 也是同样的行为。


#### 重写POST参数
```php
$post = $request->post();
foreach ($post as $key => $value) {
    $post[$key] = htmlspecialchars($value);
}
$request->setPost($post);
$request->post(); // 得到过滤后的post参数
```

#### 重写HEADER参数
```php
$request->setHeader(['host' => 'example.com']);
$request->header('host'); // 输出 example.com
```


