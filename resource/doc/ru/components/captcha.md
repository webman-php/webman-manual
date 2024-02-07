# Компоненты, связанные с капчей


## webman/captcha
Ссылка на проект: https://github.com/webman-php/captcha

### Установка
``` 
composer require webman/captcha
```

### Использование

**Создание файла `app/controller/LoginController.php`**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * Тестовая страница
     */
    public function index(Request $request)
    {
        return view('login/index');
    }

    /**
     * Вывод изображения капчи
     */
    public function captcha(Request $request)
    {
        // Инициализация класса капчи
        $builder = new CaptchaBuilder;
        // Генерация капчи
        $builder->build();
        // Сохранение значения капчи в сессию
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Получение двоичных данных изображения капчи
        $img_content = $builder->get();
        // Вывод двоичных данных капчи
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * Проверка капчи
     */
    public function check(Request $request)
    {
        // Получение поля captcha из POST-запроса
        $captcha = $request->post('captcha');
        // Сравнение значения капчи из сессии
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'Введенный код капчи неверен']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```

**Создание файла шаблона `app/view/login/index.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Тест капчи</title>  
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="Отправить" />
    </form>
</body>
</html>
```

Перейдите на страницу `http://127.0.0.1:8787/login`, внешний вид будет похож на следующий:
  ![](../../assets/img/captcha.png)

### Настройка общих параметров
```php
    /**
     * Вывод изображения капчи
     */
    public function captcha(Request $request)
    {
        // Инициализация класса капчи
        $builder = new CaptchaBuilder;
        // Длина капчи
        $length = 4;
        // Символы, включаемые в капчу
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // Генерация капчи
        $builder->build();
        // Сохранение значения капчи в сессию
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Получение двоичных данных изображения капчи
        $img_content = $builder->get();
        // Вывод двоичных данных капчи
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

Более подробную информацию о доступных методах и параметрах смотрите в документации по ссылке: https://github.com/webman-php/captcha
