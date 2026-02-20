# Validators

There are many validators available in the composer that can be used directly, such as:


#### <a href="#webman-validation"> webman/validation</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
Webman's validation component, based on `illuminate/validation`, provides manual validation, annotation-based validation, parameter-level validation, and reusable rule sets.

## Installation

```bash
composer require webman/validation
```

## Basic Concepts

- **Rule Set Reuse**: Define reusable `rules`, `messages`, `attributes`, and `scenes` by extending `support\validation\Validator`, which can be reused in manual and annotation validation.
- **Annotation (Attribute) Validation - Method-Level**: Use the PHP 8 attribute `#[Validate]` to bind validation to controller methods.
- **Annotation (Attribute) Validation - Parameter-Level**: Use the PHP 8 attribute `#[Param]` to bind validation to controller method parameters.
- **Exception Handling**: Throws `support\validation\ValidationException` on validation failure; the exception class is configurable via config.
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
> `validate()` will throw `support\validation\ValidationException` if validation fails. If you prefer not to throw exceptions, use `fails()` as shown below.

### Custom Messages and Attributes

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

If you don't want exceptions, use `fails()` and read errors from the `MessageBag`:

```php
use support\validation\Validator;

$data = ['email' => 'bad-email'];

$validator = Validator::make($data, [
    'email' => 'required|email',
]);

if ($validator->fails()) {
    $firstError = $validator->errors()->first();   // string
    $allErrors = $validator->errors()->all();      // array
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

### Use Scenes (Optional)

Scenes are optional. They are only used when you call `withScene(...)` to validate a subset of fields.

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

### Reusing Rule Sets

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

Use `in` to specify where validation data comes from:

* **query** – HTTP query parameters from `$request->get()`
* **body** – HTTP body from `$request->post()`
* **path** – Path/route parameters from `$request->route->param()`

`in` can be a string or array; when it's an array, values are merged in order and later sources override earlier ones. When `in` is omitted, it defaults to the equivalent of `['query', 'body', 'path']`.

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

Parameter-level validation also supports the `in` parameter to specify data source:

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

### Rules Support String or Array

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

### Custom Messages / Attribute

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

### Reusing Rule Constants

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

## Method-Level + Parameter-Level Mixing

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

## Automatic Rule Inference (Signature-Based)

When a method uses `#[Validate]`, or any parameter on that method uses `#[Param]`, this package will **infer and auto-complete basic validation rules from the PHP method signature**, then merge them with your existing rules and run validation.

### Examples: `#[Validate]` Equivalent Expansion

1) Enable `#[Validate]` without writing rules:

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

2) Only partial rules provided, the rest is inferred:

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

3) Default values / nullable types:

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

Validation failure throws `support\validation\ValidationException`, which inherits from `Webman\Exception\BusinessException` and does not log errors.

Default response behavior is handled by `BusinessException::render()`:

- Non-JSON requests: return a plain string message, e.g. `token is required.`
- JSON requests: return JSON response, e.g. `{"code": 422, "msg": "token is required.", "data":....}`

### Customize with a custom exception

- Global config: `exception` in `config/plugin/webman/validation/app.php`

## Multi-Language Support

The component includes built-in Chinese and English language packs and supports project overrides. Loading order:

1. Project language pack `resource/translations/{locale}/validation.php`
2. Component built-in `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Illuminate built-in English (fallback)

> **Note**  
> The default language of webman is configured in `config/translation.php`, and it can also be changed using the function `locale('en');`.

### Local Override Example

`resource/translations/en/validation.php`

```php
return [
    'email' => 'The :attribute is not a valid email format.',
];
```

## Middleware Auto-Loading

After installation, the component automatically loads the validation middleware via `config/plugin/webman/validation/middleware.php`, no manual registration required.

## CLI Generator

Use `make:validator` to generate a validator class (generated under `app/validation` by default).

> **Tip**  
> You need to install `composer require webman/console`

### Basic

- **Generate an empty template**

```bash
php webman make:validator UserValidator
```

- **Overwrite if the file already exists**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### Generate rules from a table

- **Generate rules from a table schema** (infers `$rules` from column type/nullability/length; default excluded columns depend on ORM: laravel uses `created_at/updated_at/deleted_at`, thinkorm uses `create_time/update_time/delete_time`)

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **Select a database connection** (multi-connection)

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

> The `update` scene includes the primary key (to locate the record) plus the other fields; `delete/detail` include only primary key fields by default.

### ORM selection (laravel(illuminate/database) vs think-orm)

- **Auto (default)**: uses the available ORM; if both exist, defaults to illuminate
- **Force**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### Example

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```


## Unit Testing

Enter the `webman/validation` root directory and execute:

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## All Validation Rules Reference
<a name="available-validation-rules"></a>
## Available Validation Rules

> [!IMPORTANT]
> - Webman Validation is based on `illuminate/validation`, with rule names consistent with Laravel, and the rules themselves have no Webman-specific modifications.
> - The middleware validates data from `$request->all()` (GET+POST) by default and merges route parameters, excluding uploaded files; for file-related rules, manually merge `$request->file()` into the data or call `Validator::make` manually.
> - `current_password` depends on authentication guards, `exists`/`unique` depend on database connections and query builders, and these rules are unavailable without integrating the corresponding components.

The following lists all available validation rules and their functions:

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

#### Boolean Values

<div class="collection-method-list" markdown="1">

[Accepted](#rule-accepted)
[Accepted If](#rule-accepted-if)
[Boolean](#rule-boolean)
[Declined](#rule-declined)
[Declined If](#rule-declined-if)

</div>

#### Strings

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

#### Numbers

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

#### Arrays

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

#### Dates

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

#### Files

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

#### Utilities

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

The field under validation must be `"yes"`, `"on"`, `1`, `"1"`, `true`, or `"true"`. This is commonly used for scenarios like confirming agreement to terms of service.

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

The field under validation must be `"yes"`, `"on"`, `1`, `"1"`, `true`, or `"true"` when another field equals the specified value. This is useful for conditional agreement scenarios.

<a name="rule-active-url"></a>
#### active_url

The field under validation must have a valid A or AAAA record. The rule first extracts the hostname using `parse_url` and then validates it with `dns_get_record`.

<a name="rule-after"></a>
#### after:_date_

The field under validation must be a value after the given date. The date is converted to a valid `DateTime` using `strtotime`:

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

`afterToday` and `todayOrAfter` can conveniently express "must be after today" or "must be today or later":

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

The field under validation must be after or equal to the given date. For more details, see the [after](#rule-after) rule.

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

`Rule::anyOf` allows specifying "pass if any one of the rule sets is satisfied". For example, the following rule means `username` is either an email address or an alphanumeric string with underscores/dashes of at least 6 characters:

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

The field under validation must consist of Unicode letters ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) and [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)).

To allow only ASCII (`a-z`, `A-Z`), add the `ascii` option:

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

The field under validation can only contain Unicode alphanumeric characters ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)), as well as ASCII dashes (`-`) and underscores (`_`).

To allow only ASCII (`a-z`, `A-Z`, `0-9`), add the `ascii` option:

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

The field under validation can only contain Unicode alphanumeric characters ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)).

To allow only ASCII (`a-z`, `A-Z`, `0-9`), add the `ascii` option:

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

The field under validation must be a PHP `array`.

When the `array` rule includes additional parameters, the keys in the input array must be in the parameter list. In the example, the `admin` key is not in the allowed list, so it is invalid:

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

It is recommended to explicitly specify the allowed keys for arrays in actual projects.

<a name="rule-ascii"></a>
#### ascii

The field under validation can only contain 7-bit ASCII characters.

<a name="rule-bail"></a>
#### bail

When the first validation rule for a field fails, stop validating the other rules for that field.

This rule only affects the current field. For "stop on first failure globally", use Illuminate's validator directly and call `stopOnFirstFailure()`.

<a name="rule-before"></a>
#### before:_date_

The field under validation must be before the given date. The date is converted to a valid `DateTime` using `strtotime`. Similar to the [after](#rule-after) rule, you can pass another field name for comparison.

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

`beforeToday` and `todayOrBefore` can conveniently express "must be before today" or "must be today or earlier":

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

The field under validation must be before or equal to the given date. The date is converted to a valid `DateTime` using `strtotime`. Similar to the [after](#rule-after) rule, you can pass another field name for comparison.

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

The field under validation must have a size between the given _min_ and _max_ (inclusive). Strings, numbers, arrays, and files are evaluated using the same rules as [size](#rule-size).

<a name="rule-boolean"></a>
#### boolean

The field under validation must be convertible to a boolean value. Acceptable inputs include `true`, `false`, `1`, `0`, `"1"`, `"0"`.

You can use the `strict` parameter to allow only `true` or `false`:

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

The field under validation must have a matching field `{field}_confirmation`. For example, if the field is `password`, `password_confirmation` is required.

You can also specify a custom confirmation field name, such as `confirmed:repeat_username`, which requires `repeat_username` to match the current field.

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

The field under validation must be an array and must contain all the given parameter values. This rule is often used for array validation and can be constructed using `Rule::contains`:

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

The field under validation must be an array and must not contain any of the given parameter values. You can use `Rule::doesntContain` to construct:

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

The field under validation must match the current authenticated user's password. You can specify the authentication guard via the first parameter:

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> This rule depends on the authentication component and guard configuration; do not use it without integrating authentication.

<a name="rule-date"></a>
#### date

The field under validation must be a valid (non-relative) date recognized by `strtotime`.

<a name="rule-date-equals"></a>
#### date_equals:_date_

The field under validation must equal the given date. The date is converted to a valid `DateTime` using `strtotime`.

<a name="rule-date-format"></a>
#### date_format:_format_,...

The field under validation must match one of the given formats. Use either `date` or `date_format`. This rule supports all formats of PHP [DateTime](https://www.php.net/manual/en/class.datetime.php).

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

The field under validation must be a number with the specified number of decimal places:

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

The field under validation must be `"no"`, `"off"`, `0`, `"0"`, `false`, or `"false"`.

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

The field under validation must be `"no"`, `"off"`, `0`, `"0"`, `false`, or `"false"` when another field equals the specified value.

<a name="rule-different"></a>
#### different:_field_

The field under validation must be different from _field_.

<a name="rule-digits"></a>
#### digits:_value_

The field under validation must be an integer with a length of _value_.

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

The field under validation must be an integer with a length between _min_ and _max_.

<a name="rule-dimensions"></a>
#### dimensions

The field under validation must be an image and satisfy the dimension constraints:

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

Available constraints: _min_width_, _max_width_, _min_height_, _max_height_, _width_, _height_, _ratio_.

_ratio_ is the aspect ratio, which can be expressed as a fraction or float:

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

Due to the many parameters in this rule, it is recommended to use `Rule::dimensions` to construct:

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

When validating an array, the field values must not be duplicated:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

By default, loose comparison is used. For strict comparison, add `strict`:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

You can add `ignore_case` to ignore case differences:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

The field under validation must not start with the specified values.

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

The field under validation must not end with the specified values.

<a name="rule-email"></a>
#### email

The field under validation must be a valid email address. This rule relies on [egulias/email-validator](https://github.com/egulias/EmailValidator), defaulting to `RFCValidation`, but other validation methods can be specified:

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

Available validation methods:

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` - Validates email according to RFC standards ([supported RFCs](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs)).
- `strict`: `NoRFCWarningsValidation` - Fails on warnings during RFC validation (e.g., trailing dots or consecutive dots).
- `dns`: `DNSCheckValidation` - Checks if the domain has a valid MX record.
- `spoof`: `SpoofCheckValidation` - Prevents homoglyph or deceptive Unicode characters.
- `filter`: `FilterEmailValidation` - Validates using PHP `filter_var`.
- `filter_unicode`: `FilterEmailValidation::unicode()` - Unicode-allowed `filter_var` validation.

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

The field under validation must match the specified character encoding. This rule uses `mb_check_encoding` to detect the encoding of files or strings. It can be used with the file rule builder:

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

The field under validation must end with one of the specified values.

<a name="rule-enum"></a>
#### enum

`Enum` is a class-based rule used to validate if the field value is a valid enum value. Pass the enum class name during construction. For validating basic type values, use Backed Enum:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

You can use `only`/`except` to restrict enum values:

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

You can use `when` for conditional restrictions:

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

The field under validation will be excluded from the data returned by `validate`/`validated`.

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

The field under validation will be excluded from the data returned by `validate`/`validated` when _anotherfield_ equals _value_.

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

The field under validation will be excluded from the data returned by `validate`/`validated` unless _anotherfield_ equals _value_. If _value_ is `null` (e.g., `exclude_unless:name,null`), the field is retained only if the comparison field is `null` or does not exist.

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

The field under validation will be excluded from the data returned by `validate`/`validated` when _anotherfield_ exists.

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

The field under validation will be excluded from the data returned by `validate`/`validated` when _anotherfield_ does not exist.

<a name="rule-exists"></a>
#### exists:_table_,_column_

The field under validation must exist in the specified database table.

<a name="basic-usage-of-exists-rule"></a>
#### Basic Usage of Exists Rule

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

If `column` is not specified, the field name is used by default. Thus, this example validates if the `state` column exists in the `states` table.

<a name="specifying-a-custom-column-name"></a>
#### Specifying a Custom Column Name

You can append the column name after the table name:

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

To specify a database connection, prepend the connection name to the table:

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

You can also pass a model class name, and the framework will resolve the table name:

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

To customize query conditions, use the `Rule` rule builder:

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

When validating if a group of values exists, combine with the `array` rule:

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

When `array` and `exists` coexist, a single query is generated to validate all values.

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

The uploaded file's extension must be in the allowed list:

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> Do not rely solely on extension validation for file types; it is recommended to use it with [mimes](#rule-mimes) or [mimetypes](#rule-mimetypes).

<a name="rule-file"></a>
#### file

The field under validation must be a successfully uploaded file.

<a name="rule-filled"></a>
#### filled

When the field exists, its value must not be empty.

<a name="rule-gt"></a>
#### gt:_field_

The field under validation must be greater than the given _field_ or _value_. The two fields must be of the same type. Strings, numbers, arrays, and files are evaluated using the same rules as [size](#rule-size).

<a name="rule-gte"></a>
#### gte:_field_

The field under validation must be greater than or equal to the given _field_ or _value_. The two fields must be of the same type. Strings, numbers, arrays, and files are evaluated using the same rules as [size](#rule-size).

<a name="rule-hex-color"></a>
#### hex_color

The field under validation must be a valid [hexadecimal color value](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color).

<a name="rule-image"></a>
#### image

The field under validation must be an image (jpg, jpeg, png, bmp, gif, or webp).

> [!WARNING]
> SVG is not allowed by default due to XSS risks. To allow it, add `allow_svg`: `image:allow_svg`.

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

The field under validation must be in the given list of values. You can use `Rule::in` to construct:

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

When combined with the `array` rule, every value in the input array must be in the `in` list:

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

The field under validation must exist in the value list of _anotherfield_.

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

The field under validation must be an array and must contain at least one of the given values as a key:

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

The field under validation must be an integer.

You can use the `strict` parameter to require the field type to be integer; string representations of integers will be considered invalid:

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> This rule only checks if it passes PHP's `FILTER_VALIDATE_INT`; to enforce numeric types, use it with [numeric](#rule-numeric).

<a name="rule-ip"></a>
#### ip

The field under validation must be a valid IP address.

<a name="rule-ipv4"></a>
#### ipv4

The field under validation must be a valid IPv4 address.

<a name="rule-ipv6"></a>
#### ipv6

The field under validation must be a valid IPv6 address.

<a name="rule-json"></a>
#### json

The field under validation must be a valid JSON string.

<a name="rule-lt"></a>
#### lt:_field_

The field under validation must be less than the given _field_. The two fields must be of the same type. Strings, numbers, arrays, and files are evaluated using the same rules as [size](#rule-size).

<a name="rule-lte"></a>
#### lte:_field_

The field under validation must be less than or equal to the given _field_. The two fields must be of the same type. Strings, numbers, arrays, and files are evaluated using the same rules as [size](#rule-size).

<a name="rule-lowercase"></a>
#### lowercase

The field under validation must be lowercase.

<a name="rule-list"></a>
#### list

The field under validation must be a list array. The keys in a list array must be consecutive numbers from 0 to `count($array) - 1`.

<a name="rule-mac"></a>
#### mac_address

The field under validation must be a valid MAC address.

<a name="rule-max"></a>
#### max:_value_

The field under validation must be less than or equal to _value_. Strings, numbers, arrays, and files are evaluated using the same rules as [size](#rule-size).

<a name="rule-max-digits"></a>
#### max_digits:_value_

The field under validation must be an integer with a length not exceeding _value_.

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

The file's MIME type must be in the list:

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

The MIME type is guessed by reading the file content, which may differ from the client-provided MIME.

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

The file's MIME type must correspond to the given extension:

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

Although the parameters are extensions, this rule reads the file content to determine the MIME. The extension-to-MIME mapping is from:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### MIME Types and Extensions

This rule does not validate if the "filename extension" matches the "actual MIME". For example, `mimes:png` will consider `photo.txt` with PNG content as valid. To validate extensions, use [extensions](#rule-extensions).

<a name="rule-min"></a>
#### min:_value_

The field under validation must be greater than or equal to _value_. Strings, numbers, arrays, and files are evaluated using the same rules as [size](#rule-size).

<a name="rule-min-digits"></a>
#### min_digits:_value_

The field under validation must be an integer with a length of at least _value_.

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

The field under validation must be a multiple of _value_.

<a name="rule-missing"></a>
#### missing

The field under validation must not exist in the input data.

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

The field under validation must not exist when _anotherfield_ equals any _value_.

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

The field under validation must not exist unless _anotherfield_ equals any _value_.

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

The field under validation must not exist when any of the specified fields exist.

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

The field under validation must not exist when all specified fields exist.

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

The field under validation must not be in the given list of values. You can use `Rule::notIn` to construct:

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

The field under validation must not match the given regular expression.

This rule uses PHP `preg_match`. The regex must include delimiters, e.g., `'email' => 'not_regex:/^.+$/i'`.

> [!WARNING]
> When using `regex` / `not_regex`, if the regex contains `|`, it is recommended to declare rules in array form to avoid conflicts with the `|` separator.

<a name="rule-nullable"></a>
#### nullable

The field under validation may be `null`.

<a name="rule-numeric"></a>
#### numeric

The field under validation must be [numeric](https://www.php.net/manual/en/function.is-numeric.php).

You can use the `strict` parameter to allow only integer or float types; numeric strings will be considered invalid:

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

The field under validation must exist in the input data.

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

The field under validation must exist when _anotherfield_ equals any _value_.

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

The field under validation must exist unless _anotherfield_ equals any _value_.

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

The field under validation must exist when any of the specified fields exist.

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

The field under validation must exist when all specified fields exist.

<a name="rule-prohibited"></a>
#### prohibited

The field under validation must be missing or empty. A field is "empty" if:

<div class="content-list" markdown="1">

- The value is `null`.
- The value is an empty string.
- The value is an empty array or empty `Countable` object.
- It is an uploaded file with an empty path.

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

The field under validation must be missing or empty when _anotherfield_ equals any _value_. A field is "empty" if:

<div class="content-list" markdown="1">

- The value is `null`.
- The value is an empty string.
- The value is an empty array or empty `Countable` object.
- It is an uploaded file with an empty path.

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

The field under validation must be missing or empty when _anotherfield_ is `"yes"`, `"on"`, `1`, `"1"`, `true`, or `"true"`.

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

The field under validation must be missing or empty when _anotherfield_ is `"no"`, `"off"`, `0`, `"0"`, `false`, or `"false"`.

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

The field under validation must be missing or empty unless _anotherfield_ equals any _value_. A field is "empty" if:

<div class="content-list" markdown="1">

- The value is `null`.
- The value is an empty string.
- The value is an empty array or empty `Countable` object.
- It is an uploaded file with an empty path.

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

When the field under validation exists and is not empty, all fields in _anotherfield_ must be missing or empty. A field is "empty" if:

<div class="content-list" markdown="1">

- The value is `null`.
- The value is an empty string.
- The value is an empty array or empty `Countable` object.
- It is an uploaded file with an empty path.

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

The field under validation must match the given regular expression.

This rule uses PHP `preg_match`. The regex must include delimiters, e.g., `'email' => 'regex:/^.+@.+$/i'`.

> [!WARNING]
> When using `regex` / `not_regex`, if the regex contains `|`, it is recommended to declare rules in array form to avoid conflicts with the `|` separator.

<a name="rule-required"></a>
#### required

The field under validation must exist and not be empty. A field is "empty" if:

<div class="content-list" markdown="1">

- The value is `null`.
- The value is an empty string.
- The value is an empty array or empty `Countable` object.
- It is an uploaded file with an empty path.

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

The field under validation must exist and not be empty when _anotherfield_ equals any _value_.

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

The field under validation must exist and not be empty when _anotherfield_ is `"yes"`, `"on"`, `1`, `"1"`, `true`, or `"true"`.

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

The field under validation must exist and not be empty when _anotherfield_ is `"no"`, `"off"`, `0`, `"0"`, `false`, or `"false"`.

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

The field under validation must exist and not be empty unless _anotherfield_ equals any _value_. If _value_ is `null` (e.g., `required_unless:name,null`), the field is allowed to be empty only if the comparison field is `null` or does not exist.

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

The field under validation must exist and not be empty when any specified field exists and is not empty.

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

The field under validation must exist and not be empty when all specified fields exist and are not empty.

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

The field under validation must exist and not be empty when any specified field is empty or does not exist.

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

The field under validation must exist and not be empty when all specified fields are empty or do not exist.

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

The field under validation must be an array and must contain at least the specified keys.

<a name="validating-when-present"></a>
#### sometimes

Apply subsequent validation rules only when the field exists. Commonly used for fields that are "optional but must be valid if present":

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

The field under validation must be the same as _field_.

<a name="rule-size"></a>
#### size:_value_

The field under validation must have a size equal to the given _value_. For strings, it is the character count; for numbers, it is the specified integer (use with `numeric` or `integer`); for arrays, it is the element count; for files, it is the size in KB. Example:

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

The field under validation must start with one of the specified values.

<a name="rule-string"></a>
#### string

The field under validation must be a string. To allow `null`, use with `nullable`.

<a name="rule-timezone"></a>
#### timezone

The field under validation must be a valid timezone identifier (from `DateTimeZone::listIdentifiers`). Parameters supported by this method can be passed:

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

The field under validation must be unique in the specified table.

**Specifying Custom Table/Column Names:**

You can directly specify the model class name:

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

You can specify the column name (defaults to the field name if not specified):

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**Specifying Database Connection:**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**Ignoring a Specific ID:**

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
> `ignore` should not receive user input; only use system-generated unique IDs (auto-increment IDs or model UUIDs), otherwise there may be SQL injection risks.

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

By default, the field name is used as the unique column, but you can specify the column name:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**Adding Extra Conditions:**

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

**Ignoring Soft-Deleted Records:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

If the soft-delete column name is not `deleted_at`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

The field under validation must be uppercase.

<a name="rule-url"></a>
#### url

The field under validation must be a valid URL.

You can specify allowed protocols:

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

The field under validation must be a valid [ULID](https://github.com/ulid/spec).

<a name="rule-uuid"></a>
#### uuid

The field under validation must be a valid RFC 9562 UUID (versions 1, 3, 4, 5, 6, 7, or 8).

You can specify the version:

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```






<a name="think-validate"></a>
## Validator top-think/think-validate

### Description
Official validator for ThinkPHP

### Project URL
https://github.com/top-think/think-validate

### Installation
`composer require topthink/think-validate`

### Quick Start

**Create `app/index/validate/User.php`**

```php
<?php
namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'name' => 'require|max:25',
        'age' => 'number|between:1,120',
        'email' => 'email',
    ];

    protected $message = [
        'name.require' => 'Name is required',
        'name.max' => 'Name cannot exceed 25 characters',
        'age.number' => 'Age must be a number',
        'age.between' => 'Age must be between 1 and 120',
        'email' => 'Invalid email',
    ];

}
```
  
**Usage**
```php
$data = [
    'name' => 'thinkphp',
    'email' => 'thinkphp@qq.com',
];

$validate = new \app\index\validate\User;

if (!$validate->check($data)) {
    var_dump($validate->getError());
}
```

<a name="respect-validation"></a>
# Validator workerman/validation

### Description

This project is a Chinese version of https://github.com/Respect/Validation

### Project URL

https://github.com/walkor/validation
  
  
### Installation
 
```php
composer require workerman/validation
```

### Quick Start

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
  
**Access via jquery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tomcat', password: '123456'}
  });
  ```
  
Result:

`{"code":500,"msg":"Username can only contain letters (a-z) and numbers (0-9)"}`

Explanation:

`v::input(array $input, array $rules)` is used to validate and collect data. If the data validation fails, it throws a `Respect\Validation\Exceptions\ValidationException` exception; if successful, it returns the validated data (array).

If the validation exception is not caught by the business code, the webman framework will automatically catch it and return json data (similar to `{"code":500, "msg":"xxx"}`) or a normal exception page based on the HTTP request header. If the returned format does not meet the business requirements, developers can catch the `ValidationException` exception themselves and return the required data, similar to the example below:

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

### Validator Function Guide

```php
use Respect\Validation\Validator as v;

// Single rule validation
$number = 123;
v::numericVal()->validate($number); // true

// Multiple rules chained validation
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Get the first validation failure reason
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username can only contain letters (a-z) and numbers (0-9)
}

// Get all validation failure reasons
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Will print
    // -  Username must follow these rules
    //    - Username can only contain letters (a-z) and numbers (0-9)
    //    - Username cannot contain spaces
  
    var_export($exception->getMessages());
    // Will print
    // array (
    //   'alnum' => 'Username can only contain letters (a-z) and numbers (0-9)',
    //   'noWhitespace' => 'Username cannot contain spaces',
    // )
}

// Custom error messages
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Username can only contain letters and numbers',
        'noWhitespace' => 'Username cannot have spaces',
        'length' => 'This message won't show, as it satisfies the length rule'
    ]);
    // Will print 
    // array(
    //    'alnum' => 'Username can only contain letters and numbers',
    //    'noWhitespace' => 'Username cannot have spaces'
    // )
}

// Object validation
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Array validation
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
    ->assert($data); // Also can use check() or validate()
  
// Optional validation
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Negated rule
v::not(v::intVal())->validate(10); // false
```
  
### Differences between the `validate()`, `check()`, and `assert()` methods

`validate()` returns a boolean and does not throw an exception

`check()` throws an exception when the validation fails, with the first validation failure reason accessible via `$exception->getMessage()`

`assert()` throws an exception when the validation fails, with all validation failure reasons accessible via `$exception->getFullMessage()`
  
  
### List of Common Validation Rules

`Alnum()` - Only contains letters and numbers

`Alpha()` - Only contains letters

`ArrayType()` - Array type

`Between(mixed $minimum, mixed $maximum)` - Validates whether the input is between two other values.

`BoolType()` - Validates whether it is a Boolean

`Contains(mixed $expectedValue)` - Validates whether the input contains certain values

`ContainsAny(array $needles)` - Validates whether the input contains at least one of the defined values

`Digit()` - Validates whether the input only contains numbers

`Domain()` - Validates whether it is a valid domain name

`Email()` - Validates whether it is a valid email address

`Extension(string $extension)` - Validates the file extension

`FloatType()` - Validates whether it is a floating point number

`IntType()` - Validates whether it is an integer

`Ip()` - Validates whether it is an IP address

`Json()` - Validates whether it is JSON data

`Length(int $min, int $max)` - Validates whether the length is within a given range

`LessThan(mixed $compareTo)` - Validates whether the length is less than a given value

`Lowercase()` - Validates whether it is in lowercase

`MacAddress()` - Validates whether it is a MAC address

`NotEmpty()` - Validates whether it is not empty

`NullType()` - Validates whether it is null

`Number()` - Validates whether it is a number

`ObjectType()` - Validates whether it is an object

`StringType()` - Validates whether it is a string

`Url()` - Validates whether it is a URL
  
For more validation rules, please refer to https://respect-validation.readthedocs.io/en/2.0/list-of-rules/
  
### More Information

Please visit https://respect-validation.readthedocs.io/en/2.0/
