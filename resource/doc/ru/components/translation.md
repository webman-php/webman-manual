# Мультиязычность

Мультиязычность осуществляется с помощью компонента [symfony/translation](https://github.com/symfony/translation).

## Установка
```bash
composer require symfony/translation
```

## Создание языкового пакета
По умолчанию webman размещает языковые пакеты в каталоге `resource/translations` (если его нет, создайте его самостоятельно). Если вам нужно изменить каталог, укажите его в `config/translation.php`.
Каждый язык соответствует своей подпапке, а языковые определения обычно содержатся в файле `messages.php`. Вот пример структуры каталогов:

```plaintext
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

Все языковые файлы возвращают массив, например:

```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## Настройка
`config/translation.php`

```php
return [
    // Язык по умолчанию
    'locale' => 'zh_CN',
    // Резервный язык, используемый в случае отсутствия перевода на указанном языке
    'fallback_locale' => ['zh_CN', 'en'],
    // Каталог для хранения языковых файлов
    'path' => base_path() . '/resource/translations',
];
```

## Перевод
Для перевода используйте метод `trans()`.

Создайте файл языковых данных `resource/translations/zh_CN/messages.php`:

```php
return [
    'hello' => 'Привет, мир!',
];
```

Создайте файл `app/controller/UserController.php`:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // Привет, мир!
        return response($hello);
    }
}
```

Открыв страницу `http://127.0.0.1:8787/user/get` вы увидите "Привет, мир!".

## Изменение языка по умолчанию
Для смены языка используйте метод `locale()`.

Добавьте языковой файл `resource/translations/en/messages.php`:

```php
return [
    'hello' => 'hello world!',
];
```

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Изменение языка
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```

Открыв страницу `http://127.0.0.1:8787/user/get` вы увидите "hello world!".

Также можно использовать четвертый аргумент функции `trans()` для временного изменения языка. Например, пример выше эквивалентен следующему:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Изменение языка через четвертый аргумент
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## Явное указание языка для каждого запроса
Translation - синглтон, что означает, что все запросы используют один и тот же экземпляр. Если какой-то запрос устанавливает язык по умолчанию с помощью `locale()`, это затронет все последующие запросы этого процесса. Поэтому для каждого запроса нужно явное установление языка. Например, с помощью следующего промежуточного ПО:

Создайте файл `app/middleware/Lang.php` (если каталога нет, создайте его):

```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        locale(session('lang', 'zh_CN'));
        return $handler($request);
    }
}
```

Добавьте глобальное промежуточное ПО в `config/middleware.php` следующим образом:

```php
return [
    // Глобальные промежуточные ПО
    '' => [
        // ... опустим другие промежуточные ПО
        app\middleware\Lang::class,
    ]
];
```

## Использование заполнителей
Иногда сообщение содержит переменные, которые должны быть переведены, например:

```php
trans('hello ' . $name);
```

В таких случаях используются заполнители.

Измените `resource/translations/zh_CN/messages.php` следующим образом:

```php
return [
    'hello' => 'Привет, %name%!',
];
```
При переводе данные для заполнителей передаются через второй аргумент функции `trans()`:

```php
trans('hello', ['%name%' => 'webman']); // Привет, webman!
```

## Обработка множественного числа
В некоторых языках фраза может иметь разный вид в зависимости от количества предметов, например, `There is %count% apple`. Когда `%count%` равно 1, фраза должна быть в одном виде, а когда больше 1 - в другом.

Для таких случаев используются **конвейеры** (`|`).

Добавьте в файл языковых данных `resource/translations/en/messages.php` новую фразу `apple_count`:

```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

Также можно указать диапазон чисел, чтобы создать более сложные правила для множественной формы:

```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples',
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## Указание имени языкового файла
По умолчанию имя языкового файла - `messages.php`, но на самом деле вы можете создавать файлы с другими именами.

Создайте языковой файл `resource/translations/zh_CN/admin.php`:

```php
return [
    'hello_admin' => 'Привет, администратор!',
];
```

Используйте третий аргумент функции `trans()` для указания имени языкового файла (без суффикса `.php`).
```php
trans('hello', [], 'admin', 'zh_CN'); // Привет, администратор!
```

## Дополнительная информация
Для дополнительной информации смотрите [документацию symfony/translation](https://symfony.com/doc/current/translation.html).
