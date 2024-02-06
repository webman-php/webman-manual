# छवि प्रसंस्करण पूर्वक

## intervention / image

### परियोजना पता

https://github.com/Intervention/image

### स्थापना

```php
कॉम्पोजर आवश्यक intervention / image को आवश्यक करते हैं
```

### उपयोग

**अपलोड पृष्ठ से टुकड़ा**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="प्रस्तुत">
  </form>
```

**नया `app/controller/UserController.php` बनाएँ**

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
            return response($image->encode('छवि/png'), 200, ['Content-Type' => 'छवि/png']);
        }
        return response('फ़ाइल नहीं मिली');
    }
    
}
```

### अधिक सामग्री

http://image.intervention.io/getting_started/introduction पर जाएं
