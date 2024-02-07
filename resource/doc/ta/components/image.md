# பட செயலி கோப்பாடு

## intervention/image

### திட்ட முகவரி

https://github.com/Intervention/image

### நிறுவல்

```php
composer require intervention/image
```

### பயன்பாடு

**பதிவேற்று பகுதி**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="சமர்பிக்கவும்">
  </form>
```

**புதிய `app/controller/UserController.php` உருவாக்கவும்**

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
        return response('கோப்பு கிடைக்கவில்லை');
    }
    
}
```

### மேலும் உள்ளடக்கம்

http://image.intervention.io/getting_started/introduction
