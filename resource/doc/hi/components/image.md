# छवि प्रसंस्करण संबंधी घटक

## intervention/image

### परियोजना पता

https://github.com/Intervention/image

### स्थापना

```php
composer require intervention/image
```

### उपयोग

**अपलोड पृष्ठांश**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="सबमिट">
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
            return response($image->encode('png'), 200, ['Content-Type' => 'image/png']);
        }
        return response('फ़ाइल नहीं मिली');
    }
    
}
```

### अधिक सामग्री

http://image.intervention.io/getting_started/introduction पर जाएं।
