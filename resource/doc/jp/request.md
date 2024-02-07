＃ 説明

## リクエストオブジェクトの取得
webmanは自動的にリクエストオブジェクトをアクションメソッドの最初のパラメータに注入します。例えば、

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
        // クエリ文字列からnameパラメータを取得します。もしnameパラメータが渡されていない場合は$default_nameを返します。
        $name = $request->get('name', $default_name);
        // 文字列をブラウザに返します
        return response('hello ' . $name);
    }
}
```

`$request`オブジェクトを使用することで、リクエストに関連する任意のデータを取得できます。

**時々、他のクラスで現在のリクエストの`$request`オブジェクトを取得したい場合は、`request()`ヘルパー関数を使用します。**

## GETパラメータの取得
**完全なGET配列を取得**
```php
$request->get();
```
リクエストにGETパラメータがない場合は空の配列が返されます。

**GET配列の値を取得**
```php
$request->get('name');
```
GET配列にこの値が含まれていない場合はnullが返されます。

また、第2引数にデフォルト値を指定して、対応する値が見つからない場合にはデフォルト値を返すこともできます。例：
```php
$request->get('name', 'tom');
```

## POSTパラメータの取得
**完全なPOST配列を取得**
```php
$request->post();
```
リクエストにPOSTパラメータがない場合は空の配列が返されます。

**POST配列の値を取得**
```php
$request->post('name');
```
POST配列にこの値が含まれていない場合はnullが返されます。

GET方法と同様に、第2引数にデフォルト値を指定して、対応する値が見つからない場合にはデフォルト値を返すこともできます。例：
```php
$request->post('name', 'tom');
```

## 原始リクエストのPOSTボディの取得
```php
$post = $request->rawBody();
```
これは `php-fpm` の `file_get_contents("php://input");` に似た機能です。 `application/x-www-form-urlencoded` 形式以外のPOSTリクエストデータを取得する際に便利です。

## ヘッダーの取得
**完全なヘッダー配列を取得**
```php
$request->header();
```
リクエストにヘッダーパラメータがない場合は空の配列が返されます。すべてのキーは小文字です。

**ヘッダー配列の値を取得**
```php
$request->header('host');
```
ヘッダー配列にこの値が含まれていない場合はnullが返されます。すべてのキーは小文字です。

GETメソッドと同様に、第2引数にデフォルト値を指定して、対応する値が見つからない場合にはデフォルト値を返すこともできます。例：
```php
$request->header('host', 'localhost');
```

## クッキーの取得
**完全なクッキー配列を取得**
```php
$request->cookie();
```
リクエストにクッキーパラメータがない場合は空の配列が返されます。

**クッキー配列の値を取得**
```php
$request->cookie('name');
```
クッキー配列にこの値が含まれていない場合はnullが返されます。

GETメソッドと同様に、第2引数にデフォルト値を指定して、対応する値が見つからない場合にはデフォルト値を返すこともできます。例：
```php
$request->cookie('name', 'tom');
```

## すべての入力を取得
`post` `get` の集合を含みます。
```php
$request->all();
```

## 特定の入力値を取得
`post` `get` の集合から特定の値を取得します。
```php
$request->input('name', $default_value);
```

## 一部の入力データを取得
`post` `get` の集合から一部のデータを取得します。
```php
// usernameおよびpasswordからなる配列を取得し、対応するキーがない場合は無視します
$only = $request->only(['username', 'password']);
// avatarおよびage以外のすべての入力を取得します
$except = $request->except(['avatar', 'age']);
```

## アップロードファイルの取得
**完全なアップロードファイル配列を取得**
```php
$request->file();
```

以下のようなフォームです
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

`$request->file()`が返す形式は以下のようです:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
これは`webman\Http\UploadFile`インスタンスの配列です。 `webman\Http\UploadFile`クラスはPHP組み込みの [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) クラスを継承しており、便利なメソッドを提供しています。

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // ファイルが有効かどうか、例：true|false
            var_export($spl_file->getUploadExtension()); // アップロードファイルの拡張子、例：'jpg'
            var_export($spl_file->getUploadMimeType()); // アップロードファイルのMIMEタイプ、例：'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // アップロードエラーコードを取得、例：UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // アップロードファイル名、例：'my-test.jpg'
            var_export($spl_file->getSize()); // ファイルサイズを取得、例：13364、単位はバイト
            var_export($spl_file->getPath()); // アップロードディレクトリを取得、例：'/tmp'
            var_export($spl_file->getRealPath()); // 一時ファイルパスを取得、例：`/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**注意：**

- ファイルはアップロードされた後、一時ファイルに命名されます（例： `/tmp/workerman.upload.SRliMu`）
- アップロードファイルのサイズは[defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html)によって制限されます。デフォルトは10Mであり、`config/server.php` ファイルで `max_package_size` を変更することでデフォルト値を変更できます。
- リクエストが終了すると、一時ファイルが自動的に削除されます。
- リクエストにアップロードファイルがない場合、`$request->file()`は空の配列を返します。
- アップロードされたファイルは `move_uploaded_file()` メソッドをサポートしていません。代わりに `$file->move()` メソッドを使用してください。以下の例を参照してください。

### 特定のアップロードファイルを取得
```php
$request->file('avatar');
```
ファイルが存在する場合は、対応するファイルの`webman\Http\UploadFile`インスタンスが返されます。存在しない場合はnullが返されます。

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
## ホストを取得する
リクエストのホスト情報を取得します。
```php
$request->host();
```
リクエストのアドレスが標準ではない80または443ポートである場合、ホスト情報にはポートが含まれることがあります。例えば`example.com:8080`のような形です。ポートを除外する場合は、第1引数に`true`を渡します。

```php
$request->host(true);
```

## リクエストメソッドを取得する
```php
$request->method();
```
戻り値は`GET`、`POST`、`PUT`、`DELETE`、`OPTIONS`、`HEAD`のいずれかになります。

## リクエストURIを取得する
```php
$request->uri();
```
パスとクエリ文字列の両方を含むリクエストのURIを返します。

## リクエストパスを取得する

```php
$request->path();
```
リクエストのパス部分を返します。


## リクエストクエリ文字列を取得する

```php
$request->queryString();
```
リクエストのクエリ文字列部分を返します。

## リクエストURLを取得する
`url()`メソッドは`Query`パラメータを含まないURLを返します。
```php
$request->url();
```
`//www.workerman.net/workerman-chat`のような形で返されます。

`fullUrl()`メソッドは`Query`パラメータを含むURLを返します。
```php
$request->fullUrl();
```
`//www.workerman.net/workerman-chat?type=download`のような形で返されます。

> **注意**
> `url()`と`fullUrl()`はプロトコル部分（httpまたはhttps）を含みません。
> ブラウザでは、`//example.com`のように`//`で始まるアドレスを使用すると、自動的に現在のサイトのプロトコルを認識し、httpまたはhttpsでリクエストが自動的に行われます。

Nginxプロキシを使用している場合は、`proxy_set_header X-Forwarded-Proto $scheme;`をNginxの設定に追加する必要があります。[Nginxプロキシの参考](others/nginx-proxy.md)、これにより`$request->header('x-forwarded-proto');`を使用してhttpまたはhttpsを判断できます。例えば：
```php
echo $request->header('x-forwarded-proto'); // httpまたはhttpsが出力されます
```
