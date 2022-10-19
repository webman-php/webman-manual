# 简单示例

## 返回字符串
**新建控制器**

新建文件 `app/controller/UserController.php` 如下

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

**访问**

在浏览器里访问 `http://127.0.0.1:8787/user/hello?name=tom`

浏览器将返回 `hello tom`

## 返回json
更改文件 `app/controller/UserController.php` 如下

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

**访问**

在浏览器里访问 `http://127.0.0.1:8787/user/hello?name=tom`

浏览器将返回 `{"code":0,"msg":"ok","data":"tom""}`

使用json助手函数返回数据将自动加上一个header头 `Content-Type: application/json`

## 返回xml
同理，使用助手函数 `xml($xml)` 将返回一个带 `Content-Type: text/xml` 头的`xml`响应。

其中`$xml`参数可以是`xml`字符串，也可以是`SimpleXMLElement`对象

## 返回jsonp
同理，使用助手函数 `jsonp($data, $callback_name = 'callback')` 将返回一个`jsonp`响应。

## 返回视图
更改文件 `app/controller/UserController.php` 如下

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

新建文件 `app/view/user/hello.html` 如下

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

在浏览器里访问 `http://127.0.0.1:8787/user/hello?name=tom`
将返回一个内容为 `hello tom` 的html页面。

注意：webman默认使用的是php原生语法作为模版。如果想使用其它视图参见[视图](view.md)。


