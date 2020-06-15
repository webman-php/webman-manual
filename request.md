# 说明

##  获得请求对象
webman会自动将请求对象注入到controller的每个action方法第一个参数中，例如


**例子**
```php
<?php
namespace app\controller;

use support\request;

class User
{
    public function hello(request $request)
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

## 获取上传文件
**获取整个上传文件数组**
```php
$request->file();
```
返回的文件格式类似:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
他是一个`webman\Http\UploadFile`实例的数组。`webman\Http\UploadFile`类继承了 PHP 的 SplFileInfo 类的同时也提供了各种与文件交互的方法。
 

**注意：**

 - 上传文件大小受到[defaultMaxPackageSize](../tcp-connection/default-max-package-size.md)限制，默认10M，可修改。

 - 请求结束后文件将被自动清除。

 - 如果请求没有上传文件则返回一个空的数组。

### 获取特定上传文件
```php
$request->file('avatar');
```
如果文件存在的话则返回对应文件的`webman\Http\UploadFile`实例，否则返回null。


**例子**
```php
<?php
namespace app\controller;

use support\request;

class User
{
    public function file(request $request)
    {
        $file = $request->file('upload');
        if ($file && $file->isValid()) {
            $file->move(public_path().'/files/myfile.'.$file->getExtension());
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

`full()`方法返回带有`Query` 参数 的 URL。
```php
$request->fullUrl();
```
返回类似`//www.workerman.net/workerman-chat?type=download`

注意：`url()` 和 `full()` 没有返回协议部分(没有返回http或者https)

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


## 获取请求客户端ip
```php
$request->getRemoteIp();
```

## 获取请求客户端端口
```php
$request->getRemotePort();
```

## 获取请求的ip
```php
$request->getLocalIp();
```

## 获取请求的端口
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

## 获得请求的应用名
单应用的时候始终返回空字符串`''`，多应用的时候返回应用名
```php
$request->app();
```

## 获得请求的控制器类名
获得控制器对应的类名
```php
$request->controller();
```
返回类似 `app\controller\Index`

## 获得请求的方法名
获得请求对应的控制器方法名
```php
$request->action();
```
返回类似 `Index`



