# Phản hồi
Phản hồi thực tế là một đối tượng `support\Response`. Để tạo đối tượng này dễ dàng, webman cung cấp một số hàm trợ giúp.

## Trả về một phản hồi bất kỳ
**Ví dụ**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('xin chào webman');
    }
}
```
Hàm response được hiện thực như sau:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```
Bạn cũng có thể tạo một đối tượng `response` trống trước, sau đó sử dụng `$response->cookie()`, `$response->header()`, `$response->withHeaders()`, `$response->withBody()` để thiết lập nội dung trả về tại vị trí thích hợp.
```php
public function hello(Request $request)
{
    // Tạo một đối tượng
    $response = response();
    
    // .... Logic kinh doanh được bỏ qua
    
    // Thiết lập cookie
    $response->cookie('foo', 'giá trị');
    
    // .... Logic kinh doanh được bỏ qua
    
    // Thiết lập header http
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Giá trị Header 1',
                'X-Header-Tow' => 'Giá trị Header 2',
            ]);

    // .... Logic kinh doanh được bỏ qua

    // Thiết lập dữ liệu muốn trả về
    $response->withBody('Dữ liệu trả về');
    return $response;
}
```

## Trả về json
**Ví dụ**
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
Hàm json được hiện thực như sau
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```

## Trả về xml
**Ví dụ**
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
Hàm xml được hiện thực như sau:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## Trả về view
Tạo tệp mới `app/controller/FooController.php` như sau:

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
Tạo tệp mới `app/view/foo/hello.html` như sau:

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

## Chuyển hướng
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
Hàm redirect được hiện thực như sau:
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

## Thiết lập header
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
Bạn cũng có thể sử dụng phương thức `header` và `withHeaders` để thiết lập từng cái hoặc một lúc nhiều header.
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
Bạn cũng có thể thiết lập header trước, rồi thiết lập dữ liệu muốn trả về cuối cùng.
```php
public function hello(Request $request)
{
    // Tạo một đối tượng
    $response = response();
    
    // .... Logic kinh doanh được bỏ qua
  
    // Thiết lập header http
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... Logic kinh doanh được bỏ qua

    // Thiết lập dữ liệu muốn trả về
    $response->withBody('Dữ liệu trả về');
    return $response;
}
```

## Thiết lập cookie
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->cookie('foo', 'giá trị');
    }
}
```
Bạn cũng có thể thiết lập cookie trước, rồi thiết lập dữ liệu muốn trả về cuối cùng.
```php
public function hello(Request $request)
{
    // Tạo một đối tượng
    $response = response();
    
    // .... Logic kinh doanh được bỏ qua
    
    // Thiết lập cookie
    $response->cookie('foo', 'giá trị');
    
    // .... Logic kinh doanh được bỏ qua

    // Thiết lập dữ liệu muốn trả về
    $response->withBody('Dữ liệu trả về');
    return $response;
}
```
Đầy đủ tham số của phương thức cookie như sau:

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`
## Trả về luồng tập tin
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

- webman hỗ trợ gửi tập tin siêu lớn
- Đối với tập tin lớn (lớn hơn 2M), webman sẽ không đọc toàn bộ tập tin vào bộ nhớ một lần, mà là đọc và gửi tập tin theo đoạn vào thời điểm thích hợp
- webman sẽ tối ưu tốc độ đọc và gửi tập tin dựa trên tốc độ nhận của máy khách, đảm bảo gửi tập tin nhanh nhất và giảm bộ nhớ sử dụng xuống mức thấp nhất
- Việc gửi dữ liệu là không chặn, không ảnh hưởng đến việc xử lý yêu cầu khác
- Phương thức file sẽ tự động thêm tiêu đề `if-modified-since` và kiểm tra `if-modified-since` tiêu đề trong yêu cầu tiếp theo, nếu tập tin không thay đổi thì trả về ngay 304 để tiết kiệm băng thông
- Tập tin được gửi sẽ tự động sử dụng tiêu đề `Content-Type` phù hợp gửi đến trình duyệt
- Nếu tập tin không tồn tại, sẽ tự động chuyển sang phản hồi 404


## Tải xuống tập tin
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'tên tập tin.ico');
    }
}
```
Phương thức download cơ bản giống với phương thức file nhưng khác nhau ở điểm:
1. Sau khi thiết lập tên tập tin tải xuống, tập tin sẽ được tải xuống thay vì hiển thị trên trình duyệt
2. Phương thức download sẽ không kiểm tra tiêu đề `if-modified-since`


## Nhận đầu ra
Một số thư viện sẽ in nội dung tập tin trực tiếp ra đầu ra chuẩn, có nghĩa là dữ liệu sẽ được in ra trong cửa sổ dòng lệnh và không gửi đến trình duyệt. Trong trường hợp này, chúng ta cần sử dụng `ob_start();` `ob_get_clean();` để bắt đầu và kết thúc việc lấy dữ liệu vào một biến, sau đó gửi dữ liệu đến trình duyệt, ví dụ:

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // Tạo hình ảnh
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

        // Bắt đầu lấy đầu ra
        ob_start();
        // In ra hình ảnh
        imagejpeg($im);
        // Lấy nội dung hình ảnh
        $image = ob_get_clean();
        
        // Gửi hình ảnh
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
