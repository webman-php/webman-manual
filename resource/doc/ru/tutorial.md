# Простой пример

## Возвращение строки
**Создание контроллера**

Создайте файл `app/controller/UserController.php` со следующим содержимым

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Получение параметра name из GET-запроса, если параметр name не передается, возвращается $default_name
        $name = $request->get('name', $default_name);
        // Возвращение строки в браузер
        return response('Привет, ' . $name);
    }
}
```

**Доступ**

Откройте браузер и перейдите по ссылке `http://127.0.0.1:8787/user/hello?name=tom`

Браузер вернет `Привет, tom`

## Возвращение JSON
Измените файл `app/controller/UserController.php` следующим образом

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

Откройте браузер и перейдите по ссылке `http://127.0.0.1:8787/user/hello?name=tom`

Браузер вернет `{"code":0,"msg":"ok","data":"tom"}`

Использование вспомогательной функции json для возвращения данных автоматически добавит заголовок `Content-Type: application/json`

## Возвращение XML
Точно так же, использование вспомогательной функции `xml($xml)` вернет ответ с заголовком `Content-Type: text/xml`.

Параметр `$xml` может быть строкой XML или объектом `SimpleXMLElement`.

## Возвращение JSONP
Точно так же, использование вспомогательной функции `jsonp($data, $callback_name = 'callback')` вернет ответ JSONP.

## Возвращение представления
Измените файл `app/controller/UserController.php` следующим образом

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

Создайте файл `app/view/user/hello.html` со следующим содержимым

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
Привет <?=htmlspecialchars($name)?>
</body>
</html>
```

Откройте браузер и перейдите по ссылке `http://127.0.0.1:8787/user/hello?name=tom`

Браузер вернет HTML-страницу с содержимым `Привет, tom`.

Примечание: По умолчанию webman использует стандартный синтаксис PHP в качестве шаблонов. Если вы хотите использовать другие представления, ознакомьтесь с [представлением](view.md).
