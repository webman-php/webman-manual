<div dir="rtl">

# مُحققون آخرون

يتوفر العديد من المُحققين في composer يمكن استخدامهم مباشرة، مثل:

#### <a href="#webman-validation"> webman/validation (موصى به)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# مُحقق webman/validation

مبني على `illuminate/validation`، يوفر التحقق اليدوي، التحقق بالتعليقات التوضيحية، التحقق على مستوى المعاملات، ومجموعات قواعد قابلة لإعادة الاستخدام.

## التثبيت

```bash
composer require webman/validation
```

## المفاهيم الأساسية

- **إعادة استخدام مجموعة القواعد**: تعريف `rules` و `messages` و `attributes` و `scenes` قابلة لإعادة الاستخدام عبر توسيع `support\validation\Validator`، والتي يمكن إعادة استخدامها في التحقق اليدوي والتحقق بالتعليقات التوضيحية.
- **التحقق بالتعليقات التوضيحية على مستوى الدالة**: استخدام خاصية PHP 8 `#[Validate]` لربط التحقق بدوال المتحكم.
- **التحقق بالتعليقات التوضيحية على مستوى المعاملات**: استخدام خاصية PHP 8 `#[Param]` لربط التحقق بمعاملات دوال المتحكم.
- **معالجة الاستثناءات**: يرمي `support\validation\ValidationException` عند فشل التحقق؛ فئة الاستثناء قابلة للتكوين.
- **التحقق من قاعدة البيانات**: إذا كان التحقق يتضمن قاعدة البيانات، تحتاج إلى تثبيت `composer require webman/database`.

## التحقق اليدوي

### الاستخدام الأساسي

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **ملاحظة**
> `validate()` ترمي `support\validation\ValidationException` عند فشل التحقق. إذا كنت تفضل عدم رمي الاستثناءات، استخدم طريقة `fails()` أدناه للحصول على رسائل الخطأ.

### رسائل وخصائص مخصصة

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

### التحقق بدون استثناء (الحصول على رسائل الخطأ)

إذا كنت تفضل عدم رمي الاستثناءات، استخدم `fails()` للتحقق والحصول على رسائل الخطأ عبر `errors()` (تُرجع `MessageBag`):

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

## إعادة استخدام مجموعة القواعد (مُحقق مخصص)

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

### إعادة استخدام التحقق اليدوي

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### استخدام المشاهد (اختياري)

`scenes` ميزة اختيارية؛ تتحقق فقط من مجموعة فرعية من الحقول عند استدعاء `withScene(...)`.

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

## التحقق بالتعليقات التوضيحية (على مستوى الدالة)

### قواعد مباشرة

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

### إعادة استخدام مجموعات القواعد

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

### تراكبات تحقق متعددة

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

### مصدر بيانات التحقق

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

استخدم المعامل `in` لتحديد مصدر البيانات:

* **query** معاملات استعلام طلب HTTP، من `$request->get()`
* **body** جسم طلب HTTP، من `$request->post()`
* **path** معاملات مسار طلب HTTP، من `$request->route->param()`

`in` يمكن أن تكون سلسلة أو مصفوفة؛ عند كونها مصفوفة، تُدمج القيم بالترتيب مع تجاوز القيم اللاحقة للقيم السابقة. عند عدم تمرير `in`، الافتراضي هو `['query', 'body', 'path']`.


## التحقق على مستوى المعاملات (Param)

### الاستخدام الأساسي

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

### مصدر بيانات التحقق

وبالمثل، يدعم التحقق على مستوى المعاملات المعامل `in` لتحديد المصدر:

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


### rules تدعم السلسلة أو المصفوفة

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

### رسائل / خاصية مخصصة

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

### إعادة استخدام ثابت القواعد

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

## الجمع بين التحقق على مستوى الدالة والمعاملات

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

## الاستدلال التلقائي للقواعد (بناءً على توقيع المعامل)

عند استخدام `#[Validate]` على دالة، أو عند استخدام أي معامل من معاملات تلك الدالة لـ `#[Param]`، يقوم هذا المكون **باستنتاج وإكمال قواعد التحقق الأساسية تلقائياً من توقيع معامل الدالة**، ثم دمجها مع القواعد الموجودة قبل التحقق.

### مثال: توسيع مكافئ لـ `#[Validate]`

1) تفعيل `#[Validate]` فقط دون كتابة القواعد يدوياً:

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

يعادل:

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

2) كتابة قواعد جزئية فقط، والباقي يُكمل من توقيع المعامل:

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

يعادل:

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

3) القيمة الافتراضية / النوع القابل للإبطال:

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

يعادل:

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

## معالجة الاستثناءات

### الاستثناء الافتراضي

فشل التحقق يرمي `support\validation\ValidationException` افتراضياً، والذي يمتد من `Webman\Exception\BusinessException` ولا يسجل الأخطاء.

يتم التعامل مع سلوك الاستجابة الافتراضي بواسطة `BusinessException::render()`:

- الطلبات العادية: تُرجع رسالة نصية، مثل `token is required.`
- طلبات JSON: تُرجع استجابة JSON، مثل `{"code": 422, "msg": "token is required.", "data":....}`

### تخصيص المعالجة عبر استثناء مخصص

- التكوين العام: `exception` في `config/plugin/webman/validation/app.php`

## دعم متعدد اللغات

يتضمن المكون حزم لغات صينية وإنجليزية مدمجة ويدعم تجاوزات المشروع. ترتيب التحميل:

1. حزمة لغة المشروع `resource/translations/{locale}/validation.php`
2. المكون المدمج `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. الإنجليزية المدمجة في Illuminate (احتياطي)

> **ملاحظة**
> اللغة الافتراضية لـ Webman مُكوّنة في `config/translation.php`، أو يمكن تغييرها عبر `locale('en');`.

### مثال تجاوز محلي

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute is not a valid email format.',
];
```

## التحميل التلقائي للوسيط

بعد التثبيت، يقوم المكون بتحميل وسيط التحقق تلقائياً عبر `config/plugin/webman/validation/middleware.php`؛ لا حاجة للتسجيل اليدوي.

## إنشاء من سطر الأوامر

استخدم أمر `make:validator` لإنشاء فئات المُحقق (المخرجات الافتراضية إلى مجلد `app/validation`).

> **ملاحظة**
> يتطلب `composer require webman/console`

### الاستخدام الأساسي

- **إنشاء قالب فارغ**

```bash
php webman make:validator UserValidator
```

- **الكتابة فوق الملف الموجود**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### إنشاء القواعد من بنية الجدول

- **تحديد اسم الجدول لإنشاء القواعد الأساسية** (يستنتج `$rules` من نوع الحقل/nullable/الطول إلخ؛ يستبعد حقول ORM افتراضياً: laravel يستخدم `created_at/updated_at/deleted_at`، thinkorm يستخدم `create_time/update_time/delete_time`)

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **تحديد اتصال قاعدة البيانات** (سيناريوهات الاتصال المتعدد)

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### المشاهد

- **إنشاء مشاهد CRUD**: `create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> مشهد `update` يتضمن حقل المفتاح الأساسي (لتحديد السجلات) بالإضافة إلى الحقول الأخرى؛ `delete/detail` يتضمنان المفتاح الأساسي فقط افتراضياً.

### اختيار ORM (laravel (illuminate/database) مقابل think-orm)

- **الاختيار التلقائي (افتراضي)**: يستخدم أيهما مُثبت/مُكوّن؛ عند وجود كليهما، يستخدم illuminate افتراضياً
- **التحديد القسري**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### مثال كامل

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## اختبارات الوحدة

من مجلد جذر `webman/validation`، نفّذ:

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## مرجع قواعد التحقق

<a name="available-validation-rules"></a>
## قواعد التحقق المتاحة

> [!IMPORTANT]
> - Webman Validation مبني على `illuminate/validation`؛ أسماء القواعد تطابق Laravel ولا توجد قواعد خاصة بـ Webman.
> - الوسيط يتحقق من البيانات من `$request->all()` (GET+POST) مدمجة مع معاملات المسار افتراضياً، باستثناء الملفات المرفوعة؛ لقواعد الملفات، ادمج `$request->file()` في البيانات بنفسك، أو استدعِ `Validator::make` يدوياً.
> - `current_password` يعتمد على حارس المصادقة؛ `exists`/`unique` يعتمدان على اتصال قاعدة البيانات وبناء الاستعلام؛ هذه القواعد غير متاحة عند عدم دمج المكونات المقابلة.

يسرد ما يلي جميع قواعد التحقق المتاحة وأغراضها:

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

#### منطقي

<div class="collection-method-list" markdown="1">

[Accepted](#rule-accepted)
[Accepted If](#rule-accepted-if)
[Boolean](#rule-boolean)
[Declined](#rule-declined)
[Declined If](#rule-declined-if)

</div>

#### نصي

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

#### رقمي

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

#### مصفوفة

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

#### تاريخ

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

#### ملف

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

#### قاعدة البيانات

<div class="collection-method-list" markdown="1">

[Exists](#rule-exists)
[Unique](#rule-unique)

</div>

#### أدوات مساعدة

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

يجب أن يكون الحقل `"yes"` أو `"on"` أو `1` أو `"1"` أو `true` أو `"true"`. يُستخدم عادةً لسيناريوهات مثل التحقق من موافقة المستخدم على شروط الخدمة.

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

عندما يساوي حقل آخر القيمة المحددة، يجب أن يكون الحقل `"yes"` أو `"on"` أو `1` أو `"1"` أو `true` أو `"true"`. يُستخدم عادةً لسيناريوهات الموافقة الشرطية.

<a name="rule-active-url"></a>
#### active_url

يجب أن يكون للحقل سجل A أو AAAA صالح. تستخدم هذه القاعدة أولاً `parse_url` لاستخراج اسم المضيف من الرابط، ثم تتحقق باستخدام `dns_get_record`.

<a name="rule-after"></a>
#### after:_date_

يجب أن يكون الحقل قيمة بعد التاريخ المعطى. يُمرر التاريخ إلى `strtotime` للتحويل إلى `DateTime` صالح:

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

يمكنك أيضاً تمرير اسم حقل آخر للمقارنة:

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

يمكنك استخدام بناء قاعدة `date` السلس:

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

`afterToday` و `todayOrAfter` يعبّران بسهولة عن "يجب أن يكون بعد اليوم" أو "يجب أن يكون اليوم أو لاحقاً":

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

يجب أن يكون الحقل في أو بعد التاريخ المعطى. راجع [after](#rule-after) لمزيد من التفاصيل.

يمكنك استخدام بناء قاعدة `date` السلس:

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

`Rule::anyOf` يسمح بتحديد "إرضاء مجموعة قواعد واحدة". على سبيل المثال، القاعدة التالية تعني أن `username` يجب أن يكون إما عنوان بريد إلكتروني أو سلسلة أبجدية رقمية/شرطة سفلية/شرطة من 6 أحرف على الأقل:

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

يجب أن يكون الحقل أحرف Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) و [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)).

للسماح بـ ASCII فقط (`a-z`، `A-Z`)، أضف خيار `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

يجب أن يحتوي الحقل فقط على أحرف وأرقام Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=)، [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)، [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=))، بالإضافة إلى الشرطة (`-`) والشرطة السفلية (`_`) ASCII.

للسماح بـ ASCII فقط (`a-z`، `A-Z`، `0-9`)، أضف خيار `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

يجب أن يحتوي الحقل فقط على أحرف وأرقام Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=)، [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)، [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)).

للسماح بـ ASCII فقط (`a-z`، `A-Z`، `0-9`)، أضف خيار `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

يجب أن يكون الحقل مصفوفة PHP.

عندما تكون لقاعدة `array` معاملات إضافية، يجب أن تكون مفاتيح مصفوفة الإدخال في قائمة المعاملات. في المثال، المفتاح `admin` ليس في القائمة المسموحة، لذا فهو غير صالح:

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

يُوصى بتعريف مفاتيح المصفوفة المسموحة صراحةً في المشاريع الحقيقية.

<a name="rule-ascii"></a>
#### ascii

يجب أن يحتوي الحقل فقط على أحرف ASCII 7 بت.

<a name="rule-bail"></a>
#### bail

إيقاف التحقق من القواعد الإضافية للحقل عند فشل القاعدة الأولى.

هذه القاعدة تؤثر فقط على الحقل الحالي. لـ "الإيقاف عند أول فشل عالمياً"، استخدم مُحقق Illuminate مباشرةً واستدعِ `stopOnFirstFailure()`.

<a name="rule-before"></a>
#### before:_date_

يجب أن يكون الحقل قبل التاريخ المعطى. يُمرر التاريخ إلى `strtotime` للتحويل إلى `DateTime` صالح. مثل [after](#rule-after)، يمكنك تمرير اسم حقل آخر للمقارنة.

يمكنك استخدام بناء قاعدة `date` السلس:

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

`beforeToday` و `todayOrBefore` يعبّران بسهولة عن "يجب أن يكون قبل اليوم" أو "يجب أن يكون اليوم أو قبل ذلك":

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

يجب أن يكون الحقل في أو قبل التاريخ المعطى. يُمرر التاريخ إلى `strtotime` للتحويل إلى `DateTime` صالح. مثل [after](#rule-after)، يمكنك تمرير اسم حقل آخر للمقارنة.

يمكنك استخدام بناء قاعدة `date` السلس:

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

يجب أن يكون حجم الحقل بين _min_ و _max_ (شامل). التقييم للنصوص والأرقام والمصفوفات والملفات هو نفسه [size](#rule-size).

<a name="rule-boolean"></a>
#### boolean

يجب أن يكون الحقل قابلاً للتحويل إلى منطقي. المدخلات المقبولة تشمل `true`، `false`، `1`، `0`، `"1"`، `"0"`.

استخدم المعامل `strict` للسماح فقط بـ `true` أو `false`:

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

يجب أن يكون للحقل حقل مطابق `{field}_confirmation`. على سبيل المثال، عندما يكون الحقل `password`، يُطلب `password_confirmation`.

يمكنك أيضاً تحديد اسم حقل تأكيد مخصص، مثل `confirmed:repeat_username` يتطلب أن يطابق `repeat_username` الحقل الحالي.

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

يجب أن يكون الحقل مصفوفة ويجب أن يحتوي على جميع قيم المعاملات المعطاة. تُستخدم هذه القاعدة عادةً للتحقق من المصفوفات؛ يمكنك استخدام `Rule::contains` لبنائها:

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

يجب أن يكون الحقل مصفوفة ويجب ألا يحتوي على أي من قيم المعاملات المعطاة. يمكنك استخدام `Rule::doesntContain` لبنائها:

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

يجب أن يطابق الحقل كلمة مرور المستخدم المصادق عليه حالياً. يمكنك تحديد حارس المصادقة كأول معامل:

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> تعتمد هذه القاعدة على مكون المصادقة وتكوين الحارس؛ لا تستخدمها عند عدم دمج المصادقة.

<a name="rule-date"></a>
#### date

يجب أن يكون الحقل تاريخاً صالحاً (غير نسبي) يمكن لـ `strtotime` التعرف عليه.

<a name="rule-date-equals"></a>
#### date_equals:_date_

يجب أن يساوي الحقل التاريخ المعطى. يُمرر التاريخ إلى `strtotime` للتحويل إلى `DateTime` صالح.

<a name="rule-date-format"></a>
#### date_format:_format_,...

يجب أن يطابق الحقل أحد التنسيقات المعطاة. استخدم إما `date` أو `date_format`. تدعم هذه القاعدة جميع تنسيقات PHP [DateTime](https://www.php.net/manual/en/class.datetime.php).

يمكنك استخدام بناء قاعدة `date` السلس:

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

يجب أن يكون الحقل رقماً مع المنازل العشرية المطلوبة:

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

يجب أن يكون الحقل `"no"` أو `"off"` أو `0` أو `"0"` أو `false` أو `"false"`.

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

عندما يساوي حقل آخر القيمة المحددة، يجب أن يكون الحقل `"no"` أو `"off"` أو `0` أو `"0"` أو `false` أو `"false"`.

<a name="rule-different"></a>
#### different:_field_

يجب أن يكون الحقل مختلفاً عن _field_.

<a name="rule-digits"></a>
#### digits:_value_

يجب أن يكون الحقل عدداً صحيحاً بطول _value_.

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

يجب أن يكون الحقل عدداً صحيحاً بطول بين _min_ و _max_.

<a name="rule-dimensions"></a>
#### dimensions

يجب أن يكون الحقل صورةً ويرضي قيود الأبعاد:

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

القيود المتاحة: _min\_width_، _max\_width_، _min\_height_، _max\_height_، _width_، _height_، _ratio_.

_ratio_ هي نسبة العرض إلى الارتفاع؛ يمكن التعبير عنها ككسر أو رقم عشري:

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

هذه القاعدة لها معاملات كثيرة؛ يُوصى باستخدام `Rule::dimensions` لبنائها:

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

عند التحقق من المصفوفات، يجب ألا تتكرر قيم الحقول:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

يستخدم المقارنة المرنة افتراضياً. أضف `strict` للمقارنة الصارمة:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

أضف `ignore_case` لتجاهل اختلافات حالة الأحرف:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

يجب ألا يبدأ الحقل بأي من القيم المحددة.

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

يجب ألا ينتهي الحقل بأي من القيم المحددة.

<a name="rule-email"></a>
#### email

يجب أن يكون الحقل عنوان بريد إلكتروني صالح. تعتمد هذه القاعدة على [egulias/email-validator](https://github.com/egulias/EmailValidator)، وتستخدم `RFCValidation` افتراضياً، ويمكنها استخدام طرق تحقق أخرى:

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

طرق التحقق المتاحة:

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` - التحقق من البريد الإلكتروني وفق مواصفات RFC ([RFCs المدعومة](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs)).
- `strict`: `NoRFCWarningsValidation` - الفشل عند تحذيرات RFC (مثل النقطة الزائدة أو النقاط المتتالية).
- `dns`: `DNSCheckValidation` - التحقق من وجود سجلات MX صالحة للنطاق.
- `spoof`: `SpoofCheckValidation` - منع أحرف Unicode المتشابهة أو الاحتيالية.
- `filter`: `FilterEmailValidation` - التحقق باستخدام PHP `filter_var`.
- `filter_unicode`: `FilterEmailValidation::unicode()` - تحقق `filter_var` مع السماح بـ Unicode.

</div>

يمكنك استخدام بناء القواعد السلس:

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
> `dns` و `spoof` يتطلبان امتداد PHP `intl`.

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

يجب أن يطابق الحقل ترميز الأحرف المحدد. تستخدم هذه القاعدة `mb_check_encoding` لاكتشاف ترميز الملف أو النص. يمكن استخدامها مع بناء قاعدة الملف:

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

يجب أن ينتهي الحقل بإحدى القيم المحددة.

<a name="rule-enum"></a>
#### enum

`Enum` قاعدة قائمة على الفئة للتحقق من أن قيمة الحقل هي قيمة enum صالحة. مرّر اسم فئة enum عند البناء. للقيم البدائية، استخدم Backed Enum:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

استخدم `only`/`except` لتقييد قيم enum:

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

استخدم `when` للقيود الشرطية:

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

سيتم استبعاد الحقل من البيانات المُرجعة بواسطة `validate`/`validated`.

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

عندما يساوي _anotherfield_ القيمة _value_، سيتم استبعاد الحقل من البيانات المُرجعة بواسطة `validate`/`validated`.

للشروط المعقدة، استخدم `Rule::excludeIf`:

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

ما لم يساوِ _anotherfield_ القيمة _value_، سيتم استبعاد الحقل من البيانات المُرجعة بواسطة `validate`/`validated`. إذا كانت _value_ هي `null` (مثل `exclude_unless:name,null`)، يُحتفظ بالحقل فقط عندما يكون حقل المقارنة `null` أو غائباً.

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

عندما يوجد _anotherfield_، سيتم استبعاد الحقل من البيانات المُرجعة بواسطة `validate`/`validated`.

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

عندما لا يوجد _anotherfield_، سيتم استبعاد الحقل من البيانات المُرجعة بواسطة `validate`/`validated`.

<a name="rule-exists"></a>
#### exists:_table_,_column_

يجب أن يوجد الحقل في جدول قاعدة البيانات المحدد.

<a name="basic-usage-of-exists-rule"></a>
#### الاستخدام الأساسي لقاعدة Exists

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

عند عدم تحديد `column`، يُستخدم اسم الحقل افتراضياً. لذا يتحقق هذا المثال مما إذا كان عمود `state` موجوداً في جدول `states`.

<a name="specifying-a-custom-column-name"></a>
#### تحديد اسم عمود مخصص

أضف اسم العمود بعد اسم الجدول:

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

لتحديد اتصال قاعدة البيانات، أضف اسم الاتصال قبل اسم الجدول:

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

يمكنك أيضاً تمرير اسم فئة نموذج؛ سيتعامل الإطار مع اسم الجدول:

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

لشروط الاستعلام المخصصة، استخدم بناء `Rule`:

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

يمكنك أيضاً تحديد اسم العمود مباشرةً في `Rule::exists`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'state' => [Rule::exists('states', 'abbreviation')],
])->validate();
```

للتحقق من وجود مجموعة قيم، اجمع مع قاعدة `array`:

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

عند وجود كل من `array` و `exists`، يستخدم استعلام واحد للتحقق من جميع القيم.

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

يتحقق من أن امتداد الملف المرفوع في القائمة المسموحة:

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> لا تعتمد على الامتداد وحده للتحقق من نوع الملف؛ استخدم مع [mimes](#rule-mimes) أو [mimetypes](#rule-mimetypes).

<a name="rule-file"></a>
#### file

يجب أن يكون الحقل ملفاً مرفوعاً بنجاح.

<a name="rule-filled"></a>
#### filled

عند وجود الحقل، يجب ألا تكون قيمته فارغة.

<a name="rule-gt"></a>
#### gt:_field_

يجب أن يكون الحقل أكبر من _field_ أو _value_ المعطى. يجب أن يكون للحقلين نفس النوع. التقييم للنصوص والأرقام والمصفوفات والملفات هو نفسه [size](#rule-size).

<a name="rule-gte"></a>
#### gte:_field_

يجب أن يكون الحقل أكبر من أو يساوي _field_ أو _value_ المعطى. يجب أن يكون للحقلين نفس النوع. التقييم للنصوص والأرقام والمصفوفات والملفات هو نفسه [size](#rule-size).

<a name="rule-hex-color"></a>
#### hex_color

يجب أن يكون الحقل [قيمة لون سداسي عشري](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color) صالحة.

<a name="rule-image"></a>
#### image

يجب أن يكون الحقل صورة (jpg، jpeg، png، bmp، gif، أو webp).

> [!WARNING]
> SVG غير مسموح به افتراضياً بسبب مخاطر XSS. للسماح به، أضف `allow_svg`: `image:allow_svg`.

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

يجب أن يكون الحقل في قائمة القيم المعطاة. يمكنك استخدام `Rule::in` للبناء:

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

عند الجمع مع قاعدة `array`، يجب أن تكون كل قيمة في مصفوفة الإدخال في قائمة `in`:

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

يجب أن يوجد الحقل في قائمة قيم _anotherfield_.

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

يجب أن يكون الحقل مصفوفة ويجب أن يحتوي على واحد على الأقل من القيم المعطاة كمفتاح:

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

يجب أن يكون الحقل عدداً صحيحاً.

استخدم المعامل `strict` لاشتراط أن يكون نوع الحقل عدداً صحيحاً؛ الأعداد الصحيحة النصية ستكون غير صالحة:

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> تتحقق هذه القاعدة فقط مما إذا كانت تمر بـ `FILTER_VALIDATE_INT` في PHP؛ للأنواع الرقمية الصارمة، استخدم مع [numeric](#rule-numeric).

<a name="rule-ip"></a>
#### ip

يجب أن يكون الحقل عنوان IP صالحاً.

<a name="rule-ipv4"></a>
#### ipv4

يجب أن يكون الحقل عنوان IPv4 صالحاً.

<a name="rule-ipv6"></a>
#### ipv6

يجب أن يكون الحقل عنوان IPv6 صالحاً.

<a name="rule-json"></a>
#### json

يجب أن يكون الحقل سلسلة JSON صالحة.

<a name="rule-lt"></a>
#### lt:_field_

يجب أن يكون الحقل أصغر من _field_ المعطى. يجب أن يكون للحقلين نفس النوع. التقييم للنصوص والأرقام والمصفوفات والملفات هو نفسه [size](#rule-size).

<a name="rule-lte"></a>
#### lte:_field_

يجب أن يكون الحقل أصغر من أو يساوي _field_ المعطى. يجب أن يكون للحقلين نفس النوع. التقييم للنصوص والأرقام والمصفوفات والملفات هو نفسه [size](#rule-size).

<a name="rule-lowercase"></a>
#### lowercase

يجب أن يكون الحقل بأحرف صغيرة.

<a name="rule-list"></a>
#### list

يجب أن يكون الحقل مصفوفة قائمة. يجب أن تكون مفاتيح مصفوفة القائمة أرقاماً متتالية من 0 إلى `count($array) - 1`.

<a name="rule-mac"></a>
#### mac_address

يجب أن يكون الحقل عنوان MAC صالحاً.

<a name="rule-max"></a>
#### max:_value_

يجب أن يكون الحقل أصغر من أو يساوي _value_. التقييم للنصوص والأرقام والمصفوفات والملفات هو نفسه [size](#rule-size).

<a name="rule-max-digits"></a>
#### max_digits:_value_

يجب أن يكون الحقل عدداً صحيحاً بطول لا يتجاوز _value_.

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

يتحقق من أن نوع MIME للملف في القائمة:

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

يُخمّن نوع MIME بقراءة محتوى الملف وقد يختلف عن MIME المقدم من العميل.

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

يتحقق من أن نوع MIME للملف يتوافق مع الامتداد المعطى:

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

رغم أن المعاملات امتدادات، تقرأ هذه القاعدة محتوى الملف لتحديد MIME. خريطة الامتداد إلى MIME:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### أنواع MIME والامتدادات

لا تتحقق هذه القاعدة من أن "امتداد الملف" يطابق "MIME الفعلي". على سبيل المثال، `mimes:png` يعتبر `photo.txt` بمحتوى PNG صالحاً. للتحقق من الامتداد، استخدم [extensions](#rule-extensions).

<a name="rule-min"></a>
#### min:_value_

يجب أن يكون الحقل أكبر من أو يساوي _value_. التقييم للنصوص والأرقام والمصفوفات والملفات هو نفسه [size](#rule-size).

<a name="rule-min-digits"></a>
#### min_digits:_value_

يجب أن يكون الحقل عدداً صحيحاً بطول لا يقل عن _value_.

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

يجب أن يكون الحقل مضاعفاً لـ _value_.

<a name="rule-missing"></a>
#### missing

يجب ألا يوجد الحقل في بيانات الإدخال.

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

عندما يساوي _anotherfield_ أي _value_، يجب ألا يوجد الحقل.

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

ما لم يساوِ _anotherfield_ أي _value_، يجب ألا يوجد الحقل.

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

عندما يوجد أي حقل محدد، يجب ألا يوجد الحقل.

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

عندما توجد جميع الحقول المحددة، يجب ألا يوجد الحقل.

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

يجب ألا يكون الحقل في قائمة القيم المعطاة. يمكنك استخدام `Rule::notIn` للبناء:

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

يجب ألا يطابق الحقل التعبير النمطي المعطى.

تستخدم هذه القاعدة `preg_match` في PHP. يجب أن يحتوي التعبير النمطي على محددات، مثل `'email' => 'not_regex:/^.+$/i'`.

> [!WARNING]
> عند استخدام `regex`/`not_regex`، إذا احتوى التعبير النمطي على `|`، استخدم صيغة المصفوفة لتجنب التعارض مع فاصل `|`.

<a name="rule-nullable"></a>
#### nullable

يمكن أن يكون الحقل `null`.

<a name="rule-numeric"></a>
#### numeric

يجب أن يكون الحقل [رقماً](https://www.php.net/manual/en/function.is-numeric.php).

استخدم المعامل `strict` للسماح فقط بأنواع integer أو float؛ السلاسل الرقمية ستكون غير صالحة:

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

يجب أن يوجد الحقل في بيانات الإدخال.

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

عندما يساوي _anotherfield_ أي _value_، يجب أن يوجد الحقل.

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

ما لم يساوِ _anotherfield_ أي _value_، يجب أن يوجد الحقل.

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

عندما يوجد أي حقل محدد، يجب أن يوجد الحقل.

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

عندما توجد جميع الحقول المحددة، يجب أن يوجد الحقل.

<a name="rule-prohibited"></a>
#### prohibited

يجب أن يكون الحقل غائباً أو فارغاً. "فارغ" يعني:

<div class="content-list" markdown="1">

- القيمة `null`.
- القيمة سلسلة فارغة.
- القيمة مصفوفة فارغة أو كائن Countable فارغ.
- ملف مرفوع بمسار فارغ.

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

عندما يساوي _anotherfield_ أي _value_، يجب أن يكون الحقل غائباً أو فارغاً. "فارغ" يعني:

<div class="content-list" markdown="1">

- القيمة `null`.
- القيمة سلسلة فارغة.
- القيمة مصفوفة فارغة أو كائن Countable فارغ.
- ملف مرفوع بمسار فارغ.

</div>

للشروط المعقدة، استخدم `Rule::prohibitedIf`:

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

عندما يكون _anotherfield_ هو `"yes"` أو `"on"` أو `1` أو `"1"` أو `true` أو `"true"`، يجب أن يكون الحقل غائباً أو فارغاً.

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

عندما يكون _anotherfield_ هو `"no"` أو `"off"` أو `0` أو `"0"` أو `false` أو `"false"`، يجب أن يكون الحقل غائباً أو فارغاً.

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

ما لم يساوِ _anotherfield_ أي _value_، يجب أن يكون الحقل غائباً أو فارغاً. "فارغ" يعني:

<div class="content-list" markdown="1">

- القيمة `null`.
- القيمة سلسلة فارغة.
- القيمة مصفوفة فارغة أو كائن Countable فارغ.
- ملف مرفوع بمسار فارغ.

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

عندما يوجد الحقل وليس فارغاً، يجب أن تكون جميع الحقول في _anotherfield_ غائبة أو فارغة. "فارغ" يعني:

<div class="content-list" markdown="1">

- القيمة `null`.
- القيمة سلسلة فارغة.
- القيمة مصفوفة فارغة أو كائن Countable فارغ.
- ملف مرفوع بمسار فارغ.

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

يجب أن يطابق الحقل التعبير النمطي المعطى.

تستخدم هذه القاعدة `preg_match` في PHP. يجب أن يحتوي التعبير النمطي على محددات، مثل `'email' => 'regex:/^.+@.+$/i'`.

> [!WARNING]
> عند استخدام `regex`/`not_regex`، إذا احتوى التعبير النمطي على `|`، استخدم صيغة المصفوفة لتجنب التعارض مع فاصل `|`.

<a name="rule-required"></a>
#### required

يجب أن يوجد الحقل ولا يكون فارغاً. "فارغ" يعني:

<div class="content-list" markdown="1">

- القيمة `null`.
- القيمة سلسلة فارغة.
- القيمة مصفوفة فارغة أو كائن Countable فارغ.
- ملف مرفوع بمسار فارغ.

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

عندما يساوي _anotherfield_ أي _value_، يجب أن يوجد الحقل ولا يكون فارغاً.

للشروط المعقدة، استخدم `Rule::requiredIf`:

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

عندما يكون _anotherfield_ هو `"yes"` أو `"on"` أو `1` أو `"1"` أو `true` أو `"true"`، يجب أن يوجد الحقل ولا يكون فارغاً.

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

عندما يكون _anotherfield_ هو `"no"` أو `"off"` أو `0` أو `"0"` أو `false` أو `"false"`، يجب أن يوجد الحقل ولا يكون فارغاً.

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

ما لم يساوِ _anotherfield_ أي _value_، يجب أن يوجد الحقل ولا يكون فارغاً. إذا كانت _value_ هي `null` (مثل `required_unless:name,null`)، يمكن أن يكون الحقل فارغاً فقط عندما يكون حقل المقارنة `null` أو غائباً.

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

عندما يوجد أي حقل محدد وليس فارغاً، يجب أن يوجد الحقل ولا يكون فارغاً.

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

عندما توجد جميع الحقول المحددة وليست فارغة، يجب أن يوجد الحقل ولا يكون فارغاً.

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

عندما يكون أي حقل محدد فارغاً أو غائباً، يجب أن يوجد الحقل ولا يكون فارغاً.

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

عندما تكون جميع الحقول المحددة فارغة أو غائبة، يجب أن يوجد الحقل ولا يكون فارغاً.

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

يجب أن يكون الحقل مصفوفة ويجب أن يحتوي على المفاتيح المحددة على الأقل.

<a name="validating-when-present"></a>
#### sometimes

تطبيق قواعد التحقق اللاحقة فقط عند وجود الحقل. تُستخدم عادةً للحقول "اختيارية ولكن يجب أن تكون صالحة عند وجودها":

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

يجب أن يكون الحقل مطابقاً لـ _field_.

<a name="rule-size"></a>
#### size:_value_

يجب أن يساوي حجم الحقل _value_ المعطى. للنصوص: عدد الأحرف؛ للأرقام: العدد الصحيح المحدد (استخدم مع `numeric` أو `integer`)؛ للمصفوفات: عدد العناصر؛ للملفات: الحجم بالكيلوبايت. مثال:

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

يجب أن يبدأ الحقل بإحدى القيم المحددة.

<a name="rule-string"></a>
#### string

يجب أن يكون الحقل سلسلة. للسماح بـ `null`، استخدم مع `nullable`.

<a name="rule-timezone"></a>
#### timezone

يجب أن يكون الحقل معرف منطقة زمنية صالحاً (من `DateTimeZone::listIdentifiers`). يمكنك تمرير المعاملات المدعومة من تلك الدالة:

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

يجب أن يكون الحقل فريداً في الجدول المحدد.

**تحديد اسم جدول/عمود مخصص:**

يمكنك تحديد اسم فئة النموذج مباشرةً:

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

يمكنك تحديد اسم العمود (يُستخدم اسم الحقل افتراضياً عند عدم التحديد):

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**تحديد اتصال قاعدة البيانات:**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**تجاهل معرف محدد:**

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
> يجب ألا يستقبل `ignore` إدخال المستخدم؛ استخدم فقط معرفات فريدة مولدة من النظام (معرف تلقائي أو UUID للنموذج)، وإلا قد يكون هناك خطر حقن SQL.

يمكنك أيضاً تمرير مثيل نموذج:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

إذا لم يكن المفتاح الأساسي `id`، حدد اسم المفتاح الأساسي:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

افتراضياً يستخدم اسم الحقل كعمود فريد؛ يمكنك أيضاً تحديد اسم العمود:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**إضافة شروط إضافية:**

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

**تجاهل السجلات المحذوفة بشكل ناعم:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

إذا لم يكن عمود الحذف الناعم `deleted_at`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

يجب أن يكون الحقل بأحرف كبيرة.

<a name="rule-url"></a>
#### url

يجب أن يكون الحقل رابطاً صالحاً.

يمكنك تحديد البروتوكولات المسموحة:

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

يجب أن يكون الحقل [ULID](https://github.com/ulid/spec) صالحاً.

<a name="rule-uuid"></a>
#### uuid

يجب أن يكون الحقل UUID صالحاً وفق RFC 9562 (الإصدار 1 أو 3 أو 4 أو 5 أو 6 أو 7 أو 8).

يمكنك تحديد الإصدار:

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```



<a name="think-validate"></a>
# مُحقق top-think/think-validate

## الوصف

مُحقق ThinkPHP الرسمي

## رابط المشروع

https://github.com/top-think/think-validate

## التثبيت

`composer require topthink/think-validate`

## البداية السريعة

**إنشاء `app/index/validate/User.php`**

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
  
**الاستخدام**

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

> **ملاحظة**
> webman لا يدعم دالة `Validate::rule()` الخاصة بـ think-validate

<a name="respect-validation"></a>
# مُحقق workerman/validation

## الوصف

هذا المشروع نسخة محلية من https://github.com/Respect/Validation

## رابط المشروع

https://github.com/walkor/validation
  
  
## التثبيت
 
```php
composer require workerman/validation
```

## البداية السريعة

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
  
**الوصول عبر jQuery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
النتيجة:

`{"code":500,"msg":"Username may only contain letters (a-z) and numbers (0-9)"}`

التوضيح:

`v::input(array $input, array $rules)` يتحقق ويجمع البيانات. عند فشل التحقق، يرمي `Respect\Validation\Exceptions\ValidationException`؛ عند النجاح يُرجع البيانات المُحققة (مصفوفة).

إذا لم يلتقط كود الأعمال استثناء التحقق، سيلتقطه إطار webman ويُرجع JSON (مثل `{"code":500, "msg":"xxx"}`) أو صفحة استثناء عادية بناءً على رؤوس HTTP. إذا لم يلائم تنسيق الاستجابة احتياجاتك، يمكنك التقاط `ValidationException` وإرجاع بيانات مخصصة، كما في المثال أدناه:

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

## دليل المُحقق

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
  
## الفرق بين دوال المُحقق `validate()` `check()` `assert()`

`validate()` تُرجع قيمة منطقية، لا ترمي استثناءً

`check()` ترمي استثناءً عند فشل التحقق؛ احصل على سبب الفشل الأول عبر `$exception->getMessage()`

`assert()` ترمي استثناءً عند فشل التحقق؛ احصل على جميع أسباب الفشل عبر `$exception->getFullMessage()`
  
  
## قواعد التحقق الشائعة

`Alnum()` أحرف وأرقام فقط

`Alpha()` أحرف فقط

`ArrayType()` نوع مصفوفة

`Between(mixed $minimum, mixed $maximum)` يتحقق من أن الإدخال بين قيمتين.

`BoolType()` نوع منطقي

`Contains(mixed $expectedValue)` يتحقق من أن الإدخال يحتوي على قيمة معينة

`ContainsAny(array $needles)` يتحقق من أن الإدخال يحتوي على قيمة واحدة على الأقل من القيم المعرفة

`Digit()` يتحقق من أن الإدخال يحتوي على أرقام فقط

`Domain()` اسم نطاق صالح

`Email()` عنوان بريد إلكتروني صالح

`Extension(string $extension)` امتداد ملف

`FloatType()` نوع عشري

`IntType()` نوع عدد صحيح

`Ip()` عنوان IP

`Json()` بيانات JSON

`Length(int $min, int $max)` يتحقق من أن الطول ضمن النطاق

`LessThan(mixed $compareTo)` يتحقق من أن الطول أقل من القيمة المعطاة

`Lowercase()` أحرف صغيرة

`MacAddress()` عنوان MAC

`NotEmpty()` غير فارغ

`NullType()` null

`Number()` رقم

`ObjectType()` نوع كائن

`StringType()` نوع سلسلة

`Url()` رابط
  
راجع https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ لمزيد من قواعد التحقق.
  
## المزيد

زر https://respect-validation.readthedocs.io/en/2.0/
  
</div>
