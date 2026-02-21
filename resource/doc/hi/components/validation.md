# अन्य वैधीकरणकर्ता

कॉम्पोज़र में कई वैधीकरणकर्ता उपलब्ध हैं जिन्हें सीधे उपयोग किया जा सकता है, जैसे:

#### <a href="#webman-validation"> webman/validation (अनुशंसित)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# वैधीकरणकर्ता webman/validation

`illuminate/validation` पर आधारित, यह मैनुअल वैधीकरण, एनोटेशन वैधीकरण, पैरामीटर-स्तरीय वैधीकरण और पुन: प्रयोज्य नियम सेट प्रदान करता है।

## स्थापना

```bash
composer require webman/validation
```

## मूल अवधारणाएँ

- **Rule Set Reuse**: `support\validation\Validator` का विस्तार करके पुन: प्रयोज्य `rules`, `messages`, `attributes`, और `scenes` परिभाषित करें, जिन्हें मैनुअल और एनोटेशन दोनों वैधीकरण में पुन: उपयोग किया जा सकता है।
- **Method-Level Annotation (Attribute) Validation**: कंट्रोलर मेथड्स से वैधीकरण बाइंड करने के लिए PHP 8 एट्रिब्यूट `#[Validate]` का उपयोग करें।
- **Parameter-Level Annotation (Attribute) Validation**: कंट्रोलर मेथड पैरामीटर्स पर वैधीकरण बाइंड करने के लिए PHP 8 एट्रिब्यूट `#[Param]` का उपयोग करें।
- **Exception Handling**: वैधीकरण विफलता पर `support\validation\ValidationException` फेंकता है; अपवाद क्लास कॉन्फ़िगर करने योग्य है।
- **Database Validation**: यदि डेटाबेस वैधीकरण शामिल है, तो आपको `composer require webman/database` इंस्टॉल करने की आवश्यकता है।

## मैनुअल वैधीकरण

### मूल उपयोग

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **Note**
> `validate()` वैधीकरण विफल होने पर `support\validation\ValidationException` फेंकता है। यदि आप अपवाद नहीं फेंकना चाहते, तो त्रुटि संदेश प्राप्त करने के लिए नीचे दिए गए `fails()` तरीके का उपयोग करें।

### कस्टम संदेश और विशेषताएँ

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

### अपवाद के बिना वैधीकरण (त्रुटि संदेश प्राप्त करें)

यदि आप अपवाद नहीं फेंकना चाहते, तो जाँचने और `errors()` के माध्यम से त्रुटि संदेश प्राप्त करने के लिए `fails()` का उपयोग करें (यह `MessageBag` लौटाता है):

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

## Rule Set Reuse (कस्टम वैधीकरणकर्ता)

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

### मैनुअल वैधीकरण पुन: उपयोग

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### Scenes का उपयोग (वैकल्पिक)

`scenes` एक वैकल्पिक सुविधा है; यह केवल उन फ़ील्ड्स का सबसेट वैधीकरण करता है जब आप `withScene(...)` कॉल करते हैं।

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

## एनोटेशन वैधीकरण (Method-Level)

### प्रत्यक्ष नियम

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

### Rule Sets पुन: उपयोग

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

### एकाधिक वैधीकरण ओवरले

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

### वैधीकरण डेटा स्रोत

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

डेटा स्रोत निर्दिष्ट करने के लिए `in` पैरामीटर का उपयोग करें:

* **query** HTTP अनुरोध क्वेरी पैरामीटर, `$request->get()` से
* **body** HTTP अनुरोध बॉडी, `$request->post()` से
* **path** HTTP अनुरोध पथ पैरामीटर, `$request->route->param()` से

`in` एक स्ट्रिंग या ऐरे हो सकता है; जब यह ऐरे होता है, तो मान क्रम में मर्ज होते हैं जिसमें बाद के मान पहले वाले को ओवरराइड करते हैं। जब `in` पास नहीं किया जाता, तो यह डिफ़ॉल्ट रूप से `['query', 'body', 'path']` होता है।


## पैरामीटर-स्तरीय वैधीकरण (Param)

### मूल उपयोग

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

### वैधीकरण डेटा स्रोत

इसी तरह, पैरामीटर-स्तरीय वैधीकरण स्रोत निर्दिष्ट करने के लिए `in` पैरामीटर का समर्थन करता है:

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


### rules स्ट्रिंग या ऐरे का समर्थन करता है

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

### कस्टम संदेश / विशेषता

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

### Rule कॉन्स्टेंट पुन: उपयोग

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

## Method-Level + Parameter-Level संयुक्त

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

## स्वचालित Rule अनुमान (पैरामीटर सिग्नेचर के आधार पर)

जब किसी मेथड पर `#[Validate]` उपयोग किया जाता है, या उस मेथड का कोई पैरामीटर `#[Param]` उपयोग करता है, तो यह कंपोनेंट **मेथड पैरामीटर सिग्नेचर से मूल वैधीकरण नियमों को स्वचालित रूप से अनुमानित और पूर्ण करता है**, फिर वैधीकरण से पहले उन्हें मौजूदा नियमों के साथ मर्ज करता है।

### उदाहरण: `#[Validate]` समतुल्य विस्तार

1) बिना मैन्युअल रूप से नियम लिखे केवल `#[Validate]` सक्षम करें:

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

समतुल्य:

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

2) केवल आंशिक नियम लिखे गए, शेष पैरामीटर सिग्नेचर द्वारा पूर्ण:

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

समतुल्य:

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

3) डिफ़ॉल्ट मान / nullable प्रकार:

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

समतुल्य:

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

## अपवाद प्रबंधन

### डिफ़ॉल्ट अपवाद

वैधीकरण विफलता डिफ़ॉल्ट रूप से `support\validation\ValidationException` फेंकता है, जो `Webman\Exception\BusinessException` का विस्तार करता है और त्रुटियों को लॉग नहीं करता है।

डिफ़ॉल्ट प्रतिक्रिया व्यवहार `BusinessException::render()` द्वारा संभाला जाता है:

- नियमित अनुरोध: स्ट्रिंग संदेश लौटाता है, यानी `token is required.`
- JSON अनुरोध: JSON प्रतिक्रिया लौटाता है, यानी `{"code": 422, "msg": "token is required.", "data":....}`

### कस्टम अपवाद के माध्यम से हैंडलिंग कस्टमाइज़ करें

- ग्लोबल कॉन्फ़िग: `config/plugin/webman/validation/app.php` में `exception`

## बहुभाषी समर्थन

कंपोनेंट में अंतर्निहित चीनी और अंग्रेजी भाषा पैक शामिल हैं और प्रोजेक्ट ओवरराइड का समर्थन करता है। लोड क्रम:

1. प्रोजेक्ट भाषा पैक `resource/translations/{locale}/validation.php`
2. कंपोनेंट अंतर्निहित `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Illuminate अंतर्निहित अंग्रेजी (fallback)

> **Note**
> Webman डिफ़ॉल्ट भाषा `config/translation.php` में कॉन्फ़िगर की गई है, या `locale('en');` के माध्यम से बदली जा सकती है।

### स्थानीय ओवरराइड उदाहरण

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute is not a valid email format.',
];
```

## मिडलवेयर ऑटो-लोडिंग

स्थापना के बाद, कंपोनेंट `config/plugin/webman/validation/middleware.php` के माध्यम से वैधीकरण मिडलवेयर को स्वचालित रूप से लोड करता है; किसी मैनुअल पंजीकरण की आवश्यकता नहीं है।

## कमांड-लाइन जनरेशन

वैधीकरणकर्ता क्लास जनरेट करने के लिए `make:validator` कमांड का उपयोग करें (डिफ़ॉल्ट आउटपुट `app/validation` निर्देशिका में)।

> **Note**
> आवश्यकता: `composer require webman/console`

### मूल उपयोग

- **खाली टेम्पलेट जनरेट करें**

```bash
php webman make:validator UserValidator
```

- **मौजूदा फ़ाइल ओवरराइट करें**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### टेबल संरचना से नियम जनरेट करें

- **आधार नियम जनरेट करने के लिए टेबल नाम निर्दिष्ट करें** (फ़ील्ड प्रकार/nullable/लंबाई आदि से `$rules` अनुमानित करता है; डिफ़ॉल्ट रूप से ORM-संबंधित फ़ील्ड्स को बाहर करता है: laravel `created_at/updated_at/deleted_at` उपयोग करता है, thinkorm `create_time/update_time/delete_time` उपयोग करता है)

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **डेटाबेस कनेक्शन निर्दिष्ट करें** (मल्टी-कनेक्शन परिदृश्य)

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### Scenes

- **CRUD scenes जनरेट करें**: `create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> `update` scene में प्राथमिक कुंजी फ़ील्ड (रिकॉर्ड लोकेट करने के लिए) प्लस अन्य फ़ील्ड शामिल हैं; `delete/detail` डिफ़ॉल्ट रूप से केवल प्राथमिक कुंजी शामिल करते हैं।

### ORM चयन (laravel (illuminate/database) बनाम think-orm)

- **ऑटो-चयन (डिफ़ॉल्ट)**: जो भी इंस्टॉल/कॉन्फ़िगर है उसका उपयोग करता है; जब दोनों मौजूद हों, तो डिफ़ॉल्ट रूप से illuminate उपयोग करता है
- **फोर्स निर्दिष्ट करें**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### पूर्ण उदाहरण

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## यूनिट टेस्ट

`webman/validation` रूट निर्देशिका से चलाएं:

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## वैधीकरण नियम संदर्भ

<a name="available-validation-rules"></a>
## उपलब्ध वैधीकरण नियम

> [!IMPORTANT]
> - Webman Validation `illuminate/validation` पर आधारित है; नियम नाम Laravel से मेल खाते हैं और कोई Webman-विशिष्ट नियम नहीं हैं।
> - मिडलवेयर डिफ़ॉल्ट रूप से `$request->all()` (GET+POST) से डेटा को रूट पैरामीटर्स के साथ मर्ज करके वैधीकरण करता है, अपलोड की गई फ़ाइलों को छोड़कर; फ़ाइल नियमों के लिए, डेटा में स्वयं `$request->file()` मर्ज करें, या मैन्युअल रूप से `Validator::make` कॉल करें।
> - `current_password` auth guard पर निर्भर करता है; `exists`/`unique` डेटाबेस कनेक्शन और क्वेरी बिल्डर पर निर्भर करते हैं; ये नियम संबंधित कंपोनेंट एकीकृत न होने पर अनुपलब्ध हैं।

निम्नलिखित सभी उपलब्ध वैधीकरण नियमों और उनके उद्देश्यों की सूची है:

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

फ़ील्ड `"yes"`, `"on"`, `1`, `"1"`, `true`, या `"true"` होनी चाहिए। आमतौर पर सेवा की शर्तों के लिए उपयोगकर्ता समझौते को सत्यापित करने जैसे परिदृश्यों में उपयोग किया जाता है।

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

जब दूसरी फ़ील्ड निर्दिष्ट मान के बराबर हो, तो फ़ील्ड `"yes"`, `"on"`, `1`, `"1"`, `true`, या `"true"` होनी चाहिए। आमतौर पर सशर्त समझौता परिदृश्यों में उपयोग किया जाता है।

<a name="rule-active-url"></a>
#### active_url

फ़ील्ड में वैध A या AAAA रिकॉर्ड होना चाहिए। यह नियम पहले URL होस्टनाम निकालने के लिए `parse_url` उपयोग करता है, फिर `dns_get_record` के साथ वैधीकरण करता है।

<a name="rule-after"></a>
#### after:_date_

फ़ील्ड दी गई तारीख के बाद का मान होना चाहिए। तारीख को वैध `DateTime` में बदलने के लिए `strtotime` को पास किया जाता है:

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

तुलना के लिए आप दूसरा फ़ील्ड नाम भी पास कर सकते हैं:

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

आप fluent `date` rule बिल्डर उपयोग कर सकते हैं:

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

`afterToday` और `todayOrAfter` सुविधाजनक रूप से "आज के बाद होना चाहिए" या "आज या बाद में होना चाहिए" व्यक्त करते हैं:

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

फ़ील्ड दी गई तारीख पर या उसके बाद होनी चाहिए। अधिक विवरण के लिए [after](#rule-after) देखें।

आप fluent `date` rule बिल्डर उपयोग कर सकते हैं:

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

`Rule::anyOf` "किसी एक नियम सेट को संतुष्ट करें" निर्दिष्ट करने की अनुमति देता है। उदाहरण के लिए, निम्नलिखित नियम का अर्थ है कि `username` या तो ईमेल पता होना चाहिए या कम से कम 6 अक्षरों का अल्फ़ान्यूमेरिक/अंडरस्कोर/डैश स्ट्रिंग:

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

फ़ील्ड Unicode अक्षर होने चाहिए ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) और [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=))।

केवल ASCII (`a-z`, `A-Z`) की अनुमति देने के लिए, `ascii` विकल्प जोड़ें:

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

फ़ील्ड में केवल Unicode अक्षर और संख्याएँ हो सकती हैं ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)), प्लस ASCII हाइफ़न (`-`) और अंडरस्कोर (`_`)।

केवल ASCII (`a-z`, `A-Z`, `0-9`) की अनुमति देने के लिए, `ascii` विकल्प जोड़ें:

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

फ़ील्ड में केवल Unicode अक्षर और संख्याएँ हो सकती हैं ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=))।

केवल ASCII (`a-z`, `A-Z`, `0-9`) की अनुमति देने के लिए, `ascii` विकल्प जोड़ें:

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

फ़ील्ड PHP `array` होनी चाहिए।

जब `array` नियम में अतिरिक्त पैरामीटर हों, तो इनपुट ऐरे कुंजियाँ पैरामीटर सूची में होनी चाहिए। उदाहरण में, `admin` कुंजी अनुमत सूची में नहीं है, इसलिए अमान्य है:

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

वास्तविक परियोजनाओं में अनुमत ऐरे कुंजियाँ स्पष्ट रूप से परिभाषित करने की अनुशंसा की जाती है।

<a name="rule-ascii"></a>
#### ascii

फ़ील्ड में केवल 7-बिट ASCII अक्षर हो सकते हैं।

<a name="rule-bail"></a>
#### bail

पहला नियम विफल होने पर फ़ील्ड के लिए आगे के नियमों का वैधीकरण रोकें।

यह नियम केवल वर्तमान फ़ील्ड को प्रभावित करता है। "ग्लोबली पहली विफलता पर रोकें" के लिए, Illuminate के वैधीकरणकर्ता को सीधे उपयोग करें और `stopOnFirstFailure()` कॉल करें।

<a name="rule-before"></a>
#### before:_date_

फ़ील्ड दी गई तारीख से पहले होनी चाहिए। तारीख को वैध `DateTime` में बदलने के लिए `strtotime` को पास किया जाता है। [after](#rule-after) की तरह, तुलना के लिए आप दूसरा फ़ील्ड नाम पास कर सकते हैं।

आप fluent `date` rule बिल्डर उपयोग कर सकते हैं:

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

`beforeToday` और `todayOrBefore` सुविधाजनक रूप से "आज से पहले होना चाहिए" या "आज या बाद में होना चाहिए" व्यक्त करते हैं:

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

फ़ील्ड दी गई तारीख पर या उससे पहले होनी चाहिए। तारीख को वैध `DateTime` में बदलने के लिए `strtotime` को पास किया जाता है। [after](#rule-after) की तरह, तुलना के लिए आप दूसरा फ़ील्ड नाम पास कर सकते हैं।

आप fluent `date` rule बिल्डर उपयोग कर सकते हैं:

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

फ़ील्ड का आकार _min_ और _max_ के बीच (समावेशी) होना चाहिए। स्ट्रिंग, संख्या, ऐरे और फ़ाइलों के लिए मूल्यांकन [size](#rule-size) के समान है।

<a name="rule-boolean"></a>
#### boolean

फ़ील्ड boolean में परिवर्तनीय होनी चाहिए। स्वीकार्य इनपुट में `true`, `false`, `1`, `0`, `"1"`, `"0"` शामिल हैं।

केवल `true` या `false` की अनुमति देने के लिए `strict` पैरामीटर उपयोग करें:

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

फ़ील्ड में मिलान वाला `{field}_confirmation` फ़ील्ड होना चाहिए। उदाहरण के लिए, जब फ़ील्ड `password` हो, तो `password_confirmation` आवश्यक है।

आप कस्टम पुष्टिकरण फ़ील्ड नाम भी निर्दिष्ट कर सकते हैं, उदा. `confirmed:repeat_username` के लिए `repeat_username` को वर्तमान फ़ील्ड से मेल खाना चाहिए।

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

फ़ील्ड ऐरे होनी चाहिए और सभी दिए गए पैरामीटर मानों को शामिल करनी चाहिए। यह नियम आमतौर पर ऐरे वैधीकरण के लिए उपयोग किया जाता है; आप इसे बनाने के लिए `Rule::contains` उपयोग कर सकते हैं:

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

फ़ील्ड ऐरे होनी चाहिए और दिए गए पैरामीटर मानों में से किसी को शामिल नहीं करनी चाहिए। आप इसे बनाने के लिए `Rule::doesntContain` उपयोग कर सकते हैं:

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

फ़ील्ड वर्तमान प्रमाणित उपयोगकर्ता के पासवर्ड से मेल खाना चाहिए। आप पहले पैरामीटर के रूप में auth guard निर्दिष्ट कर सकते हैं:

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> यह नियम auth कंपोनेंट और guard कॉन्फ़िगरेशन पर निर्भर करता है; auth एकीकृत न होने पर उपयोग न करें।

<a name="rule-date"></a>
#### date

फ़ील्ड `strtotime` द्वारा पहचानी जाने वाली वैध (गैर-सापेक्ष) तारीख होनी चाहिए।

<a name="rule-date-equals"></a>
#### date_equals:_date_

फ़ील्ड दी गई तारीख के बराबर होनी चाहिए। तारीख को वैध `DateTime` में बदलने के लिए `strtotime` को पास किया जाता है।

<a name="rule-date-format"></a>
#### date_format:_format_,...

फ़ील्ड दिए गए प्रारूपों में से एक से मेल खाना चाहिए। `date` या `date_format` में से किसी एक का उपयोग करें। यह नियम सभी PHP [DateTime](https://www.php.net/manual/en/class.datetime.php) प्रारूपों का समर्थन करता है।

आप fluent `date` rule बिल्डर उपयोग कर सकते हैं:

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

फ़ील्ड आवश्यक दशमलव स्थानों के साथ संख्यात्मक होनी चाहिए:

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

फ़ील्ड `"no"`, `"off"`, `0`, `"0"`, `false`, या `"false"` होनी चाहिए।

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

जब दूसरी फ़ील्ड निर्दिष्ट मान के बराबर हो, तो फ़ील्ड `"no"`, `"off"`, `0`, `"0"`, `false`, या `"false"` होनी चाहिए।

<a name="rule-different"></a>
#### different:_field_

फ़ील्ड _field_ से भिन्न होनी चाहिए।

<a name="rule-digits"></a>
#### digits:_value_

फ़ील्ड लंबाई _value_ के साथ पूर्णांक होनी चाहिए।

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

फ़ील्ड लंबाई _min_ और _max_ के बीच के साथ पूर्णांक होनी चाहिए।

<a name="rule-dimensions"></a>
#### dimensions

फ़ील्ड छवि होनी चाहिए और आयाम बाधाओं को संतुष्ट करनी चाहिए:

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

उपलब्ध बाधाएँ: _min\_width_, _max\_width_, _min\_height_, _max\_height_, _width_, _height_, _ratio_।

_ratio_ पहलू अनुपात है; इसे भिन्न या फ्लोट के रूप में व्यक्त किया जा सकता है:

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

इस नियम में कई पैरामीटर हैं; इसे बनाने के लिए `Rule::dimensions` उपयोग करने की अनुशंसा की जाती है:

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

ऐरे वैधीकरण करते समय, फ़ील्ड मान डुप्लिकेट नहीं होने चाहिए:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

डिफ़ॉल्ट रूप से ढीली तुलना उपयोग करता है। सख्त तुलना के लिए `strict` जोड़ें:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

केस अंतर को अनदेखा करने के लिए `ignore_case` जोड़ें:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

फ़ील्ड निर्दिष्ट मानों में से किसी से शुरू नहीं होनी चाहिए।

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

फ़ील्ड निर्दिष्ट मानों में से किसी से समाप्त नहीं होनी चाहिए।

<a name="rule-email"></a>
#### email

फ़ील्ड वैध ईमेल पता होनी चाहिए। यह नियम [egulias/email-validator](https://github.com/egulias/EmailValidator) पर निर्भर करता है, डिफ़ॉल्ट रूप से `RFCValidation` उपयोग करता है, और अन्य वैधीकरण विधियों का उपयोग कर सकता है:

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

उपलब्ध वैधीकरण विधियाँ:

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` - RFC स्पेक के अनुसार ईमेल वैधीकरण ([supported RFCs](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs))।
- `strict`: `NoRFCWarningsValidation` - RFC चेतावनियों पर विफल (जैसे ट्रेलिंग डॉट या लगातार डॉट्स)।
- `dns`: `DNSCheckValidation` - जाँचें कि डोमेन में वैध MX रिकॉर्ड हैं।
- `spoof`: `SpoofCheckValidation` - होमोग्राफ या स्पूफिंग Unicode अक्षरों को रोकें।
- `filter`: `FilterEmailValidation` - PHP `filter_var` उपयोग करके वैधीकरण।
- `filter_unicode`: `FilterEmailValidation::unicode()` - Unicode की अनुमति देने वाला `filter_var` वैधीकरण।

</div>

आप fluent rule बिल्डर उपयोग कर सकते हैं:

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
> `dns` और `spoof` के लिए PHP `intl` एक्सटेंशन आवश्यक है।

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

फ़ील्ड निर्दिष्ट कैरेक्टर एन्कोडिंग से मेल खानी चाहिए। यह नियम फ़ाइल या स्ट्रिंग एन्कोडिंग का पता लगाने के लिए `mb_check_encoding` उपयोग करता है। फ़ाइल rule बिल्डर के साथ उपयोग किया जा सकता है:

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

फ़ील्ड निर्दिष्ट मानों में से किसी से समाप्त होनी चाहिए।

<a name="rule-enum"></a>
#### enum

`Enum` एक क्लास-आधारित नियम है जो यह वैधीकरण करता है कि फ़ील्ड मान वैध enum मान है। निर्माण करते समय enum क्लास नाम पास करें। आदिम मानों के लिए, Backed Enum उपयोग करें:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

enum मानों को प्रतिबंधित करने के लिए `only`/`except` उपयोग करें:

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

सशर्त प्रतिबंधों के लिए `when` उपयोग करें:

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

फ़ील्ड `validate`/`validated` द्वारा लौटाए गए डेटा से बाहर रखी जाएगी।

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

जब _anotherfield_ _value_ के बराबर हो, तो फ़ील्ड `validate`/`validated` द्वारा लौटाए गए डेटा से बाहर रखी जाएगी।

जटिल शर्तों के लिए, `Rule::excludeIf` उपयोग करें:

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

जब तक _anotherfield_ _value_ के बराबर न हो, फ़ील्ड `validate`/`validated` द्वारा लौटाए गए डेटा से बाहर रखी जाएगी। यदि _value_ `null` है (जैसे `exclude_unless:name,null`), तो फ़ील्ड केवल तभी रखी जाती है जब तुलना फ़ील्ड `null` या अनुपस्थित हो।

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

जब _anotherfield_ मौजूद हो, तो फ़ील्ड `validate`/`validated` द्वारा लौटाए गए डेटा से बाहर रखी जाएगी।

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

जब _anotherfield_ मौजूद न हो, तो फ़ील्ड `validate`/`validated` द्वारा लौटाए गए डेटा से बाहर रखी जाएगी।

<a name="rule-exists"></a>
#### exists:_table_,_column_

फ़ील्ड निर्दिष्ट डेटाबेस टेबल में मौजूद होनी चाहिए।

<a name="basic-usage-of-exists-rule"></a>
#### Exists नियम का मूल उपयोग

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

जब `column` निर्दिष्ट नहीं है, तो डिफ़ॉल्ट रूप से फ़ील्ड नाम उपयोग किया जाता है। तो यह उदाहरण वैधीकरण करता है कि `state` कॉलम `states` टेबल में मौजूद है या नहीं।

<a name="specifying-a-custom-column-name"></a>
#### कस्टम कॉलम नाम निर्दिष्ट करना

टेबल नाम के बाद कॉलम नाम जोड़ें:

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

डेटाबेस कनेक्शन निर्दिष्ट करने के लिए, कनेक्शन नाम के साथ टेबल नाम उपसर्ग करें:

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

आप मॉडल क्लास नाम भी पास कर सकते हैं; फ्रेमवर्क टेबल नाम हल करेगा:

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

कस्टम क्वेरी शर्तों के लिए, `Rule` बिल्डर उपयोग करें:

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

आप `Rule::exists` में सीधे कॉलम नाम भी निर्दिष्ट कर सकते हैं:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'state' => [Rule::exists('states', 'abbreviation')],
])->validate();
```

मानों के सेट के अस्तित्व को वैधीकरण करने के लिए, `array` नियम के साथ संयोजित करें:

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

जब `array` और `exists` दोनों मौजूद हों, तो एकल क्वेरी सभी मानों को वैधीकरण करती है।

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

वैधीकरण करता है कि अपलोड की गई फ़ाइल का एक्सटेंशन अनुमत सूची में है:

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> फ़ाइल प्रकार वैधीकरण के लिए केवल एक्सटेंशन पर भरोसा न करें; [mimes](#rule-mimes) या [mimetypes](#rule-mimetypes) के साथ उपयोग करें।

<a name="rule-file"></a>
#### file

फ़ील्ड सफलतापूर्वक अपलोड की गई फ़ाइल होनी चाहिए।

<a name="rule-filled"></a>
#### filled

जब फ़ील्ड मौजूद हो, तो उसका मान खाली नहीं होना चाहिए।

<a name="rule-gt"></a>
#### gt:_field_

फ़ील्ड दिए गए _field_ या _value_ से बड़ी होनी चाहिए। दोनों फ़ील्डों का प्रकार समान होना चाहिए। स्ट्रिंग्स, संख्याओं, ऐरे और फ़ाइलों के लिए मूल्यांकन [size](#rule-size) के समान है।

<a name="rule-gte"></a>
#### gte:_field_

फ़ील्ड दिए गए _field_ या _value_ से बड़ी या बराबर होनी चाहिए। दोनों फ़ील्डों का प्रकार समान होना चाहिए। स्ट्रिंग्स, संख्याओं, ऐरे और फ़ाइलों के लिए मूल्यांकन [size](#rule-size) के समान है।

<a name="rule-hex-color"></a>
#### hex_color

फ़ील्ड वैध [hex color value](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color) होनी चाहिए।

<a name="rule-image"></a>
#### image

फ़ील्ड छवि होनी चाहिए (jpg, jpeg, png, bmp, gif, या webp)।

> [!WARNING]
> XSS जोखिम के कारण SVG डिफ़ॉल्ट रूप से अनुमत नहीं है। इसे अनुमति देने के लिए, `allow_svg` जोड़ें: `image:allow_svg`।

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

फ़ील्ड दिए गए मान सूची में होनी चाहिए। आप निर्माण के लिए `Rule::in` उपयोग कर सकते हैं:

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

`array` नियम के साथ संयोजित करने पर, इनपुट ऐरे में प्रत्येक मान `in` सूची में होना चाहिए:

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

फ़ील्ड _anotherfield_ के मान सूची में मौजूद होनी चाहिए।

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

फ़ील्ड ऐरे होनी चाहिए और दिए गए मानों में से कम से कम एक को कुंजी के रूप में शामिल करनी चाहिए:

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

फ़ील्ड पूर्णांक होनी चाहिए।

फ़ील्ड प्रकार को पूर्णांक की आवश्यकता के लिए `strict` पैरामीटर उपयोग करें; स्ट्रिंग पूर्णांक अमान्य होंगे:

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> यह नियम केवल यह वैधीकरण करता है कि क्या यह PHP के `FILTER_VALIDATE_INT` पास करता है; सख्त संख्यात्मक प्रकारों के लिए, [numeric](#rule-numeric) के साथ उपयोग करें।

<a name="rule-ip"></a>
#### ip

फ़ील्ड वैध IP पता होना चाहिए।

<a name="rule-ipv4"></a>
#### ipv4

फ़ील्ड वैध IPv4 पता होना चाहिए।

<a name="rule-ipv6"></a>
#### ipv6

फ़ील्ड वैध IPv6 पता होना चाहिए।

<a name="rule-json"></a>
#### json

फ़ील्ड वैध JSON स्ट्रिंग होनी चाहिए।

<a name="rule-lt"></a>
#### lt:_field_

फ़ील्ड दिए गए _field_ से छोटी होनी चाहिए। दोनों फ़ील्डों का प्रकार समान होना चाहिए। स्ट्रिंग्स, संख्याओं, ऐरे और फ़ाइलों के लिए मूल्यांकन [size](#rule-size) के समान है।

<a name="rule-lte"></a>
#### lte:_field_

फ़ील्ड दिए गए _field_ से छोटी या बराबर होनी चाहिए। दोनों फ़ील्डों का प्रकार समान होना चाहिए। स्ट्रिंग्स, संख्याओं, ऐरे और फ़ाइलों के लिए मूल्यांकन [size](#rule-size) के समान है।

<a name="rule-lowercase"></a>
#### lowercase

फ़ील्ड लोअरकेस होनी चाहिए।

<a name="rule-list"></a>
#### list

फ़ील्ड सूची ऐरे होनी चाहिए। सूची ऐरे कुंजियाँ 0 से `count($array) - 1` तक लगातार संख्याएँ होनी चाहिए।

<a name="rule-mac"></a>
#### mac_address

फ़ील्ड वैध MAC पता होना चाहिए।

<a name="rule-max"></a>
#### max:_value_

फ़ील्ड _value_ से छोटी या बराबर होनी चाहिए। स्ट्रिंग्स, संख्याओं, ऐरे और फ़ाइलों के लिए मूल्यांकन [size](#rule-size) के समान है।

<a name="rule-max-digits"></a>
#### max_digits:_value_

फ़ील्ड लंबाई _value_ से अधिक न होने वाला पूर्णांक होनी चाहिए।

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

वैधीकरण करता है कि फ़ाइल का MIME प्रकार सूची में है:

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

MIME प्रकार फ़ाइल सामग्री पढ़कर अनुमान लगाया जाता है और क्लाइंट-प्रदत्त MIME से भिन्न हो सकता है।

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

वैधीकरण करता है कि फ़ाइल का MIME प्रकार दिए गए एक्सटेंशन से मेल खाता है:

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

हालाँकि पैरामीटर एक्सटेंशन हैं, यह नियम MIME निर्धारित करने के लिए फ़ाइल सामग्री पढ़ता है। एक्सटेंशन-से-MIME मैपिंग:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### MIME प्रकार और एक्सटेंशन

यह नियम यह वैधीकरण नहीं करता कि "फ़ाइल एक्सटेंशन" "वास्तविक MIME" से मेल खाता है। उदाहरण के लिए, `mimes:png` PNG सामग्री वाली `photo.txt` को वैध मानता है। एक्सटेंशन वैधीकरण के लिए, [extensions](#rule-extensions) उपयोग करें।

<a name="rule-min"></a>
#### min:_value_

फ़ील्ड _value_ से बड़ी या बराबर होनी चाहिए। स्ट्रिंग्स, संख्याओं, ऐरे और फ़ाइलों के लिए मूल्यांकन [size](#rule-size) के समान है।

<a name="rule-min-digits"></a>
#### min_digits:_value_

फ़ील्ड लंबाई _value_ से कम न होने वाला पूर्णांक होनी चाहिए।

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

फ़ील्ड _value_ का गुणज होनी चाहिए।

<a name="rule-missing"></a>
#### missing

फ़ील्ड इनपुट डेटा में मौजूद नहीं होनी चाहिए।

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

जब _anotherfield_ किसी _value_ के बराबर हो, तो फ़ील्ड मौजूद नहीं होनी चाहिए।

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

जब तक _anotherfield_ किसी _value_ के बराबर न हो, फ़ील्ड मौजूद नहीं होनी चाहिए।

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

जब कोई निर्दिष्ट फ़ील्ड मौजूद हो, तो फ़ील्ड मौजूद नहीं होनी चाहिए।

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

जब सभी निर्दिष्ट फ़ील्ड मौजूद हों, तो फ़ील्ड मौजूद नहीं होनी चाहिए।

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

फ़ील्ड दिए गए मान सूची में नहीं होनी चाहिए। आप निर्माण के लिए `Rule::notIn` उपयोग कर सकते हैं:

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

फ़ील्ड दिए गए रेगुलर एक्सप्रेशन से मेल नहीं खानी चाहिए।

यह नियम PHP `preg_match` उपयोग करता है। रेगेक्स में डिलीमिटर होने चाहिए, जैसे `'email' => 'not_regex:/^.+$/i'`।

> [!WARNING]
> `regex`/`not_regex` उपयोग करते समय, यदि रेगेक्स में `|` शामिल है, तो `|` विभाजक के साथ संघर्ष से बचने के लिए ऐरे रूप उपयोग करें।

<a name="rule-nullable"></a>
#### nullable

फ़ील्ड `null` हो सकती है।

<a name="rule-numeric"></a>
#### numeric

फ़ील्ड [numeric](https://www.php.net/manual/en/function.is-numeric.php) होनी चाहिए।

केवल पूर्णांक या फ्लोट प्रकारों की अनुमति के लिए `strict` पैरामीटर उपयोग करें; संख्यात्मक स्ट्रिंग अमान्य होंगी:

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

फ़ील्ड इनपुट डेटा में मौजूद होनी चाहिए।

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

जब _anotherfield_ किसी _value_ के बराबर हो, तो फ़ील्ड मौजूद होनी चाहिए।

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

जब तक _anotherfield_ किसी _value_ के बराबर न हो, फ़ील्ड मौजूद होनी चाहिए।

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

जब कोई निर्दिष्ट फ़ील्ड मौजूद हो, तो फ़ील्ड मौजूद होनी चाहिए।

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

जब सभी निर्दिष्ट फ़ील्ड मौजूद हों, तो फ़ील्ड मौजूद होनी चाहिए।

<a name="rule-prohibited"></a>
#### prohibited

फ़ील्ड अनुपस्थित या खाली होनी चाहिए। "खाली" का अर्थ है:

<div class="content-list" markdown="1">

- मान `null` है।
- मान खाली स्ट्रिंग है।
- मान खाली ऐरे या खाली `Countable` ऑब्जेक्ट है।
- खाली पथ वाली अपलोड की गई फ़ाइल।

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

जब _anotherfield_ किसी _value_ के बराबर हो, तो फ़ील्ड अनुपस्थित या खाली होनी चाहिए। "खाली" का अर्थ है:

<div class="content-list" markdown="1">

- मान `null` है।
- मान खाली स्ट्रिंग है।
- मान खाली ऐरे या खाली `Countable` ऑब्जेक्ट है।
- खाली पथ वाली अपलोड की गई फ़ाइल।

</div>

जटिल शर्तों के लिए, `Rule::prohibitedIf` उपयोग करें:

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

जब _anotherfield_ `"yes"`, `"on"`, `1`, `"1"`, `true`, या `"true"` हो, तो फ़ील्ड अनुपस्थित या खाली होनी चाहिए।

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

जब _anotherfield_ `"no"`, `"off"`, `0`, `"0"`, `false`, या `"false"` हो, तो फ़ील्ड अनुपस्थित या खाली होनी चाहिए।

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

जब तक _anotherfield_ किसी _value_ के बराबर न हो, फ़ील्ड अनुपस्थित या खाली होनी चाहिए। "खाली" का अर्थ है:

<div class="content-list" markdown="1">

- मान `null` है।
- मान खाली स्ट्रिंग है।
- मान खाली ऐरे या खाली `Countable` ऑब्जेक्ट है।
- खाली पथ वाली अपलोड की गई फ़ाइल।

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

जब फ़ील्ड मौजूद हो और खाली न हो, तो _anotherfield_ में सभी फ़ील्ड अनुपस्थित या खाली होनी चाहिए। "खाली" का अर्थ है:

<div class="content-list" markdown="1">

- मान `null` है।
- मान खाली स्ट्रिंग है।
- मान खाली ऐरे या खाली `Countable` ऑब्जेक्ट है।
- खाली पथ वाली अपलोड की गई फ़ाइल।

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

फ़ील्ड दिए गए रेगुलर एक्सप्रेशन से मेल खानी चाहिए।

यह नियम PHP `preg_match` उपयोग करता है। रेगेक्स में डिलीमिटर होने चाहिए, जैसे `'email' => 'regex:/^.+@.+$/i'`।

> [!WARNING]
> `regex`/`not_regex` उपयोग करते समय, यदि रेगेक्स में `|` शामिल है, तो `|` विभाजक के साथ संघर्ष से बचने के लिए ऐरे रूप उपयोग करें।

<a name="rule-required"></a>
#### required

फ़ील्ड मौजूद होनी चाहिए और खाली नहीं होनी चाहिए। "खाली" का अर्थ है:

<div class="content-list" markdown="1">

- मान `null` है।
- मान खाली स्ट्रिंग है।
- मान खाली ऐरे या खाली `Countable` ऑब्जेक्ट है।
- खाली पथ वाली अपलोड की गई फ़ाइल।

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

जब _anotherfield_ किसी _value_ के बराबर हो, तो फ़ील्ड मौजूद होनी चाहिए और खाली नहीं होनी चाहिए।

जटिल शर्तों के लिए, `Rule::requiredIf` उपयोग करें:

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

जब _anotherfield_ `"yes"`, `"on"`, `1`, `"1"`, `true`, या `"true"` हो, तो फ़ील्ड मौजूद होनी चाहिए और खाली नहीं होनी चाहिए।

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

जब _anotherfield_ `"no"`, `"off"`, `0`, `"0"`, `false`, या `"false"` हो, तो फ़ील्ड मौजूद होनी चाहिए और खाली नहीं होनी चाहिए।

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

जब तक _anotherfield_ किसी _value_ के बराबर न हो, फ़ील्ड मौजूद होनी चाहिए और खाली नहीं होनी चाहिए। यदि _value_ `null` है (जैसे `required_unless:name,null`), तो फ़ील्ड केवल तभी खाली हो सकती है जब तुलना फ़ील्ड `null` या अनुपस्थित हो।

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

जब कोई निर्दिष्ट फ़ील्ड मौजूद हो और खाली न हो, तो फ़ील्ड मौजूद होनी चाहिए और खाली नहीं होनी चाहिए।

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

जब सभी निर्दिष्ट फ़ील्ड मौजूद हों और खाली न हों, तो फ़ील्ड मौजूद होनी चाहिए और खाली नहीं होनी चाहिए।

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

जब कोई निर्दिष्ट फ़ील्ड खाली या अनुपस्थित हो, तो फ़ील्ड मौजूद होनी चाहिए और खाली नहीं होनी चाहिए।

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

जब सभी निर्दिष्ट फ़ील्ड खाली या अनुपस्थित हों, तो फ़ील्ड मौजूद होनी चाहिए और खाली नहीं होनी चाहिए।

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

फ़ील्ड ऐरे होनी चाहिए और कम से कम निर्दिष्ट कुंजियाँ शामिल करनी चाहिए।

<a name="validating-when-present"></a>
#### sometimes

केवल तभी बाद के वैधीकरण नियम लागू करें जब फ़ील्ड मौजूद हो। आमतौर पर "वैकल्पिक लेकिन मौजूद होने पर वैध होना चाहिए" फ़ील्डों के लिए उपयोग किया जाता है:

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

फ़ील्ड _field_ के समान होनी चाहिए।

<a name="rule-size"></a>
#### size:_value_

फ़ील्ड का आकार दिए गए _value_ के बराबर होना चाहिए। स्ट्रिंग्स के लिए: कैरेक्टर गिनती; संख्याओं के लिए: निर्दिष्ट पूर्णांक (`numeric` या `integer` के साथ उपयोग करें); ऐरे के लिए: तत्व गिनती; फ़ाइलों के लिए: KB में आकार। उदाहरण:

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

फ़ील्ड निर्दिष्ट मानों में से किसी से शुरू होनी चाहिए।

<a name="rule-string"></a>
#### string

फ़ील्ड स्ट्रिंग होनी चाहिए। `null` की अनुमति देने के लिए, `nullable` के साथ उपयोग करें।

<a name="rule-timezone"></a>
#### timezone

फ़ील्ड वैध टाइमज़ोन पहचानकर्ता होना चाहिए (`DateTimeZone::listIdentifiers` से)। आप उस विधि द्वारा समर्थित पैरामीटर पास कर सकते हैं:

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

फ़ील्ड निर्दिष्ट टेबल में अद्वितीय होनी चाहिए।

**कस्टम टेबल/कॉलम नाम निर्दिष्ट करें:**

आप सीधे मॉडल क्लास नाम निर्दिष्ट कर सकते हैं:

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

आप कॉलम नाम निर्दिष्ट कर सकते हैं (निर्दिष्ट न होने पर डिफ़ॉल्ट रूप से फ़ील्ड नाम उपयोग किया जाता है):

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**डेटाबेस कनेक्शन निर्दिष्ट करें:**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**निर्दिष्ट ID को अनदेखा करें:**

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
> `ignore` को उपयोगकर्ता इनपुट प्राप्त नहीं करना चाहिए; केवल सिस्टम-जनित अद्वितीय ID (ऑटो-इंक्रीमेंट ID या मॉडल UUID) उपयोग करें, अन्यथा SQL इंजेक्शन जोखिम हो सकता है।

आप मॉडल इंस्टेंस भी पास कर सकते हैं:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

यदि प्राथमिक कुंजी `id` नहीं है, तो प्राथमिक कुंजी नाम निर्दिष्ट करें:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

डिफ़ॉल्ट रूप से अद्वितीय कॉलम के रूप में फ़ील्ड नाम उपयोग करता है; आप कॉलम नाम भी निर्दिष्ट कर सकते हैं:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**अतिरिक्त शर्तें जोड़ें:**

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

**सॉफ्ट-डिलीटेड रिकॉर्ड अनदेखा करें:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

यदि सॉफ्ट डिलीट कॉलम `deleted_at` नहीं है:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

फ़ील्ड अपरकेस होनी चाहिए।

<a name="rule-url"></a>
#### url

फ़ील्ड वैध URL होनी चाहिए।

आप अनुमत प्रोटोकॉल निर्दिष्ट कर सकते हैं:

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

फ़ील्ड वैध [ULID](https://github.com/ulid/spec) होनी चाहिए।

<a name="rule-uuid"></a>
#### uuid

फ़ील्ड वैध RFC 9562 UUID होनी चाहिए (संस्करण 1, 3, 4, 5, 6, 7, या 8)।

आप संस्करण निर्दिष्ट कर सकते हैं:

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```



<a name="think-validate"></a>
# वैधीकरणकर्ता top-think/think-validate

## विवरण

आधिकारिक ThinkPHP वैधीकरणकर्ता

## प्रोजेक्ट URL

https://github.com/top-think/think-validate

## स्थापना

`composer require topthink/think-validate`

## त्वरित प्रारंभ

**`app/index/validate/User.php` बनाएं**

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
  
**उपयोग**

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
> webman think-validate की `Validate::rule()` विधि का समर्थन नहीं करता है

<a name="respect-validation"></a>
# वैधीकरणकर्ता workerman/validation

## विवरण

यह प्रोजेक्ट https://github.com/Respect/Validation का स्थानीयकृत संस्करण है

## प्रोजेक्ट URL

https://github.com/walkor/validation
  
  
## स्थापना
 
```php
composer require workerman/validation
```

## त्वरित प्रारंभ

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
  
**jQuery के माध्यम से एक्सेस करें**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
परिणाम:

`{"code":500,"msg":"Username may only contain letters (a-z) and numbers (0-9)"}`

व्याख्या:

`v::input(array $input, array $rules)` डेटा को वैधीकरण और एकत्र करता है। यदि वैधीकरण विफल होता है, तो यह `Respect\Validation\Exceptions\ValidationException` फेंकता है; सफलता पर यह वैधीकृत डेटा (ऐरे) लौटाता है।

यदि व्यावसायिक कोड वैधीकरण अपवाद को कैच नहीं करता है, तो webman फ्रेमवर्क इसे कैच करेगा और HTTP हेडर के आधार पर JSON (जैसे `{"code":500, "msg":"xxx"}`) या सामान्य अपवाद पृष्ठ लौटाएगा। यदि प्रतिक्रिया प्रारूप आपकी आवश्यकताओं को पूरा नहीं करता है, तो आप `ValidationException` को कैच कर सकते हैं और नीचे दिए गए उदाहरण की तरह कस्टम डेटा लौटा सकते हैं:

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

## वैधीकरणकर्ता गाइड

```php
use Respect\Validation\Validator as v;

// एकल नियम वैधीकरण
$number = 123;
v::numericVal()->validate($number); // true

// श्रृंखलित वैधीकरण
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// पहला वैधीकरण विफलता कारण प्राप्त करें
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username may only contain letters (a-z) and numbers (0-9)
}

// सभी वैधीकरण विफलता कारण प्राप्त करें
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // प्रिंट करेगा
    // -  Username must satisfy the following rules
    //     - Username may only contain letters (a-z) and numbers (0-9)
    //     - Username must not contain whitespace
  
    var_export($exception->getMessages());
    // प्रिंट करेगा
    // array (
    //   'alnum' => 'Username may only contain letters (a-z) and numbers (0-9)',
    //   'noWhitespace' => 'Username must not contain whitespace',
    // )
}

// कस्टम त्रुटि संदेश
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Username may only contain letters and numbers',
        'noWhitespace' => 'Username must not contain spaces',
        'length' => 'length satisfies the rule, so this will not be shown'
    ]));
    // प्रिंट करेगा 
    // array(
    //    'alnum' => 'Username may only contain letters and numbers',
    //    'noWhitespace' => 'Username must not contain spaces'
    // )
}

// ऑब्जेक्ट वैधीकरण
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// ऐरे वैधीकरण
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
    ->assert($data); // check() या validate() भी उपयोग कर सकते हैं
  
// वैकल्पिक वैधीकरण
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// निषेध नियम
v::not(v::intVal())->validate(10); // false
```
  
## वैधीकरणकर्ता विधियों `validate()` `check()` `assert()` के बीच अंतर

`validate()` बूलियन लौटाता है, अपवाद नहीं फेंकता

`check()` वैधीकरण विफलता पर अपवाद फेंकता है; पहला विफलता कारण `$exception->getMessage()` के माध्यम से प्राप्त करें

`assert()` वैधीकरण विफलता पर अपवाद फेंकता है; सभी विफलता कारण `$exception->getFullMessage()` के माध्यम से प्राप्त करें
  
  
## सामान्य वैधीकरण नियम

`Alnum()` केवल अक्षर और संख्याएँ

`Alpha()` केवल अक्षर

`ArrayType()` ऐरे प्रकार

`Between(mixed $minimum, mixed $maximum)` वैधीकरण करता है कि इनपुट दो मानों के बीच है।

`BoolType()` बूलियन प्रकार वैधीकरण

`Contains(mixed $expectedValue)` वैधीकरण करता है कि इनपुट में निश्चित मान शामिल है

`ContainsAny(array $needles)` वैधीकरण करता है कि इनपुट में कम से कम एक परिभाषित मान शामिल है

`Digit()` वैधीकरण करता है कि इनपुट में केवल अंक हैं

`Domain()` वैध डोमेन नाम वैधीकरण

`Email()` वैध ईमेल पता वैधीकरण

`Extension(string $extension)` फ़ाइल एक्सटेंशन वैधीकरण

`FloatType()` फ्लोट प्रकार वैधीकरण

`IntType()` पूर्णांक प्रकार वैधीकरण

`Ip()` IP पता वैधीकरण

`Json()` JSON डेटा वैधीकरण

`Length(int $min, int $max)` लंबाई सीमा के भीतर वैधीकरण

`LessThan(mixed $compareTo)` लंबाई दिए गए मान से कम वैधीकरण

`Lowercase()` लोअरकेस वैधीकरण

`MacAddress()` MAC पता वैधीकरण

`NotEmpty()` खाली नहीं वैधीकरण

`NullType()` null वैधीकरण

`Number()` संख्या वैधीकरण

`ObjectType()` ऑब्जेक्ट प्रकार वैधीकरण

`StringType()` स्ट्रिंग प्रकार वैधीकरण

`Url()` URL वैधीकरण
  
अधिक वैधीकरण नियमों के लिए https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ देखें।
  
## अधिक

https://respect-validation.readthedocs.io/en/2.0/ पर जाएं।
  
