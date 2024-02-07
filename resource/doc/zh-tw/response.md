# 回應
回應實際上是一個`support\Response`對象，為了方便建立這個對象，webman提供了一些輔助函數。

## 返回一個任意回應

**範例**
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

response 函數實現如下：
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

你也可以先建立一個空的 `response` 對象，然後在適當的位置利用 `$response->cookie()` `$response->header()` `$response->withHeaders()` `$response->withBody()` 設置返回內容。
```php
public function hello(Request $request)
{
    // 創建一個對象
    $response = response();
    
    // .... 業務邏輯省略
    
    // 設置 cookie
    $response->cookie('foo', 'value');
    
    // .... 業務邏輯省略
    
    // 設置 http 頭
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... 業務邏輯省略

    // 設置要返回的資料
    $response->withBody('返回的數據');
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
json 函數實現如下
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
xml 函數實現如下：
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```



## 返回視圖
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
redirect 函數實現如下：
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


## header設置
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
也可以利用 `header` 和 `withHeaders` 方法來單個或者批量設置 header。
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
你也可以提前設置 header，最後設置將要返回的資料。
```php
public function hello(Request $request)
{
    // 創建一個對象
    $response = response();
    
    // .... 業務邏輯省略
  
    // 設置 http 頭
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... 業務邏輯省略

    // 設置要返回的資料
    $response->withBody('返回的數據');
    return $response;
}
```



## 設置cookie

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
你也可以提前設置cookie，最後設置要返回的資料。
```php
public function hello(Request $request)
{
    // 創建一個對象
    $response = response();
    
    // .... 業務邏輯省略
    
    // 設置 cookie
    $response->cookie('foo', 'value');
    
    // .... 業務邏輯省略

    // 設置要返回的資料
    $response->withBody('返回的數據');
    return $response;
}
```
cookie 方法完整參數如下：

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`
## 返回檔案流
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

- webman支援發送超大檔案
- 對於大檔案（超過2M），webman不會一次性將整個檔案讀入內存，而是在適當的時機分段讀取檔案並發送
- webman會根據客戶端接收速度來優化檔案讀取發送速度，保證最快速發送檔案的同時將內存佔用減少到最低
- 數據發送是非阻塞的，不會影響其他請求處理
- file方法會自動添加`if-modified-since`頭並在下一個請求時檢測`if-modified-since`頭，如果檔案未修改則直接返回304以便節省帶寬
- 發送的檔案會自動使用合適的`Content-Type`頭發送給瀏覽器
- 如果檔案不存在，會自動轉為404響應


## 下載檔案
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', '檔案名.ico');
    }
}
```
download方法與file方法基本一致，的區別是
1、設置下載的檔案名後檔案會被下載下來，而不是顯示在瀏覽器裡
2、download方法不會檢查`if-modified-since`頭


## 獲取輸出
有些類庫是將檔案內容直接列印到標準輸出的，也就是數據會列印在命令行終端裡，並不會發送給瀏覽器，這時候我們需要通過`ob_start();` `ob_get_clean();` 將數據捕獲到一個變量中，再將數據發送給瀏覽器，例如：

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // 創建圖像
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

        // 開始獲取輸出
        ob_start();
        // 輸出圖像
        imagejpeg($im);
        // 獲得圖像內容
        $image = ob_get_clean();
        
        // 發送圖像
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
