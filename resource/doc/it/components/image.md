# Componente di elaborazione delle immagini

## intervention/image

### Indirizzo del progetto

https://github.com/Intervention/image

### Installazione

```php
composer require intervention/image
```

### Utilizzo

**Snippet della pagina di caricamento**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="Invia">
  </form>
```

**Creare `app/controller/UserController.php`**

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
        return response('file non trovato');
    }
    
}
```

### Ulteriori informazioni

Visita http://image.intervention.io/getting_started/introduction
