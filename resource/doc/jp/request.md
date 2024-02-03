# 説明

## リクエストオブジェクトの取得
webmanは自動的にリクエストオブジェクトをactionメソッドの最初の引数に注入します。例えば、

**例**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // getリクエストからnameパラメータを取得し、パラメータが渡されていない場合は$default_nameを返します。
        $name = $request->get('name', $default_name);
        // ブラウザに文字列を返す
        return response('hello ' . $name);
    }
}
```

`$request`オブジェクトを使用することで、リクエストに関連する任意のデータを取得できます。

**他のクラスで現在のリクエストの`$request`オブジェクトを取得する場合は、ヘルパー関数`request()`を使用してください**。

## ゲットリクエストパラメータの取得

**全てのゲット配列を取得**
```php
$request->get();
```
リクエストにゲットパラメータがない場合は空の配列が返されます。

**ゲット配列の特定の値を取得**
```php
$request->get('name');
```
ゲット配列にその値が含まれていない場合はnullが返されます。

また、ゲットメソッドの第2引数にデフォルト値を渡すこともできます。ゲット配列に対応する値が見つからない場合はデフォルト値が返されます。例：
```php
$request->get('name', 'tom');
```

## ポストリクエストパラメータの取得
**全てのポスト配列を取得**
```php
$request->post();
```
リクエストにポストパラメータがない場合は空の配列が返されます。

**ポスト配列の特定の値を取得**
```php
$request->post('name');
```
ポスト配列にその値が含まれていない場合はnullが返されます。

ゲットメソッドと同様に、ポストメソッドの第2引数にデフォルト値を渡すこともできます。ポスト配列に対応する値が見つからない場合はデフォルト値が返されます。例：
```php
$request->post('name', 'tom');
```

## リクエストの生ポストボディを取得
```php
$post = $request->rawBody();
```
この機能は `php-fpm` の `file_get_contents("php://input");` 操作と似ています。非`application/x-www-form-urlencoded`形式のポストリクエストデータを取得する際に便利です。

## ヘッダーの取得
**全てのヘッダー配列を取得**
```php
$request->header();
```
リクエストにヘッダーパラメータがない場合は空の配列が返されます。すべてのキーは小文字であることに注意してください。

**ヘッダー配列の特定の値を取得**
```php
$request->header('host');
```
ヘッダー配列にその値が含まれていない場合はnullが返されます。すべてのキーは小文字であることに注意してください。

ゲットメソッドと同様に、ヘッダーメソッドの第2引数にデフォルト値を渡すこともできます。ヘッダー配列に対応する値が見つからない場合はデフォルト値が返されます。例：
```php
$request->header('host', 'localhost');
```

## クッキーの取得
**全てのクッキー配列を取得**
```php
$request->cookie();
```
リクエストにクッキーパラメータがない場合は空の配列が返されます。

**クッキー配列の特定の値を取得**
```php
$request->cookie('name');
```
クッキー配列にその値が含まれていない場合はnullが返されます。

ゲットメソッドと同様に、クッキーメソッドの第2引数にデフォルト値を渡すこともできます。クッキー配列に対応する値が見つからない場合はデフォルト値が返されます。例：
```php
$request->cookie('name', 'tom');
```

## 全ての入力の取得
`post` と `get` を含む。
```php
$request->all();
```

## 特定の入力値の取得
`post` と `get` から特定の値を取得。
```php
$request->input('name', $default_value);
```

## 一部の入力データの取得
`post` と `get` から一部のデータを取得。
```php
// username と password の配列を取得します。該当するキーがない場合は無視されます
$only = $request->only(['username', 'password']);
// avatar と age を除く全ての入力を取得します
$except = $request->except(['avatar', 'age']);
```

## アップロードファイルの取得
**全てのアップロードファイル配列を取得**
```php
$request->file();
```

フォームは以下のようになります。
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

`$request->file()`で返される形式は次のようになります。
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
これは`webman\Http\UploadFile`インスタンスの配列です。`webman\Http\UploadFile`クラスはPHPの組み込みの[`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php)クラスを継承しており、いくつかの便利なメソッドを提供しています。

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // ファイルが有効かどうか、例: 真|偽
            var_export($spl_file->getUploadExtension()); // アップロードファイルの拡張子、例: 'jpg'
            var_export($spl_file->getUploadMimeType()); // アップロードファイルのMIMEタイプ、例: 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // アップロードエラーコードの取得、例: UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // アップロードファイル名、例: 'my-test.jpg'
            var_export($spl_file->getSize()); // ファイルサイズを取得、例: 13364、単位はバイト
            var_export($spl_file->getPath()); // アップロードディレクトリを取得、例: '/tmp'
            var_export($spl_file->getRealPath()); // 一時ファイルのパスを取得、例: `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**注意:**

- ファイルはアップロード後、一時ファイルとして名前が付けられます。例: `/tmp/workerman.upload.SRliMu`
- アップロードファイルのサイズは[defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html)で制限されます。デフォルトは10Mで、`config/server.php`ファイルで`max_package_size`を変更してデフォルト値を変更できます。
- リクエスト終了時に一時ファイルは自動的にクリアされます
- リクエストにアップロードファイルがない場合は`$request->file()`が空の配列を返します
- アップロードファイルは`move_uploaded_file()`メソッドをサポートしていません。代わりに`$file->move()`メソッドを使用してください。下記の例を参照してください。

### 特定のアップロードファイルの取得
```php
$request->file('avatar');
```
ファイルが存在する場合は該当するファイルの`webman\Http\UploadFile`インスタンスが返され、存在しない場合はnullが返されます。

**例**
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

## ホストの取得
リクエストのホスト情報を取得します。
```php
$request->host();
```
リクエストのアドレスが標準の80番または443番ポートでない場合、ホスト情報にポートが含まれる可能性があります。ポート情報が必要ない場合は、第1引数に`true`を渡してください。

```php
$request->host(true);
```

## リクエストメソッドの取得
```php
 $request->method();
```
返り値は`GET`、`POST`、`PUT`、`DELETE`、`OPTIONS`、`HEAD`のいずれかです。

## リクエストURIの取得
```php
$request->uri();
```
リクエストのURIを返します。パスとクエリ文字列が含まれます。

## リクエストパスの取得

```php
$request->path();
```
リクエストのパス部分を返します。

## リクエストクエリ文字列の取得

```php
$request->queryString();
```
リクエストのクエリ文字列部分を返します。

## リクエストURLの取得
`url()`メソッドは`Query` パラメーターを含まないURLを返します。
```php
$request->url();
```
`//www.workerman.net/workerman-chat`のように返されます。

`fullUrl()`メソッドは`Query` パラメーターを含むURLを返します。
```php
$request->fullUrl();
```
`//www.workerman.net/workerman-chat?type=download`のように返されます。

> **注意**
> `url()` および `fullUrl()` ではプロトコル部分( httpまたはhttps)は返されません。
> これはブラウザで `//example.com` のようにプロトコルが指定されていないアドレスを使うと、自動的に現在のサイトのプロトコル (httpまたはhttps) でリクエストされるためです。

nginxプロキシを使用している場合は、nginxの設定に `proxy_set_header X-Forwarded-Proto $scheme;` を追加してください。[nginxプロキシの参照](others/nginx-proxy.md)。
これにより、`$request->header('x-forwarded-proto');` を使用してhttpまたはhttpsを判断できます。例：
```php
echo $request->header('x-forwarded-proto'); // httpまたはhttpsを出力
```

## リクエストHTTPバージョンの取得

```php
$request->protocolVersion();
```
文字列 `1.1` または `1.0` を返します。


## リクエストセッションIDの取得

```php
$request->sessionId();
```
英数字で構成された文字列を返します


## リクエストクライアントIPの取得
```php
$request->getRemoteIp();
```

## リクエストクライアントポートの取得
```php
$request->getRemotePort();
```
## リクエストクライアントの実際のIPを取得する
```php
$request->getRealIp($safe_mode=true);
```

プロジェクトで代理(例えばnginx)を使用している場合、`$request->getRemoteIp()`を使用しても通常は代理サーバーのIP（たとえば`127.0.0.1`、`192.168.x.x`のようなもの）が取得され、クライアントの実際のIPではないことがよくあります。その場合は`$request->getRealIp()`を使用して、クライアントの実際のIPを取得することができます。

`$request->getRealIp()`は、HTTPヘッダーの`x-real-ip`、`x-forwarded-for`、`client-ip`、`x-client-ip`、`via`フィールドから実際のIPを取得しようと試みます。

> HTTPヘッダーは簡単に偽装できるため、このメソッドで取得したクライアントのIPは100%信頼できるわけではなく、特に`$safe_mode`がfalseの場合はそうです。代理を介してクライアントの実際のIPを確実に取得する信頼性の高い方法は、安全な代理サーバーのIPが既知であり、実際のIPを携帯するのがどのHTTPヘッダーかが明確である場合です。`$request->getRemoteIp()`が返すIPが既知の安全な代理サーバーであることを確認し、それから`$request->header('携帯真実IPのHTTPヘッダー')`を使用して実際のIPを取得します。


## サーバーのIPを取得する
```php
$request->getLocalIp();
```

## サーバーのポートを取得する
```php
$request->getLocalPort();
```

## Ajaxリクエストかどうかを判断する
```php
$request->isAjax();
```

## Pjaxリクエストかどうかを判断する
```php
$request->isPjax();
```

## JSONレスポンスを期待するかどうかを判断する
```php
$request->expectsJson();
```

## クライアントがJSONレスポンスを受け入れるかどうかを判断する
```php
$request->acceptJson();
```

## リクエストのプラグイン名を取得する
プラグインのリクエストでは空の文字列`''`が返されます。
```php
$request->plugin;
```
> この機能はwebman>=1.4.0が必要です

## リクエストのアプリ名を取得する
単一のアプリの場合、常に空の文字列`''`が返されます。[マルチアプリ](multiapp.md)の場合はアプリ名が返されます。
```php
$request->app;
```

> 無名関数はどのアプリにも属していないため、クロージャルートからのリクエストでは`$request->app`は常に空の文字列`''`を返します 
> クロージャルートは[ルート](route.md)を参照してください 

## リクエストのコントローラークラス名を取得する
コントローラーに対応するクラス名を取得します
```php
$request->controller;
```
例えば、`app\controller\IndexController`のように返されます。

> 無名関数はどのコントローラーにも属していないため、クロージャルートからのリクエストでは`$request->controller`は常に空の文字列`''`を返します 
> クロージャルートは[ルート](route.md)を参照してください 

## リクエストのメソッド名を取得する
リクエストに対応するコントローラーメソッド名を取得します
```php
$request->action;
```
`index`のように返されます。

> 無名関数はどのコントローラーにも属していないため、クロージャルートからのリクエストでは`$request->action`は常に空の文字列`''`を返します 
> クロージャルートは[ルート](route.md)を参照してください 

