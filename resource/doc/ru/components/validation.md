# Другие валидаторы

В composer доступно множество валидаторов, которые можно использовать напрямую, например:

#### <a href="#webman-validation"> webman/validation (Рекомендуется)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# Валидатор webman/validation

Основан на `illuminate/validation`, предоставляет ручную валидацию, валидацию через аннотации, валидацию на уровне параметров и переиспользуемые наборы правил.

## Установка

```bash
composer require webman/validation
```

## Основные концепции

- **Переиспользование набора правил**: Определяйте переиспользуемые `rules`, `messages`, `attributes` и `scenes` путём наследования от `support\validation\Validator`, которые можно использовать как при ручной, так и при валидации через аннотации.
- **Валидация на уровне метода (атрибуты)**: Используйте PHP 8 атрибут `#[Validate]` для привязки валидации к методам контроллера.
- **Валидация на уровне параметра (атрибуты)**: Используйте PHP 8 атрибут `#[Param]` для привязки валидации к параметрам методов контроллера.
- **Обработка исключений**: При ошибке валидации выбрасывается `support\validation\ValidationException`; класс исключения настраивается.
- **Валидация с базой данных**: При использовании валидации с БД необходимо установить `composer require webman/database`.

## Ручная валидация

### Базовое использование

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **Примечание**
> `validate()` выбрасывает `support\validation\ValidationException` при ошибке валидации. Если вы предпочитаете не использовать исключения, используйте подход с `fails()` ниже для получения сообщений об ошибках.

### Пользовательские сообщения и атрибуты

```php
use support\validation\Validator;

$data = ['contact' => 'user@example.com'];

Validator::make(
    $data,
    ['contact' => 'required|email'],
    ['contact.email' => 'Invalid email format'],
    ['contact' => 'Email']
)->validate();
```

### Валидация без исключения (получение сообщений об ошибках)

Если вы предпочитаете не использовать исключения, используйте `fails()` для проверки и получения сообщений об ошибках через `errors()` (возвращает `MessageBag`):

```php
use support\validation\Validator;

$data = ['email' => 'bad-email'];

$validator = Validator::make($data, [
    'email' => 'required|email',
]);

if ($validator->fails()) {
    $firstError = $validator->errors()->first();      // string
    $allErrors = $validator->errors()->all();         // array
    $errorsByField = $validator->errors()->toArray(); // array
    // handle errors...
}
```

## Переиспользование набора правил (пользовательский валидатор)

```php
namespace app\validation;

use support\validation\Validator;

class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:1',
        'name' => 'required|string|min:2|max:20',
        'email' => 'required|email',
    ];

    protected array $messages = [
        'name.required' => 'Name is required',
        'email.required' => 'Email is required',
        'email.email' => 'Invalid email format',
    ];

    protected array $attributes = [
        'name' => 'Name',
        'email' => 'Email',
    ];
}
```

### Переиспользование при ручной валидации

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### Использование сцен (опционально)

`scenes` — опциональная функция; она валидирует только подмножество полей при вызове `withScene(...)`.

```php
namespace app\validation;

use support\validation\Validator;

class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:1',
        'name' => 'required|string|min:2|max:20',
        'email' => 'required|email',
    ];

    protected array $scenes = [
        'create' => ['name', 'email'],
        'update' => ['id', 'name', 'email'],
    ];
}
```

```php
use app\validation\UserValidator;

// Сцена не указана -> валидируются все правила
UserValidator::make($data)->validate();

// Сцена указана -> валидируются только поля в этой сцене
UserValidator::make($data)->withScene('create')->validate();
```

## Валидация через аннотации (на уровне метода)

### Прямые правила

```php
use support\Request;
use support\validation\annotation\Validate;

class AuthController
{
    #[Validate(
        rules: [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ],
        messages: [
            'email.required' => 'Email is required',
            'password.required' => 'Password is required',
        ],
        attributes: [
            'email' => 'Email',
            'password' => 'Password',
        ]
    )]
    public function login(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Переиспользование наборов правил

```php
use app\validation\UserValidator;
use support\Request;
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(validator: UserValidator::class, scene: 'create')]
    public function create(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Множественные слои валидации

```php
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(rules: ['email' => 'required|email'])]
    #[Validate(rules: ['token' => 'required|string'])]
    public function send()
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Источник данных для валидации

```php
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(
        rules: ['email' => 'required|email'],
        in: ['query', 'body', 'path']
    )]
    public function send()
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

Параметр `in` указывает источник данных:

* **query** — параметры HTTP-запроса из `$request->get()`
* **body** — тело HTTP-запроса из `$request->post()`
* **path** — параметры пути HTTP-запроса из `$request->route->param()`

`in` может быть строкой или массивом; при массиве значения объединяются по порядку, при этом более поздние перезаписывают более ранние. Если `in` не указан, по умолчанию используется `['query', 'body', 'path']`.


## Валидация на уровне параметра (Param)

### Базовое использование

```php
use support\validation\annotation\Param;

class MailController
{
    public function send(
        #[Param(rules: 'required|email')] string $from,
        #[Param(rules: 'required|email')] string $to,
        #[Param(rules: 'required|string|min:1|max:500')] string $content
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Источник данных для валидации

Аналогично, валидация на уровне параметра также поддерживает параметр `in` для указания источника:

```php
use support\validation\annotation\Param;

class MailController
{
    public function send(
        #[Param(rules: 'required|email', in: ['body'])] string $from
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```


### rules поддерживает строку или массив

```php
use support\validation\annotation\Param;

class MailController
{
    public function send(
        #[Param(rules: ['required', 'email'])] string $from
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Пользовательские сообщения / атрибут

```php
use support\validation\annotation\Param;

class UserController
{
    public function updateEmail(
        #[Param(
            rules: 'required|email',
            messages: ['email.email' => 'Invalid email format'],
            attribute: 'Email'
        )]
        string $email
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Переиспользование констант правил

```php
final class ParamRules
{
    public const EMAIL = ['required', 'email'];
}

class UserController
{
    public function send(
        #[Param(rules: ParamRules::EMAIL)] string $email
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

## Комбинация валидации на уровне метода и параметра

```php
use support\Request;
use support\validation\annotation\Param;
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(rules: ['token' => 'required|string'])]
    public function send(
        Request $request,
        #[Param(rules: 'required|email')] string $from,
        #[Param(rules: 'required|integer')] int $id
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

## Автоматический вывод правил (на основе сигнатуры параметров)

При использовании `#[Validate]` на методе или при использовании `#[Param]` на любом параметре этого метода компонент **автоматически выводит и дополняет базовые правила валидации из сигнатуры параметров метода**, затем объединяет их с существующими правилами перед валидацией.

### Пример: эквивалентное развёртывание `#[Validate]`

1) Включён только `#[Validate]` без ручного указания правил:

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate]
    public function create(string $content, int $uid)
    {
    }
}
```

Эквивалентно:

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate(rules: [
        'content' => 'required|string',
        'uid' => 'required|integer',
    ])]
    public function create(string $content, int $uid)
    {
    }
}
```

2) Указаны только частичные правила, остальное дополняется из сигнатуры параметров:

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate(rules: [
        'content' => 'min:2',
    ])]
    public function create(string $content, int $uid)
    {
    }
}
```

Эквивалентно:

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate(rules: [
        'content' => 'required|string|min:2',
        'uid' => 'required|integer',
    ])]
    public function create(string $content, int $uid)
    {
    }
}
```

3) Значение по умолчанию / nullable тип:

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate]
    public function create(string $content = 'default', ?int $uid = null)
    {
    }
}
```

Эквивалентно:

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate(rules: [
        'content' => 'string',
        'uid' => 'integer|nullable',
    ])]
    public function create(string $content = 'default', ?int $uid = null)
    {
    }
}
```

## Обработка исключений

### Исключение по умолчанию

При ошибке валидации по умолчанию выбрасывается `support\validation\ValidationException`, который наследует `Webman\Exception\BusinessException` и не логирует ошибки.

Поведение ответа по умолчанию обрабатывается в `BusinessException::render()`:

- Обычные запросы: возвращается строковое сообщение, например `token is required.`
- JSON-запросы: возвращается JSON-ответ, например `{"code": 422, "msg": "token is required.", "data":....}`

### Настройка через пользовательское исключение

- Глобальная настройка: `exception` в `config/plugin/webman/validation/app.php`

## Многоязычная поддержка

Компонент включает встроенные языковые пакеты китайского и английского языков и поддерживает переопределение в проекте. Порядок загрузки:

1. Языковой пакет проекта `resource/translations/{locale}/validation.php`
2. Встроенный компонент `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Встроенный английский Illuminate (резервный)

> **Примечание**
> Язык по умолчанию Webman настраивается в `config/translation.php` или может быть изменён через `locale('en');`.

### Пример локального переопределения

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute is not a valid email format.',
];
```

## Автозагрузка middleware

После установки компонент автоматически загружает middleware валидации через `config/plugin/webman/validation/middleware.php`; ручная регистрация не требуется.

## Генерация из командной строки

Используйте команду `make:validator` для генерации классов валидаторов (по умолчанию вывод в каталог `app/validation`).

> **Примечание**
> Требуется `composer require webman/console`

### Базовое использование

- **Генерация пустого шаблона**

```bash
php webman make:validator UserValidator
```

- **Перезапись существующего файла**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### Генерация правил из структуры таблицы

- **Указание имени таблицы для генерации базовых правил** (выводит `$rules` из типа поля/nullable/длины и т.д.; по умолчанию исключает поля, связанные с ORM: laravel использует `created_at/updated_at/deleted_at`, thinkorm использует `create_time/update_time/delete_time`)

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **Указание подключения к базе данных** (сценарии с несколькими подключениями)

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### Сцены

- **Генерация CRUD-сцен**: `create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> Сцена `update` включает поле первичного ключа (для поиска записей) плюс остальные поля; `delete/detail` по умолчанию включают только первичный ключ.

### Выбор ORM (laravel (illuminate/database) vs think-orm)

- **Автовыбор (по умолчанию)**: Использует установленный/настроенный; при наличии обоих по умолчанию используется illuminate
- **Принудительное указание**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### Полный пример

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## Модульные тесты

Из корневой директории `webman/validation` выполните:

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## Справочник правил валидации

<a name="available-validation-rules"></a>
## Доступные правила валидации

> [!IMPORTANT]
> - Webman Validation основан на `illuminate/validation`; имена правил совпадают с Laravel, специфичных правил Webman нет.
> - Middleware по умолчанию валидирует данные из `$request->all()` (GET+POST), объединённые с параметрами маршрута, исключая загруженные файлы; для правил файлов объедините `$request->file()` с данными самостоятельно или вызовите `Validator::make` вручную.
> - `current_password` зависит от auth guard; `exists`/`unique` зависят от подключения к БД и query builder; эти правила недоступны при отсутствии соответствующих компонентов.

Ниже перечислены все доступные правила валидации и их назначение:

<style>
    .collection-method-list > p {
        columns: 10.8em 3; -moz-columns: 10.8em 3; -webkit-columns: 10.8em 3;
    }

    .collection-method-list a {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

#### Boolean

<div class="collection-method-list" markdown="1">

[Accepted](#rule-accepted)
[Accepted If](#rule-accepted-if)
[Boolean](#rule-boolean)
[Declined](#rule-declined)
[Declined If](#rule-declined-if)

</div>

#### String

<div class="collection-method-list" markdown="1">

[Active URL](#rule-active-url)
[Alpha](#rule-alpha)
[Alpha Dash](#rule-alpha-dash)
[Alpha Numeric](#rule-alpha-num)
[Ascii](#rule-ascii)
[Confirmed](#rule-confirmed)
[Current Password](#rule-current-password)
[Different](#rule-different)
[Doesnt Start With](#rule-doesnt-start-with)
[Doesnt End With](#rule-doesnt-end-with)
[Email](#rule-email)
[Ends With](#rule-ends-with)
[Enum](#rule-enum)
[Hex Color](#rule-hex-color)
[In](#rule-in)
[IP Address](#rule-ip)
[IPv4](#rule-ipv4)
[IPv6](#rule-ipv6)
[JSON](#rule-json)
[Lowercase](#rule-lowercase)
[MAC Address](#rule-mac)
[Max](#rule-max)
[Min](#rule-min)
[Not In](#rule-not-in)
[Regular Expression](#rule-regex)
[Not Regular Expression](#rule-not-regex)
[Same](#rule-same)
[Size](#rule-size)
[Starts With](#rule-starts-with)
[String](#rule-string)
[Uppercase](#rule-uppercase)
[URL](#rule-url)
[ULID](#rule-ulid)
[UUID](#rule-uuid)

</div>

#### Numeric

<div class="collection-method-list" markdown="1">

[Between](#rule-between)
[Decimal](#rule-decimal)
[Different](#rule-different)
[Digits](#rule-digits)
[Digits Between](#rule-digits-between)
[Greater Than](#rule-gt)
[Greater Than Or Equal](#rule-gte)
[Integer](#rule-integer)
[Less Than](#rule-lt)
[Less Than Or Equal](#rule-lte)
[Max](#rule-max)
[Max Digits](#rule-max-digits)
[Min](#rule-min)
[Min Digits](#rule-min-digits)
[Multiple Of](#rule-multiple-of)
[Numeric](#rule-numeric)
[Same](#rule-same)
[Size](#rule-size)

</div>

#### Array

<div class="collection-method-list" markdown="1">

[Array](#rule-array)
[Between](#rule-between)
[Contains](#rule-contains)
[Doesnt Contain](#rule-doesnt-contain)
[Distinct](#rule-distinct)
[In Array](#rule-in-array)
[In Array Keys](#rule-in-array-keys)
[List](#rule-list)
[Max](#rule-max)
[Min](#rule-min)
[Size](#rule-size)

</div>

#### Date

<div class="collection-method-list" markdown="1">

[After](#rule-after)
[After Or Equal](#rule-after-or-equal)
[Before](#rule-before)
[Before Or Equal](#rule-before-or-equal)
[Date](#rule-date)
[Date Equals](#rule-date-equals)
[Date Format](#rule-date-format)
[Different](#rule-different)
[Timezone](#rule-timezone)

</div>

#### File

<div class="collection-method-list" markdown="1">

[Between](#rule-between)
[Dimensions](#rule-dimensions)
[Encoding](#rule-encoding)
[Extensions](#rule-extensions)
[File](#rule-file)
[Image](#rule-image)
[Max](#rule-max)
[MIME Types](#rule-mimetypes)
[MIME Type By File Extension](#rule-mimes)
[Size](#rule-size)

</div>

#### Database

<div class="collection-method-list" markdown="1">

[Exists](#rule-exists)
[Unique](#rule-unique)

</div>

#### Utility

<div class="collection-method-list" markdown="1">

[Any Of](#rule-anyof)
[Bail](#rule-bail)
[Exclude](#rule-exclude)
[Exclude If](#rule-exclude-if)
[Exclude Unless](#rule-exclude-unless)
[Exclude With](#rule-exclude-with)
[Exclude Without](#rule-exclude-without)
[Filled](#rule-filled)
[Missing](#rule-missing)
[Missing If](#rule-missing-if)
[Missing Unless](#rule-missing-unless)
[Missing With](#rule-missing-with)
[Missing With All](#rule-missing-with-all)
[Nullable](#rule-nullable)
[Present](#rule-present)
[Present If](#rule-present-if)
[Present Unless](#rule-present-unless)
[Present With](#rule-present-with)
[Present With All](#rule-present-with-all)
[Prohibited](#rule-prohibited)
[Prohibited If](#rule-prohibited-if)
[Prohibited If Accepted](#rule-prohibited-if-accepted)
[Prohibited If Declined](#rule-prohibited-if-declined)
[Prohibited Unless](#rule-prohibited-unless)
[Prohibits](#rule-prohibits)
[Required](#rule-required)
[Required If](#rule-required-if)
[Required If Accepted](#rule-required-if-accepted)
[Required If Declined](#rule-required-if-declined)
[Required Unless](#rule-required-unless)
[Required With](#rule-required-with)
[Required With All](#rule-required-with-all)
[Required Without](#rule-required-without)
[Required Without All](#rule-required-without-all)
[Required Array Keys](#rule-required-array-keys)
[Sometimes](#validating-when-present)

</div>

<a name="rule-accepted"></a>
#### accepted

Поле должно быть `"yes"`, `"on"`, `1`, `"1"`, `true` или `"true"`. Обычно используется для сценариев подтверждения согласия пользователя с условиями использования.

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

Когда другое поле равно указанному значению, поле должно быть `"yes"`, `"on"`, `1`, `"1"`, `true` или `"true"`. Обычно используется для условных сценариев согласия.

<a name="rule-active-url"></a>
#### active_url

Поле должно иметь валидную A или AAAA запись. Это правило сначала использует `parse_url` для извлечения хоста URL, затем валидирует с помощью `dns_get_record`.

<a name="rule-after"></a>
#### after:_date_

Поле должно быть значением после указанной даты. Дата передаётся в `strtotime` для преобразования в валидный `DateTime`:

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

Можно также передать имя другого поля для сравнения:

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

Можно использовать fluent-построитель правила `date`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->after(\Carbon\Carbon::today()->addDays(7)),
    ],
])->validate();
```

`afterToday` и `todayOrAfter` удобно выражают «должно быть после сегодня» или «должно быть сегодня или позже»:

```php
Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->afterToday(),
    ],
])->validate();
```

<a name="rule-after-or-equal"></a>
#### after_or_equal:_date_

Поле должно быть в указанную дату или после неё. Подробнее см. [after](#rule-after).

Можно использовать fluent-построитель правила `date`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->afterOrEqual(\Carbon\Carbon::today()->addDays(7)),
    ],
])->validate();
```

<a name="rule-anyof"></a>
#### anyOf

`Rule::anyOf` позволяет указать «удовлетворять любому одному набору правил». Например, следующее правило означает, что `username` должен быть либо email-адресом, либо строкой из букв, цифр, подчёркиваний и дефисов длиной не менее 6 символов:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'username' => [
        'required',
        Rule::anyOf([
            ['string', 'email'],
            ['string', 'alpha_dash', 'min:6'],
        ]),
    ],
])->validate();
```

<a name="rule-alpha"></a>
#### alpha

Поле должно содержать только Unicode-буквы ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) и [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)).

Для разрешения только ASCII (`a-z`, `A-Z`) добавьте опцию `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

Поле может содержать только Unicode-буквы и цифры ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)), плюс ASCII дефис (`-`) и подчёркивание (`_`).

Для разрешения только ASCII (`a-z`, `A-Z`, `0-9`) добавьте опцию `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

Поле может содержать только Unicode-буквы и цифры ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)).

Для разрешения только ASCII (`a-z`, `A-Z`, `0-9`) добавьте опцию `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

Поле должно быть PHP `array`.

При наличии дополнительных параметров у правила `array` ключи входного массива должны быть в списке параметров. В примере ключ `admin` не входит в разрешённый список, поэтому он невалиден:

```php
use support\validation\Validator;

$input = [
    'user' => [
        'name' => 'Taylor Otwell',
        'username' => 'taylorotwell',
        'admin' => true,
    ],
];

Validator::make($input, [
    'user' => 'array:name,username',
])->validate();
```

Рекомендуется явно определять разрешённые ключи массива в реальных проектах.

<a name="rule-ascii"></a>
#### ascii

Поле может содержать только 7-битные ASCII-символы.

<a name="rule-bail"></a>
#### bail

Остановить валидацию остальных правил для поля при первой ошибке.

Это правило влияет только на текущее поле. Для «остановки при первой ошибке глобально» используйте валидатор Illuminate напрямую и вызовите `stopOnFirstFailure()`.

<a name="rule-before"></a>
#### before:_date_

Поле должно быть до указанной даты. Дата передаётся в `strtotime` для преобразования в валидный `DateTime`. Как и в [after](#rule-after), можно передать имя другого поля для сравнения.

Можно использовать fluent-построитель правила `date`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->before(\Carbon\Carbon::today()->subDays(7)),
    ],
])->validate();
```

`beforeToday` и `todayOrBefore` удобно выражают «должно быть до сегодня» или «должно быть сегодня или раньше»:

```php
Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->beforeToday(),
    ],
])->validate();
```

<a name="rule-before-or-equal"></a>
#### before_or_equal:_date_

Поле должно быть в указанную дату или до неё. Дата передаётся в `strtotime` для преобразования в валидный `DateTime`. Как и в [after](#rule-after), можно передать имя другого поля для сравнения.

Можно использовать fluent-построитель правила `date`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->beforeOrEqual(\Carbon\Carbon::today()->subDays(7)),
    ],
])->validate();
```

<a name="rule-between"></a>
#### between:_min_,_max_

Размер поля должен быть между _min_ и _max_ (включительно). Оценка для строк, чисел, массивов и файлов такая же, как в [size](#rule-size).

<a name="rule-boolean"></a>
#### boolean

Поле должно преобразовываться в boolean. Допустимые значения: `true`, `false`, `1`, `0`, `"1"`, `"0"`.

Используйте параметр `strict` для разрешения только `true` или `false`:

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

Поле должно иметь соответствующее поле `{field}_confirmation`. Например, когда поле — `password`, требуется `password_confirmation`.

Можно также указать пользовательское имя поля подтверждения, например `confirmed:repeat_username` требует, чтобы `repeat_username` совпадало с текущим полем.

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

Поле должно быть массивом и содержать все указанные значения параметров. Это правило обычно используется для валидации массивов; можно использовать `Rule::contains` для его построения:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'roles' => [
        'required',
        'array',
        Rule::contains(['admin', 'editor']),
    ],
])->validate();
```

<a name="rule-doesnt-contain"></a>
#### doesnt_contain:_foo_,_bar_,...

Поле должно быть массивом и не должно содержать ни одного из указанных значений параметров. Можно использовать `Rule::doesntContain` для построения:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'roles' => [
        'required',
        'array',
        Rule::doesntContain(['admin', 'editor']),
    ],
])->validate();
```

<a name="rule-current-password"></a>
#### current_password

Поле должно совпадать с паролем текущего аутентифицированного пользователя. Можно указать auth guard первым параметром:

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> Это правило зависит от компонента auth и конфигурации guard; не используйте при отсутствии интеграции auth.

<a name="rule-date"></a>
#### date

Поле должно быть валидной (не относительной) датой, распознаваемой `strtotime`.

<a name="rule-date-equals"></a>
#### date_equals:_date_

Поле должно быть равно указанной дате. Дата передаётся в `strtotime` для преобразования в валидный `DateTime`.

<a name="rule-date-format"></a>
#### date_format:_format_,...

Поле должно соответствовать одному из указанных форматов. Используйте либо `date`, либо `date_format`. Это правило поддерживает все форматы PHP [DateTime](https://www.php.net/manual/en/class.datetime.php).

Можно использовать fluent-построитель правила `date`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->format('Y-m-d'),
    ],
])->validate();
```

<a name="rule-decimal"></a>
#### decimal:_min_,_max_

Поле должно быть числовым с требуемым количеством десятичных знаков:

```php
Validator::make($data, [
    'price' => 'decimal:2',
])->validate();

Validator::make($data, [
    'price' => 'decimal:2,4',
])->validate();
```

<a name="rule-declined"></a>
#### declined

Поле должно быть `"no"`, `"off"`, `0`, `"0"`, `false` или `"false"`.

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

Когда другое поле равно указанному значению, поле должно быть `"no"`, `"off"`, `0`, `"0"`, `false` или `"false"`.

<a name="rule-different"></a>
#### different:_field_

Поле должно отличаться от _field_.

<a name="rule-digits"></a>
#### digits:_value_

Поле должно быть целым числом длиной _value_.

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

Поле должно быть целым числом длиной между _min_ и _max_.

<a name="rule-dimensions"></a>
#### dimensions

Поле должно быть изображением и удовлетворять ограничениям размеров:

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

Доступные ограничения: _min\_width_, _max\_width_, _min\_height_, _max\_height_, _width_, _height_, _ratio_.

_ratio_ — соотношение сторон; может быть выражено дробью или float:

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

У этого правила много параметров; рекомендуется использовать `Rule::dimensions` для построения:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'avatar' => [
        'required',
        Rule::dimensions()
            ->maxWidth(1000)
            ->maxHeight(500)
            ->ratio(3 / 2),
    ],
])->validate();
```

<a name="rule-distinct"></a>
#### distinct

При валидации массивов значения полей не должны дублироваться:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

По умолчанию используется нестрогое сравнение. Добавьте `strict` для строгого сравнения:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

Добавьте `ignore_case` для игнорирования регистра:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

Поле не должно начинаться ни с одного из указанных значений.

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

Поле не должно заканчиваться ни на одно из указанных значений.

<a name="rule-email"></a>
#### email

Поле должно быть валидным email-адресом. Это правило зависит от [egulias/email-validator](https://github.com/egulias/EmailValidator), по умолчанию использует `RFCValidation` и может использовать другие методы валидации:

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

Доступные методы валидации:

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` — валидация email по спецификации RFC ([поддерживаемые RFC](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs)).
- `strict`: `NoRFCWarningsValidation` — ошибка при предупреждениях RFC (например, завершающая точка или последовательные точки).
- `dns`: `DNSCheckValidation` — проверка наличия валидных MX-записей у домена.
- `spoof`: `SpoofCheckValidation` — предотвращение гомографов или подделки Unicode-символами.
- `filter`: `FilterEmailValidation` — валидация с помощью PHP `filter_var`.
- `filter_unicode`: `FilterEmailValidation::unicode()` — валидация `filter_var` с разрешением Unicode.

</div>

Можно использовать fluent-построитель правил:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        'required',
        Rule::email()
            ->rfcCompliant(strict: false)
            ->validateMxRecord()
            ->preventSpoofing(),
    ],
])->validate();
```

> [!WARNING]
> `dns` и `spoof` требуют расширения PHP `intl`.

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

Поле должно соответствовать указанной кодировке символов. Это правило использует `mb_check_encoding` для определения кодировки файла или строки. Можно использовать с построителем правил для файлов:

```php
use Illuminate\Validation\Rules\File;
use support\validation\Validator;

Validator::make($data, [
    'attachment' => [
        'required',
        File::types(['csv'])->encoding('utf-8'),
    ],
])->validate();
```

<a name="rule-ends-with"></a>
#### ends_with:_foo_,_bar_,...

Поле должно заканчиваться одним из указанных значений.

<a name="rule-enum"></a>
#### enum

`Enum` — правило на основе класса для валидации того, что значение поля является валидным значением enum. Передайте имя класса enum при построении. Для примитивных значений используйте Backed Enum:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

Используйте `only`/`except` для ограничения значений enum:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [
        Rule::enum(ServerStatus::class)
            ->only([ServerStatus::Pending, ServerStatus::Active]),
    ],
])->validate();

Validator::make($data, [
    'status' => [
        Rule::enum(ServerStatus::class)
            ->except([ServerStatus::Pending, ServerStatus::Active]),
    ],
])->validate();
```

Используйте `when` для условных ограничений:

```php
use app\Enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [
        Rule::enum(ServerStatus::class)->when(
            $isAdmin,
            fn ($rule) => $rule->only(ServerStatus::Active),
            fn ($rule) => $rule->only(ServerStatus::Pending),
        ),
    ],
])->validate();
```

<a name="rule-exclude"></a>
#### exclude

Поле будет исключено из данных, возвращаемых `validate`/`validated`.

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

Когда _anotherfield_ равно _value_, поле будет исключено из данных, возвращаемых `validate`/`validated`.

Для сложных условий используйте `Rule::excludeIf`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'role_id' => Rule::excludeIf($isAdmin),
])->validate();

Validator::make($data, [
    'role_id' => Rule::excludeIf(fn () => $isAdmin),
])->validate();
```

<a name="rule-exclude-unless"></a>
#### exclude_unless:_anotherfield_,_value_

Если _anotherfield_ не равно _value_, поле будет исключено из данных, возвращаемых `validate`/`validated`. Если _value_ — `null` (например, `exclude_unless:name,null`), поле сохраняется только когда поле сравнения `null` или отсутствует.

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

Когда _anotherfield_ существует, поле будет исключено из данных, возвращаемых `validate`/`validated`.

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

Когда _anotherfield_ не существует, поле будет исключено из данных, возвращаемых `validate`/`validated`.

<a name="rule-exists"></a>
#### exists:_table_,_column_

Поле должно существовать в указанной таблице базы данных.

<a name="basic-usage-of-exists-rule"></a>
#### Базовое использование правила Exists

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

Когда `column` не указан, по умолчанию используется имя поля. Этот пример проверяет, существует ли столбец `state` в таблице `states`.

<a name="specifying-a-custom-column-name"></a>
#### Указание пользовательского имени столбца

Добавьте имя столбца после имени таблицы:

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

Для указания подключения к БД добавьте имя подключения перед именем таблицы:

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

Можно также передать имя класса модели; фреймворк определит имя таблицы:

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

Для пользовательских условий запроса используйте построитель `Rule`:

```php
use Illuminate\Database\Query\Builder;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        'required',
        Rule::exists('staff')->where(function (Builder $query) {
            $query->where('account_id', 1);
        }),
    ],
])->validate();
```

Можно также указать имя столбца напрямую в `Rule::exists`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'state' => [Rule::exists('states', 'abbreviation')],
])->validate();
```

Для проверки существования набора значений объедините с правилом `array`:

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

При наличии и `array`, и `exists` один запрос валидирует все значения.

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

Проверяет, что расширение загруженного файла входит в разрешённый список:

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> Не полагайтесь только на расширение для валидации типа файла; используйте вместе с [mimes](#rule-mimes) или [mimetypes](#rule-mimetypes).

<a name="rule-file"></a>
#### file

Поле должно быть успешно загруженным файлом.

<a name="rule-filled"></a>
#### filled

Когда поле существует, его значение не должно быть пустым.

<a name="rule-gt"></a>
#### gt:_field_

Поле должно быть больше указанного _field_ или _value_. Оба поля должны иметь один тип. Оценка для строк, чисел, массивов и файлов такая же, как в [size](#rule-size).

<a name="rule-gte"></a>
#### gte:_field_

Поле должно быть больше или равно указанному _field_ или _value_. Оба поля должны иметь один тип. Оценка для строк, чисел, массивов и файлов такая же, как в [size](#rule-size).

<a name="rule-hex-color"></a>
#### hex_color

Поле должно быть валидным [hex-цветом](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color).

<a name="rule-image"></a>
#### image

Поле должно быть изображением (jpg, jpeg, png, bmp, gif или webp).

> [!WARNING]
> SVG по умолчанию не разрешён из-за риска XSS. Для разрешения добавьте `allow_svg`: `image:allow_svg`.

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

Поле должно быть в указанном списке значений. Можно использовать `Rule::in` для построения:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'zones' => [
        'required',
        Rule::in(['first-zone', 'second-zone']),
    ],
])->validate();
```

При объединении с правилом `array` каждое значение во входном массиве должно быть в списке `in`:

```php
use support\validation\Rule;
use support\validation\Validator;

$input = [
    'airports' => ['NYC', 'LAS'],
];

Validator::make($input, [
    'airports' => [
        'required',
        'array',
    ],
    'airports.*' => Rule::in(['NYC', 'LIT']),
])->validate();
```

<a name="rule-in-array"></a>
#### in_array:_anotherfield_.*

Поле должно существовать в списке значений _anotherfield_.

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

Поле должно быть массивом и содержать хотя бы один из указанных значений в качестве ключа:

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

Поле должно быть целым числом.

Используйте параметр `strict` для требования типа integer; строковые целые числа будут невалидны:

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> Это правило только проверяет прохождение PHP `FILTER_VALIDATE_INT`; для строгих числовых типов используйте вместе с [numeric](#rule-numeric).

<a name="rule-ip"></a>
#### ip

Поле должно быть валидным IP-адресом.

<a name="rule-ipv4"></a>
#### ipv4

Поле должно быть валидным IPv4-адресом.

<a name="rule-ipv6"></a>
#### ipv6

Поле должно быть валидным IPv6-адресом.

<a name="rule-json"></a>
#### json

Поле должно быть валидной JSON-строкой.

<a name="rule-lt"></a>
#### lt:_field_

Поле должно быть меньше указанного _field_. Оба поля должны иметь один тип. Оценка для строк, чисел, массивов и файлов такая же, как в [size](#rule-size).

<a name="rule-lte"></a>
#### lte:_field_

Поле должно быть меньше или равно указанному _field_. Оба поля должны иметь один тип. Оценка для строк, чисел, массивов и файлов такая же, как в [size](#rule-size).

<a name="rule-lowercase"></a>
#### lowercase

Поле должно быть в нижнем регистре.

<a name="rule-list"></a>
#### list

Поле должно быть списком массива. Ключи списка должны быть последовательными числами от 0 до `count($array) - 1`.

<a name="rule-mac"></a>
#### mac_address

Поле должно быть валидным MAC-адресом.

<a name="rule-max"></a>
#### max:_value_

Поле должно быть меньше или равно _value_. Оценка для строк, чисел, массивов и файлов такая же, как в [size](#rule-size).

<a name="rule-max-digits"></a>
#### max_digits:_value_

Поле должно быть целым числом длиной не более _value_.

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

Проверяет, что MIME-тип файла входит в список:

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

MIME-тип определяется по содержимому файла и может отличаться от MIME, указанного клиентом.

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

Проверяет, что MIME-тип файла соответствует указанному расширению:

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

Хотя параметры — расширения, это правило читает содержимое файла для определения MIME. Соответствие расширения и MIME:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### MIME-типы и расширения

Это правило не проверяет соответствие «расширения файла» и «фактического MIME». Например, `mimes:png` считает валидным `photo.txt` с содержимым PNG. Для проверки расширения используйте [extensions](#rule-extensions).

<a name="rule-min"></a>
#### min:_value_

Поле должно быть больше или равно _value_. Оценка для строк, чисел, массивов и файлов такая же, как в [size](#rule-size).

<a name="rule-min-digits"></a>
#### min_digits:_value_

Поле должно быть целым числом длиной не менее _value_.

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

Поле должно быть кратным _value_.

<a name="rule-missing"></a>
#### missing

Поле не должно существовать во входных данных.

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

Когда _anotherfield_ равно любому _value_, поле не должно существовать.

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

Если _anotherfield_ не равно любому _value_, поле не должно существовать.

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

Когда любое указанное поле существует, поле не должно существовать.

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

Когда все указанные поля существуют, поле не должно существовать.

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

Поле не должно быть в указанном списке значений. Можно использовать `Rule::notIn` для построения:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'toppings' => [
        'required',
        Rule::notIn(['sprinkles', 'cherries']),
    ],
])->validate();
```

<a name="rule-not-regex"></a>
#### not_regex:_pattern_

Поле не должно соответствовать указанному регулярному выражению.

Это правило использует PHP `preg_match`. Регулярное выражение должно иметь разделители, например `'email' => 'not_regex:/^.+$/i'`.

> [!WARNING]
> При использовании `regex`/`not_regex`, если regex содержит `|`, используйте форму массива во избежание конфликта с разделителем `|`.

<a name="rule-nullable"></a>
#### nullable

Поле может быть `null`.

<a name="rule-numeric"></a>
#### numeric

Поле должно быть [числовым](https://www.php.net/manual/en/function.is-numeric.php).

Используйте параметр `strict` для разрешения только типов integer или float; числовые строки будут невалидны:

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

Поле должно существовать во входных данных.

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

Когда _anotherfield_ равно любому _value_, поле должно существовать.

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

Если _anotherfield_ не равно любому _value_, поле должно существовать.

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

Когда любое указанное поле существует, поле должно существовать.

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

Когда все указанные поля существуют, поле должно существовать.

<a name="rule-prohibited"></a>
#### prohibited

Поле должно отсутствовать или быть пустым. «Пусто» означает:

<div class="content-list" markdown="1">

- Значение `null`.
- Пустая строка.
- Пустой массив или пустой объект Countable.
- Загруженный файл с пустым путём.

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

Когда _anotherfield_ равно любому _value_, поле должно отсутствовать или быть пустым. «Пусто» означает:

<div class="content-list" markdown="1">

- Значение `null`.
- Пустая строка.
- Пустой массив или пустой объект Countable.
- Загруженный файл с пустым путём.

</div>

Для сложных условий используйте `Rule::prohibitedIf`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'role_id' => Rule::prohibitedIf($isAdmin),
])->validate();

Validator::make($data, [
    'role_id' => Rule::prohibitedIf(fn () => $isAdmin),
])->validate();
```

<a name="rule-prohibited-if-accepted"></a>
#### prohibited_if_accepted:_anotherfield_,...

Когда _anotherfield_ — `"yes"`, `"on"`, `1`, `"1"`, `true` или `"true"`, поле должно отсутствовать или быть пустым.

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

Когда _anotherfield_ — `"no"`, `"off"`, `0`, `"0"`, `false` или `"false"`, поле должно отсутствовать или быть пустым.

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

Если _anotherfield_ не равно любому _value_, поле должно отсутствовать или быть пустым. «Пусто» означает:

<div class="content-list" markdown="1">

- Значение `null`.
- Пустая строка.
- Пустой массив или пустой объект Countable.
- Загруженный файл с пустым путём.

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

Когда поле существует и не пусто, все поля в _anotherfield_ должны отсутствовать или быть пустыми. «Пусто» означает:

<div class="content-list" markdown="1">

- Значение `null`.
- Пустая строка.
- Пустой массив или пустой объект Countable.
- Загруженный файл с пустым путём.

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

Поле должно соответствовать указанному регулярному выражению.

Это правило использует PHP `preg_match`. Регулярное выражение должно иметь разделители, например `'email' => 'regex:/^.+@.+$/i'`.

> [!WARNING]
> При использовании `regex`/`not_regex`, если regex содержит `|`, используйте форму массива во избежание конфликта с разделителем `|`.

<a name="rule-required"></a>
#### required

Поле должно существовать и не быть пустым. «Пусто» означает:

<div class="content-list" markdown="1">

- Значение `null`.
- Пустая строка.
- Пустой массив или пустой объект Countable.
- Загруженный файл с пустым путём.

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

Когда _anotherfield_ равно любому _value_, поле должно существовать и не быть пустым.

Для сложных условий используйте `Rule::requiredIf`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'role_id' => Rule::requiredIf($isAdmin),
])->validate();

Validator::make($data, [
    'role_id' => Rule::requiredIf(fn () => $isAdmin),
])->validate();
```

<a name="rule-required-if-accepted"></a>
#### required_if_accepted:_anotherfield_,...

Когда _anotherfield_ — `"yes"`, `"on"`, `1`, `"1"`, `true` или `"true"`, поле должно существовать и не быть пустым.

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

Когда _anotherfield_ — `"no"`, `"off"`, `0`, `"0"`, `false` или `"false"`, поле должно существовать и не быть пустым.

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

Если _anotherfield_ не равно любому _value_, поле должно существовать и не быть пустым. Если _value_ — `null` (например, `required_unless:name,null`), поле может быть пустым только когда поле сравнения `null` или отсутствует.

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

Когда любое указанное поле существует и не пусто, поле должно существовать и не быть пустым.

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

Когда все указанные поля существуют и не пусты, поле должно существовать и не быть пустым.

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

Когда любое указанное поле пусто или отсутствует, поле должно существовать и не быть пустым.

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

Когда все указанные поля пусты или отсутствуют, поле должно существовать и не быть пустым.

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

Поле должно быть массивом и содержать хотя бы указанные ключи.

<a name="validating-when-present"></a>
#### sometimes

Применять последующие правила валидации только когда поле существует. Обычно используется для полей «опционально, но при наличии должно быть валидным»:

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

Поле должно совпадать с _field_.

<a name="rule-size"></a>
#### size:_value_

Размер поля должен быть равен указанному _value_. Для строк: количество символов; для чисел: указанное целое (используйте с `numeric` или `integer`); для массивов: количество элементов; для файлов: размер в КБ. Пример:

```php
Validator::make($data, [
    'title' => 'size:12',
    'seats' => 'integer|size:10',
    'tags' => 'array|size:5',
    'image' => 'file|size:512',
])->validate();
```

<a name="rule-starts-with"></a>
#### starts_with:_foo_,_bar_,...

Поле должно начинаться с одного из указанных значений.

<a name="rule-string"></a>
#### string

Поле должно быть строкой. Для разрешения `null` используйте вместе с `nullable`.

<a name="rule-timezone"></a>
#### timezone

Поле должно быть валидным идентификатором часового пояса (из `DateTimeZone::listIdentifiers`). Можно передать параметры, поддерживаемые этим методом:

```php
Validator::make($data, [
    'timezone' => 'required|timezone:all',
])->validate();

Validator::make($data, [
    'timezone' => 'required|timezone:Africa',
])->validate();

Validator::make($data, [
    'timezone' => 'required|timezone:per_country,US',
])->validate();
```

<a name="rule-unique"></a>
#### unique:_table_,_column_

Поле должно быть уникальным в указанной таблице.

**Указание пользовательской таблицы/столбца:**

Можно указать имя класса модели напрямую:

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

Можно указать имя столбца (по умолчанию используется имя поля):

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**Указание подключения к БД:**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**Игнорирование указанного ID:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        'required',
        Rule::unique('users')->ignore($user->id),
    ],
])->validate();
```

> [!WARNING]
> `ignore` не должен получать пользовательский ввод; используйте только системно сгенерированные уникальные ID (автоинкрементный ID или UUID модели), иначе может возникнуть риск SQL-инъекции.

Можно также передать экземпляр модели:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

Если первичный ключ не `id`, укажите имя первичного ключа:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

По умолчанию используется имя поля как столбец уникальности; можно также указать имя столбца:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**Добавление дополнительных условий:**

```php
use Illuminate\Database\Query\Builder;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->where(
            fn (Builder $query) => $query->where('account_id', 1)
        ),
    ],
])->validate();
```

**Игнорирование мягко удалённых записей:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

Если столбец мягкого удаления не `deleted_at`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

Поле должно быть в верхнем регистре.

<a name="rule-url"></a>
#### url

Поле должно быть валидным URL.

Можно указать разрешённые протоколы:

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

Поле должно быть валидным [ULID](https://github.com/ulid/spec).

<a name="rule-uuid"></a>
#### uuid

Поле должно быть валидным RFC 9562 UUID (версия 1, 3, 4, 5, 6, 7 или 8).

Можно указать версию:

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```



<a name="think-validate"></a>
# Валидатор top-think/think-validate

## Описание

Официальный валидатор ThinkPHP

## URL проекта

https://github.com/top-think/think-validate

## Установка

`composer require topthink/think-validate`

## Быстрый старт

**Создайте `app/index/validate/User.php`**

```php
<?php
namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    protected $rule =   [
        'name'  => 'require|max:25',
        'age'   => 'number|between:1,120',
        'email' => 'email',    
    ];

    protected $message  =   [
        'name.require' => 'Name is required',
        'name.max'     => 'Name cannot exceed 25 characters',
        'age.number'   => 'Age must be a number',
        'age.between'  => 'Age must be between 1 and 120',
        'email'        => 'Invalid email format',    
    ];

}
```
  
**Использование**

```php
$data = [
    'name'  => 'thinkphp',
    'email' => 'thinkphp@qq.com',
];

$validate = new \app\index\validate\User;

if (!$validate->check($data)) {
    var_dump($validate->getError());
}
```

> **Примечание**
> webman не поддерживает метод `Validate::rule()` think-validate

<a name="respect-validation"></a>
# Валидатор workerman/validation

## Описание

Этот проект — локализованная версия https://github.com/Respect/Validation

## URL проекта

https://github.com/walkor/validation
  
  
## Установка
 
```php
composer require workerman/validation
```

## Быстрый старт

```php
<?php
namespace app\controller;

use support\Request;
use Respect\Validation\Validator as v;
use support\Db;

class IndexController
{
    public function index(Request $request)
    {
        $data = v::input($request->post(), [
            'nickname' => v::length(1, 64)->setName('Nickname'),
            'username' => v::alnum()->length(5, 64)->setName('Username'),
            'password' => v::length(5, 64)->setName('Password')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
  
**Доступ через jQuery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
Результат:

`{"code":500,"msg":"Username may only contain letters (a-z) and numbers (0-9)"}`

Пояснение:

`v::input(array $input, array $rules)` валидирует и собирает данные. При ошибке валидации выбрасывается `Respect\Validation\Exceptions\ValidationException`; при успехе возвращаются валидированные данные (массив).

Если бизнес-код не перехватывает исключение валидации, фреймворк webman перехватит его и вернёт JSON (например, `{"code":500, "msg":"xxx"}`) или обычную страницу исключения в зависимости от HTTP-заголовков. Если формат ответа не соответствует вашим потребностям, можно перехватить `ValidationException` и вернуть пользовательские данные, как в примере ниже:

```php
<?php
namespace app\controller;

use support\Request;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

class IndexController
{
    public function index(Request $request)
    {
        try {
            $data = v::input($request->post(), [
                'username' => v::alnum()->length(5, 64)->setName('Username'),
                'password' => v::length(5, 64)->setName('Password')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```

## Руководство по валидатору

```php
use Respect\Validation\Validator as v;

// Валидация по одному правилу
$number = 123;
v::numericVal()->validate($number); // true

// Цепочка валидации
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Получить причину первой ошибки валидации
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username may only contain letters (a-z) and numbers (0-9)
}

// Получить все причины ошибок валидации
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Выведет
    // -  Username must satisfy the following rules
    //     - Username may only contain letters (a-z) and numbers (0-9)
    //     - Username must not contain whitespace
  
    var_export($exception->getMessages());
    // Выведет
    // array (
    //   'alnum' => 'Username may only contain letters (a-z) and numbers (0-9)',
    //   'noWhitespace' => 'Username must not contain whitespace',
    // )
}

// Пользовательские сообщения об ошибках
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Username may only contain letters and numbers',
        'noWhitespace' => 'Username must not contain spaces',
        'length' => 'length satisfies the rule, so this will not be shown'
    ]));
    // Выведет 
    // array(
    //    'alnum' => 'Username may only contain letters and numbers',
    //    'noWhitespace' => 'Username must not contain spaces'
    // )
}

// Валидация объекта
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Валидация массива
$data = [
    'parentKey' => [
        'field1' => 'value1',
        'field2' => 'value2'
        'field3' => true,
    ]
];
v::key(
    'parentKey',
    v::key('field1', v::stringType())
        ->key('field2', v::stringType())
        ->key('field3', v::boolType())
    )
    ->assert($data); // Можно также использовать check() или validate()
  
// Опциональная валидация
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Правило отрицания
v::not(v::intVal())->validate(10); // false
```
  
## Различия между методами валидатора `validate()` `check()` `assert()`

`validate()` возвращает boolean, не выбрасывает исключение

`check()` выбрасывает исключение при ошибке валидации; получить причину первой ошибки через `$exception->getMessage()`

`assert()` выбрасывает исключение при ошибке валидации; получить все причины ошибок через `$exception->getFullMessage()`
  
  
## Распространённые правила валидации

`Alnum()` Только буквы и цифры

`Alpha()` Только буквы

`ArrayType()` Тип массива

`Between(mixed $minimum, mixed $maximum)` Проверяет, что ввод находится между двумя значениями.

`BoolType()` Тип boolean

`Contains(mixed $expectedValue)` Проверяет, что ввод содержит определённое значение

`ContainsAny(array $needles)` Проверяет, что ввод содержит хотя бы одно из определённых значений

`Digit()` Проверяет, что ввод содержит только цифры

`Domain()` Проверяет валидное доменное имя

`Email()` Проверяет валидный email-адрес

`Extension(string $extension)` Проверяет расширение файла

`FloatType()` Проверяет тип float

`IntType()` Проверяет тип integer

`Ip()` Проверяет IP-адрес

`Json()` Проверяет JSON-данные

`Length(int $min, int $max)` Проверяет, что длина в допустимом диапазоне

`LessThan(mixed $compareTo)` Проверяет, что длина меньше указанного значения

`Lowercase()` Проверяет нижний регистр

`MacAddress()` Проверяет MAC-адрес

`NotEmpty()` Проверяет непустое значение

`NullType()` Проверяет null

`Number()` Проверяет число

`ObjectType()` Проверяет тип объекта

`StringType()` Проверяет тип строки

`Url()` Проверяет URL
  
См. https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ для дополнительных правил валидации.
  
## Дополнительно

Посетите https://respect-validation.readthedocs.io/en/2.0/
  
