## Обработка статических файлов
webman поддерживает доступ к статическим файлам, которые находятся в папке `public`. Например, доступ к `http://127.0.0.8787/upload/avatar.png` фактически означает доступ к `{основной_каталог_проекта}/public/upload/avatar.png`.

> **Примечание**
> Начиная с версии 1.4, webman поддерживает приложения-плагины, и доступ к статическим файлам, начинающимся с `/app/xx/имя_файла`, фактически означает доступ к папке `public` приложения-плагина. То есть начиная с webman >=1.4.0, доступ к папкам в `{основной_каталог_проекта}/public/app/` не поддерживается.
> Подробнее см. [Приложения-плагины](./plugin/app.md)

### Отключение поддержки статических файлов
Если необходима отключение поддержки статических файлов, откройте файл `config/static.php` и измените параметр `enable` на false. После отключения все попытки доступа к статическим файлам будут возвращать ошибку 404.

### Изменение папки со статическими файлами
По умолчанию webman использует папку `public` в качестве папки со статическими файлами. Если необходимо внести изменения, измените функцию `public_path()` в файле `support/helpers.php`.

### Middleware для статических файлов
webman поставляется со встроенным middleware для статических файлов, расположенным в `app/middleware/StaticFile.php`.
Иногда нам нужно выполнить некоторые операции с статическими файлами, например, добавить заголовки HTTP-запроса по CORS, запретить доступ к файлам, имена которых начинаются с точки (`.`).

Содержание файла `app/middleware/StaticFile.php` выглядит примерно так:
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // Запрет доступа к скрытым файлам, начинающимся с точки (.)
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 forbidden</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // Добавление заголовков HTTP-запроса по CORS
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
Если требуется использование этого middleware, необходимо включить его в параметре `middleware` файла `config/static.php`.

