# Phản hồi
Thực tế, phản hồi là một đối tượng `support\Response`. Để dễ dàng tạo đối tượng này, webman cung cấp một số hàm trợ giúp.

## Trả về một phản hồi tùy ý

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

Hàm response được triển khai như sau:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

Bạn cũng có thể tạo một đối tượng `response` trống trước, sau đó sử dụng `response->cookie()` `$response->header()` `$response->withHeaders()` `$response->withBody()` ở vị trí thích hợp để thiết lập nội dung trả về.
```php
public function hello(Request $request)
{
    // Tạo một đối tượng
    $response = response();
    
    // .... Bỏ qua logic kinh doanh
    
    // Thiết lập cookie
    $response->cookie('foo', 'giá trị');
    
    // .... Bỏ qua logic kinh doanh
    
    // Thiết lập tiêu đề http
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Giá trị tiêu đề 1',
                'X-Header-Tow' => 'Giá trị tiêu đề 2',
            ]);

    // .... Bỏ qua logic kinh doanh
    
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
Hàm json được triển khai như sau
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

Hàm xml được triển khai như sau:
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
Tạo tệp `app/controller/FooController.php` như sau:

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

Tạo tệp `app/view/foo/hello.html` như sau:

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
xin chào <?=htmlspecialchars($name)?>
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

Hàm redirect được triển khai như sau:
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

## Thiết lập tiêu đề
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('xin chào webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Giá trị tiêu đề' 
        ]);
    }
}
```
Bạn cũng có thể sử dụng phương thức `header` và `withHeaders` để thiết lập tiêu đề từng cái hoặc một lần thiết lập các tiêu đề.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('xin chào webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'Giá trị tiêu đề 1',
            'X-Header-Tow' => 'Giá trị tiêu đề 2',
        ]);
    }
}
```
Bạn cũng có thể thiết lập tiêu đề trước, sau đó thiết lập dữ liệu muốn trả về.
```php
public function hello(Request $request)
{
    // Tạo một đối tượng
    $response = response();
    
    // .... Bỏ qua logic kinh doanh
  
    // Thiết lập tiêu đề http
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Giá trị tiêu đề 1',
                'X-Header-Tow' => 'Giá trị tiêu đề 2',
            ]);

    // .... Bỏ qua logic kinh doanh

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
        return response('xin chào webman')
        ->cookie('foo', 'giá trị');
    }
}
```
Bạn cũng có thể thiết lập cookie trước, sau đó thiết lập dữ liệu muốn trả về.
```php
public function hello(Request $request)
{
    // Tạo một đối tượng
    $response = response();
    
    // .... Bỏ qua logic kinh doanh
    
    // Thiết lập cookie
    $response->cookie('foo', 'giá trị');
    
    // .... Bỏ qua logic kinh doanh

    // Thiết lập dữ liệu muốn trả về
    $response->withBody('Dữ liệu trả về');
    return $response;
}
```
Các tham số đầy đủ của phương thức cookie như sau:

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## Trả về luồng tệp
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

- webman hỗ trợ gửi tệp cực lớn
- Đối với tệp lớn (lớn hơn 2M), webman sẽ không đọc toàn bộ tệp vào bộ nhớ một lần, mà sẽ đọc và gửi tệp theo từng đoạn vào đúng thời điểm
- webman sẽ tối ưu hóa tốc độ đọc gửi tệp dựa trên tốc độ nhận của máy khách, giữ cho việc chiếm bộ nhớ tối thiểu nhất
- Dữ liệu gửi đi là không chặn, không ảnh hưởng đến việc xử lý yêu cầu khác
- Phương thức file sẽ tự động thêm tiêu đề `if-modified-since` và kiểm tra tiêu đề `if-modified-since` trong yêu cầu tiếp theo, nếu tệp không thay đổi thì trả về trạng thái 304 để tiết kiệm băng thông
- Tệp gửi đi sẽ tự động sử dụng tiêu đề `Content-Type` phù hợp để gửi đến trình duyệt
- Nếu tệp không tồn tại, sẽ tự động chuyển và trả về trạng thái 404

## Tải tệp về
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'tên tệp.ico');
    }
}
```
Phương thức download tương tự như phương thức file, nhưng khác biệt ở chỗ:
1. Sau khi thiết lập tên tệp tải xuống, tệp sẽ được tải xuống thay vì hiển thị trên trình duyệt
2. Phương thức download không kiểm tra tiêu đề `if-modified-since`

## Lấy đầu ra
Một số thư viện làm cho nội dung tệp được in trực tiếp ra đầu ra tiêu chuẩn, nghĩa là dữ liệu sẽ được in ra cửa sổ terminal và không gửi đến trình duyệt. Khi đó, chúng ta cần sử dụng `ob_start();` `ob_get_clean();` để bắt dữ liệu vào một biến rồi gửi dữ liệu đó đến trình duyệt. Ví dụ:

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
        // In hình ảnh
        imagejpeg($im);
        // Nhận nội dung hình ảnh
        $image = ob_get_clean();
        
        // Gửi hình ảnh
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
