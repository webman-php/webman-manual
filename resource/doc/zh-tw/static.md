## 處理靜態檔案
webman支援靜態檔案存取，靜態檔案都放置在`public`目錄下，例如存取`http://127.0.0.8787/upload/avatar.png`實際上是存取`{主專案目錄}/public/upload/avatar.png`。

> **注意**
> webman 自1.4版本起支援應用程式插件，以`/app/xx/檔案名`開頭的靜態檔案存取實際上是存取應用程式插件的`public`目錄，也就是說 webman >=1.4.0 時不支援`{主專案目錄}/public/app/`目錄的存取。
> 更多請參考[應用程式插件](./plugin/app.md)

### 關閉靜態檔案支援
如果不需要靜態檔案支援，請打開`config/static.php`並將`enable`選項改為false。關閉後所有靜態檔案的存取將會返回404。

### 更改靜態檔案目錄
webman預設使用public目錄作為靜態檔案目錄。如果需要更改，請修改`support/helpers.php`中的`public_path()`輔助函數。

### 靜態檔案中介層
webman內建一個靜態檔案中介層，位置在`app/middleware/StaticFile.php`。
有時我們需要對靜態檔案進行一些處理，例如給靜態檔案增加跨域 HTTP 標頭，禁止存取以點(`.`)開頭的檔案，這時可以使用這個中介層。

`app/middleware/StaticFile.php` 內容類似如下：
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // 禁止存取.開頭的隱藏檔案
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // 增加跨域 HTTP 標頭
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
若需要此中介層，請到`config/static.php`中的`middleware`選項中啟用。
