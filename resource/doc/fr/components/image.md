# Composant de traitement d'images

## intervention/image

### Adresse du projet

https://github.com/Intervention/image
  
### Installation
 
```php
composer require intervention/image
```
  
### Utilisation

**Fragment de code pour la page de téléversement**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="Soumettre">
  </form>
```

**Créer `app/controller/UserController.php`**

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
        return response('fichier introuvable');
    }
    
}
```
  
### Pour plus d'informations

Visitez http://image.intervention.io/getting_started/introduction
