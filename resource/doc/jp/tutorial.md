# シンプルな例

## 文字列を返す
**コントローラーを作成**

次のように `app/controller/UserController.php` ファイルを作成します。

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // GETリクエストからnameパラメータを取得し、nameパラメータが渡されていない場合は$default_nameを返します
        $name = $request->get('name', $default_name);
        // 文字列をブラウザに返します
        return response('hello ' . $name);
    }
}
```

**アクセス**

ブラウザで `http://127.0.0.1:8787/user/hello?name=tom` にアクセスします。

ブラウザは `hello tom` と返します。

## JSONを返す
`app/controller/UserController.php` ファイルを以下のように変更します。

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

ブラウザで `http://127.0.0.1:8787/user/hello?name=tom` にアクセスします。

ブラウザは `{"code":0,"msg":"ok","data":"tom"}` と返します。

データを返す際に`json`ヘルパー関数を使用すると、自動的に`Content-Type: application/json`ヘッダーが付加されます。

## XMLを返す
同様に、`xml($xml)` ヘルパー関数を使用すると、`Content-Type: text/xml` ヘッダーが付加される`xml`応答が返されます。

ここで、`$xml`パラメータは`xml`文字列または`SimpleXMLElement`オブジェクトであることができます。

## JSONPを返す
同様に、`jsonp($data, $callback_name = 'callback')` ヘルパー関数を使用すると、`jsonp`応答を返します。

## ビューを返す
`app/controller/UserController.php` ファイルを以下のように変更します。

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

次のように `app/view/user/hello.html` ファイルを作成します。

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

ブラウザで `http://127.0.0.1:8787/user/hello?name=tom` にアクセスすると、`hello tom` という内容のHTMLページが返されます。

注意：webmanはデフォルトでPHPのネイティブ構文をテンプレートとして使用します。他のビューを使用したい場合は[ビュー](view.md)を参照してください。
