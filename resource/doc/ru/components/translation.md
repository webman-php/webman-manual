# Мультиязычность

Для мультиязычности используется компонент [symfony/translation](https://github.com/symfony/translation).

## Установка
```
composer require symfony/translation
```

## Создание языкового пакета
По умолчанию webman помещает языковые пакеты в каталог `resource/translations` (если его нет, создайте его самостоятельно). Если вам нужно изменить этот каталог, установите его в `config/translation.php`.
Каждый язык соответствует своему подкаталогу, где определение языка по умолчанию находится в файле `messages.php`. Например:
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

Все языковые файлы должны возвращать массив, например:
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## Конфигурация

`config/translation.php`

```php
return [
    // Язык по умолчанию
    'locale' => 'zh_CN',
    // Резервный язык, используемый, если перевод не найден для текущего языка
    'fallback_locale' => ['zh_CN', 'en'],
    // Путь к каталогу с языковыми файлами
    'path' => base_path() . '/resource/translations',
];
```

## Перевод

Для перевода используйте метод `trans()`.

Создайте языковой файл `resource/translations/zh_CN/messages.php` следующего содержания:
```php
return [
    'hello' => 'Привет, мир!',
];
```

Создайте файл `app/controller/UserController.php`.
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

После посещения `http://127.0.0.1:8787/user/get` будет возвращено "Привет, мир!".

## Изменение языка по умолчанию

Для изменения языка используйте метод `locale()`.

Добавьте языковой файл `resource/translations/en/messages.php` следующего содержания:
```php
return [
    'hello' => 'Привет, мир!',
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
После посещения `http://127.0.0.1:8787/user/get` будет возвращено "hello world!".

Вы также можете использовать четвертый параметр функции `trans()` для временного изменения языка. Например, предыдущий пример и следующий эквивалентны:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Изменение языка с помощью четвертого параметра
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## Явное указание языка для каждого запроса
Перевод - это синглтон, что означает, что все запросы используют один и тот же экземпляр. Если для определенного запроса используется установка языка через `locale()`, это повлияет на все последующие запросы этого процесса. Поэтому для каждого запроса следует явно указывать язык. Например, следующий пример использует следующий промежуточный программный обеспечения.

Создайте файл `app/middleware/Lang.php` (создайте каталог, если он отсутствует) следующего содержания:
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

В файле `config/middleware.php` добавьте глобальное промежуточное программное обеспечение, как показано ниже:
```php
return [
    // Глобальные промежуточные программы
    '' => [
        // ... Здесь представлены прочие промежуточные программы
        app\middleware\Lang::class,
    ]
];
```

## Использование заполнителей
Иногда сообщение содержит переменные для перевода, например,
```php
trans('hello ' . $name);
```
В таких случаях используйте заполнители.

Измените `resource/translations/zh_CN/messages.php`, как показано ниже:
```php
return [
    'hello' => 'Привет, %name%!',
```
При переводе данные для заполнителей передаются вторым аргументом функции, например:
```php
trans('hello', ['%name%' => 'webman']); // Привет, webman!
```

## Обработка множественного числа
Для некоторых языков из-за различного количества предметов применяются разные формы предложений, например, `There is %count% apple`. Когда `%count%` равно 1, предложение строится верно, но если больше 1, то неверно.

В таких случаях используется **конвейер** (`|`), чтобы перечислить формы множественного числа.

В языковом файле `resource/translations/en/messages.php` добавьте `apple_count`, следующего содержания:
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

Также можно указать диапазон чисел, чтобы создать более сложные правила для множественного числа:
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## Указание языкового файла
Имя языкового файла по умолчанию - `messages.php`, но на самом деле вы можете создать файлы с другими именами.

Создайте языковой файл `resource/translations/zh_CN/admin.php` следующего содержания:
```php
return [
    'hello_admin' => 'Привет, администратор!',
];
```

С помощью третьего параметра функции `trans()` (без расширения `.php`) можно указать имя языкового файла.
```php
trans('hello', [], 'admin', 'zh_CN'); // Привет, администратор!
```

## Дополнительная информация
См. [документацию по symfony/translation](https://symfony.com/doc/current/translation.html)
