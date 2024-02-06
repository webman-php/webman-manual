# ইমেজ প্রসেসিং কম্পোনেন্ট

## intervention/image

### প্রজেক্ট লিঙ্ক

https://github.com/Intervention/image
  
### ইন্সটলেশন
 
```php
composer require intervention/image
```
  
### ব্যবহার

**আপলোড পেজ ফ্রেগমেন্ট**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="সাবমিট">
  </form>
```

**নতুন `app/controller/UserController.php` তৈরি করুন**

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
        return response('ফাইল পাওয়া যায়নি');
    }
    
}
```
  
### অধিক তথ্য

http://image.intervention.io/getting_started/introduction এ ভিজিট করুন
  
