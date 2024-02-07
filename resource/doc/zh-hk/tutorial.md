# 簡單示例

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
        // 從get請求裡獲得name參數，如果沒有傳遞name參數則返回$default_name
        $name = $request->get('name', $default_name);
        // 向瀏覽器返回字符串
        return response('hello ' . $name);
    }
}
```

**訪問**

在瀏覽器裡訪問 `http://127.0.0.1:8787/user/hello?name=tom`

瀏覽器將返回 `hello tom`

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

**訪問**

在瀏覽器裡訪問 `http://127.0.0.1:8787/user/hello?name=tom`

瀏覽器將返回 `{"code":0,"msg":"ok","data":"tom""}`

使用json助手函數返回數據將自動加上一個header頭 `Content-Type: application/json`

## 返回xml
同理，使用助手函數 `xml($xml)` 將返回一個帶 `Content-Type: text/xml` 頭的 `xml` 響應。

其中 `$xml` 參數可以是 `xml` 字符串，也可以是 `SimpleXMLElement` 對象

## 返回jsonp
同理，使用助手函數 `jsonp($data, $callback_name = 'callback')` 將返回一個 `jsonp` 響應。

## 返回視圖
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

在瀏覽器裡訪問 `http://127.0.0.1:8787/user/hello?name=tom`
將返回一個內容為 `hello tom` 的html頁面。

注意：webman默認使用的是php原生語法作為模版。如果想使用其他視圖參見[視圖](view.md)。
