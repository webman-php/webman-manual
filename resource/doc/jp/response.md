# レスポンス
実際のレスポンスは `support\Response` オブジェクトです。このオブジェクトを簡単に作成するため、webman はいくつかのヘルパー関数を提供しています。

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
また、空の `response` オブジェクトをまず作成し、適切な位置で `$response->cookie()`、`$response->header()`、`$response->withHeaders()`、`$response->withBody()` を使って返す内容を設定することもできます。

```php
public function hello(Request $request)
{
    // オブジェクトを作成
    $response = response();
    
    // .... ビジネスロジック省略
    
    // Cookie を設定
    $response->cookie('foo', 'value');
    
    // .... ビジネスロジック省略
    
    // HTTP ヘッダを設定
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... ビジネスロジック省略

    // 返すデータを設定
    $response->withBody('返すデータ');
    return $response;
}
```

## JSON を返す
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
json 関数の実装は以下の通りです：
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```

## XML を返す
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
以下のように、 `app/controller/FooController.php` ファイルを作成します。
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
次に、 `app/view/foo/hello.html` ファイルを作成します。
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

## ヘッダーを設定
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
`header` および `withHeaders` メソッドを使用して、単一または複数のヘッダーを設定することもできます。

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
また、ヘッダーを事前に設定し、最後に返すデータを設定することもできます。

```php
public function hello(Request $request)
{
    // オブジェクトを作成
    $response = response();
    
    // .... ビジネスロジック省略
  
    // HTTP ヘッダを設定
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... ビジネスロジック省略

    // 返すデータを設定
    $response->withBody('返すデータ');
    return $response;
}
```

## Cookie を設定
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
また、Cookie を事前に設定し、最後に返すデータを設定することもできます。
```php
public function hello(Request $request)
{
    // オブジェクトを作成
    $response = response();
    
    // .... ビジネスロジック省略
    
    // Cookie を設定
    $response->cookie('foo', 'value');
    
    // .... ビジネスロジック省略

    // 返すデータを設定
    $response->withBody('返すデータ');
    return $response;
}
```
`cookie` メソッドの完全なパラメータは次のとおりです：
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
- webman は超大きなファイルを送信することをサポートしています。
- 大きなファイル（2M を超える）については、webman はファイル全体を一度にメモリに読み込むのではなく、適切なタイミングでファイルを分割して読み込み、送信します。
- webman はクライアントの受信速度に応じてファイルの読み取り送信速度を最適化し、ファイル送信時のメモリ使用量を最小限に抑えます。
- データ送信はノンブロッキングで行われ、他のリクエスト処理に影響を与えません。
- `file` メソッドは自動的に `if-modified-since` ヘッダを追加し、次のリクエスト時に `if-modified-since` ヘッダをチェックします。ファイルが変更されていない場合は帯域幅を節約するために直接 304 を返します。
- 送信するファイルは、ブラウザに適切な `Content-Type` ヘッダを使用して自動的に送信されます。
- ファイルが存在しない場合、自動的に 404 レスポンスに変換されます。
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
downloadメソッドはfileメソッドとほぼ同じですが、違いは次のとおりです。
1. ダウンロードするファイル名を設定すると、ファイルがダウンロードされます。ブラウザに表示されるのではありません。
2. downloadメソッドは`if-modified-since`ヘッダーを確認しません。


## 出力の取得
一部のライブラリは、ファイルコンテンツを直接標準出力に出力し、つまりデータはブラウザに送信されず、コマンドライン端末に出力されます。このような場合、`ob_start();` `ob_get_clean();` を使用してデータを変数にキャプチャし、その後ブラウザにデータを送信する必要があります。例：

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // 画像の生成
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

        // 出力のキャプチャを開始
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
