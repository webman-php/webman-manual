# คอมโพเนนต์การประมวลภาพ

## intervention/image

### ที่อยู่โปรเจค

https://github.com/Intervention/image

### การติดตั้ง

```php
composer require intervention/image
```

### การใช้งาน

**โค้ดส่วนหน้า**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="Submit">
  </form>
```

**สร้าง `app/controller/UserController.php` ใหม่**

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

### เนื้อหาเพิ่มเติม

เข้าชม http://image.intervention.io/getting_started/introduction
