# Componente de procesamiento de imágenes

## intervention/image

### Dirección del proyecto

https://github.com/Intervention/image
  
### Instalación
  
```php
composer require intervention/image
```
  
### Uso

**Fragmento de página de carga**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="Enviar">
  </form>
```

**Crear `app/controller/UserController.php`**

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
        return response('archivo no encontrado');
    }
    
}
```
  
### Más información

Visita http://image.intervention.io/getting_started/introduction
