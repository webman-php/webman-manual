# 簡單示例

## 返回字串
**建立控制器**

建立檔案 `app/controller/UserController.php` 如下

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // 從GET請求中取得name參數，如果未傳遞name參數則返回$default_name
        $name = $request->get('name', $default_name);
        // 返回字串到瀏覽器
        return response('hello ' . $name);
    }
}
```

**訪問**

在瀏覽器中訪問 `http://127.0.0.1:8787/user/hello?name=tom`

瀏覽器將返回 `hello tom`

## 返回json
更改檔案 `app/controller/UserController.php` 如下

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

在瀏覽器中訪問 `http://127.0.0.1:8787/user/hello?name=tom`

瀏覽器將返回 `{"code":0,"msg":"ok","data":"tom""}`

使用json輔助函數返回的數據將自動添加一個標頭 `Content-Type: application/json`

## 返回xml
同樣地, 使用輔助函數 `xml($xml)` 將返回帶有 `Content-Type: text/xml` 標頭的 `xml` 響應。

其中 `$xml` 參數可以是 `xml` 字串，也可以是 `SimpleXMLElement` 物件

## 返回jsonp
同樣地, 使用輔助函數 `jsonp($data, $callback_name = 'callback')` 將返回一個 `jsonp` 響應。

## 返回視圖
更改檔案 `app/controller/UserController.php` 如下

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

建立檔案 `app/view/user/hello.html` 如下

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

在瀏覽器中訪問 `http://127.0.0.1:8787/user/hello?name=tom`
將返回一個內容為 `hello tom` 的HTML頁面。

注意：webman預設使用PHP原生語法作為模板。如果想使用其他視圖請參見[視圖](view.md)。