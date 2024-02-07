# Обработка исключений

## Настройка
`config/exception.php`
```php
return [
    // Здесь настраивается класс обработки исключений
    '' => support\exception\Handler::class,
];
```
В режиме множественных приложений вы можете настраивать отдельный класс обработки исключений для каждого приложения, см. [Множественные приложения](multiapp.md).

## Класс обработки исключений по умолчанию
В webman исключения по умолчанию обрабатываются классом `support\exception\Handler`. Можно изменить класс обработки исключений по уумолчанию, изменив файл настроек `config/exception.php`. Класс обработки исключений должен реализовывать интерфейс `Webman\Exception\ExceptionHandlerInterface`.
```php
interface ExceptionHandlerInterface
{
    /**
     * Запись журнала
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * Рендеринг ответа
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```

## Рендеринг ответа
Метод `render` класса обработки исключений используется для рендеринга ответа.

Если значение `debug` в файле настроек `config/app.php` установлено как `true` (далее `app.debug=true`), будут возвращены подробные сведения об исключении, в противном случае - краткая информация об исключении.

Если запрос ожидает JSON-ответ, исключение будет возвращено в формате JSON, например:
```json
{
    "code": "500",
    "msg": "Информация об исключении"
}
```
Если `app.debug=true`, в JSON-данных будет также дополнительное поле `trace`, содержащее подробный стек вызовов.

Вы можете создать собственный класс обработки исключений для изменения логики обработки исключений по умолчанию.

# Бизнес-исключение BusinessException
Иногда мы хотим прервать выполнение запроса во вложенной функции и вернуть клиенту сообщение об ошибке, для этого можно использовать исключение `BusinessException`.
Например:

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->chackInpout($request->post());
        return response('hello index');
    }
    
    protected function chackInpout($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('Ошибка параметра', 3000);
        }
    }
}
```

В этом примере будет возвращено
```json
{"code": 3000, "msg": "Ошибка параметра"}
```

> **Примечание**
> Для бизнес-исключения BusinessException не требуется обработка в блоке try-catch, фреймворк будет автоматически обрабатывать и, в зависимости от типа запроса, возвращать соответствующий результат.

## Пользовательское бизнес-исключение

Если указанный выше ответ не соответствует вашим требованиям, например, если вы хотите изменить `msg` на `message`, вы можете создать собственное исключение `MyBusinessException`.

Создайте файл `app/exception/MyBusinessException.php` с содержимым:
```php
<?php

namespace app\exception;

use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class MyBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        // JSON-запрос возвращает данные в формате JSON
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // Если запрос не является JSON-ом, то возвращается страница
        return new Response(200, [], $this->getMessage());
    }
}
```

Теперь при вызове бизнес-исключения
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Ошибка параметра', 3000);
```
в случае JSON-запроса будет получен JSON-ответ подобный следующему:
```json
{"code": 3000, "message": "Ошибка параметра"}
```

> **Подсказка**
> Поскольку исключение BusinessException относится к бизнес-исключениям (например, неправильные входные параметры пользователя), которые можно предсказать, фреймворк не считает исключение фатальной ошибкой и не регистрирует его в журнале.

## Вывод

В любом случае, когда требуется прервать текущий запрос и вернуть информацию клиенту, можно использовать исключение `BusinessException`.
