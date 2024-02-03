## 處理靜態文件
webman支援靜態文件訪問，靜態文件都放置於`public`目錄下，例如訪問 `http://127.0.0.8787/upload/avatar.png` 實際上是訪問`{主項目目錄}/public/upload/avatar.png`。

> **注意**
> webman 從1.4開始支援應用插件，以`/app/xx/文件名`開頭的靜態文件訪問實際是訪問應用插件的`public`目錄，也就是說 webman >=1.4.0 時不支援 `{主項目目錄}/public/app/`下的目錄訪問。
> 更多請參考[應用插件](./plugin/app.md)

### 關閉靜態文件支援
如果不需要靜態文件支援，打開`config/static.php`將`enable`選項改成false。關閉後所有靜態文件的訪問會返回404。

### 更改靜態文件目錄
webman預設使用public目錄為靜態文件目錄。如需修改請更改`support/helpers.php`中的`public_path()`助手函數。

### 靜態文件中間件
webman自帶一個靜態文件中間件，位置`app/middleware/StaticFile.php`。
有時我們需要對靜態文件做一些處理，例如給靜態文件增加跨域http頭，禁止訪問以點(`.`)開頭的文件可以使用這個中間件

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
        // 禁止訪問.開頭的隱藏文件
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // 增加跨域http頭
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
如果需要此中間件時，需要到`config/static.php`中`middleware`選項中開啟。