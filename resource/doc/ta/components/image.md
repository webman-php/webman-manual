# படத்தின் செயலாக்க உபகரணம்

## intervention/image

### திட்ட முகவரி

https://github.com/Intervention/image
  
### நிறுவப்படுத்துதல்
 
```php
composer require intervention/image
```
  
### பயன்பாடு

**பதிவேற்ற அகல சிக்கல்**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="சமர்ப்பிக்கவும்">
  </form>
```

**புதிய `app/controller/UserController.php` உருவாக்கு**

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
        return response('கோப்பு காணப்படவில்லை');
    }
    
}
```
  
  
### மேலும் உள்ளடக்கம்

http://image.intervention.io/getting_started/introduction பார்க்க.
