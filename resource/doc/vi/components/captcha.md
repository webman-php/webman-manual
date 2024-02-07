# Các thành phần liên quan đến mã xác minh

## webman/captcha
Địa chỉ dự án: https://github.com/webman-php/captcha

### Cài đặt
``` 
composer require webman/captcha
```

### Sử dụng

**Tạo tệp `app/controller/LoginController.php`**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * Trang kiểm thử
     */
    public function index(Request $request)
    {
        return view('login/index');
    }

    /**
     * Xuất hình ảnh mã xác minh
     */
    public function captcha(Request $request)
    {
        // Khởi tạo lớp mã xác minh
        $builder = new CaptchaBuilder;
        // Tạo mã xác minh
        $builder->build();
        // Lưu giá trị mã xác minh vào phiên
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Lấy dữ liệu nhị phân của hình ảnh mã xác minh
        $img_content = $builder->get();
        // Trả về dữ liệu nhị phân của mã xác minh
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * Kiểm tra mã xác minh
     */
    public function check(Request $request)
    {
        // Lấy trường mã xác minh từ yêu cầu post
        $captcha = $request->post('captcha');
        // So sánh giá trị mã xác minh trong phiên
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'Mã xác minh không đúng']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

**Tạo tệp mẫu `app/view/login/index.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kiểm tra mã xác minh</title>
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="Gửi" />
    </form>
</body>
</html>
```

Truy cập trang `http://127.0.0.1:8787/login` sẽ hiển thị giao diện tương tự như sau:
  ![](../../assets/img/captcha.png)

### Cài đặt thông số phổ biến
```php
    /**
     * Xuất hình ảnh mã xác minh
     */
    public function captcha(Request $request)
    {
        // Khởi tạo lớp mã xác minh
        $builder = new CaptchaBuilder;
        // Độ dài mã xác minh
        $length = 4;
        // Bao gồm những ký tự nào
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // Tạo mã xác minh
        $builder->build();
        // Lưu giá trị mã xác minh vào phiên
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Lấy dữ liệu nhị phân của hình ảnh mã xác minh
        $img_content = $builder->get();
        // Trả về dữ liệu nhị phân của mã xác minh
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

Xem thêm về các giao diện và thông số tại: https://github.com/webman-php/captcha
