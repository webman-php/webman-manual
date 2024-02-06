# Bộ xử lý hình ảnh

## intervention/image

### Địa chỉ dự án

https://github.com/Intervention/image
  
### Cài đặt
 
```php
composer require intervention/image
```
  
### Sử dụng

**Đoạn mã để tải lên trang**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="Gửi">
  </form>
```

**Tạo mới `app/controller/UserController.php`**

```php
<?php
namespace app\controller;
use support\Request;
use Intervention\Image\ImageManagerStatic as Image;

class UserController
{
    public function img(Request $request)
    {
        $file = $request->file('file');
        if ($file && $file->isValid()) {
            $image = Image::make($file)->resize(100, 100);
            return response($image->encode('png'), 200, ['Content-Type' => 'image/png']);
        }
        return response('file not found');
    }
    
}
```
  
  
### Thêm thông tin

Truy cập http://image.intervention.io/getting_started/introduction
