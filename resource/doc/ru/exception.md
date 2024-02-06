# Обработка исключений

## Конфигурация
`config/exception.php`
```php
return [
    // Здесь настраивается класс обработки исключений
    '' => support\exception\Handler::class,
];
```
В случае множественных приложений вы можете настраивать отдельный класс обработки исключений для каждого приложения. См. [Множественные приложения](multiapp.md).

## Класс обработки исключений по умолчанию
Исключения в webman по умолчанию обрабатываются классом `support\exception\Handler`. Вы можете изменить класс обработки исключений по уумолчанию, отредактировав файл конфигурации `config/exception.php`. Класс обработки исключений должен реализовывать интерфейс `Webman\Exception\ExceptionHandlerInterface`.
```php
interface ExceptionHandlerInterface
{
    /**
     * Запись в журнал
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * Визуализация ответа
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```

## Визуализация ответа
Метод `render` класса обработки исключений используется для отображения ответа.

Если значение `debug` в файле конфигурации `config/app.php` равно `true` (дальше в тексте - `app.debug=true`), будут возвращены подробные сведения об исключении, в противном случае будут возвращены краткие сведения об исключении.

Если запрос ожидает возврата JSON, информация об исключении будет возвращена в формате JSON, например:
```json
{
    "code": "500",
    "msg": "Информация об исключении"
}
```
Если `app.debug=true`, данные JSON будут дополнительно содержать поле `trace`, позволяющее получить подробный стек вызовов.

Вы можете написать собственный класс обработки исключений, чтобы изменить логику обработки исключений по умолчанию.

# Бизнес-исключения BusinessException
Иногда мы хотим прервать запрос внутри вложенной функции и вернуть клиенту сообщение об ошибке, в этом случае можно использовать исключение `BusinessException`.
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
            throw new BusinessException('Ошибка параметров', 3000);
        }
    }
}
```

В приведенном выше примере будет возвращено следующее:
```json
{"code": 3000, "msg": "Ошибка параметров"}
```

> **Внимание**
> Бизнес-исключение BusinessException не требует обработки через try-catch, фреймворк автоматически обрабатывает исключение и возвращает соответствующий вывод в зависимости от типа запроса.

## Пользовательские бизнес-исключения
Если описанный выше ответ не соответствует вашим потребностям, например, если вы хотите изменить `msg` на `message`, вы можете создать собственное исключение `MyBusinessException`.

Создайте файл `app/exception/MyBusinessException.php` со следующим содержимым:
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
        // Возврат данных JSON при json-запросе
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // Возврат страницы при не json-запросе
        return new Response(200, [], $this->getMessage());
    }
}
```

Таким образом, при вызове бизнес-исключения
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Ошибка параметров', 3000);
```
при json-запросе будет получен вывод в формате JSON, подобный следующему:
```json
{"code": 3000, "message": "Ошибка параметров"}
```

> **Подсказка**
> Поскольку бизнес-исключение BusinessException относится к бизнес-исключениям (например, к ошибкам ввода пользователем), оно предсказуемо, поэтому фреймворк не считает его фатальной ошибкой и не выполняет запись в журнал.

## Вывод
Можно использовать исключение `BusinessException`, чтобы прервать текущий запрос и вернуть информацию клиенту в любое время.
