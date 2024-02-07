# シンプルな例

## 文字列を返す
**コントローラーの作成**

以下のようにファイル `app/controller/UserController.php` を新規作成します。

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // nameパラメーターをGETリクエストから取得し、パラメーターが指定されていない場合は$default_nameを返します
        $name = $request->get('name', $default_name);
        // 文字列をブラウザに返す
        return response('hello ' . $name);
    }
}
```

**アクセス**

ブラウザで `http://127.0.0.1:8787/user/hello?name=tom` にアクセスすると、ブラウザは `hello tom` と返します。

## JSONを返す
ファイル `app/controller/UserController.php` を以下のように変更します。

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

**アクセス**

ブラウザで `http://127.0.0.1:8787/user/hello?name=tom` にアクセスすると、ブラウザは `{"code":0,"msg":"ok","data":"tom""}` と返します。

データをJSONヘルパー関数で返すと、自動的に `Content-Type: application/json` ヘッダーが追加されます。

## XMLを返す
同様に、ヘルパー関数 `xml($xml)` を使用すると、`text/xml` ヘッダーのついた `XML` レスポンスが返ります。

ここでの `$xml` パラメータは`XML`文字列または`SimpleXMLElement`オブジェクトとなります。

## JSONPを返す
同様に、ヘルパー関数 `jsonp($data, $callback_name = 'callback')` を使用すると、`JSONP` レスポンスが返ります。

## ビューを返す
ファイル `app/controller/UserController.php` を以下のように変更します。

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

ファイル `app/view/user/hello.html` を以下のように新規作成します。

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

ブラウザで `http://127.0.0.1:8787/user/hello?name=tom` にアクセスすると、`hello tom` という内容のHTMLページが返ります。

注意：webmanはデフォルトでPHPのネイティブ構文を使用するためのテンプレートエンジンです。他のビューを使用する場合は、[ビュー](view.md) を参照してください。
