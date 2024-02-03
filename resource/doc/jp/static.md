## 静的ファイルの処理
webmanでは静的ファイルのアクセスがサポートされており、静的ファイルはすべて`public`ディレクトリに配置されています。たとえば、`http://127.0.0.8787/upload/avatar.png`にアクセスすると実際には`{プロジェクトディレクトリ}/public/upload/avatar.png`がアクセスされます。

> **注意**
> webmanは1.4以降、アプリケーションプラグインをサポートしており、`/app/xx/ファイル名`で始まる静的ファイルアクセスは実際にはアプリケーションプラグインの`public`ディレクトリにアクセスすることになります。つまり、webman >=1.4.0の場合、`{プロジェクトディレクトリ}/public/app/`以下のディレクトリへのアクセスはサポートされていません。
> 詳細については[アプリケーションプラグイン](./plugin/app.md)を参照してください。

### 静的ファイルのサポートを停止する
静的ファイルのサポートが不要な場合は、`config/static.php`を開き、`enable`オプションをfalseに変更してください。これにより、静的ファイルへのアクセスはすべて404を返します。

### 静的ファイルディレクトリの変更
webmanはデフォルトでpublicディレクトリを静的ファイルディレクトリとして使用します。変更が必要な場合は、`support/helpers.php`の中の`public_path()`ヘルパー関数を変更してください。

### 静的ファイルミドルウェア
webmanにはデフォルトで静的ファイルミドルウェアが付属しており、場所は`app/middleware/StaticFile.php`です。
時には静的ファイルに対して処理を行いたいことがあります。たとえば、静的ファイルにクロスオリジンHTTPヘッダーを追加したり、`.`で始まるファイルへのアクセスを禁止したりすることができます。

`app/middleware/StaticFile.php`の内容は次のようになります：
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
        // `.`で始まる隠しファイルへのアクセスを禁止
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // クロスオリジンHTTPヘッダーの追加
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
このミドルウェアが必要な場合は、`config/static.php`の`middleware`オプションを有効にする必要があります。
