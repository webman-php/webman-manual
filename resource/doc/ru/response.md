# Ответ
Фактический ответ представляет собой объект `support\Response`, и для удобства создания этого объекта webman предоставляет несколько вспомогательных функций.

## Возвращение любого ответа

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

Вы также можете сначала создать пустой объект `response`, а затем, в соответствующем месте, использовать методы такие как `$response->cookie()`, `$response->header()`, `$response->withHeaders()`, `$response->withBody()` для установки возвращаемого содержимого.
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
                'X-Header-One' => 'Значение заголовка 1',
                'X-Header-Tow' => 'Значение заголовка 2',
            ]);

    // .... Пропущена бизнес-логика

    // Установка возвращаемых данных
    $response->withBody('Возвращаемые данные');
    return $response;
}
```

## Возврат JSON
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

## Возврат XML
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

## Возврат представления
Создайте файл `app/controller/FooController.php`, содержащий следующий код:

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

Создайте файл `app/view/foo/hello.html` с содержимым:

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

## Настройка заголовков
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
            'X-Header-One' => 'Значение заголовка' 
        ]);
    }
}
```
Вы также можете использовать методы `header` и `withHeaders` для установки одного или нескольких заголовков.
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
            'X-Header-One' => 'Значение заголовка 1',
            'X-Header-Tow' => 'Значение заголовка 2',
        ]);
    }
}
```
Вы также можете предварительно установить заголовки, а затем задать данные для возврата.

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
Вы также можете заранее установить cookie, а затем задать данные для возврата.
```php
public function hello(Request $request)
{
    // Создание объекта
    $response = response();
    
    // .... Пропущена бизнес-логика
    
    // Установка cookie
    $response->cookie('foo', 'value');
    
    // .... Пропущена бизнес-логика

    // Установка возвращаемых данных
    $response->withBody('Возвращаемые данные');
    return $response;
}
```
Полный список параметров метода cookie:
`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## Возврат потока файла
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
- webman поддерживает отправку очень больших файлов
- Для больших файлов (более 2 МБ) webman не считывает весь файл в память сразу, а вместо этого в подходящий момент читает файл по частям и отправляет его
- webman оптимизирует скорость чтения файла в зависимости от скорости получателя, чтобы обеспечить быструю отправку файла, при этом минимизируется использование памяти
- Отправка данных осуществляется неблокирующим образом, не влияя на обработку других запросов
- Метод file автоматически добавляет заголовок `if-modified-since`, и при следующем запросе проверяет заголовок `if-modified-since`, чтобы, если файл не был изменен, вернуть статус 304 для экономии пропускной способности
- Отправляемый файл автоматически отправляется браузеру с правильным заголовком `Content-Type`
- Если файл не существует, он автоматически преобразуется в ответ 404
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'файл.ico');
    }
}
```

download метод практически идентичен методу file, с тем отличием, что
1. После установки имени загружаемого файла он будет загружен, а не отображен в браузере
2. Метод download не будет проверять заголовок `if-modified-since`

## Получение вывода
Некоторые библиотеки печатают содержимое файла непосредственно на стандартный вывод, то есть данные выводятся в терминал командной строки и не отправляются в браузер. В таких случаях нам нужно использовать `ob_start();` `ob_get_clean();` для захвата данных в переменную и отправки их в браузер, например:

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
        imagestring($im, 1, 5, 5,  'Простая текстовая строка', $text_color);

        // Начало захвата вывода
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
