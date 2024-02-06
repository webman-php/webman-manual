# คอมโพเนนต์ประมวลผลรูปภาพ

## intervention/image

### ที่อยู่โครงการ

https://github.com/Intervention/image
  
### การติดตั้ง
 
```php
composer require intervention/image
```
  
### การใช้งาน

**ส่วนตัวอัปโหลด**

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

เยี่ยมชม http://image.intervention.io/getting_started/introduction
