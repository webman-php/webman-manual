# Görüntü İşleme Bileşeni

## intervention/image

### Proje Adresi

https://github.com/Intervention/image

### Kurulum

```php
composer require intervention/image
```

### Kullanım

**Yükleme Sayfası Parçası**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="Gönder">
  </form>
```

**`app/controller/UserController.php` içinde yeni oluşturun**

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
        return response('dosya bulunamadı');
    }
}
```

### Daha Fazla İçerik

http://image.intervention.io/getting_started/introduction
