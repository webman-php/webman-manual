## Пользовательская 404
Когда возникает ошибка 404, webman автоматически возвращает содержимое файла `public/404.html`, поэтому разработчики могут изменить файл `public/404.html` напрямую.

Если вы хотите динамически управлять содержимым ошибки 404, например, возвращать JSON-данные `{"code:"404", "msg":"404 not found"}` для ajax-запроса и возвращать шаблон `app/view/404.html` для запросов страниц, пожалуйста, ознакомьтесь со следующим примером.

> Ниже приведен пример на чистом PHP, но принципы работы с другими шаблонами `twig`, `blade`, `think-tmplate` аналогичны.

**Создание файла `app/view/404.html`**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 not found</title>
</head>
<body>
<?=htmlspecialchars($error)?>
</body>
</html>
```

**Добавление следующего кода в файл `config/route.php`:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // возвратить JSON для ajax-запроса
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // вернуть шаблон 404.html для запроса страницы
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## Пользовательская 500
**Создание `app/view/500.html`**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
Пользовательский шаблон ошибки:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**Создание `app/exception/Handler.php` (если каталога не существует, создайте его самостоятельно)**
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
        // возврат JSON для ajax-запроса
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // вернуть шаблон 500.html для запроса страницы
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**Настройка `config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```
