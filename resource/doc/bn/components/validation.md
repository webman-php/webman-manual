# অন্যান্য ভ্যালিডেটর

কম্পোজারে সরাসরি ব্যবহারযোগ্য অনেক ভ্যালিডেটর উপলব্ধ, যেমন:

#### <a href="#webman-validation"> webman/validation (প্রস্তাবিত)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# ভ্যালিডেটর webman/validation

`illuminate/validation` ভিত্তিক, এটি ম্যানুয়াল ভ্যালিডেশন, অ্যানোটেশন ভ্যালিডেশন, প্যারামিটার-লেভেল ভ্যালিডেশন এবং পুনঃব্যবহারযোগ্য রুল সেট প্রদান করে।

## ইনস্টলেশন

```bash
composer require webman/validation
```

## মৌলিক ধারণা

- **রুল সেট পুনঃব্যবহার**: `support\validation\Validator` এক্সটেন্ড করে পুনঃব্যবহারযোগ্য `rules`, `messages`, `attributes` এবং `scenes` সংজ্ঞায়িত করুন, যা ম্যানুয়াল এবং অ্যানোটেশন উভয় ভ্যালিডেশনে পুনঃব্যবহার করা যায়।
- **মেথড-লেভেল অ্যানোটেশন (অ্যাট্রিবিউট) ভ্যালিডেশন**: PHP 8 অ্যাট্রিবিউট `#[Validate]` ব্যবহার করে ভ্যালিডেশন কন্ট্রোলার মেথডে বাইন্ড করুন।
- **প্যারামিটার-লেভেল অ্যানোটেশন (অ্যাট্রিবিউট) ভ্যালিডেশন**: PHP 8 অ্যাট্রিবিউট `#[Param]` ব্যবহার করে ভ্যালিডেশন কন্ট্রোলার মেথড প্যারামিটারে বাইন্ড করুন।
- **এক্সেপশন হ্যান্ডলিং**: ভ্যালিডেশন ব্যর্থ হলে `support\validation\ValidationException` নিক্ষেপ করে; এক্সেপশন ক্লাস কনফিগারযোগ্য।
- **ডাটাবেস ভ্যালিডেশন**: ডাটাবেস ভ্যালিডেশন জড়িত থাকলে, `composer require webman/database` ইনস্টল করতে হবে।

## ম্যানুয়াল ভ্যালিডেশন

### মৌলিক ব্যবহার

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **নোট**
> `validate()` ভ্যালিডেশন ব্যর্থ হলে `support\validation\ValidationException` নিক্ষেপ করে। এক্সেপশন নিক্ষেপ না করতে চাইলে, নিচের `fails()` পদ্ধতি ব্যবহার করে এরর মেসেজ পান।

### কাস্টম মেসেজ এবং অ্যাট্রিবিউট

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

### এক্সেপশন ছাড়া ভ্যালিডেট করুন (এরর মেসেজ পান)

এক্সেপশন নিক্ষেপ না করতে চাইলে, `fails()` দিয়ে চেক করুন এবং `errors()` (রিটার্ন `MessageBag`) দিয়ে এরর মেসেজ পান:

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

## রুল সেট পুনঃব্যবহার (কাস্টম ভ্যালিডেটর)

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

### ম্যানুয়াল ভ্যালিডেশন পুনঃব্যবহার

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### দৃশ্য ব্যবহার করুন (ঐচ্ছিক)

`scenes` একটি ঐচ্ছিক বৈশিষ্ট্য; `withScene(...)` কল করলে শুধুমাত্র ফিল্ডের একটি উপসেট ভ্যালিডেট হয়।

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

## অ্যানোটেশন ভ্যালিডেশন (মেথড-লেভেল)

### সরাসরি রুল

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

### রুল সেট পুনঃব্যবহার

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

### একাধিক ভ্যালিডেশন ওভারলে

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

### ভ্যালিডেশন ডেটা সোর্স

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

`in` প্যারামিটার দিয়ে ডেটা সোর্স নির্দিষ্ট করুন:

* **query** HTTP রিকোয়েস্ট কুয়েরি প্যারামিটার, `$request->get()` থেকে
* **body** HTTP রিকোয়েস্ট বডি, `$request->post()` থেকে
* **path** HTTP রিকোয়েস্ট পাথ প্যারামিটার, `$request->route->param()` থেকে

`in` স্ট্রিং বা অ্যারে হতে পারে; অ্যারে হলে মান ক্রমানুসারে মার্জ হয় এবং পরবর্তী মান পূর্ববর্তীকে ওভাররাইড করে। `in` পাস না করলে ডিফল্ট `['query', 'body', 'path']`।


## প্যারামিটার-লেভেল ভ্যালিডেশন (Param)

### মৌলিক ব্যবহার

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

### ভ্যালিডেশন ডেটা সোর্স

একইভাবে, প্যারামিটার-লেভেল ভ্যালিডেশনও সোর্স নির্দিষ্ট করতে `in` প্যারামিটার সমর্থন করে:

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


### rules স্ট্রিং বা অ্যারে সমর্থন করে

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

### কাস্টম মেসেজ / অ্যাট্রিবিউট

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

### রুল কনস্ট্যান্ট পুনঃব্যবহার

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

## মেথড-লেভেল + প্যারামিটার-লেভেল সংযুক্ত

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

## স্বয়ংক্রিয় রুল ইনফারেন্স (প্যারামিটার সিগনেচার ভিত্তিক)

যখন একটি মেথডে `#[Validate]` ব্যবহার করা হয়, অথবা সেই মেথডের কোনো প্যারামিটার `#[Param]` ব্যবহার করে, এই কম্পোনেন্ট **মেথড প্যারামিটার সিগনেচার থেকে মৌলিক ভ্যালিডেশন রুল স্বয়ংক্রিয়ভাবে অনুমান এবং সম্পূর্ণ করে**, তারপর ভ্যালিডেশনের আগে বিদ্যমান রুলের সাথে মার্জ করে।

### উদাহরণ: `#[Validate]` সমতুল্য সম্প্রসারণ

1) শুধুমাত্র `#[Validate]` সক্রিয় করুন, ম্যানুয়ালি রুল লিখবেন না:

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

সমতুল্য:

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

2) শুধুমাত্র আংশিক রুল লিখা, বাকি প্যারামিটার সিগনেচার দ্বারা সম্পূর্ণ:

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

সমতুল্য:

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

3) ডিফল্ট মান / nullable টাইপ:

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

সমতুল্য:

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

## এক্সেপশন হ্যান্ডলিং

### ডিফল্ট এক্সেপশন

ভ্যালিডেশন ব্যর্থ হলে ডিফল্টভাবে `support\validation\ValidationException` নিক্ষেপ করে, যা `Webman\Exception\BusinessException` এক্সটেন্ড করে এবং এরর লগ করে না।

ডিফল্ট রেসপন্স আচরণ `BusinessException::render()` দ্বারা হ্যান্ডেল হয়:

- নিয়মিত রিকোয়েস্ট: স্ট্রিং মেসেজ রিটার্ন করে, যেমন `token is required.`
- JSON রিকোয়েস্ট: JSON রেসপন্স রিটার্ন করে, যেমন `{"code": 422, "msg": "token is required.", "data":....}`

### কাস্টম এক্সেপশন দিয়ে হ্যান্ডলিং কাস্টমাইজ করুন

- গ্লোবাল কনফিগ: `config/plugin/webman/validation/app.php` এ `exception`

## মাল্টিলিঙ্গুয়াল সাপোর্ট

কম্পোনেন্টে চীনা এবং ইংরেজি ভাষা প্যাক অন্তর্নির্মিত রয়েছে এবং প্রজেক্ট ওভাররাইড সমর্থন করে। লোড ক্রম:

1. প্রজেক্ট ভাষা প্যাক `resource/translations/{locale}/validation.php`
2. কম্পোনেন্ট অন্তর্নির্মিত `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Illuminate অন্তর্নির্মিত ইংরেজি (ফলব্যাক)

> **নোট**
> Webman ডিফল্ট ভাষা `config/translation.php` এ কনফিগার করা হয়, অথবা `locale('en');` দিয়ে পরিবর্তন করা যায়।

### লোকাল ওভাররাইড উদাহরণ

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute is not a valid email format.',
];
```

## মিডলওয়্যার অটো-লোডিং

ইনস্টলেশনের পর, কম্পোনেন্ট `config/plugin/webman/validation/middleware.php` এর মাধ্যমে ভ্যালিডেশন মিডলওয়্যার অটো-লোড করে; ম্যানুয়াল রেজিস্ট্রেশন প্রয়োজন নেই।

## কমান্ড-লাইন জেনারেশন

`make:validator` কমান্ড ব্যবহার করে ভ্যালিডেটর ক্লাস জেনারেট করুন (ডিফল্ট আউটপুট `app/validation` ডিরেক্টরিতে)।

> **নোট**
> `composer require webman/console` প্রয়োজন

### মৌলিক ব্যবহার

- **খালি টেমপ্লেট জেনারেট করুন**

```bash
php webman make:validator UserValidator
```

- **বিদ্যমান ফাইল ওভাররাইট করুন**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### টেবিল স্ট্রাকচার থেকে রুল জেনারেট করুন

- **বেস রুল জেনারেট করতে টেবিল নাম নির্দিষ্ট করুন** (ফিল্ড টাইপ/nullable/length ইত্যাদি থেকে `$rules` অনুমান করে; ডিফল্টে ORM-সম্পর্কিত ফিল্ড বাদ: laravel `created_at/updated_at/deleted_at` ব্যবহার করে, thinkorm `create_time/update_time/delete_time` ব্যবহার করে)

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **ডাটাবেস কানেকশন নির্দিষ্ট করুন** (মাল্টি-কানেকশন সিনারিও)

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### দৃশ্য

- **CRUD দৃশ্য জেনারেট করুন**: `create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> `update` দৃশ্যে প্রাইমারি কী ফিল্ড (রেকর্ড খুঁজে পেতে) প্লাস অন্যান্য ফিল্ড অন্তর্ভুক্ত; `delete/detail` ডিফল্টে শুধুমাত্র প্রাইমারি কী অন্তর্ভুক্ত।

### ORM নির্বাচন (laravel (illuminate/database) বনাম think-orm)

- **অটো-সিলেক্ট (ডিফল্ট)**: যা ইনস্টল/কনফিগার করা আছে তা ব্যবহার করে; উভয় থাকলে ডিফল্টে illuminate ব্যবহার করে
- **জোর করে নির্দিষ্ট করুন**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### সম্পূর্ণ উদাহরণ

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## ইউনিট টেস্ট

`webman/validation` রুট ডিরেক্টরি থেকে চালান:

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## ভ্যালিডেশন রুল রেফারেন্স

<a name="available-validation-rules"></a>
## উপলব্ধ ভ্যালিডেশন রুল

> [!IMPORTANT]
> - Webman Validation `illuminate/validation` ভিত্তিক; রুল নাম Laravel এর সাথে মিলে এবং Webman-নির্দিষ্ট রুল নেই।
> - মিডলওয়্যার ডিফল্টে `$request->all()` (GET+POST) মার্জ রুট প্যারামিটারের সাথে ডেটা ভ্যালিডেট করে, আপলোডেড ফাইল বাদ; ফাইল রুলের জন্য, নিজে `$request->file()` ডেটায় মার্জ করুন, অথবা ম্যানুয়ালি `Validator::make` কল করুন।
> - `current_password` auth guard এর উপর নির্ভর করে; `exists`/`unique` ডাটাবেস কানেকশন এবং কুয়েরি বিল্ডারের উপর নির্ভর করে; সংশ্লিষ্ট কম্পোনেন্ট ইন্টিগ্রেট না থাকলে এই রুলগুলো উপলব্ধ নয়।

নিচে সমস্ত উপলব্ধ ভ্যালিডেশন রুল এবং তাদের উদ্দেশ্য তালিকাভুক্ত:

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

ফিল্ডটি অবশ্যই `"yes"`, `"on"`, `1`, `"1"`, `true`, অথবা `"true"` হতে হবে। পরিষেবার শর্তাদিতে ব্যবহারকারীর সম্মতি যাচাইয়ের মতো সিনারিওতে সাধারণভাবে ব্যবহৃত।

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

অন্য ফিল্ড নির্দিষ্ট মানের সমান হলে, ফিল্ডটি অবশ্যই `"yes"`, `"on"`, `1`, `"1"`, `true`, অথবা `"true"` হতে হবে। শর্তাধীন সম্মতি সিনারিওতে সাধারণভাবে ব্যবহৃত।

<a name="rule-active-url"></a>
#### active_url

ফিল্ডে অবশ্যই বৈধ A বা AAAA রেকর্ড থাকতে হবে। এই রুল প্রথমে URL হোস্টনেম এক্সট্রাক্ট করতে `parse_url` ব্যবহার করে, তারপর `dns_get_record` দিয়ে ভ্যালিডেট করে।

<a name="rule-after"></a>
#### after:_date_

ফিল্ডটি প্রদত্ত তারিখের পরে একটি মান হতে হবে। তারিখ বৈধ `DateTime` এ রূপান্তর করতে `strtotime` এ পাস করা হয়:

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

তুলনার জন্য অন্য ফিল্ড নামও পাস করতে পারেন:

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

ফ্লুয়েন্ট `date` রুল বিল্ডার ব্যবহার করতে পারেন:

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

`afterToday` এবং `todayOrAfter` সুবিধাজনকভাবে "আজকের পরে হতে হবে" বা "আজ অথবা তার পরে হতে হবে" প্রকাশ করে:

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

ফিল্ডটি প্রদত্ত তারিখে অথবা তার পরে হতে হবে। আরও বিস্তারিত জানতে [after](#rule-after) দেখুন।

ফ্লুয়েন্ট `date` রুল বিল্ডার ব্যবহার করতে পারেন:

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

`Rule::anyOf` "যেকোনো একটি রুল সেট সন্তুষ্ট করুন" নির্দিষ্ট করতে দেয়। উদাহরণস্বরূপ, নিচের রুলের অর্থ `username` হয় ইমেইল অ্যাড্রেস অথবা কমপক্ষে ৬ অক্ষরের আলফানিউমেরিক/আন্ডারস্কোর/ড্যাশ স্ট্রিং হতে হবে:

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

ফিল্ডটি অবশ্যই ইউনিকোড অক্ষর হতে হবে ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) এবং [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=))।

শুধুমাত্র ASCII (`a-z`, `A-Z`) অনুমোদন করতে `ascii` অপশন যোগ করুন:

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

ফিল্ডে শুধুমাত্র ইউনিকোড অক্ষর এবং সংখ্যা থাকতে পারে ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)), প্লাস ASCII হাইফেন (`-`) এবং আন্ডারস্কোর (`_`)।

শুধুমাত্র ASCII (`a-z`, `A-Z`, `0-9`) অনুমোদন করতে `ascii` অপশন যোগ করুন:

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

ফিল্ডে শুধুমাত্র ইউনিকোড অক্ষর এবং সংখ্যা থাকতে পারে ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=))।

শুধুমাত্র ASCII (`a-z`, `A-Z`, `0-9`) অনুমোদন করতে `ascii` অপশন যোগ করুন:

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

ফিল্ডটি অবশ্যই PHP `array` হতে হবে।

`array` রুলে অতিরিক্ত প্যারামিটার থাকলে, ইনপুট অ্যারে কীগুলো প্যারামিটার তালিকায় থাকতে হবে। উদাহরণে `admin` কী অনুমোদিত তালিকায় নেই, তাই অবৈধ:

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

প্রকৃত প্রজেক্টে অনুমোদিত অ্যারে কী স্পষ্টভাবে সংজ্ঞায়িত করার পরামর্শ দেওয়া হয়।

<a name="rule-ascii"></a>
#### ascii

ফিল্ডে শুধুমাত্র ৭-বিট ASCII অক্ষর থাকতে পারে।

<a name="rule-bail"></a>
#### bail

প্রথম রুল ব্যর্থ হলে ফিল্ডের পরবর্তী রুল ভ্যালিডেশন বন্ধ করুন।

এই রুল শুধুমাত্র বর্তমান ফিল্ডকে প্রভাবিত করে। "গ্লোবালি প্রথম ব্যর্থতায় থামুন" এর জন্য সরাসরি Illuminate এর ভ্যালিডেটর ব্যবহার করুন এবং `stopOnFirstFailure()` কল করুন।

<a name="rule-before"></a>
#### before:_date_

ফিল্ডটি প্রদত্ত তারিখের আগে হতে হবে। তারিখ বৈধ `DateTime` এ রূপান্তর করতে `strtotime` এ পাস করা হয়। [after](#rule-after) এর মতো, তুলনার জন্য অন্য ফিল্ড নাম পাস করতে পারেন।

ফ্লুয়েন্ট `date` রুল বিল্ডার ব্যবহার করতে পারেন:

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

`beforeToday` এবং `todayOrBefore` সুবিধাজনকভাবে "আজকের আগে হতে হবে" বা "আজ অথবা তার আগে হতে হবে" প্রকাশ করে:

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

ফিল্ডটি প্রদত্ত তারিখে অথবা তার আগে হতে হবে। তারিখ বৈধ `DateTime` এ রূপান্তর করতে `strtotime` এ পাস করা হয়। [after](#rule-after) এর মতো, তুলনার জন্য অন্য ফিল্ড নাম পাস করতে পারেন।

ফ্লুয়েন্ট `date` রুল বিল্ডার ব্যবহার করতে পারেন:

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

ফিল্ড সাইজ _min_ এবং _max_ এর মধ্যে (সমেত) হতে হবে। স্ট্রিং, সংখ্যা, অ্যারে এবং ফাইলের জন্য মূল্যায়ন [size](#rule-size) এর মতো।

<a name="rule-boolean"></a>
#### boolean

ফিল্ডটি বুলিয়ানে রূপান্তরযোগ্য হতে হবে। গ্রহণযোগ্য ইনপুটের মধ্যে রয়েছে `true`, `false`, `1`, `0`, `"1"`, `"0"`।

শুধুমাত্র `true` বা `false` অনুমোদন করতে `strict` প্যারামিটার ব্যবহার করুন:

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

ফিল্ডে অবশ্যই মিলে যাওয়া `{field}_confirmation` ফিল্ড থাকতে হবে। উদাহরণস্বরূপ, ফিল্ড `password` হলে `password_confirmation` প্রয়োজন।

কাস্টম কনফার্মেশন ফিল্ড নামও নির্দিষ্ট করতে পারেন, যেমন `confirmed:repeat_username` বর্তমান ফিল্ডের সাথে মিলের জন্য `repeat_username` প্রয়োজন।

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

ফিল্ডটি অবশ্যই অ্যারে হতে হবে এবং প্রদত্ত প্যারামিটার মানগুলো সব থাকতে হবে। এই রুল অ্যারে ভ্যালিডেশনে সাধারণভাবে ব্যবহৃত; এটি তৈরি করতে `Rule::contains` ব্যবহার করতে পারেন:

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

ফিল্ডটি অবশ্যই অ্যারে হতে হবে এবং প্রদত্ত প্যারামিটার মানগুলোর কোনোটি থাকতে পারবে না। এটি তৈরি করতে `Rule::doesntContain` ব্যবহার করতে পারেন:

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

ফিল্ডটি বর্তমান প্রমাণীকৃত ব্যবহারকারীর পাসওয়ার্ডের সাথে মিলতে হবে। প্রথম প্যারামিটার হিসেবে auth guard নির্দিষ্ট করতে পারেন:

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> এই রুল auth কম্পোনেন্ট এবং guard কনফিগারেশনের উপর নির্ভর করে; auth ইন্টিগ্রেট না থাকলে ব্যবহার করবেন না।

<a name="rule-date"></a>
#### date

ফিল্ডটি `strtotime` দ্বারা সনাক্তযোগ্য বৈধ (নন-রিলেটিভ) তারিখ হতে হবে।

<a name="rule-date-equals"></a>
#### date_equals:_date_

ফিল্ডটি প্রদত্ত তারিখের সমান হতে হবে। তারিখ বৈধ `DateTime` এ রূপান্তর করতে `strtotime` এ পাস করা হয়।

<a name="rule-date-format"></a>
#### date_format:_format_,...

ফিল্ডটি প্রদত্ত ফরম্যাটগুলোর একটি মিলতে হবে। `date` অথবা `date_format` ব্যবহার করুন। এই রুল সমস্ত PHP [DateTime](https://www.php.net/manual/en/class.datetime.php) ফরম্যাট সমর্থন করে।

ফ্লুয়েন্ট `date` রুল বিল্ডার ব্যবহার করতে পারেন:

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

ফিল্ডটি প্রয়োজনীয় দশমিক স্থান সহ সংখ্যাগত হতে হবে:

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

ফিল্ডটি অবশ্যই `"no"`, `"off"`, `0`, `"0"`, `false`, অথবা `"false"` হতে হবে।

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

অন্য ফিল্ড নির্দিষ্ট মানের সমান হলে, ফিল্ডটি অবশ্যই `"no"`, `"off"`, `0`, `"0"`, `false`, অথবা `"false"` হতে হবে।

<a name="rule-different"></a>
#### different:_field_

ফিল্ডটি _field_ থেকে ভিন্ন হতে হবে।

<a name="rule-digits"></a>
#### digits:_value_

ফিল্ডটি দৈর্ঘ্য _value_ সহ পূর্ণসংখ্যা হতে হবে।

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

ফিল্ডটি _min_ এবং _max_ এর মধ্যে দৈর্ঘ্য সহ পূর্ণসংখ্যা হতে হবে।

<a name="rule-dimensions"></a>
#### dimensions

ফিল্ডটি অবশ্যই ইমেজ হতে হবে এবং মাত্রা সীমাবদ্ধতা সন্তুষ্ট করতে হবে:

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

উপলব্ধ সীমাবদ্ধতা: _min\_width_, _max\_width_, _min\_height_, _max\_height_, _width_, _height_, _ratio_।

_ratio_ অ্যাসপেক্ট রেশিও; ভগ্নাংশ বা ফ্লোট হিসাবে প্রকাশ করা যায়:

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

এই রুলে অনেক প্যারামিটার আছে; এটি তৈরি করতে `Rule::dimensions` ব্যবহার করার পরামর্শ দেওয়া হয়:

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

অ্যারে ভ্যালিডেট করার সময়, ফিল্ড মান ডুপ্লিকেট হতে পারবে না:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

ডিফল্টে loose তুলনা ব্যবহার করে। strict তুলনার জন্য `strict` যোগ করুন:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

কেস পার্থক্য উপেক্ষা করতে `ignore_case` যোগ করুন:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

ফিল্ডটি নির্দিষ্ট মানগুলোর কোনো দিয়ে শুরু হতে পারবে না।

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

ফিল্ডটি নির্দিষ্ট মানগুলোর কোনো দিয়ে শেষ হতে পারবে না।

<a name="rule-email"></a>
#### email

ফিল্ডটি অবশ্যই বৈধ ইমেইল অ্যাড্রেস হতে হবে। এই রুল [egulias/email-validator](https://github.com/egulias/EmailValidator) এর উপর নির্ভর করে, ডিফল্টে `RFCValidation` ব্যবহার করে, এবং অন্যান্য ভ্যালিডেশন পদ্ধতি ব্যবহার করতে পারে:

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

উপলব্ধ ভ্যালিডেশন পদ্ধতি:

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` - RFC স্পেস অনুযায়ী ইমেইল ভ্যালিডেট করুন ([supported RFCs](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs))।
- `strict`: `NoRFCWarningsValidation` - RFC সতর্কতায় ব্যর্থ (যেমন ট্রেইলিং ডট বা ধারাবাহিক ডট)।
- `dns`: `DNSCheckValidation` - ডোমেনে বৈধ MX রেকর্ড আছে কিনা চেক করুন।
- `spoof`: `SpoofCheckValidation` - হোমোগ্রাফ বা স্পুফিং ইউনিকোড অক্ষর প্রতিরোধ করুন।
- `filter`: `FilterEmailValidation` - PHP `filter_var` ব্যবহার করে ভ্যালিডেট করুন।
- `filter_unicode`: `FilterEmailValidation::unicode()` - ইউনিকোড অনুমোদনকারী `filter_var` ভ্যালিডেশন।

</div>

ফ্লুয়েন্ট রুল বিল্ডার ব্যবহার করতে পারেন:

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
> `dns` এবং `spoof` এর জন্য PHP `intl` এক্সটেনশন প্রয়োজন।

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

ফিল্ডটি নির্দিষ্ট ক্যারেক্টার এনকোডিংয়ের সাথে মিলতে হবে। এই রুল ফাইল বা স্ট্রিং এনকোডিং সনাক্ত করতে `mb_check_encoding` ব্যবহার করে। ফাইল রুল বিল্ডারের সাথে ব্যবহার করা যায়:

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

ফিল্ডটি নির্দিষ্ট মানগুলোর একটি দিয়ে শেষ হতে হবে।

<a name="rule-enum"></a>
#### enum

`Enum` ফিল্ড মান বৈধ enum মান কিনা ভ্যালিডেট করার জন্য ক্লাস-ভিত্তিক রুল। তৈরি করার সময় enum ক্লাস নাম পাস করুন। প্রিমিটিভ মানের জন্য Backed Enum ব্যবহার করুন:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

enum মান সীমাবদ্ধ করতে `only`/`except` ব্যবহার করুন:

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

শর্তাধীন সীমাবদ্ধতার জন্য `when` ব্যবহার করুন:

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

ফিল্ড `validate`/`validated` দ্বারা রিটার্ন করা ডেটা থেকে বাদ দেওয়া হবে।

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

_anotherfield_ _value_ এর সমান হলে, ফিল্ড `validate`/`validated` দ্বারা রিটার্ন করা ডেটা থেকে বাদ দেওয়া হবে।

জটিল শর্তের জন্য `Rule::excludeIf` ব্যবহার করুন:

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

_anotherfield_ _value_ এর সমান না হলে, ফিল্ড `validate`/`validated` দ্বারা রিটার্ন করা ডেটা থেকে বাদ দেওয়া হবে। _value_ `null` হলে (যেমন `exclude_unless:name,null`), তুলনা ফিল্ড `null` বা অনুপস্থিত থাকলে শুধুমাত্র ফিল্ড রাখা হয়।

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

_anotherfield_ থাকলে, ফিল্ড `validate`/`validated` দ্বারা রিটার্ন করা ডেটা থেকে বাদ দেওয়া হবে।

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

_anotherfield_ না থাকলে, ফিল্ড `validate`/`validated` দ্বারা রিটার্ন করা ডেটা থেকে বাদ দেওয়া হবে।

<a name="rule-exists"></a>
#### exists:_table_,_column_

ফিল্ডটি নির্দিষ্ট ডাটাবেস টেবিলে বিদ্যমান হতে হবে।

<a name="basic-usage-of-exists-rule"></a>
#### Exists রুলের মৌলিক ব্যবহার

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

`column` নির্দিষ্ট না করলে, ডিফল্টে ফিল্ড নাম ব্যবহার করা হয়। তাই এই উদাহরণ `states` টেবিলে `state` কলাম বিদ্যমান কিনা ভ্যালিডেট করে।

<a name="specifying-a-custom-column-name"></a>
#### কাস্টম কলাম নাম নির্দিষ্ট করা

টেবিল নামের পরে কলাম নাম যোগ করুন:

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

ডাটাবেস কানেকশন নির্দিষ্ট করতে, টেবিল নামের আগে কানেকশন নাম প্রিফিক্স করুন:

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

মডেল ক্লাস নামও পাস করতে পারেন; ফ্রেমওয়ার্ক টেবিল নাম রেজলভ করবে:

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

কাস্টম কুয়েরি শর্তের জন্য `Rule` বিল্ডার ব্যবহার করুন:

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

`Rule::exists` এ সরাসরি কলাম নামও নির্দিষ্ট করতে পারেন:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'state' => [Rule::exists('states', 'abbreviation')],
])->validate();
```

মান সেট বিদ্যমান কিনা ভ্যালিডেট করতে, `array` রুলের সাথে সংযুক্ত করুন:

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

`array` এবং `exists` উভয় থাকলে, একটি কুয়েরি সব মান ভ্যালিডেট করে।

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

আপলোডেড ফাইল এক্সটেনশন অনুমোদিত তালিকায় আছে কিনা ভ্যালিডেট করে:

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> ফাইল টাইপ ভ্যালিডেশনের জন্য শুধুমাত্র এক্সটেনশনের উপর নির্ভর করবেন না; [mimes](#rule-mimes) বা [mimetypes](#rule-mimetypes) এর সাথে ব্যবহার করুন।

<a name="rule-file"></a>
#### file

ফিল্ডটি সফলভাবে আপলোড করা ফাইল হতে হবে।

<a name="rule-filled"></a>
#### filled

ফিল্ড থাকলে, এর মান খালি হতে পারবে না।

<a name="rule-gt"></a>
#### gt:_field_

ফিল্ডটি প্রদত্ত _field_ বা _value_ এর চেয়ে বড় হতে হবে। উভয় ফিল্ডের একই টাইপ হতে হবে। স্ট্রিং, সংখ্যা, অ্যারে এবং ফাইলের জন্য মূল্যায়ন [size](#rule-size) এর মতো।

<a name="rule-gte"></a>
#### gte:_field_

ফিল্ডটি প্রদত্ত _field_ বা _value_ এর সমান বা বড় হতে হবে। উভয় ফিল্ডের একই টাইপ হতে হবে। স্ট্রিং, সংখ্যা, অ্যারে এবং ফাইলের জন্য মূল্যায়ন [size](#rule-size) এর মতো।

<a name="rule-hex-color"></a>
#### hex_color

ফিল্ডটি অবশ্যই বৈধ [hex color value](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color) হতে হবে।

<a name="rule-image"></a>
#### image

ফিল্ডটি অবশ্যই ইমেজ হতে হবে (jpg, jpeg, png, bmp, gif, অথবা webp)।

> [!WARNING]
> XSS ঝুঁকির কারণে ডিফল্টে SVG অনুমোদিত নয়। অনুমোদন করতে `allow_svg` যোগ করুন: `image:allow_svg`।

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

ফিল্ডটি প্রদত্ত মান তালিকায় থাকতে হবে। তৈরি করতে `Rule::in` ব্যবহার করতে পারেন:

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

`array` রুলের সাথে সংযুক্ত হলে, ইনপুট অ্যারের প্রতিটি মান `in` তালিকায় থাকতে হবে:

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

ফিল্ডটি _anotherfield_ এর মান তালিকায় বিদ্যমান হতে হবে।

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

ফিল্ডটি অবশ্যই অ্যারে হতে হবে এবং প্রদত্ত মানগুলোর কমপক্ষে একটি কী হিসেবে থাকতে হবে:

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

ফিল্ডটি অবশ্যই পূর্ণসংখ্যা হতে হবে।

ফিল্ড টাইপ পূর্ণসংখ্যা প্রয়োজন করতে `strict` প্যারামিটার ব্যবহার করুন; স্ট্রিং পূর্ণসংখ্যা অবৈধ হবে:

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> এই রুল শুধুমাত্র PHP এর `FILTER_VALIDATE_INT` পাস কিনা ভ্যালিডেট করে; strict সংখ্যাগত টাইপের জন্য [numeric](#rule-numeric) এর সাথে ব্যবহার করুন।

<a name="rule-ip"></a>
#### ip

ফিল্ডটি অবশ্যই বৈধ IP অ্যাড্রেস হতে হবে।

<a name="rule-ipv4"></a>
#### ipv4

ফিল্ডটি অবশ্যই বৈধ IPv4 অ্যাড্রেস হতে হবে।

<a name="rule-ipv6"></a>
#### ipv6

ফিল্ডটি অবশ্যই বৈধ IPv6 অ্যাড্রেস হতে হবে।

<a name="rule-json"></a>
#### json

ফিল্ডটি অবশ্যই বৈধ JSON স্ট্রিং হতে হবে।

<a name="rule-lt"></a>
#### lt:_field_

ফিল্ডটি প্রদত্ত _field_ এর চেয়ে কম হতে হবে। উভয় ফিল্ডের একই টাইপ হতে হবে। স্ট্রিং, সংখ্যা, অ্যারে এবং ফাইলের জন্য মূল্যায়ন [size](#rule-size) এর মতো।

<a name="rule-lte"></a>
#### lte:_field_

ফিল্ডটি প্রদত্ত _field_ এর সমান বা কম হতে হবে। উভয় ফিল্ডের একই টাইপ হতে হবে। স্ট্রিং, সংখ্যা, অ্যারে এবং ফাইলের জন্য মূল্যায়ন [size](#rule-size) এর মতো।

<a name="rule-lowercase"></a>
#### lowercase

ফিল্ডটি লোয়ারকেস হতে হবে।

<a name="rule-list"></a>
#### list

ফিল্ডটি অবশ্যই লিস্ট অ্যারে হতে হবে। লিস্ট অ্যারে কীগুলো 0 থেকে `count($array) - 1` পর্যন্ত ধারাবাহিক সংখ্যা হতে হবে।

<a name="rule-mac"></a>
#### mac_address

ফিল্ডটি অবশ্যই বৈধ MAC অ্যাড্রেস হতে হবে।

<a name="rule-max"></a>
#### max:_value_

ফিল্ডটি _value_ এর সমান বা কম হতে হবে। স্ট্রিং, সংখ্যা, অ্যারে এবং ফাইলের জন্য মূল্যায়ন [size](#rule-size) এর মতো।

<a name="rule-max-digits"></a>
#### max_digits:_value_

ফিল্ডটি দৈর্ঘ্য _value_ অতিক্রম না করা পূর্ণসংখ্যা হতে হবে।

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

ফাইলের MIME টাইপ তালিকায় আছে কিনা ভ্যালিডেট করে:

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

MIME টাইপ ফাইল কন্টেন্ট পড়ে অনুমান করা হয় এবং ক্লায়েন্ট-প্রদত্ত MIME থেকে ভিন্ন হতে পারে।

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

ফাইলের MIME টাইপ প্রদত্ত এক্সটেনশনের সাথে মিলে কিনা ভ্যালিডেট করে:

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

প্যারামিটারগুলো এক্সটেনশন হলেও, এই রুল MIME নির্ধারণ করতে ফাইল কন্টেন্ট পড়ে। এক্সটেনশন-টু-MIME ম্যাপিং:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### MIME টাইপ এবং এক্সটেনশন

এই রুল "ফাইল এক্সটেনশন" "প্রকৃত MIME" এর সাথে মিলে কিনা ভ্যালিডেট করে না। উদাহরণস্বরূপ, `mimes:png` PNG কন্টেন্ট সহ `photo.txt` কে বৈধ হিসেবে বিবেচনা করে। এক্সটেনশন ভ্যালিডেট করতে [extensions](#rule-extensions) ব্যবহার করুন।

<a name="rule-min"></a>
#### min:_value_

ফিল্ডটি _value_ এর সমান বা বড় হতে হবে। স্ট্রিং, সংখ্যা, অ্যারে এবং ফাইলের জন্য মূল্যায়ন [size](#rule-size) এর মতো।

<a name="rule-min-digits"></a>
#### min_digits:_value_

ফিল্ডটি দৈর্ঘ্য _value_ এর কম নয় এমন পূর্ণসংখ্যা হতে হবে।

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

ফিল্ডটি _value_ এর গুণিতক হতে হবে।

<a name="rule-missing"></a>
#### missing

ফিল্ডটি ইনপুট ডেটায় বিদ্যমান হতে পারবে না।

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

_anotherfield_ যেকোনো _value_ এর সমান হলে, ফিল্ডটি বিদ্যমান হতে পারবে না।

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

_anotherfield_ যেকোনো _value_ এর সমান না হলে, ফিল্ডটি বিদ্যমান হতে পারবে না।

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

নির্দিষ্ট যেকোনো ফিল্ড থাকলে, ফিল্ডটি বিদ্যমান হতে পারবে না।

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

নির্দিষ্ট সব ফিল্ড থাকলে, ফিল্ডটি বিদ্যমান হতে পারবে না।

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

ফিল্ডটি প্রদত্ত মান তালিকায় থাকতে পারবে না। তৈরি করতে `Rule::notIn` ব্যবহার করতে পারেন:

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

ফিল্ডটি প্রদত্ত রেগুলার এক্সপ্রেশনের সাথে মিলতে পারবে না।

এই রুল PHP `preg_match` ব্যবহার করে। রেগেক্সে ডিলিমিটার থাকতে হবে, যেমন `'email' => 'not_regex:/^.+$/i'`।

> [!WARNING]
> `regex`/`not_regex` ব্যবহার করার সময়, রেগেক্সে `|` থাকলে, `|` সেপারেটরের সাথে দ্বন্দ্ব এড়াতে অ্যারে ফরম ব্যবহার করুন।

<a name="rule-nullable"></a>
#### nullable

ফিল্ডটি `null` হতে পারে।

<a name="rule-numeric"></a>
#### numeric

ফিল্ডটি অবশ্যই [numeric](https://www.php.net/manual/en/function.is-numeric.php) হতে হবে।

শুধুমাত্র পূর্ণসংখ্যা বা ফ্লোট টাইপ অনুমোদন করতে `strict` প্যারামিটার ব্যবহার করুন; সংখ্যাগত স্ট্রিং অবৈধ হবে:

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

ফিল্ডটি ইনপুট ডেটায় বিদ্যমান হতে হবে।

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

_anotherfield_ যেকোনো _value_ এর সমান হলে, ফিল্ডটি বিদ্যমান হতে হবে।

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

_anotherfield_ যেকোনো _value_ এর সমান না হলে, ফিল্ডটি বিদ্যমান হতে হবে।

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

নির্দিষ্ট যেকোনো ফিল্ড থাকলে, ফিল্ডটি বিদ্যমান হতে হবে।

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

নির্দিষ্ট সব ফিল্ড থাকলে, ফিল্ডটি বিদ্যমান হতে হবে।

<a name="rule-prohibited"></a>
#### prohibited

ফিল্ডটি অনুপস্থিত বা খালি হতে হবে। "খালি" অর্থ:

<div class="content-list" markdown="1">

- মান `null`।
- মান খালি স্ট্রিং।
- মান খালি অ্যারে বা খালি `Countable` অবজেক্ট।
- খালি পাথ সহ আপলোড করা ফাইল।

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

_anotherfield_ যেকোনো _value_ এর সমান হলে, ফিল্ডটি অনুপস্থিত বা খালি হতে হবে। "খালি" অর্থ:

<div class="content-list" markdown="1">

- মান `null`।
- মান খালি স্ট্রিং।
- মান খালি অ্যারে বা খালি `Countable` অবজেক্ট।
- খালি পাথ সহ আপলোড করা ফাইল।

</div>

জটিল শর্তের জন্য `Rule::prohibitedIf` ব্যবহার করুন:

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

_anotherfield_ `"yes"`, `"on"`, `1`, `"1"`, `true`, অথবা `"true"` হলে, ফিল্ডটি অনুপস্থিত বা খালি হতে হবে।

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

_anotherfield_ `"no"`, `"off"`, `0`, `"0"`, `false`, অথবা `"false"` হলে, ফিল্ডটি অনুপস্থিত বা খালি হতে হবে।

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

_anotherfield_ যেকোনো _value_ এর সমান না হলে, ফিল্ডটি অনুপস্থিত বা খালি হতে হবে। "খালি" অর্থ:

<div class="content-list" markdown="1">

- মান `null`।
- মান খালি স্ট্রিং।
- মান খালি অ্যারে বা খালি `Countable` অবজেক্ট।
- খালি পাথ সহ আপলোড করা ফাইল।

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

ফিল্ড বিদ্যমান এবং খালি না হলে, _anotherfield_ এর সব ফিল্ড অনুপস্থিত বা খালি হতে হবে। "খালি" অর্থ:

<div class="content-list" markdown="1">

- মান `null`।
- মান খালি স্ট্রিং।
- মান খালি অ্যারে বা খালি `Countable` অবজেক্ট।
- খালি পাথ সহ আপলোড করা ফাইল।

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

ফিল্ডটি প্রদত্ত রেগুলার এক্সপ্রেশনের সাথে মিলতে হবে।

এই রুল PHP `preg_match` ব্যবহার করে। রেগেক্সে ডিলিমিটার থাকতে হবে, যেমন `'email' => 'regex:/^.+@.+$/i'`।

> [!WARNING]
> `regex`/`not_regex` ব্যবহার করার সময়, রেগেক্সে `|` থাকলে, `|` সেপারেটরের সাথে দ্বন্দ্ব এড়াতে অ্যারে ফরম ব্যবহার করুন।

<a name="rule-required"></a>
#### required

ফিল্ডটি বিদ্যমান হতে হবে এবং খালি হতে পারবে না। "খালি" অর্থ:

<div class="content-list" markdown="1">

- মান `null`।
- মান খালি স্ট্রিং।
- মান খালি অ্যারে বা খালি `Countable` অবজেক্ট।
- খালি পাথ সহ আপলোড করা ফাইল।

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

_anotherfield_ যেকোনো _value_ এর সমান হলে, ফিল্ডটি বিদ্যমান হতে হবে এবং খালি হতে পারবে না।

জটিল শর্তের জন্য `Rule::requiredIf` ব্যবহার করুন:

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

_anotherfield_ `"yes"`, `"on"`, `1`, `"1"`, `true`, অথবা `"true"` হলে, ফিল্ডটি বিদ্যমান হতে হবে এবং খালি হতে পারবে না।

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

_anotherfield_ `"no"`, `"off"`, `0`, `"0"`, `false`, অথবা `"false"` হলে, ফিল্ডটি বিদ্যমান হতে হবে এবং খালি হতে পারবে না।

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

_anotherfield_ যেকোনো _value_ এর সমান না হলে, ফিল্ডটি বিদ্যমান হতে হবে এবং খালি হতে পারবে না। _value_ `null` হলে (যেমন `required_unless:name,null`), তুলনা ফিল্ড `null` বা অনুপস্থিত থাকলে শুধুমাত্র ফিল্ড খালি হতে পারে।

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

নির্দিষ্ট যেকোনো ফিল্ড বিদ্যমান এবং খালি না হলে, ফিল্ডটি বিদ্যমান হতে হবে এবং খালি হতে পারবে না।

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

নির্দিষ্ট সব ফিল্ড বিদ্যমান এবং খালি না হলে, ফিল্ডটি বিদ্যমান হতে হবে এবং খালি হতে পারবে না।

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

নির্দিষ্ট যেকোনো ফিল্ড খালি বা অনুপস্থিত হলে, ফিল্ডটি বিদ্যমান হতে হবে এবং খালি হতে পারবে না।

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

নির্দিষ্ট সব ফিল্ড খালি বা অনুপস্থিত হলে, ফিল্ডটি বিদ্যমান হতে হবে এবং খালি হতে পারবে না।

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

ফিল্ডটি অবশ্যই অ্যারে হতে হবে এবং কমপক্ষে নির্দিষ্ট কীগুলো থাকতে হবে।

<a name="validating-when-present"></a>
#### sometimes

শুধুমাত্র ফিল্ড বিদ্যমান থাকলে পরবর্তী ভ্যালিডেশন রুল প্রয়োগ করুন। "ঐচ্ছিক কিন্তু উপস্থিত থাকলে বৈধ হতে হবে" ফিল্ডের জন্য সাধারণভাবে ব্যবহৃত:

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

ফিল্ডটি _field_ এর সাথে একই হতে হবে।

<a name="rule-size"></a>
#### size:_value_

ফিল্ড সাইজ প্রদত্ত _value_ এর সমান হতে হবে। স্ট্রিংয়ের জন্য: অক্ষর সংখ্যা; সংখ্যার জন্য: নির্দিষ্ট পূর্ণসংখ্যা (`numeric` বা `integer` এর সাথে ব্যবহার করুন); অ্যারের জন্য: এলিমেন্ট সংখ্যা; ফাইলের জন্য: KB এ সাইজ। উদাহরণ:

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

ফিল্ডটি নির্দিষ্ট মানগুলোর একটি দিয়ে শুরু হতে হবে।

<a name="rule-string"></a>
#### string

ফিল্ডটি অবশ্যই স্ট্রিং হতে হবে। `null` অনুমোদন করতে `nullable` এর সাথে ব্যবহার করুন।

<a name="rule-timezone"></a>
#### timezone

ফিল্ডটি অবশ্যই বৈধ টাইমজোন আইডেন্টিফায়ার হতে হবে (`DateTimeZone::listIdentifiers` থেকে)। সেই মেথড দ্বারা সমর্থিত প্যারামিটার পাস করতে পারেন:

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

ফিল্ডটি নির্দিষ্ট টেবিলে ইউনিক হতে হবে।

**কাস্টম টেবিল/কলাম নাম নির্দিষ্ট করুন:**

মডেল ক্লাস নাম সরাসরি নির্দিষ্ট করতে পারেন:

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

কলাম নাম নির্দিষ্ট করতে পারেন (নির্দিষ্ট না করলে ডিফল্টে ফিল্ড নাম):

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**ডাটাবেস কানেকশন নির্দিষ্ট করুন:**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**নির্দিষ্ট ID উপেক্ষা করুন:**

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
> `ignore` ব্যবহারকারী ইনপুট গ্রহণ করবে না; শুধুমাত্র সিস্টেম-জেনারেটেড ইউনিক ID (অটো-ইনক্রিমেন্ট ID বা মডেল UUID) ব্যবহার করুন, অন্যথায় SQL ইনজেকশন ঝুঁকি থাকতে পারে।

মডেল ইনস্ট্যান্সও পাস করতে পারেন:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

প্রাইমারি কী `id` না হলে, প্রাইমারি কী নাম নির্দিষ্ট করুন:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

ডিফল্টে ফিল্ড নাম ইউনিক কলাম হিসেবে ব্যবহার করে; কলাম নামও নির্দিষ্ট করতে পারেন:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**অতিরিক্ত শর্ত যোগ করুন:**

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

**সফট-ডিলিটেড রেকর্ড উপেক্ষা করুন:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

সফট ডিলিট কলাম `deleted_at` না হলে:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

ফিল্ডটি আপারকেস হতে হবে।

<a name="rule-url"></a>
#### url

ফিল্ডটি অবশ্যই বৈধ URL হতে হবে।

অনুমোদিত প্রোটোকল নির্দিষ্ট করতে পারেন:

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

ফিল্ডটি অবশ্যই বৈধ [ULID](https://github.com/ulid/spec) হতে হবে।

<a name="rule-uuid"></a>
#### uuid

ফিল্ডটি অবশ্যই বৈধ RFC 9562 UUID হতে হবে (সংস্করণ 1, 3, 4, 5, 6, 7, অথবা 8)।

সংস্করণ নির্দিষ্ট করতে পারেন:

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```



<a name="think-validate"></a>
# ভ্যালিডেটর top-think/think-validate

## বর্ণনা

অফিসিয়াল ThinkPHP ভ্যালিডেটর

## প্রজেক্ট URL

https://github.com/top-think/think-validate

## ইনস্টলেশন

`composer require topthink/think-validate`

## দ্রুত শুরু

**`app/index/validate/User.php` তৈরি করুন**

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
  
**ব্যবহার**

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

> **নোট**
> webman think-validate এর `Validate::rule()` মেথড সমর্থন করে না

<a name="respect-validation"></a>
# ভ্যালিডেটর workerman/validation

## বর্ণনা

এই প্রজেক্টটি https://github.com/Respect/Validation এর লোকালাইজড সংস্করণ

## প্রজেক্ট URL

https://github.com/walkor/validation
  
  
## ইনস্টলেশন
 
```php
composer require workerman/validation
```

## দ্রুত শুরু

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
  
**jQuery দিয়ে অ্যাক্সেস করুন**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
ফলাফল:

`{"code":500,"msg":"Username may only contain letters (a-z) and numbers (0-9)"}`

ব্যাখ্যা:

`v::input(array $input, array $rules)` ডেটা ভ্যালিডেট এবং সংগ্রহ করে। ভ্যালিডেশন ব্যর্থ হলে `Respect\Validation\Exceptions\ValidationException` নিক্ষেপ করে; সফল হলে ভ্যালিডেটেড ডেটা (অ্যারে) রিটার্ন করে।

ব্যবসায়িক কোড ভ্যালিডেশন এক্সেপশন ক্যাচ না করলে, webman ফ্রেমওয়ার্ক এটি ক্যাচ করে এবং HTTP হেডার অনুযায়ী JSON (যেমন `{"code":500, "msg":"xxx"}`) অথবা সাধারণ এক্সেপশন পেজ রিটার্ন করে। রেসপন্স ফরম্যাট আপনার প্রয়োজন পূরণ না করলে, `ValidationException` ক্যাচ করে নিচের উদাহরণের মতো কাস্টম ডেটা রিটার্ন করতে পারেন:

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

## ভ্যালিডেটর গাইড

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
    ]));
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
  
## ভ্যালিডেটর মেথড `validate()` `check()` `assert()` এর মধ্যে পার্থক্য

`validate()` বুলিয়ান রিটার্ন করে, এক্সেপশন নিক্ষেপ করে না

`check()` ভ্যালিডেশন ব্যর্থ হলে এক্সেপশন নিক্ষেপ করে; `$exception->getMessage()` দিয়ে প্রথম ব্যর্থতার কারণ পান

`assert()` ভ্যালিডেশন ব্যর্থ হলে এক্সেপশন নিক্ষেপ করে; `$exception->getFullMessage()` দিয়ে সব ব্যর্থতার কারণ পান
  
  
## সাধারণ ভ্যালিডেশন রুল

`Alnum()` শুধুমাত্র অক্ষর এবং সংখ্যা

`Alpha()` শুধুমাত্র অক্ষর

`ArrayType()` অ্যারে টাইপ

`Between(mixed $minimum, mixed $maximum)` ইনপুট দুটি মানের মধ্যে কিনা ভ্যালিডেট করে।

`BoolType()` বুলিয়ান টাইপ ভ্যালিডেট করে

`Contains(mixed $expectedValue)` ইনপুটে নির্দিষ্ট মান আছে কিনা ভ্যালিডেট করে

`ContainsAny(array $needles)` ইনপুটে কমপক্ষে একটি সংজ্ঞায়িত মান আছে কিনা ভ্যালিডেট করে

`Digit()` ইনপুটে শুধুমাত্র অঙ্ক আছে কিনা ভ্যালিডেট করে

`Domain()` বৈধ ডোমেইন নাম ভ্যালিডেট করে

`Email()` বৈধ ইমেইল অ্যাড্রেস ভ্যালিডেট করে

`Extension(string $extension)` ফাইল এক্সটেনশন ভ্যালিডেট করে

`FloatType()` ফ্লোট টাইপ ভ্যালিডেট করে

`IntType()` পূর্ণসংখ্যা টাইপ ভ্যালিডেট করে

`Ip()` IP অ্যাড্রেস ভ্যালিডেট করে

`Json()` JSON ডেটা ভ্যালিডেট করে

`Length(int $min, int $max)` দৈর্ঘ্য সীমার মধ্যে কিনা ভ্যালিডেট করে

`LessThan(mixed $compareTo)` দৈর্ঘ্য প্রদত্ত মানের চেয়ে কম কিনা ভ্যালিডেট করে

`Lowercase()` লোয়ারকেস ভ্যালিডেট করে

`MacAddress()` MAC অ্যাড্রেস ভ্যালিডেট করে

`NotEmpty()` খালি নয় ভ্যালিডেট করে

`NullType()` null ভ্যালিডেট করে

`Number()` সংখ্যা ভ্যালিডেট করে

`ObjectType()` অবজেক্ট টাইপ ভ্যালিডেট করে

`StringType()` স্ট্রিং টাইপ ভ্যালিডেট করে

`Url()` URL ভ্যালিডেট করে
  
আরও ভ্যালিডেশন রুলের জন্য https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ দেখুন।
  
## আরও

https://respect-validation.readthedocs.io/en/2.0/ ভিজিট করুন
  
```
