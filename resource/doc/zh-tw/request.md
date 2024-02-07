# 說明

## 獲取請求對象
webman會自動將請求對象注入到action方法的第一個參數中，例如

**範例**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // 從get請求中獲取name參數，如果沒有傳遞name參數則返回$default_name
        $name = $request->get('name', $default_name);
        // 向瀏覽器返回字串
        return response('hello ' . $name);
    }
}
```

透過`$request`對象，我們能夠獲取到請求相關的任何數據。

**有時候我們想在其他類中獲取當前請求的`$request`對象，這時候我們只要使用助手函數`request()`即可**;

## 獲取請求參數get

**獲取整個get陣列**
```php
$request->get();
```
如果請求沒有get參數則返回一個空的陣列。

**獲取get陣列的某一個值**
```php
$request->get('name');
```
如果get陣列中不包含這個值則返回null。

你也可以給get方法第二個參數傳遞一個默認值，如果get陣列中沒找到對應值則返回默認值。例如：
```php
$request->get('name', 'tom');
```

## 獲取請求參數post
**獲取整個post陣列**
```php
$request->post();
```
如果請求沒有post參數則返回一個空的陣列。

**獲取post陣列的某一個值**
```php
$request->post('name');
```
如果post陣列中不包含這個值則返回null。

與get方法一樣，你也可以給post方法第二個參數傳遞一個默認值，如果post陣列中沒找到對應值則返回默認值。例如：
```php
$request->post('name', 'tom');
```

## 獲取原始請求post包體
```php
$post = $request->rawBody();
```
這個功能類似與 `php-fpm`裡的 `file_get_contents("php://input");`操作。用於獲取http原始請求包體。這在獲取非`application/x-www-form-urlencoded`格式的post請求數據時很有用。  

## 獲取header
**獲取整個header陣列**
```php
$request->header();
```
如果請求沒有header參數則返回一個空的陣列。注意所有key均為小寫。

**獲取header陣列的某一個值**
```php
$request->header('host');
```
如果header陣列中不包含這個值則返回null。注意所有key均為小寫。

與get方法一樣，你也可以給header方法第二個參數傳遞一個默認值，如果header陣列中沒找到對應值則返回默認值。例如：
```php
$request->header('host', 'localhost');
```

## 獲取cookie
**獲取整個cookie陣列**
```php
$request->cookie();
```
如果請求沒有cookie參數則返回一個空的陣列。

**獲取cookie陣列的某一個值**
```php
$request->cookie('name');
```
如果cookie陣列中不包含這個值則返回null。

與get方法一樣，你也可以給cookie方法第二個參數傳遞一個默認值，如果cookie陣列中沒找到對應值則返回默認值。例如：
```php
$request->cookie('name', 'tom');
```

## 獲取所有輸入
包含了`post` `get` 的集合。
```php
$request->all();
```

## 獲取指定輸入值
從`post` `get` 的集合中獲取某個值。
```php
$request->input('name', $default_value);
```

## 獲取部分輸入數據
從`post` `get`的集合中獲取部分數據。
```php
// 獲取 username 和 password 組成的陣列，如果對應的key沒有則忽略
$only = $request->only(['username', 'password']);
// 獲得除了avatar 和 age 以外的所有輸入
$except = $request->except(['avatar', 'age']);
```
## 獲取上傳檔案
**獲取整個上傳檔案陣列**
```php
$request->file();
```

類似表單:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

`$request->file()` 返回的格式類似:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
它是一個`webman\Http\UploadFile`實例的陣列。`webman\Http\UploadFile`類繼承了 PHP 內置的 [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) 類，並且提供了一些實用的方法。

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
            var_export($spl_file->getUploadExtension()); // 上傳文件後綴名，例如'jpg'
            var_export($spl_file->getUploadMimeType()); // 上傳文件mine類型，例如 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // 獲取上傳錯誤碼，例如 UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // 上傳文件名，例如 'my-test.jpg'
            var_export($spl_file->getSize()); // 獲取文件大小，例如 13364，單位位元組
            var_export($spl_file->getPath()); // 獲取上傳的目錄，例如 '/tmp'
            var_export($spl_file->getRealPath()); // 獲取臨時檔案路徑，例如 `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**注意：**

- 檔案被上傳後會被命名為一個臨時檔案，類似 `/tmp/workerman.upload.SRliMu`
- 上傳檔案大小受到[defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html) 限制，預設10M，可在`config/server.php`檔案中修改`max_package_size`更改預設值。
- 請求結束後臨時檔案將被自動清除
- 如果請求沒有上傳檔案則`$request->file()` 返回一個空的陣列
- 上傳的檔案不支持 `move_uploaded_file()` 方法，請使用 `$file->move()`方法代替，參見下面的例子

### 獲取特定上傳檔案
```php
$request->file('avatar');
```
如果檔案存在的話則返回對應檔案的`webman\Http\UploadFile` 實例，否則返回null。

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

## 獲取host
獲取請求的host信息。
```php
$request->host();
```
如果請求的地址是非標準的80或者443端口，host信息可能會攜帶端口，例如`example.com:8080`。如果不需要端口第一個參數可以傳入`true`。

```php
$request->host(true);
```

## 獲取請求方法
```php
 $request->method();
```
返回值可能是`GET`、`POST`、`PUT`、`DELETE`、`OPTIONS`、`HEAD`中的一個。

## 獲取請求uri
```php
$request->uri();
```
返回請求的uri，包括path和queryString部分。

## 獲取請求路徑

```php
$request->path();
```
返回請求的path部分。


## 獲取請求queryString

```php
$request->queryString();
```
返回請求的queryString部分。

## 獲取請求url
`url()`方法返回不帶有`Query` 參數 的 URL。
```php
$request->url();
```
返回類似`//www.workerman.net/workerman-chat`

`fullUrl()`方法返回帶有`Query` 參數 的 URL。
```php
$request->fullUrl();
```
返回類似`//www.workerman.net/workerman-chat?type=download`

> **注意**
> `url()` 和 `fullUrl()` 沒有返回協定部分(沒有返回http或者https)。
> 因為瀏覽器裡使用 `//example.com` 這樣以`//`開頭的地址會自動識別當前站點的協定，自動以http或https發起請求。

如果你使用了nginx代理，請將 `proxy_set_header X-Forwarded-Proto $scheme;` 加入到nginx配置中，[參考nginx代理](others/nginx-proxy.md)，
這樣就可以用`$request->header('x-forwarded-proto');`來判斷是http還是https，例如：
```php
echo $request->header('x-forwarded-proto'); // 輸出 http 或 https
```

## 獲取請求HTTP版本

```php
$request->protocolVersion();
```
返回字符串 `1.1` 或者`1.0`。


## 獲取請求sessionId

```php
$request->sessionId();
```
返回字符串，由字母和數字組成


## 獲取請求客戶端IP
```php
$request->getRemoteIp();
```


## 獲取請求客戶端端口
```php
$request->getRemotePort();
```
## 取得請求客戶端真實IP
```php
$request->getRealIp($safe_mode=true);
```

當專案使用代理（例如nginx）時，使用`$request->getRemoteIp()`得到的往往是代理伺服器IP（類似`127.0.0.1`、`192.168.x.x`），並非客戶端真實IP。這時候可以嘗試使用`$request->getRealIp()`取得客戶端真實IP。

`$request->getRealIp()`會嘗試從HTTP頭的`x-real-ip`、`x-forwarded-for`、`client-ip`、`x-client-ip`、`via`字段中獲取真實IP。

> 由於HTTP頭很容易被偽造，所以此方法獲得的客戶端IP並非100%可信，尤其是`$safe_mode`為false時。透過代理獲得客戶端真實IP的比較可靠的方法是，已知安全的代理伺服器IP，並且明確知道攜帶真實IP是哪個HTTP頭，如果`$request->getRemoteIp()`返回的IP確認為已知的安全的代理伺服器，然後通過`$request->header('攜帶真實IP的HTTP頭')`獲取真實IP。


## 取得伺服端IP
```php
$request->getLocalIp();
```

## 取得伺服端埠
```php
$request->getLocalPort();
```

## 判斷是否為ajax請求
```php
$request->isAjax();
```

## 判斷是否為pjax請求
```php
$request->isPjax();
```

## 判斷是否為期待json返回
```php
$request->expectsJson();
```

## 判斷客戶端是否接受json返回
```php
$request->acceptJson();
```

## 獲得請求的插件名稱
非插件請求返回空字串`''`。
```php
$request->plugin;
```
> 此特性需要webman>=1.4.0

## 獲得請求的應用名稱
單應用的時候始終返回空字串`''`，[多應用](multiapp.md)的時候返回應用名稱
```php
$request->app;
```
> 因為閉包函數不屬於任何應用，所以來自閉包路由的請求`$request->app`始終返回空字串`''`
> 閉包路由參見 [路由](route.md)

## 獲得請求的控制器類別名稱
獲得控制器對應的類別名稱
```php
$request->controller;
```
返回類似 `app\controller\IndexController`

> 因為閉包函數不屬於任何控制器，所以來自閉包路由的請求`$request->controller`始終返回空字串`''`
> 閉包路由參見 [路由](route.md)

## 獲得請求的方法名稱
獲得請求對應的控制器方法名稱
```php
$request->action;
```
返回類似 `index`

> 因為閉包函數不屬於任何控制器，所以來自閉包路由的請求`$request->action`始終返回空字串`''`
> 閉包路由參見 [路由](route.md)
