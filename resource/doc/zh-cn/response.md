# 响应
响应实际上是一个`support\Response`对象，为了方便创建这个对象，webman提供了一些助手函数。

## 返回一个任意响应

**例子**
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

response函数实现如下：
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

你也可以先创建一个空的`response`对象，然后在适当的位置利用`$response->cookie()` `$response->header()` `$response->withHeaders()` `$response->withBody()`设置返回内容。
```php
public function hello(Request $request)
{
    // 创建一个对象
    $response = response();
    
    // .... 业务逻辑省略
    
    // 设置cookie
    $response->cookie('foo', 'value');
    
    // .... 业务逻辑省略
    
    // 设置http头
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... 业务逻辑省略

    // 设置要返回的数据
    $response->withBody('返回的数据');
    return $response;
}
```

## 返回json
**例子**
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
json函数实现如下
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```


## 返回xml
**例子**
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

xml函数实现如下：
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## 返回视图
新建文件 `app/controller/FooController.php` 如下

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

新建文件 `app/view/foo/hello.html` 如下

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

## 重定向
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

redirect函数实现如下：
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

## header设置
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
也可以利用`header`和`withHeaders`方法来单个或者批量设置header。
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
你也可以提前设置header，最后设置将要返回的数据。
```php
public function hello(Request $request)
{
    // 创建一个对象
    $response = response();
    
    // .... 业务逻辑省略
  
    // 设置http头
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... 业务逻辑省略

    // 设置要返回的数据
    $response->withBody('返回的数据');
    return $response;
}
```

## 设置cookie

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

你也可以提前设置cookie，最后设置要返回的数据。
```php
public function hello(Request $request)
{
    // 创建一个对象
    $response = response();
    
    // .... 业务逻辑省略
    
    // 设置cookie
    $response->cookie('foo', 'value');
    
    // .... 业务逻辑省略

    // 设置要返回的数据
    $response->withBody('返回的数据');
    return $response;
}
```

cookie方法完整参数如下：

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## 返回文件流
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

- webman支持发送超大文件
- 对于大文件(超过2M)，webman不会将整个文件一次性读入内存，而是在合适的时机分段读取文件并发送
- webman会根据客户端接收速度来优化文件读取发送速度，保证最快速发送文件的同时将内存占用减少到最低
- 数据发送是非阻塞的，不会影响其它请求处理
- file方法会自动添加`if-modified-since`头并在下一个请求时检测`if-modified-since`头，如果文件未修改则直接返回304以便节省带宽
- 发送的文件会自动使用合适的`Content-Type`头发送给浏览器
- 如果文件不存在，会自动转为404响应


## 下载文件
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', '文件名.ico');
    }
}
```
download方法与file方法基本一致，的区别是
1、设置下载的文件名后文件会被下载下来，而不是显示在浏览器里
2、download方法不会检查`if-modified-since`头


## 获取输出
有些类库是将文件内容直接打印到标准输出的，也就是数据会打印在命令行终端里，并不会发送给浏览器，这时候我们需要通过`ob_start();` `ob_get_clean();` 将数据捕获到一个变量中，再将数据发送给浏览器，例如：

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // 创建图像
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

        // 开始获取输出
        ob_start();
        // 输出图像
        imagejpeg($im);
        // 获得图像内容
        $image = ob_get_clean();
        
        // 发送图像
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
