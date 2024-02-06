# Простой пример

## Возвращение строки
**Создание контроллера**

Создайте файл `app/controller/UserController.php` следующего содержания:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Получаем параметр name из GET-запроса. Если параметр name не передан, возвращаем $default_name
        $name = $request->get('name', $default_name);
        // Возвращаем строку в браузер
        return response('hello ' . $name);
    }
}
```

**Доступ**

Откройте в браузере страницу `http://127.0.0.1:8787/user/hello?name=tom`

Браузер вернет `hello tom`

## Возвращение json
Измените файл `app/controller/UserController.php` следующим образом:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return json([
            'code' => 0, 
            'msg' => 'ok', 
            'data' => $name
        ]);
    }
}
```

**Доступ**

Откройте в браузере страницу `http://127.0.0.1:8787/user/hello?name=tom`

Браузер вернет `{"code":0,"msg":"ok","data":"tom"}`

Использование вспомогательной функции json для возврата данных автоматически добавляет заголовок `Content-Type: application/json`

## Возвращение xml
Аналогично, использование функции-помощника `xml($xml)` вернет ответ `xml` с заголовком `Content-Type: text/xml`.

Параметр `$xml` может быть строкой `xml` или объектом `SimpleXMLElement`.

## Возвращение jsonp
Аналогично, использование функции-помощника `jsonp($data, $callback_name = 'callback')` вернет ответ `jsonp`.

## Возвращение представления
Измените файл `app/controller/UserController.php` следующим образом:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return view('user/hello', ['name' => $name]);
    }
}
```

Создайте файл `app/view/user/hello.html` следующего содержания:

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

Откройте в браузере страницу `http://127.0.0.1:8787/user/hello?name=tom`
Браузер вернет html-страницу с содержимым `hello tom`.

Примечание: по умолчанию webman использует оригинальный синтаксис PHP в качестве шаблона. Если хотите использовать другие представления, ознакомьтесь с [представлениями](view.md).
