# Ответ
Фактически, ответ представляет собой объект `support\Response`. Для удобства создания этого объекта webman предоставляет некоторые вспомогательные функции.

## Возвращает любой ответ

**Пример**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

Реализация функции response:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

Также вы можете сначала создать пустой объект `response`, а затем в нужном месте использовать методы `$response->cookie()`, `$response->header()`, `$response->withHeaders()`, `$response->withBody()` для установки возвращаемого содержимого.
```php
public function hello(Request $request)
{
    // Создание объекта
    $response = response();
    
    // .... Пропущена бизнес-логика
    
    // Установка cookie
    $response->cookie('foo', 'value');
    
    // .... Пропущена бизнес-логика
    
    // Установка HTTP-заголовка
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... Пропущена бизнес-логика
    
    // Установка данных для возврата
    $response->withBody('Возвращаемые данные');
    return $response;
}
```

## Возвращает json
**Пример**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```
Реализация функции json:
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```


## Возвращает xml
**Пример**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        $xml = <<<XML
               <?xml version='1.0' standalone='yes'?>
               <values>
                   <truevalue>1</truevalue>
                   <falsevalue>0</falsevalue>
               </values>
               XML;
        return xml($xml);
    }
}
```
Реализация функции xml:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## Возвращает представление
Создайте файл `app/controller/FooController.php` следующим образом:

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return view('foo/hello', ['name' => 'webman']);
    }
}
```

Создайте файл `app/view/foo/hello.html` следующим образом:

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

## Перенаправление
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return redirect('/user');
    }
}
```

Реализация функции redirect:
```php
function redirect($location, $status = 302, $headers = [])
{
    $response = new Response($status, ['Location' => $location]);
    if (!empty($headers)) {
        $response->withHeaders($headers);
    }
    return $response;
}
```

## Установка заголовка
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'Header Value' 
        ]);
    }
}
```
Также можно использовать методы `header` и `withHeaders` для установки одного или нескольких заголовков.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'Header Value 1',
            'X-Header-Tow' => 'Header Value 2',
        ]);
    }
}
```
Также вы можете заранее установить заголовок и затем установить данные для возврата.
```php
public function hello(Request $request)
{
    // Создание объекта
    $response = response();
    
    // .... Пропущена бизнес-логика
  
    // Установка HTTP-заголовка
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... Пропущена бизнес-логика

    // Установка данных для возврата
    $response->withBody('Возвращаемые данные');
    return $response;
}
```

## Установка cookie

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->cookie('foo', 'value');
    }
}
```

Также можно заранее установить cookie, а затем установить данные для возврата.
```php
public function hello(Request $request)
{
    // Создание объекта
    $response = response();
    
    // .... Пропущена бизнес-логика
    
    // Установка cookie
    $response->cookie('foo', 'value');
    
    // .... Пропущена бизнес-логика

    // Установка данных для возврата
    $response->withBody('Возвращаемые данные');
    return $response;
}
```

Полный список параметров метода cookie такой:
`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## Возвращает поток файла
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->file(public_path() . '/favicon.ico');
    }
}
```

- Webman поддерживает отправку очень больших файлов.
- Для больших файлов (более 2 МБ) webman не считывает весь файл целиком в память, а вместо этого, в подходящий момент, разбивает файл на части и отправляет их.
- Webman оптимизирует скорость чтения файла и отправки в соответствии со скоростью получения клиентом, обеспечивая наиболее быструю отправку файла при минимальном использовании памяти.
- Отправка данных является неблокирующей и не влияет на обработку других запросов.
- Метод file автоматически добавляет заголовок `if-modified-since` и при следующем запросе проверяет заголовок `if-modified-since`, если файл не изменен, то прямо возвращается 304, чтобы сэкономить полосу пропускания.
- Отправляемый файл автоматически использует соответствующий заголовок `Content-Type` для отправки браузеру.
- Если файла не существует, он автоматически преобразуется в ответ 404.

## Скачать файл
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'имя_файла.ico');
    }
}
```
Метод download почти идентичен методу file с одним отличием:
1. После установки имени загрузки файл будет загружен, а не отображен в браузере.
2. Метод download не проверяет заголовок `if-modified-since`.

## Получение вывода
Некоторые библиотеки выводят содержимое файла непосредственно в стандартный вывод, т.е. данные выводятся в командной строке и не отсылаются браузеру, в этом случае нужно использовать `ob_start();` `ob_get_clean();` для захвата данных в переменной, а затем отправить их браузеру. Например:

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // Создание изображения
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

        // Начало получения вывода
        ob_start();
        // Вывод изображения
        imagejpeg($im);
        // Получение содержимого изображения
        $image = ob_get_clean();
        
        // Отправка изображения
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
