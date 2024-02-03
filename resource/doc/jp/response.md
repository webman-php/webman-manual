# レスポンス

レスポンスは実際には `support\Response` オブジェクトであり、このオブジェクトを簡単に作成するために、webmanはいくつかのヘルパー関数を提供しています。

## 任意のレスポンスを返す

**例**
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

response 関数の実装は以下の通りです：
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

また、空の `response` オブジェクトを作成して、適切な位置で `$response->cookie()`、`$response->header()`、`$response->withHeaders()`、`$response->withBody()` を利用して返り値を設定することもできます。
```php
public function hello(Request $request)
{
    // オブジェクトを作成
    $response = response();
    
    // .... ビジネスロジックは省略
    
    // クッキーを設定
    $response->cookie('foo', 'value');
    
    // .... ビジネスロジックは省略
    
    // HTTPヘッダを設定
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... ビジネスロジックは省略
    
    // 返すデータをセット
    $response->withBody('返すデータ');
    return $response;
}
```

## JSONを返す
**例**
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
json 関数の実装は以下の通りです
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```

## XMLを返す
**例**
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

xml 関数の実装は以下の通りです：
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## ビューを返す
次のようにファイルを作成します: `app/controller/FooController.php`
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

そして、次のように `app/view/foo/hello.html` ファイルを作成します:
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

## リダイレクト
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

redirect 関数の実装は以下の通りです：
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

## ヘッダーの設定
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
また、`header` および `withHeaders` メソッドを使用して個々のヘッダーまたは複数のヘッダーを設定することもできます。
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
また、ヘッダーを事前に設定しておき、最後に返すデータを設定することもできます。
```php
public function hello(Request $request)
{
    // オブジェクトを作成
    $response = response();
    
    // .... ビジネスロジックは省略
  
    // HTTPヘッダを設定
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... ビジネスロジックは省略

    // 返すデータを設定
    $response->withBody('返すデータ');
    return $response;
}
```

## クッキーの設定

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

また、クッキーを事前に設定し、最後に返すデータを設定することもできます。
```php
public function hello(Request $request)
{
    // オブジェクトを作成
    $response = response();
    
    // .... ビジネスロジックは省略
    
    // クッキーを設定
    $response->cookie('foo', 'value');
    
    // .... ビジネスロジックは省略

    // 返すデータを設定
    $response->withBody('返すデータ');
    return $response;
}
```

cookie メソッドの完全な引数は次のとおりです：

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`


## ファイルストリームを返す
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

- webmanは超大きなファイルを送信することができます
- 大きなファイル（2Mを超える）の場合、webmanはファイル全体を一度にメモリに読み込むのではなく、適切なタイミングでファイルをセグメントに読み取り、送信します
- webmanはクライアントの受信速度に応じてファイルの読み込みと送信速度を最適化し、ファイルを最速で送信しながらメモリ使用量を最小限に抑えます
- データ送信はノンブロッキングであり、他のリクエスト処理に影響を与えません
- file メソッドは自動的に `if-modified-since` ヘッダーを追加し、次のリクエスト時にファイルが変更されていない場合は、帯域幅を節約するために直接304を返します
- 送信するファイルは、ブラウザに適切な `Content-Type` ヘッダーを使用して自動的に送信されます
- ファイルが存在しない場合、自動的に404レスポンスに変換されます


## ファイルのダウンロード
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'ファイル名.ico');
    }
}
```
download メソッドは基本的に file メソッドと同じですが、以下の点が異なります：
1、ダウンロードするファイル名を設定した後、ファイルはダウンロードされますが、ブラウザに表示されません
2、download メソッドは `if-modified-since` ヘッダーをチェックしません

## 出力の取得
いくつかのライブラリは、ファイルのコンテンツを直接標準出力にプリントするもので、データはブラウザに送信されるのではなく、コマンドライン端末に表示されます。このような場合、`ob_start();`、`ob_get_clean();` を使用してデータを変数にキャプチャし、そのデータをブラウザに送信する必要があります。例えば：

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // 画像を作成
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

        // 出力を取得開始
        ob_start();
        // 画像を出力
        imagejpeg($im);
        // 画像の内容を取得
        $image = ob_get_clean();
        
        // 画像を送信
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
