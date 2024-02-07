## 静的ファイルの処理
webmanは静的ファイルのアクセスをサポートしており、静的ファイルはすべて`public`ディレクトリに配置されています。例えば、`http://127.0.0.8787/upload/avatar.png`にアクセスすると実際には`{プロジェクトディレクトリ}/public/upload/avatar.png`にアクセスしています。

> **注意**
> webman 1.4からアプリケーションプラグインをサポートしており、`/app/xx/ファイル名`で始まる静的ファイルアクセスは実際にはアプリケーションプラグインの`public`ディレクトリにアクセスしています。つまり、webman >=1.4.0では`{プロジェクトディレクトリ}/public/app/`以下のディレクトリにはアクセスできません。
> 詳細については[アプリケーションプラグイン](./plugin/app.md)を参照してください。

### 静的ファイルサポートの無効化
静的ファイルのサポートが不要な場合は、`config/static.php`を開いて`enable`オプションをfalseに変更してください。無効化すると、すべての静的ファイルのアクセスは404を返します。

### 静的ファイルディレクトリの変更
webmanでは、デフォルトでpublicディレクトリが静的ファイルディレクトリとして使用されます。変更が必要な場合は、`support/helpers.php`の`public_path()`ヘルパー関数を変更してください。

### 静的ファイルミドルウェア
webmanには、静的ファイルミドルウェアが付属しており、場所は`app/middleware/StaticFile.php`です。
静的ファイルに対して特定の処理を行いたい場合、例えば静的ファイルにクロスドメインのヘッダーを追加したり、ドット（`.`）で始まるファイルへのアクセスを禁止したりする場合、このミドルウェアを使用できます。

`app/middleware/StaticFile.php`の内容は以下のようになります：
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
        // ドットで始まる隠しファイルのアクセスを禁止
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // クロスドメインのヘッダーを追加
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
このミドルウェアが必要な場合は、`config/static.php`の`middleware`オプションで有効にしてください。
