# مكون معالجة الصور

## intervention/image

### عنوان المشروع

https://github.com/Intervention/image
  
### التثبيت
 
```php
composer require intervention/image
```
  
### الاستخدام

**قطعة الصفحة لتحميل الصور**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="إرسال">
  </form>
```

**إنشاء ملف `app/controller/UserController.php`**

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
        return response('الملف غير موجود');
    }
    
}
```
  
### المزيد

زيارة http://image.intervention.io/getting_started/introduction
