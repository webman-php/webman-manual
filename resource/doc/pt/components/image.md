# Componente de Processamento de Imagens

## intervention/image

### Endereço do Projeto

https://github.com/Intervention/image

### Instalação

```php
composer require intervention/image
```

### Utilização

**Trecho de Página de Upload**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="Enviar">
  </form>
```

**Crie `app/controller/UserController.php`**

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
        return response('arquivo não encontrado');
    }
}
```

### Mais Informações

Visite http://image.intervention.io/getting_started/introduction
