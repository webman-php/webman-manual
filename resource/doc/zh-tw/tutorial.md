# 簡單範例

## 返回字串
**建立控制器**

建立文件 `app/controller/UserController.php` 如下

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // 從get請求中取得name參數，如果沒有傳遞name參數則返回$default_name
        $name = $request->get('name', $default_name);
        // 回應字串給瀏覽器
        return response('hello ' . $name);
    }
}
```

**存取**

在瀏覽器中存取 `http://127.0.0.1:8787/user/hello?name=tom`

瀏覽器將返回 `hello tom`

## 返回json
修改檔案 `app/controller/UserController.php` 如下

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

**存取**

在瀏覽器中存取 `http://127.0.0.1:8787/user/hello?name=tom`

瀏覽器將返回 `{"code":0,"msg":"ok","data":"tom"}`

使用json輔助函數返回的資料將自動加上一個標頭 `Content-Type: application/json`

## 返回xml
同樣，使用輔助函數 `xml($xml)` 將回傳一個帶有 `Content-Type: text/xml` 標頭的 `xml` 回應。

其中`$xml`參數可以是`xml`字串，也可以是`SimpleXMLElement`物件

## 返回jsonp
同樣，使用輔助函數 `jsonp($data, $callback_name = 'callback')` 將回傳一個`jsonp`回應。

## 返回視圖
修改檔案 `app/controller/UserController.php` 如下

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

建立文件 `app/view/user/hello.html` 如下

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

在瀏覽器中存取 `http://127.0.0.1:8787/user/hello?name=tom`
將返回一個內容為 `hello tom` 的html頁面。

注意：webman預設使用的是php原生語法作為模版。如果想使用其他視圖參見[視圖](view.md)。
