# Ví dụ đơn giản

## Trả về chuỗi
**Tạo controller mới**

Tạo file `app/controller/UserController.php` như sau

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Lấy tham số name từ yêu cầu get, nếu không có tham số name được truyền thì trả về $default_name
        $name = $request->get('name', $default_name);
        // Trả về chuỗi cho trình duyệt
        return response('hello ' . $name);
    }
}
```

**Truy cập**

Truy cập vào trình duyệt `http://127.0.0.1:8787/user/hello?name=tom`

Trình duyệt sẽ trả về `hello tom`

## Trả về json
Thay đổi file `app/controller/UserController.php` như sau

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

**Truy cập**

Truy cập vào trình duyệt `http://127.0.0.1:8787/user/hello?name=tom`

Trình duyệt sẽ trả về `{"code":0,"msg":"ok","data":"tom""}`

Sử dụng hàm trợ giúp json sẽ tự động thêm header `Content-Type: application/json` vào phản hồi.

## Trả về xml
Tương tự, sử dụng hàm trợ giúp `xml($xml)` sẽ trả về một phản hồi `xml` có header `Content-Type: text/xml`.

Tham số `$xml` có thể là chuỗi `xml`, hoặc là đối tượng `SimpleXMLElement`

## Trả về jsonp
Tương tự, sử dụng hàm trợ giúp `jsonp($data, $callback_name = 'callback')` sẽ trả về một phản hồi `jsonp`.

## Trả về view
Thay đổi file `app/controller/UserController.php` như sau

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

Tạo file `app/view/user/hello.html` như sau

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

Truy cập vào trình duyệt `http://127.0.0.1:8787/user/hello?name=tom`
sẽ trả về một trang html có nội dung `hello tom`.

Lưu ý: webman mặc định sử dụng cú pháp gốc của php làm mẫu. Để sử dụng các mẫu khác, xem chi tiết tại [view](view.md).
