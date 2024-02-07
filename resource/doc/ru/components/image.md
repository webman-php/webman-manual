# Компонент обработки изображений

## intervention/image

### Адрес проекта

https://github.com/Intervention/image

### Установка

```php
composer require intervention/image
```

### Использование

**Фрагмент страницы загрузки**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="Отправить">
  </form>
```

**Создание `app/controller/UserController.php`**

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
        return response('файл не найден');
    }
    
}
```

### Дополнительная информация

Посетите http://image.intervention.io/getting_started/introduction
