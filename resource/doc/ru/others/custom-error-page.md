## Пользовательская страница 404
При возникновении ошибки 404 webman автоматически возвращает содержимое файла `public/404.html`, поэтому разработчики могут просто изменить файл `public/404.html`.

Если вы хотите динамически управлять содержимым ошибки 404, например, возвращать json данные `{"code:"404", "msg":"404 not found"}` при ajax-запросе или возвращать шаблон `app/view/404.html` при запросе страницы, ознакомьтесь со следующим примером.

> В следующем примере используется обычный шаблон PHP, принципы для других шаблонов, таких как `twig`, `blade`, `think-template`, аналогичны.

**Создание файла `app/view/404.html`:**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 not found</title>
</head>
<body>
<?= htmlspecialchars($error) ?>
</body>
</html>
```

**Добавление следующего кода в файл `config/route.php`:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // Возврат json при ajax-запросе
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // Возвращение шаблона 404.html при запросе страницы
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## Пользовательская страница 500
**Создание файла `app/view/500.html`:**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
Пользовательский шаблон ошибки:
<?= htmlspecialchars($exception) ?>
</body>
</html>
```

**Создание файла `app/exception/Handler.php` (если каталога не существует, создайте его):**
```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * Рендеринг и возврат
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // Возврат json данных при ajax-запросе
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // Возвращение шаблона 500.html при запросе страницы
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**Настройка файла `config/exception.php`:**
```php
return [
    '' => \app\exception\Handler::class,
];
```
