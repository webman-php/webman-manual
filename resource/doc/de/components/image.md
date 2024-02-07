# Bildverarbeitungskomponente

## intervention/image

### Projektadresse

https://github.com/Intervention/image

### Installation

```php
composer require intervention/image
```

### Verwendung

**Upload-Seitenabschnitt**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="Einreichen">
  </form>
```

**Erstellen Sie `app/controller/UserController.php` neu**

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
        return response('Datei nicht gefunden');
    }
}
```

### Weitere Informationen

Besuchen Sie http://image.intervention.io/getting_started/introduction
