# Объяснение

## Получение объекта запроса
Webman автоматически внедряет объект запроса в первый аргумент метода действия, например

**Пример**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // Получение параметра name из запроса GET, если параметр не передан, возвращается $default_name
        $name = $request->get('name', $default_name);
        // Возврат строки в браузер
        return response('hello ' . $name);
    }
}
```

Через объект `$request` мы можем получить любую связанную с запросом информацию.

**Иногда нам нужно получить объект `$request` текущего запроса в другом классе, в этом случае мы можем просто использовать вспомогательную функцию `request()`**;

## Получение параметров запроса GET

**Получение всего массива GET**
```php
$request->get();
```
Если запрос не содержит параметры GET, то возвращается пустой массив.

**Получение одного значения из массива GET**
```php
$request->get('name');
```
Если в массиве GET нет значения, то возвращается null.

Вы также можете передать второй аргумент метода get в качестве значения по умолчанию. Если соответствующее значение не найдено в массиве GET, возвращается значение по умолчанию. Например:
```php
$request->get('name', 'tom');
```

## Получение параметров запроса POST
**Получение всего массива POST**
```php
$request->post();
```
Если запрос не содержит параметры POST, то возвращается пустой массив.

**Получение одного значения из массива POST**
```php
$request->post('name');
```
Если в массиве POST нет значения, то возвращается null.

Как и в методе get, вы также можете передать второй аргумент метода post в качестве значения по умолчанию. Если соответствующее значение не найдено в массиве POST, возвращается значение по умолчанию. Например:
```php
$request->post('name', 'tom');
```

## Получение исходного тела запроса POST
```php
$post = $request->rawBody();
```
Эта функция аналогична операции `file_get_contents("php://input");` в `php-fpm`. Она используется для получения исходного тела HTTP-запроса. Это очень полезно при получении данных POST-запроса в формате, отличном от `application/x-www-form-urlencoded`.

## Получение заголовка
**Получение всего массива заголовков**
```php
$request->header();
```
Если запрос не содержит заголовки, то возвращается пустой массив. Обратите внимание, что все ключи в нижнем регистре.

**Получение одного значения из массива заголовков**
```php
$request->header('host');
```
Если в массиве заголовков нет значения, то возвращается null. Обратите внимание, что все ключи в нижнем регистре.

Как и в методе get, вы также можете передать второй аргумент метода header в качестве значения по умолчанию. Если соответствующее значение не найдено в массиве заголовков, возвращается значение по умолчанию. Например:
```php
$request->header('host', 'localhost');
```

## Получение cookie
**Получение всего массива cookie**
```php
$request->cookie();
```
Если запрос не содержит куки, то возвращается пустой массив.

**Получение одного значения из массива cookie**
```php
$request->cookie('name');
```
Если в массиве cookie нет значения, то возвращается null.

Как и в методе get, вы также можете передать второй аргумент метода cookie в качестве значения по умолчанию. Если соответствующее значение не найдено в массиве cookie, возвращается значение по умолчанию. Например:
```php
$request->cookie('name', 'tom');
```

## Получение всех входных данных
Включает в себя объединение `post` и `get`.
```php
$request->all();
```

## Получение определенного значения ввода
Получение значения из объединения `post` и `get`.
```php
$request->input('name', $default_value);
```

## Получение части входных данных
Получение части данных из объединения `post` и `get`.
```php
// Получение массива, состоящего из username и password, если соответствующего ключа нет, он будет проигнорирован
$only = $request->only(['username', 'password']);
// Получение всех входных данных, кроме avatar и age
$except = $request->except(['avatar', 'age']);
```

## Получение загруженного файла
**Получение всего массива загруженных файлов**
```php
$request->file();
```

Форма аналогична:
```html
<form method="post" action="http://127.0.0.1:8787/upload/files" enctype="multipart/form-data" />
<input name="file1" multiple="multiple" type="file">
<input name="file2" multiple="multiple" type="file">
<input type="submit">
</form>
```

`$request->file()` возвращает следующий формат:
```php
array (
    'file1' => object(webman\Http\UploadFile),
    'file2' => object(webman\Http\UploadFile)
)
```
Это массив экземпляров класса `webman\Http\UploadFile`. Класс `webman\Http\UploadFile` наследует встроенный класс PHP [`SplFileInfo`](https://www.php.net/manual/zh/class.splfileinfo.php) и предоставляет несколько полезных методов.

```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function files(Request $request)
    {
        foreach ($request->file() as $key => $spl_file) {
            var_export($spl_file->isValid()); // Валиден ли файл, например true или false
            var_export($spl_file->getUploadExtension()); // Расширение загруженного файла, например 'jpg'
            var_export($spl_file->getUploadMimeType()); // MIME-тип загруженного файла, например 'image/jpeg'
            var_export($spl_file->getUploadErrorCode()); // Получить код ошибки загрузки, например UPLOAD_ERR_NO_TMP_DIR UPLOAD_ERR_NO_FILE UPLOAD_ERR_CANT_WRITE
            var_export($spl_file->getUploadName()); // Имя загруженного файла, например 'my-test.jpg'
            var_export($spl_file->getSize()); // Получение размера файла, например 13364 байт
            var_export($spl_file->getPath()); // Получение каталога для загрузок, например '/tmp'
            var_export($spl_file->getRealPath()); // Получение пути временного файла, например `/tmp/workerman.upload.SRliMu`
        }
        return response('ok');
    }
}
```

**Примечание:**

- После загрузки файл будет назван временным файлом, например `/tmp/workerman.upload.SRliMu`
- Размер загружаемого файла ограничен [defaultMaxPackageSize](http://doc.workerman.net/tcp-connection/default-max-package-size.html), по умолчанию 10 Мб, можно изменить значение по умолчанию в файле `config/server.php`, изменив `max_package_size`.
- После завершения запроса временные файлы будут автоматически очищены
- Если в запросе нет загруженных файлов, метод `file()` вернет пустой массив
- Загруженный файл не поддерживает метод `move_uploaded_file()`, используйте метод `$file->move()` вместо него, см. пример ниже

### Получение конкретного загруженного файла
```php
$request->file('avatar');
```
Если файл существует, возвращается экземпляр `webman\Http\UploadFile`, в противном случае возвращается null.

**Пример**
```php
<?php
namespace app\controller;

use support\Request;

class UploadController
{
    public function file(Request $request)
    {
        $file = $request->file('avatar');
        if ($file && $file->isValid()) {
            $file->move(public_path().'/files/myfile.'.$file->getUploadExtension());
            return json(['code' => 0, 'msg' => 'upload success']);
        }
        return json(['code' => 1, 'msg' => 'file not found']);
    }
}
```
## Получение хоста
Получение информации о хосте запроса.
```php
$request->host();
```
Если адрес запроса содержит нестандартный порт 80 или 443, информация о хосте может включать порт, например `example.com:8080`. Если необходимости в порте нет, можно передать `true` в качестве первого параметра.

```php
$request->host(true);
```

## Получение метода запроса
```php
 $request->method();
```
Возвращаемое значение может быть одним из `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`, `HEAD`.

## Получение uri запроса
```php
$request->uri();
```
Возвращает uri запроса, включая path и часть queryString.

## Получение пути запроса

```php
$request->path();
```
Возвращает часть пути запроса.


## Получение queryString запроса

```php
$request->queryString();
```
Возвращает часть queryString запроса.

## Получение URL запроса
Метод `url()` возвращает URL без параметра`Query`.
```php
$request->url();
```
Возвращает что-то вроде `//www.workerman.net/workerman-chat`.

Метод `fullUrl()` возвращает URL с параметром`Query`.
```php
$request->fullUrl();
```
Возвращает что-то вроде `//www.workerman.net/workerman-chat?type=download`.

> **Заметка**
> `url()` и `fullUrl()` не возвращают часть протокола (не возвращает http или https).
> Так как в браузере можно использовать адреса, начинающиеся с `//example.com`, и он автоматически распознает протокол текущего сайта, автоматически выполняет запрос через http или https.

Если вы используете прокси-сервер nginx, добавьте `proxy_set_header X-Forwarded-Proto $scheme;` в конфигурацию nginx, [см. прокси-сервер nginx](others/nginx-proxy.md), 
таким образом можно использовать `$request->header('x-forwarded-proto');` для определения http или https, например:
```php
echo $request->header('x-forwarded-proto'); // выводит http или https
```

## Получение версии HTTP запроса

```php
$request->protocolVersion();
```
Возвращает строку `1.1` или `1.0`.

## Получение sessionId запроса

```php
$request->sessionId();
```
Возвращает строку состоящую из букв и цифр.


## Получение IP клиента запроса
```php
$request->getRemoteIp();
```

## Получение порта клиента запроса
```php
$request->getRemotePort();
```

## Получение реального IP клиента запроса
```php
$request->getRealIp($safe_mode=true);
```

Когда используется прокси-сервер (например, nginx), использование `$request->getRemoteIp()` часто возвращает IP прокси-сервера (например, `127.0.0.1` `192.168.x.x`), а не реальный IP клиента. В этом случае можно попробовать использовать `$request->getRealIp()` для получения реального IP клиента.

`$request->getRealIp()` попытается получить реальный IP из заголовков HTTP `x-real-ip`, `x-forwarded-for`, `client-ip`, `x-client-ip`, `via`.

> Поскольку заголовки HTTP легко подделать, поэтому метод получения IP клиента не является 100% надежным, особенно при `$safe_mode=false`. Наиболее надежный способ получения реального IP клиента через прокси - знание безопасного IP адреса прокси-сервера и явное указание, через какой заголовок HTTP передается реальный IP. Если IP, возвращенный `$request->getRemoteIp()`, соответствует известному безопасному IP прокси-сервера, затем через `$request->header('заголовок HTTP, содержащий реальный IP')` можно получить реальный IP.

## Получение IP сервера

```php
$request->getLocalIp();
```

## Получение порта сервера
```php
$request->getLocalPort();
```

## Проверка, является ли запрос ajax
```php
$request->isAjax();
```

## Проверка, является ли запрос pjax
```php
$request->isPjax();
```

## Проверка, ожидает ли клиент возврат в формате json
```php
$request->expectsJson();
```

## Проверка, принимает ли клиент возврат в формате json
```php
$request->acceptJson();
```

## Получение имени плагина запроса
Для запросов без плагина возвращает пустую строку `''`.
```php
$request->plugin;
```
> Эта функция требует webman>=1.4.0

## Получение имени приложения запроса
Для одного приложения всегда возвращает пустую строку `''`, [для множества приложений](multiapp.md) возвращает имя приложения.
```php
$request->app;
```

> Поскольку замыкание функций не относятся ни к одному приложению, поэтому для запросов из замыканий маршрутов `$request->app` всегда возвращает пустую строку `''`
> См. замыкание маршрутов [Маршрут](route.md)

## Получение имени класса контроллера запроса
Получает имя класса, соответствующего контроллеру
```php
$request->controller;
```
Возвращает что-то вроде `app\controller\IndexController`.

> Поскольку замыкание функций не относятся ни к одному контроллеру, поэтому для запросов из замыканий маршрутов `$request->controller` всегда возвращает пустую строку `''`
> См. замыкание маршрутов [Маршрут](route.md)

## Получение имени метода запроса
Получает имя метода контроллера запроса
```php
$request->action;
```
Возвращает что-то вроде `index`.

> Поскольку замыкание функций не относятся ни к одному контроллеру, поэтому для запросов из замыканий маршрутов `$request->action` всегда возвращает пустую строку `''`
> См. замыкание маршрутов [Маршрут](route.md)
