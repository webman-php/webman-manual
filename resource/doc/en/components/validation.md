# Other Validators

There are many validators available in composer that can be used directly, such as:

#### <a href="#webman-validation"> webman/validation (Recommended)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# Validator webman/validation

Based on `illuminate/validation`, it provides manual validation, annotation validation, parameter-level validation, and reusable rule sets.

## Installation

```bash
composer require webman/validation
```

## Basic Concepts

- **Rule Set Reuse**: Define reusable `rules`, `messages`, `attributes`, and `scenes` by extending `support\validation\Validator`, which can be reused in both manual and annotation validation.
- **Method-Level Annotation (Attribute) Validation**: Use PHP 8 attribute `#[Validate]` to bind validation to controller methods.
- **Parameter-Level Annotation (Attribute) Validation**: Use PHP 8 attribute `#[Param]` to bind validation to controller method parameters.
- **Exception Handling**: Throws `support\validation\ValidationException` on validation failure; the exception class is configurable.
- **Database Validation**: If database validation is involved, you need to install `composer require webman/database`.

## Manual Validation

### Basic Usage

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **Note**
> `validate()` throws `support\validation\ValidationException` when validation fails. If you prefer not to throw exceptions, use the `fails()` approach below to get error messages.

### Custom messages and attributes

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

### Validate Without Exception (Get Error Messages)

If you prefer not to throw exceptions, use `fails()` to check and get error messages via `errors()` (returns `MessageBag`):

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

## Rule Set Reuse (Custom Validator)

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

### Manual Validation Reuse

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### Use scenes (Optional)

`scenes` is an optional feature; it only validates a subset of fields when you call `withScene(...)`.

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

// No scene specified -> validate all rules
UserValidator::make($data)->validate();

// Specify scene -> validate only fields in that scene
UserValidator::make($data)->withScene('create')->validate();
```

## Annotation Validation (Method-Level)

### Direct Rules

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

### Reuse Rule Sets

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

### Multiple Validation Overlays

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

### Validation Data Source

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

Use the `in` parameter to specify the data source:

* **query** HTTP request query parameters, from `$request->get()`
* **body** HTTP request body, from `$request->post()`
* **path** HTTP request path parameters, from `$request->route->param()`

`in` can be a string or array; when it is an array, values are merged in order with later values overriding earlier ones. When `in` is not passed, it defaults to `['query', 'body', 'path']`.


## Parameter-Level Validation (Param)

### Basic Usage

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

### Validation Data Source

Similarly, parameter-level validation also supports the `in` parameter to specify the source:

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


### rules supports string or array

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

### Custom messages / attribute

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

### Rule Constant Reuse

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

## Method-Level + Parameter-Level Combined

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

## Automatic Rule Inference (Based on Parameter Signature)

When `#[Validate]` is used on a method, or any parameter of that method uses `#[Param]`, this component **automatically infers and completes basic validation rules from the method parameter signature**, then merges them with existing rules before validation.

### Example: `#[Validate]` equivalent expansion

1) Only enable `#[Validate]` without writing rules manually:

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

Equivalent to:

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

2) Only partial rules written, rest completed by parameter signature:

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

Equivalent to:

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

3) Default value / nullable type:

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

Equivalent to:

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

## Exception Handling

### Default Exception

Validation failure throws `support\validation\ValidationException` by default, which extends `Webman\Exception\BusinessException` and does not log errors.

Default response behavior is handled by `BusinessException::render()`:

- Regular requests: returns string message, e.g. `token is required.`
- JSON requests: returns JSON response, e.g. `{"code": 422, "msg": "token is required.", "data":....}`

### Customize handling via custom exception

- Global config: `exception` in `config/plugin/webman/validation/app.php`

## Multilingual Support

The component includes built-in Chinese and English language packs and supports project overrides. Load order:

1. Project language pack `resource/translations/{locale}/validation.php`
2. Component built-in `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Illuminate built-in English (fallback)

> **Note**
> Webman default language is configured in `config/translation.php`, or can be changed via `locale('en');`.

### Local Override Example

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute is not a valid email format.',
];
```

## Middleware Auto-Loading

After installation, the component auto-loads the validation middleware via `config/plugin/webman/validation/middleware.php`; no manual registration is needed.

## Command-Line Generation

Use the `make:validator` command to generate validator classes (default output to `app/validation` directory).

> **Note**
> Requires `composer require webman/console`

### Basic Usage

- **Generate empty template**

```bash
php webman make:validator UserValidator
```

- **Overwrite existing file**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### Generate rules from table structure

- **Specify table name to generate base rules** (infers `$rules` from field type/nullable/length etc.; excludes ORM-related fields by default: laravel uses `created_at/updated_at/deleted_at`, thinkorm uses `create_time/update_time/delete_time`)

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **Specify database connection** (multi-connection scenarios)

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### Scenes

- **Generate CRUD scenes**: `create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> The `update` scene includes the primary key field (for locating records) plus other fields; `delete/detail` include only the primary key by default.

### ORM selection (laravel (illuminate/database) vs think-orm)

- **Auto-select (default)**: Uses whichever is installed/configured; when both exist, uses illuminate by default
- **Force specify**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### Complete example

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## Unit Tests

From the `webman/validation` root directory, run:

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## Validation Rules Reference

<a name="available-validation-rules"></a>
## Available Validation Rules

> [!IMPORTANT]
> - Webman Validation is based on `illuminate/validation`; rule names match Laravel and there are no Webman-specific rules.
> - Middleware validates data from `$request->all()` (GET+POST) merged with route parameters by default, excluding uploaded files; for file rules, merge `$request->file()` into the data yourself, or call `Validator::make` manually.
> - `current_password` depends on auth guard; `exists`/`unique` depend on database connection and query builder; these rules are unavailable when the corresponding components are not integrated.

The following lists all available validation rules and their purposes:

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

The field must be `"yes"`, `"on"`, `1`, `"1"`, `true`, or `"true"`. Commonly used for scenarios like verifying user agreement to terms of service.

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

When another field equals the specified value, the field must be `"yes"`, `"on"`, `1`, `"1"`, `true`, or `"true"`. Commonly used for conditional agreement scenarios.

<a name="rule-active-url"></a>
#### active_url

The field must have a valid A or AAAA record. This rule first uses `parse_url` to extract the URL hostname, then validates with `dns_get_record`.

<a name="rule-after"></a>
#### after:_date_

The field must be a value after the given date. The date is passed to `strtotime` to convert to a valid `DateTime`:

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

You can also pass another field name for comparison:

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

You can use the fluent `date` rule builder:

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

`afterToday` and `todayOrAfter` conveniently express "must be after today" or "must be today or later":

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

The field must be on or after the given date. See [after](#rule-after) for more details.

You can use the fluent `date` rule builder:

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

`Rule::anyOf` allows specifying "satisfy any one rule set". For example, the following rule means `username` must be either an email address or an alphanumeric/underscore/dash string of at least 6 characters:

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

The field must be Unicode letters ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) and [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)).

To allow only ASCII (`a-z`, `A-Z`), add the `ascii` option:

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

The field may only contain Unicode letters and numbers ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)), plus ASCII hyphen (`-`) and underscore (`_`).

To allow only ASCII (`a-z`, `A-Z`, `0-9`), add the `ascii` option:

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

The field may only contain Unicode letters and numbers ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)).

To allow only ASCII (`a-z`, `A-Z`, `0-9`), add the `ascii` option:

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

The field must be a PHP `array`.

When the `array` rule has extra parameters, the input array keys must be in the parameter list. In the example, the `admin` key is not in the allowed list, so it is invalid:

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

It is recommended to explicitly define allowed array keys in real projects.

<a name="rule-ascii"></a>
#### ascii

The field may only contain 7-bit ASCII characters.

<a name="rule-bail"></a>
#### bail

Stop validating further rules for the field when the first rule fails.

This rule only affects the current field. For "stop on first failure globally", use Illuminate's validator directly and call `stopOnFirstFailure()`.

<a name="rule-before"></a>
#### before:_date_

The field must be before the given date. The date is passed to `strtotime` to convert to a valid `DateTime`. Like [after](#rule-after), you can pass another field name for comparison.

You can use the fluent `date` rule builder:

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

`beforeToday` and `todayOrBefore` conveniently express "must be before today" or "must be today or earlier":

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

The field must be on or before the given date. The date is passed to `strtotime` to convert to a valid `DateTime`. Like [after](#rule-after), you can pass another field name for comparison.

You can use the fluent `date` rule builder:

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

The field size must be between _min_ and _max_ (inclusive). Evaluation for strings, numbers, arrays, and files is the same as [size](#rule-size).

<a name="rule-boolean"></a>
#### boolean

The field must be convertible to boolean. Acceptable inputs include `true`, `false`, `1`, `0`, `"1"`, `"0"`.

Use the `strict` parameter to allow only `true` or `false`:

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

The field must have a matching `{field}_confirmation` field. For example, when the field is `password`, `password_confirmation` is required.

You can also specify a custom confirmation field name, e.g. `confirmed:repeat_username` requires `repeat_username` to match the current field.

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

The field must be an array and must contain all given parameter values. This rule is commonly used for array validation; you can use `Rule::contains` to construct it:

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

The field must be an array and must not contain any of the given parameter values. You can use `Rule::doesntContain` to construct it:

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

The field must match the current authenticated user's password. You can specify the auth guard as the first parameter:

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> This rule depends on the auth component and guard configuration; do not use when auth is not integrated.

<a name="rule-date"></a>
#### date

The field must be a valid (non-relative) date recognizable by `strtotime`.

<a name="rule-date-equals"></a>
#### date_equals:_date_

The field must equal the given date. The date is passed to `strtotime` to convert to a valid `DateTime`.

<a name="rule-date-format"></a>
#### date_format:_format_,...

The field must match one of the given formats. Use either `date` or `date_format`. This rule supports all PHP [DateTime](https://www.php.net/manual/en/class.datetime.php) formats.

You can use the fluent `date` rule builder:

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

The field must be numeric with the required decimal places:

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

The field must be `"no"`, `"off"`, `0`, `"0"`, `false`, or `"false"`.

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

When another field equals the specified value, the field must be `"no"`, `"off"`, `0`, `"0"`, `false`, or `"false"`.

<a name="rule-different"></a>
#### different:_field_

The field must be different from _field_.

<a name="rule-digits"></a>
#### digits:_value_

The field must be an integer with length _value_.

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

The field must be an integer with length between _min_ and _max_.

<a name="rule-dimensions"></a>
#### dimensions

The field must be an image and satisfy dimension constraints:

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

Available constraints: _min\_width_, _max\_width_, _min\_height_, _max\_height_, _width_, _height_, _ratio_.

_ratio_ is the aspect ratio; it can be expressed as a fraction or float:

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

This rule has many parameters; it is recommended to use `Rule::dimensions` to construct it:

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

When validating arrays, field values must not be duplicated:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

Uses loose comparison by default. Add `strict` for strict comparison:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

Add `ignore_case` to ignore case differences:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

The field must not start with any of the specified values.

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

The field must not end with any of the specified values.

<a name="rule-email"></a>
#### email

The field must be a valid email address. This rule depends on [egulias/email-validator](https://github.com/egulias/EmailValidator), uses `RFCValidation` by default, and can use other validation methods:

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

Available validation methods:

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` - Validate email per RFC spec ([supported RFCs](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs)).
- `strict`: `NoRFCWarningsValidation` - Fail on RFC warnings (e.g. trailing dot or consecutive dots).
- `dns`: `DNSCheckValidation` - Check if domain has valid MX records.
- `spoof`: `SpoofCheckValidation` - Prevent homograph or spoofing Unicode characters.
- `filter`: `FilterEmailValidation` - Validate using PHP `filter_var`.
- `filter_unicode`: `FilterEmailValidation::unicode()` - `filter_var` validation allowing Unicode.

</div>

You can use the fluent rule builder:

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
> `dns` and `spoof` require the PHP `intl` extension.

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

The field must match the specified character encoding. This rule uses `mb_check_encoding` to detect file or string encoding. Can be used with the file rule builder:

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

The field must end with one of the specified values.

<a name="rule-enum"></a>
#### enum

`Enum` is a class-based rule for validating that the field value is a valid enum value. Pass the enum class name when constructing. For primitive values, use Backed Enum:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

Use `only`/`except` to restrict enum values:

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

Use `when` for conditional restrictions:

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

The field will be excluded from data returned by `validate`/`validated`.

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

When _anotherfield_ equals _value_, the field will be excluded from data returned by `validate`/`validated`.

For complex conditions, use `Rule::excludeIf`:

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

Unless _anotherfield_ equals _value_, the field will be excluded from data returned by `validate`/`validated`. If _value_ is `null` (e.g. `exclude_unless:name,null`), the field is only kept when the comparison field is `null` or absent.

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

When _anotherfield_ exists, the field will be excluded from data returned by `validate`/`validated`.

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

When _anotherfield_ does not exist, the field will be excluded from data returned by `validate`/`validated`.

<a name="rule-exists"></a>
#### exists:_table_,_column_

The field must exist in the specified database table.

<a name="basic-usage-of-exists-rule"></a>
#### Basic usage of Exists rule

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

When `column` is not specified, the field name is used by default. So this example validates whether the `state` column exists in the `states` table.

<a name="specifying-a-custom-column-name"></a>
#### Specifying a custom column name

Append the column name after the table name:

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

To specify a database connection, prefix the table name with the connection name:

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

You can also pass a model class name; the framework will resolve the table name:

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

For custom query conditions, use the `Rule` builder:

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

You can also specify the column name directly in `Rule::exists`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'state' => [Rule::exists('states', 'abbreviation')],
])->validate();
```

To validate a set of values exist, combine with the `array` rule:

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

When both `array` and `exists` are present, a single query validates all values.

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

Validates that the uploaded file extension is in the allowed list:

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> Do not rely on extension alone for file type validation; use with [mimes](#rule-mimes) or [mimetypes](#rule-mimetypes).

<a name="rule-file"></a>
#### file

The field must be a successfully uploaded file.

<a name="rule-filled"></a>
#### filled

When the field exists, its value must not be empty.

<a name="rule-gt"></a>
#### gt:_field_

The field must be greater than the given _field_ or _value_. Both fields must have the same type. Evaluation for strings, numbers, arrays, and files is the same as [size](#rule-size).

<a name="rule-gte"></a>
#### gte:_field_

The field must be greater than or equal to the given _field_ or _value_. Both fields must have the same type. Evaluation for strings, numbers, arrays, and files is the same as [size](#rule-size).

<a name="rule-hex-color"></a>
#### hex_color

The field must be a valid [hex color value](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color).

<a name="rule-image"></a>
#### image

The field must be an image (jpg, jpeg, png, bmp, gif, or webp).

> [!WARNING]
> SVG is not allowed by default due to XSS risk. To allow it, add `allow_svg`: `image:allow_svg`.

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

The field must be in the given value list. You can use `Rule::in` to construct:

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

When combined with the `array` rule, each value in the input array must be in the `in` list:

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

The field must exist in the value list of _anotherfield_.

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

The field must be an array and must contain at least one of the given values as a key:

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

The field must be an integer.

Use the `strict` parameter to require the field type to be integer; string integers will be invalid:

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> This rule only validates whether it passes PHP's `FILTER_VALIDATE_INT`; for strict numeric types, use with [numeric](#rule-numeric).

<a name="rule-ip"></a>
#### ip

The field must be a valid IP address.

<a name="rule-ipv4"></a>
#### ipv4

The field must be a valid IPv4 address.

<a name="rule-ipv6"></a>
#### ipv6

The field must be a valid IPv6 address.

<a name="rule-json"></a>
#### json

The field must be a valid JSON string.

<a name="rule-lt"></a>
#### lt:_field_

The field must be less than the given _field_. Both fields must have the same type. Evaluation for strings, numbers, arrays, and files is the same as [size](#rule-size).

<a name="rule-lte"></a>
#### lte:_field_

The field must be less than or equal to the given _field_. Both fields must have the same type. Evaluation for strings, numbers, arrays, and files is the same as [size](#rule-size).

<a name="rule-lowercase"></a>
#### lowercase

The field must be lowercase.

<a name="rule-list"></a>
#### list

The field must be a list array. List array keys must be consecutive numbers from 0 to `count($array) - 1`.

<a name="rule-mac"></a>
#### mac_address

The field must be a valid MAC address.

<a name="rule-max"></a>
#### max:_value_

The field must be less than or equal to _value_. Evaluation for strings, numbers, arrays, and files is the same as [size](#rule-size).

<a name="rule-max-digits"></a>
#### max_digits:_value_

The field must be an integer with length not exceeding _value_.

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

Validates that the file's MIME type is in the list:

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

MIME type is guessed by reading file content and may differ from the client-provided MIME.

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

Validates that the file's MIME type corresponds to the given extension:

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

Although the parameters are extensions, this rule reads file content to determine MIME. Extension-to-MIME mapping:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### MIME types and extensions

This rule does not validate that "file extension" matches "actual MIME". For example, `mimes:png` treats `photo.txt` with PNG content as valid. To validate extension, use [extensions](#rule-extensions).

<a name="rule-min"></a>
#### min:_value_

The field must be greater than or equal to _value_. Evaluation for strings, numbers, arrays, and files is the same as [size](#rule-size).

<a name="rule-min-digits"></a>
#### min_digits:_value_

The field must be an integer with length not less than _value_.

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

The field must be a multiple of _value_.

<a name="rule-missing"></a>
#### missing

The field must not exist in the input data.

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

When _anotherfield_ equals any _value_, the field must not exist.

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

Unless _anotherfield_ equals any _value_, the field must not exist.

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

When any specified field exists, the field must not exist.

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

When all specified fields exist, the field must not exist.

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

The field must not be in the given value list. You can use `Rule::notIn` to construct:

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

The field must not match the given regular expression.

This rule uses PHP `preg_match`. The regex must have delimiters, e.g. `'email' => 'not_regex:/^.+$/i'`.

> [!WARNING]
> When using `regex`/`not_regex`, if the regex contains `|`, use array form to avoid conflict with the `|` separator.

<a name="rule-nullable"></a>
#### nullable

The field may be `null`.

<a name="rule-numeric"></a>
#### numeric

The field must be [numeric](https://www.php.net/manual/en/function.is-numeric.php).

Use the `strict` parameter to allow only integer or float types; numeric strings will be invalid:

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

The field must exist in the input data.

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

When _anotherfield_ equals any _value_, the field must exist.

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

Unless _anotherfield_ equals any _value_, the field must exist.

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

When any specified field exists, the field must exist.

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

When all specified fields exist, the field must exist.

<a name="rule-prohibited"></a>
#### prohibited

The field must be missing or empty. "Empty" means:

<div class="content-list" markdown="1">

- Value is `null`.
- Value is empty string.
- Value is empty array or empty `Countable` object.
- Uploaded file with empty path.

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

When _anotherfield_ equals any _value_, the field must be missing or empty. "Empty" means:

<div class="content-list" markdown="1">

- Value is `null`.
- Value is empty string.
- Value is empty array or empty `Countable` object.
- Uploaded file with empty path.

</div>

For complex conditions, use `Rule::prohibitedIf`:

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

When _anotherfield_ is `"yes"`, `"on"`, `1`, `"1"`, `true`, or `"true"`, the field must be missing or empty.

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

When _anotherfield_ is `"no"`, `"off"`, `0`, `"0"`, `false`, or `"false"`, the field must be missing or empty.

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

Unless _anotherfield_ equals any _value_, the field must be missing or empty. "Empty" means:

<div class="content-list" markdown="1">

- Value is `null`.
- Value is empty string.
- Value is empty array or empty `Countable` object.
- Uploaded file with empty path.

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

When the field exists and is not empty, all fields in _anotherfield_ must be missing or empty. "Empty" means:

<div class="content-list" markdown="1">

- Value is `null`.
- Value is empty string.
- Value is empty array or empty `Countable` object.
- Uploaded file with empty path.

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

The field must match the given regular expression.

This rule uses PHP `preg_match`. The regex must have delimiters, e.g. `'email' => 'regex:/^.+@.+$/i'`.

> [!WARNING]
> When using `regex`/`not_regex`, if the regex contains `|`, use array form to avoid conflict with the `|` separator.

<a name="rule-required"></a>
#### required

The field must exist and not be empty. "Empty" means:

<div class="content-list" markdown="1">

- Value is `null`.
- Value is empty string.
- Value is empty array or empty `Countable` object.
- Uploaded file with empty path.

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

When _anotherfield_ equals any _value_, the field must exist and not be empty.

For complex conditions, use `Rule::requiredIf`:

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

When _anotherfield_ is `"yes"`, `"on"`, `1`, `"1"`, `true`, or `"true"`, the field must exist and not be empty.

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

When _anotherfield_ is `"no"`, `"off"`, `0`, `"0"`, `false`, or `"false"`, the field must exist and not be empty.

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

Unless _anotherfield_ equals any _value_, the field must exist and not be empty. If _value_ is `null` (e.g. `required_unless:name,null`), the field may be empty only when the comparison field is `null` or absent.

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

When any specified field exists and is not empty, the field must exist and not be empty.

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

When all specified fields exist and are not empty, the field must exist and not be empty.

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

When any specified field is empty or absent, the field must exist and not be empty.

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

When all specified fields are empty or absent, the field must exist and not be empty.

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

The field must be an array and must contain at least the specified keys.

<a name="validating-when-present"></a>
#### sometimes

Apply subsequent validation rules only when the field exists. Commonly used for "optional but must be valid when present" fields:

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

The field must be the same as _field_.

<a name="rule-size"></a>
#### size:_value_

The field size must equal the given _value_. For strings: character count; for numbers: specified integer (use with `numeric` or `integer`); for arrays: element count; for files: size in KB. Example:

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

The field must start with one of the specified values.

<a name="rule-string"></a>
#### string

The field must be a string. To allow `null`, use with `nullable`.

<a name="rule-timezone"></a>
#### timezone

The field must be a valid timezone identifier (from `DateTimeZone::listIdentifiers`). You can pass parameters supported by that method:

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

The field must be unique in the specified table.

**Specify custom table/column name:**

You can specify the model class name directly:

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

You can specify the column name (defaults to field name when not specified):

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**Specify database connection:**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**Ignore specified ID:**

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
> `ignore` should not receive user input; only use system-generated unique IDs (auto-increment ID or model UUID), otherwise SQL injection risk may exist.

You can also pass a model instance:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

If the primary key is not `id`, specify the primary key name:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

By default uses the field name as the unique column; you can also specify the column name:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**Add extra conditions:**

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

**Ignore soft-deleted records:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

If the soft delete column is not `deleted_at`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

The field must be uppercase.

<a name="rule-url"></a>
#### url

The field must be a valid URL.

You can specify allowed protocols:

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

The field must be a valid [ULID](https://github.com/ulid/spec).

<a name="rule-uuid"></a>
#### uuid

The field must be a valid RFC 9562 UUID (version 1, 3, 4, 5, 6, 7, or 8).

You can specify the version:

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```



<a name="think-validate"></a>
# Validator top-think/think-validate

## Description

Official ThinkPHP validator

## Project URL

https://github.com/top-think/think-validate

## Installation

`composer require topthink/think-validate`

## Quick Start

**Create `app/index/validate/User.php`**

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
  
**Usage**

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

> **Note**
> webman does not support think-validate's `Validate::rule()` method

<a name="respect-validation"></a>
# Validator workerman/validation

## Description

This project is a localized version of https://github.com/Respect/Validation

## Project URL

https://github.com/walkor/validation
  
  
## Installation
 
```php
composer require workerman/validation
```

## Quick Start

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
  
**Access via jQuery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
Result:

`{"code":500,"msg":"Username may only contain letters (a-z) and numbers (0-9)"}`

Explanation:

`v::input(array $input, array $rules)` validates and collects data. If validation fails, it throws `Respect\Validation\Exceptions\ValidationException`; on success it returns the validated data (array).

If the business code does not catch the validation exception, the webman framework will catch it and return JSON (like `{"code":500, "msg":"xxx"}`) or a normal exception page based on HTTP headers. If the response format does not meet your needs, you can catch `ValidationException` and return custom data, as in the example below:

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

## Validator Guide

```php
use Respect\Validation\Validator as v;

// Single rule validation
$number = 123;
v::numericVal()->validate($number); // true

// Chained validation
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Get first validation failure reason
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username may only contain letters (a-z) and numbers (0-9)
}

// Get all validation failure reasons
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Will print
    // -  Username must satisfy the following rules
    //     - Username may only contain letters (a-z) and numbers (0-9)
    //     - Username must not contain whitespace
  
    var_export($exception->getMessages());
    // Will print
    // array (
    //   'alnum' => 'Username may only contain letters (a-z) and numbers (0-9)',
    //   'noWhitespace' => 'Username must not contain whitespace',
    // )
}

// Custom error messages
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Username may only contain letters and numbers',
        'noWhitespace' => 'Username must not contain spaces',
        'length' => 'length satisfies the rule, so this will not be shown'
    ]);
    // Will print 
    // array(
    //    'alnum' => 'Username may only contain letters and numbers',
    //    'noWhitespace' => 'Username must not contain spaces'
    // )
}

// Validate object
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Validate array
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
    ->assert($data); // Can also use check() or validate()
  
// Optional validation
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Negation rule
v::not(v::intVal())->validate(10); // false
```
  
## Difference between Validator methods `validate()` `check()` `assert()`

`validate()` returns boolean, does not throw exception

`check()` throws exception on validation failure; get first failure reason via `$exception->getMessage()`

`assert()` throws exception on validation failure; get all failure reasons via `$exception->getFullMessage()`
  
  
## Common Validation Rules

`Alnum()` Only letters and numbers

`Alpha()` Only letters

`ArrayType()` Array type

`Between(mixed $minimum, mixed $maximum)` Validates that input is between two values.

`BoolType()` Validates boolean type

`Contains(mixed $expectedValue)` Validates that input contains certain value

`ContainsAny(array $needles)` Validates that input contains at least one defined value

`Digit()` Validates that input contains only digits

`Domain()` Validates valid domain name

`Email()` Validates valid email address

`Extension(string $extension)` Validates file extension

`FloatType()` Validates float type

`IntType()` Validates integer type

`Ip()` Validates IP address

`Json()` Validates JSON data

`Length(int $min, int $max)` Validates length is within range

`LessThan(mixed $compareTo)` Validates length is less than given value

`Lowercase()` Validates lowercase

`MacAddress()` Validates MAC address

`NotEmpty()` Validates not empty

`NullType()` Validates null

`Number()` Validates number

`ObjectType()` Validates object type

`StringType()` Validates string type

`Url()` Validates URL
  
See https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ for more validation rules.
  
## More

Visit https://respect-validation.readthedocs.io/en/2.0/
  
