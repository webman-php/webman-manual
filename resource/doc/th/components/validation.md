# ตัวตรวจสอบอื่นๆ

มีตัวตรวจสอบหลายตัวใน composer ที่สามารถใช้งานได้โดยตรง เช่น:

#### <a href="#webman-validation"> webman/validation (แนะนำ)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# ตัวตรวจสอบ webman/validation

อิงจาก `illuminate/validation` รองรับการตรวจสอบแบบแมนนวล การตรวจสอบด้วย annotation การตรวจสอบระดับพารามิเตอร์ และชุดกฎที่นำกลับมาใช้ได้

## การติดตั้ง

```bash
composer require webman/validation
```

## แนวคิดพื้นฐาน

- **การใช้ชุดกฎซ้ำ**: กำหนด `rules`, `messages`, `attributes` และ `scenes` ที่นำกลับมาใช้ได้โดยสืบทอดจาก `support\validation\Validator` สามารถใช้ซ้ำได้ทั้งการตรวจสอบแบบแมนนวลและ annotation
- **การตรวจสอบด้วย Annotation (Attribute) ระดับเมธอด**: ใช้ PHP 8 attribute `#[Validate]` เพื่อผูกการตรวจสอบกับเมธอดของ controller
- **การตรวจสอบด้วย Annotation (Attribute) ระดับพารามิเตอร์**: ใช้ PHP 8 attribute `#[Param]` เพื่อผูกการตรวจสอบกับพารามิเตอร์ของเมธอด controller
- **การจัดการข้อยกเว้น**: โยน `support\validation\ValidationException` เมื่อการตรวจสอบล้มเหลว คลาสข้อยกเว้นสามารถกำหนดค่าได้
- **การตรวจสอบฐานข้อมูล**: หากมีการตรวจสอบฐานข้อมูล ต้องติดตั้ง `composer require webman/database`

## การตรวจสอบแบบแมนนวล

### การใช้งานพื้นฐาน

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **หมายเหตุ**
> `validate()` จะโยน `support\validation\ValidationException` เมื่อการตรวจสอบล้มเหลว หากไม่ต้องการให้โยนข้อยกเว้น ให้ใช้วิธี `fails()` ด้านล่างเพื่อรับข้อความข้อผิดพลาด

### ข้อความและ attributes ที่กำหนดเอง

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

### การตรวจสอบโดยไม่โยนข้อยกเว้น (รับข้อความข้อผิดพลาด)

หากไม่ต้องการให้โยนข้อยกเว้น ให้ใช้ `fails()` เพื่อตรวจสอบและรับข้อความข้อผิดพลาดผ่าน `errors()` (คืนค่า `MessageBag`):

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

## การใช้ชุดกฎซ้ำ (ตัวตรวจสอบที่กำหนดเอง)

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

### การใช้การตรวจสอบแบบแมนนวลซ้ำ

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### การใช้ scenes (ตัวเลือก)

`scenes` เป็นฟีเจอร์ตัวเลือก จะตรวจสอบเฉพาะฟิลด์ย่อยเมื่อเรียก `withScene(...)`

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

// ไม่ระบุ scene -> ตรวจสอบกฎทั้งหมด
UserValidator::make($data)->validate();

// ระบุ scene -> ตรวจสอบเฉพาะฟิลด์ใน scene นั้น
UserValidator::make($data)->withScene('create')->validate();
```

## การตรวจสอบด้วย Annotation (ระดับเมธอด)

### กฎโดยตรง

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

### การใช้ชุดกฎซ้ำ

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

### การตรวจสอบหลายชั้นซ้อนทับกัน

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

### แหล่งข้อมูลการตรวจสอบ

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

ใช้พารามิเตอร์ `in` เพื่อระบุแหล่งข้อมูล:

* **query** พารามิเตอร์ query ของ HTTP request จาก `$request->get()`
* **body** body ของ HTTP request จาก `$request->post()`
* **path** พารามิเตอร์ path ของ HTTP request จาก `$request->route->param()`

`in` สามารถเป็น string หรือ array ได้ เมื่อเป็น array ค่าจะถูกรวมตามลำดับ โดยค่าที่มาทีหลังจะแทนที่ค่าก่อนหน้า เมื่อไม่ส่ง `in` ค่าเริ่มต้นคือ `['query', 'body', 'path']`


## การตรวจสอบระดับพารามิเตอร์ (Param)

### การใช้งานพื้นฐาน

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

### แหล่งข้อมูลการตรวจสอบ

เช่นเดียวกัน การตรวจสอบระดับพารามิเตอร์ยังรองรับพารามิเตอร์ `in` เพื่อระบุแหล่งที่มา:

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


### rules รองรับ string หรือ array

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

### ข้อความ / attribute ที่กำหนดเอง

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

### การใช้ค่าคงที่ของกฎซ้ำ

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

## การรวมระดับเมธอด + ระดับพารามิเตอร์

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

## การอนุมานกฎอัตโนมัติ (อิงจากลายเซ็นพารามิเตอร์)

เมื่อใช้ `#[Validate]` บนเมธอด หรือพารามิเตอร์ใดๆ ของเมธอดนั้นใช้ `#[Param]` คอมโพเนนต์นี้จะ**อนุมานและเติมกฎการตรวจสอบพื้นฐานจากลายเซ็นพารามิเตอร์ของเมธอดโดยอัตโนมัติ** จากนั้นรวมกับกฎที่มีอยู่ก่อนการตรวจสอบ

### ตัวอย่าง: การขยายเทียบเท่า `#[Validate]`

1) เปิดใช้งาน `#[Validate]` โดยไม่เขียนกฎด้วยตนเอง:

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

เทียบเท่ากับ:

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

2) เขียนกฎบางส่วนเท่านั้น ส่วนที่เหลือเติมจากลายเซ็นพารามิเตอร์:

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

เทียบเท่ากับ:

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

3) ค่าเริ่มต้น / ประเภท nullable:

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

เทียบเท่ากับ:

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

## การจัดการข้อยกเว้น

### ข้อยกเว้นเริ่มต้น

การตรวจสอบล้มเหลวจะโยน `support\validation\ValidationException` โดยค่าเริ่มต้น ซึ่งสืบทอดจาก `Webman\Exception\BusinessException` และไม่บันทึกข้อผิดพลาด

พฤติกรรมการตอบกลับเริ่มต้นจัดการโดย `BusinessException::render()`:

- คำขอปกติ: คืนค่าข้อความ string เช่น `token is required.`
- คำขอ JSON: คืนค่าการตอบกลับ JSON เช่น `{"code": 422, "msg": "token is required.", "data":....}`

### การกำหนดการจัดการผ่านข้อยกเว้นที่กำหนดเอง

- การกำหนดค่าทั่วโลก: `exception` ใน `config/plugin/webman/validation/app.php`

## การรองรับหลายภาษา

คอมโพเนนต์มีชุดภาษาจีนและอังกฤษในตัว และรองรับการแทนที่ของโปรเจกต์ ลำดับการโหลด:

1. ชุดภาษาของโปรเจกต์ `resource/translations/{locale}/validation.php`
2. คอมโพเนนต์ในตัว `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Illuminate อังกฤษในตัว (fallback)

> **หมายเหตุ**
> ภาษาเริ่มต้นของ Webman กำหนดใน `config/translation.php` หรือเปลี่ยนได้ผ่าน `locale('en');`

### ตัวอย่างการแทนที่ในท้องถิ่น

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute is not a valid email format.',
];
```

## การโหลด Middleware อัตโนมัติ

หลังการติดตั้ง คอมโพเนนต์จะโหลด validation middleware อัตโนมัติผ่าน `config/plugin/webman/validation/middleware.php` ไม่ต้องลงทะเบียนด้วยตนเอง

## การสร้างด้วย Command-Line

ใช้คำสั่ง `make:validator` เพื่อสร้างคลาสตัวตรวจสอบ (ส่งออกไปยังไดเรกทอรี `app/validation` โดยค่าเริ่มต้น)

> **หมายเหตุ**
> ต้องใช้ `composer require webman/console`

### การใช้งานพื้นฐาน

- **สร้างเทมเพลตว่าง**

```bash
php webman make:validator UserValidator
```

- **เขียนทับไฟล์ที่มีอยู่**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### สร้างกฎจากโครงสร้างตาราง

- **ระบุชื่อตารางเพื่อสร้างกฎพื้นฐาน** (อนุมาน `$rules` จากประเภทฟิลด์/nullable/ความยาว ฯลฯ ไม่รวมฟิลด์ที่เกี่ยวกับ ORM โดยค่าเริ่มต้น: laravel ใช้ `created_at/updated_at/deleted_at` thinkorm ใช้ `create_time/update_time/delete_time`)

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **ระบุการเชื่อมต่อฐานข้อมูล** (สถานการณ์การเชื่อมต่อหลายตัว)

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### Scenes

- **สร้าง CRUD scenes**: `create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> scene `update` รวมฟิลด์คีย์หลัก (สำหรับระบุเรคอร์ด) และฟิลด์อื่นๆ `delete/detail` รวมเฉพาะคีย์หลักโดยค่าเริ่มต้น

### การเลือก ORM (laravel (illuminate/database) vs think-orm)

- **เลือกอัตโนมัติ (ค่าเริ่มต้น)**: ใช้ตัวที่ติดตั้ง/กำหนดค่าไว้ เมื่อมีทั้งสองตัว ใช้ illuminate โดยค่าเริ่มต้น
- **บังคับระบุ**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### ตัวอย่างสมบูรณ์

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## Unit Tests

จากไดเรกทอรีราก `webman/validation` รัน:

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## อ้างอิงกฎการตรวจสอบ

<a name="available-validation-rules"></a>
## กฎการตรวจสอบที่มีให้ใช้

> [!IMPORTANT]
> - Webman Validation อิงจาก `illuminate/validation` ชื่อกฎตรงกับ Laravel และไม่มีกฎเฉพาะของ Webman
> - Middleware ตรวจสอบข้อมูลจาก `$request->all()` (GET+POST) รวมกับพารามิเตอร์ route โดยค่าเริ่มต้น ไม่รวมไฟล์ที่อัปโหลด สำหรับกฎไฟล์ ให้รวม `$request->file()` เข้ากับข้อมูลด้วยตนเอง หรือเรียก `Validator::make` แบบแมนนวล
> - `current_password` ขึ้นอยู่กับ auth guard; `exists`/`unique` ขึ้นอยู่กับการเชื่อมต่อฐานข้อมูลและ query builder กฎเหล่านี้ไม่พร้อมใช้งานเมื่อคอมโพเนนต์ที่เกี่ยวข้องไม่ได้รวมอยู่

รายการด้านล่างคือกฎการตรวจสอบทั้งหมดที่มีให้ใช้และวัตถุประสงค์:

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

ฟิลด์ต้องเป็น `"yes"`, `"on"`, `1`, `"1"`, `true` หรือ `"true"` มักใช้สำหรับสถานการณ์เช่นการยืนยันว่าผู้ใช้ยอมรับข้อกำหนดการให้บริการ

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

เมื่อฟิลด์อื่นเท่ากับค่าที่ระบุ ฟิลด์ต้องเป็น `"yes"`, `"on"`, `1`, `"1"`, `true` หรือ `"true"` มักใช้สำหรับสถานการณ์การยอมรับแบบมีเงื่อนไข

<a name="rule-active-url"></a>
#### active_url

ฟิลด์ต้องมีเรคอร์ด A หรือ AAAA ที่ถูกต้อง กฎนี้ใช้ `parse_url` เพื่อแยก hostname ของ URL ก่อน จากนั้นตรวจสอบด้วย `dns_get_record`

<a name="rule-after"></a>
#### after:_date_

ฟิลด์ต้องเป็นค่าหลังวันที่กำหนด วันที่จะถูกส่งไปยัง `strtotime` เพื่อแปลงเป็น `DateTime` ที่ถูกต้อง:

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

สามารถส่งชื่อฟิลด์อื่นเพื่อเปรียบเทียบได้:

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

สามารถใช้ตัวสร้างกฎ `date` แบบ fluent ได้:

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

`afterToday` และ `todayOrAfter` แสดงความหมาย "ต้องหลังวันนี้" หรือ "ต้องเป็นวันนี้หรือหลังจากนี้" ได้สะดวก:

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

ฟิลด์ต้องอยู่ในหรือหลังวันที่กำหนด ดู [after](#rule-after) สำหรับรายละเอียดเพิ่มเติม

สามารถใช้ตัวสร้างกฎ `date` แบบ fluent ได้:

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

`Rule::anyOf` อนุญาตให้ระบุ "เป็นไปตามชุดกฎใดชุดหนึ่ง" ตัวอย่างเช่น กฎต่อไปนี้หมายความว่า `username` ต้องเป็นอีเมลหรือสตริงตัวอักษร/ตัวเลข/ขีดล่าง/ขีดกลางอย่างน้อย 6 ตัวอักษร:

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

ฟิลด์ต้องเป็นตัวอักษร Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) และ [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=))

เพื่ออนุญาตเฉพาะ ASCII (`a-z`, `A-Z`) เท่านั้น ให้เพิ่มตัวเลือก `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

ฟิลด์อาจมีเฉพาะตัวอักษรและตัวเลข Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)) บวกขีด (`-`) และขีดล่าง (`_`) ของ ASCII

เพื่ออนุญาตเฉพาะ ASCII (`a-z`, `A-Z`, `0-9`) เท่านั้น ให้เพิ่มตัวเลือก `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

ฟิลด์อาจมีเฉพาะตัวอักษรและตัวเลข Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=))

เพื่ออนุญาตเฉพาะ ASCII (`a-z`, `A-Z`, `0-9`) เท่านั้น ให้เพิ่มตัวเลือก `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

ฟิลด์ต้องเป็น PHP `array`

เมื่อกฎ `array` มีพารามิเตอร์เพิ่มเติม คีย์ของอาร์เรย์อินพุตต้องอยู่ในรายการพารามิเตอร์ ในตัวอย่าง คีย์ `admin` ไม่อยู่ในรายการที่อนุญาต จึงไม่ถูกต้อง:

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

แนะนำให้กำหนดคีย์อาร์เรย์ที่อนุญาตอย่างชัดเจนในโปรเจกต์จริง

<a name="rule-ascii"></a>
#### ascii

ฟิลด์อาจมีเฉพาะอักขระ ASCII 7-bit

<a name="rule-bail"></a>
#### bail

หยุดตรวจสอบกฎถัดไปสำหรับฟิลด์เมื่อกฎแรกล้มเหลว

กฎนี้มีผลเฉพาะฟิลด์ปัจจุบัน สำหรับ "หยุดเมื่อล้มเหลวครั้งแรกทั่วโลก" ให้ใช้ validator ของ Illuminate โดยตรงและเรียก `stopOnFirstFailure()`

<a name="rule-before"></a>
#### before:_date_

ฟิลด์ต้องอยู่ก่อนวันที่กำหนด วันที่จะถูกส่งไปยัง `strtotime` เพื่อแปลงเป็น `DateTime` ที่ถูกต้อง เช่นเดียวกับ [after](#rule-after) สามารถส่งชื่อฟิลด์อื่นเพื่อเปรียบเทียบได้

สามารถใช้ตัวสร้างกฎ `date` แบบ fluent ได้:

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

`beforeToday` และ `todayOrBefore` แสดงความหมาย "ต้องก่อนวันนี้" หรือ "ต้องเป็นวันนี้หรือก่อนหน้า" ได้สะดวก:

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

ฟิลด์ต้องอยู่ในหรือก่อนวันที่กำหนด วันที่จะถูกส่งไปยัง `strtotime` เพื่อแปลงเป็น `DateTime` ที่ถูกต้อง เช่นเดียวกับ [after](#rule-after) สามารถส่งชื่อฟิลด์อื่นเพื่อเปรียบเทียบได้

สามารถใช้ตัวสร้างกฎ `date` แบบ fluent ได้:

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

ขนาดฟิลด์ต้องอยู่ระหว่าง _min_ และ _max_ (รวม) การประเมินสำหรับสตริง ตัวเลข อาร์เรย์ และไฟล์เหมือนกับ [size](#rule-size)

<a name="rule-boolean"></a>
#### boolean

ฟิลด์ต้องแปลงเป็น boolean ได้ ค่าอินพุตที่ยอมรับได้รวมถึง `true`, `false`, `1`, `0`, `"1"`, `"0"`

ใช้พารามิเตอร์ `strict` เพื่ออนุญาตเฉพาะ `true` หรือ `false`:

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

ฟิลด์ต้องมีฟิลด์ `{field}_confirmation` ที่ตรงกัน ตัวอย่างเช่น เมื่อฟิลด์เป็น `password` ต้องมี `password_confirmation`

สามารถระบุชื่อฟิลด์ยืนยันที่กำหนดเองได้ เช่น `confirmed:repeat_username` ต้องการให้ `repeat_username` ตรงกับฟิลด์ปัจจุบัน

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

ฟิลด์ต้องเป็นอาร์เรย์และต้องมีค่าพารามิเตอร์ที่กำหนดทั้งหมด กฎนี้มักใช้สำหรับการตรวจสอบอาร์เรย์ สามารถใช้ `Rule::contains` เพื่อสร้าง:

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

ฟิลด์ต้องเป็นอาร์เรย์และต้องไม่มีค่าพารามิเตอร์ที่กำหนดใดๆ สามารถใช้ `Rule::doesntContain` เพื่อสร้าง:

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

ฟิลด์ต้องตรงกับรหัสผ่านของผู้ใช้ที่รับรองความถูกต้องปัจจุบัน สามารถระบุ auth guard เป็นพารามิเตอร์แรก:

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> กฎนี้ขึ้นอยู่กับคอมโพเนนต์ auth และการกำหนดค่า guard อย่าใช้เมื่อไม่ได้รวม auth

<a name="rule-date"></a>
#### date

ฟิลด์ต้องเป็นวันที่ที่ถูกต้อง (ไม่ใช่แบบสัมพัทธ์) ที่ `strtotime` รู้จักได้

<a name="rule-date-equals"></a>
#### date_equals:_date_

ฟิลด์ต้องเท่ากับวันที่กำหนด วันที่จะถูกส่งไปยัง `strtotime` เพื่อแปลงเป็น `DateTime` ที่ถูกต้อง

<a name="rule-date-format"></a>
#### date_format:_format_,...

ฟิลด์ต้องตรงกับรูปแบบที่กำหนดอย่างน้อยหนึ่งรูปแบบ ใช้ `date` หรือ `date_format` อย่างใดอย่างหนึ่ง กฎนี้รองรับรูปแบบ [DateTime](https://www.php.net/manual/en/class.datetime.php) ทั้งหมดของ PHP

สามารถใช้ตัวสร้างกฎ `date` แบบ fluent ได้:

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

ฟิลด์ต้องเป็นตัวเลขที่มีทศนิยมตามที่ต้องการ:

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

ฟิลด์ต้องเป็น `"no"`, `"off"`, `0`, `"0"`, `false` หรือ `"false"`

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

เมื่อฟิลด์อื่นเท่ากับค่าที่ระบุ ฟิลด์ต้องเป็น `"no"`, `"off"`, `0`, `"0"`, `false` หรือ `"false"`

<a name="rule-different"></a>
#### different:_field_

ฟิลด์ต้องแตกต่างจาก _field_

<a name="rule-digits"></a>
#### digits:_value_

ฟิลด์ต้องเป็นจำนวนเต็มที่มีความยาว _value_

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

ฟิลด์ต้องเป็นจำนวนเต็มที่มีความยาวระหว่าง _min_ และ _max_

<a name="rule-dimensions"></a>
#### dimensions

ฟิลด์ต้องเป็นรูปภาพและเป็นไปตามข้อจำกัดมิติ:

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

ข้อจำกัดที่มี: _min\_width_, _max\_width_, _min\_height_, _max\_height_, _width_, _height_, _ratio_

_ratio_ คืออัตราส่วนภาพ สามารถแสดงเป็นเศษส่วนหรือ float:

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

กฎนี้มีพารามิเตอร์มาก แนะนำให้ใช้ `Rule::dimensions` เพื่อสร้าง:

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

เมื่อตรวจสอบอาร์เรย์ ค่าฟิลด์ต้องไม่ซ้ำกัน:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

ใช้การเปรียบเทียบแบบหลวมโดยค่าเริ่มต้น เพิ่ม `strict` สำหรับการเปรียบเทียบแบบเข้มงวด:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

เพิ่ม `ignore_case` เพื่อไม่สนใจความแตกต่างของตัวพิมพ์:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

ฟิลด์ต้องไม่ขึ้นต้นด้วยค่าที่ระบุใดๆ

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

ฟิลด์ต้องไม่ลงท้ายด้วยค่าที่ระบุใดๆ

<a name="rule-email"></a>
#### email

ฟิลด์ต้องเป็นที่อยู่อีเมลที่ถูกต้อง กฎนี้ขึ้นอยู่กับ [egulias/email-validator](https://github.com/egulias/EmailValidator) ใช้ `RFCValidation` โดยค่าเริ่มต้น และสามารถใช้วิธีการตรวจสอบอื่นได้:

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

วิธีการตรวจสอบที่มี:

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` - ตรวจสอบอีเมลตาม spec ของ RFC ([supported RFCs](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs))
- `strict`: `NoRFCWarningsValidation` - ล้มเหลวเมื่อมีคำเตือน RFC (เช่น จุดต่อท้ายหรือจุดติดกัน)
- `dns`: `DNSCheckValidation` - ตรวจสอบว่าโดเมนมีเรคอร์ด MX ที่ถูกต้องหรือไม่
- `spoof`: `SpoofCheckValidation` - ป้องกันอักขระ Unicode แบบ homograph หรือ spoofing
- `filter`: `FilterEmailValidation` - ตรวจสอบโดยใช้ PHP `filter_var`
- `filter_unicode`: `FilterEmailValidation::unicode()` - การตรวจสอบ `filter_var` ที่อนุญาต Unicode

</div>

สามารถใช้ตัวสร้างกฎแบบ fluent ได้:

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
> `dns` และ `spoof` ต้องการส่วนขยาย PHP `intl`

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

ฟิลด์ต้องตรงกับ character encoding ที่ระบุ กฎนี้ใช้ `mb_check_encoding` เพื่อตรวจจับ encoding ของไฟล์หรือสตริง สามารถใช้กับตัวสร้างกฎไฟล์ได้:

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

ฟิลด์ต้องลงท้ายด้วยค่าที่ระบุอย่างน้อยหนึ่งค่า

<a name="rule-enum"></a>
#### enum

`Enum` เป็นกฎแบบคลาสสำหรับตรวจสอบว่าค่าฟิลด์เป็นค่า enum ที่ถูกต้อง ส่งชื่อคลาส enum เมื่อสร้าง สำหรับค่าดั้งเดิม ให้ใช้ Backed Enum:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

ใช้ `only`/`except` เพื่อจำกัดค่าของ enum:

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

ใช้ `when` สำหรับข้อจำกัดแบบมีเงื่อนไข:

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

ฟิลด์จะถูกแยกออกจากข้อมูลที่คืนโดย `validate`/`validated`

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

เมื่อ _anotherfield_ เท่ากับ _value_ ฟิลด์จะถูกแยกออกจากข้อมูลที่คืนโดย `validate`/`validated`

สำหรับเงื่อนไขที่ซับซ้อน ให้ใช้ `Rule::excludeIf`:

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

เว้นแต่ _anotherfield_ จะเท่ากับ _value_ ฟิลด์จะถูกแยกออกจากข้อมูลที่คืนโดย `validate`/`validated` หาก _value_ เป็น `null` (เช่น `exclude_unless:name,null`) ฟิลด์จะถูกเก็บไว้เฉพาะเมื่อฟิลด์เปรียบเทียบเป็น `null` หรือไม่มี

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

เมื่อ _anotherfield_ มีอยู่ ฟิลด์จะถูกแยกออกจากข้อมูลที่คืนโดย `validate`/`validated`

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

เมื่อ _anotherfield_ ไม่มีอยู่ ฟิลด์จะถูกแยกออกจากข้อมูลที่คืนโดย `validate`/`validated`

<a name="rule-exists"></a>
#### exists:_table_,_column_

ฟิลด์ต้องมีอยู่ในตารางฐานข้อมูลที่ระบุ

<a name="basic-usage-of-exists-rule"></a>
#### การใช้งานพื้นฐานของกฎ Exists

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

เมื่อไม่ระบุ `column` จะใช้ชื่อฟิลด์โดยค่าเริ่มต้น ดังนั้นตัวอย่างนี้ตรวจสอบว่าคอลัมน์ `state` มีอยู่ในตาราง `states` หรือไม่

<a name="specifying-a-custom-column-name"></a>
#### การระบุชื่อคอลัมน์ที่กำหนดเอง

ต่อชื่อคอลัมน์หลังชื่อตาราง:

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

เพื่อระบุการเชื่อมต่อฐานข้อมูล นำหน้าชื่อตารางด้วยชื่อการเชื่อมต่อ:

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

สามารถส่งชื่อคลาส model ได้ เฟรมเวิร์กจะแก้ไขชื่อตาราง:

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

สำหรับเงื่อนไข query ที่กำหนดเอง ให้ใช้ตัวสร้าง `Rule`:

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

สามารถระบุชื่อคอลัมน์โดยตรงใน `Rule::exists` ได้:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'state' => [Rule::exists('states', 'abbreviation')],
])->validate();
```

เพื่อตรวจสอบว่าชุดค่ามีอยู่ ให้รวมกับกฎ `array`:

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

เมื่อมีทั้ง `array` และ `exists` การ query เดียวจะตรวจสอบค่าทั้งหมด

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

ตรวจสอบว่านามสกุลไฟล์ที่อัปโหลดอยู่ในรายการที่อนุญาต:

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> อย่าพึ่งพานามสกุลอย่างเดียวในการตรวจสอบประเภทไฟล์ ให้ใช้ร่วมกับ [mimes](#rule-mimes) หรือ [mimetypes](#rule-mimetypes)

<a name="rule-file"></a>
#### file

ฟิลด์ต้องเป็นไฟล์ที่อัปโหลดสำเร็จ

<a name="rule-filled"></a>
#### filled

เมื่อฟิลด์มีอยู่ ค่าของมันต้องไม่ว่าง

<a name="rule-gt"></a>
#### gt:_field_

ฟิลด์ต้องมากกว่าฟิลด์หรือค่าที่กำหนด _field_ ทั้งสองต้องมีประเภทเดียวกัน การประเมินสำหรับสตริง ตัวเลข อาร์เรย์ และไฟล์เหมือนกับ [size](#rule-size)

<a name="rule-gte"></a>
#### gte:_field_

ฟิลด์ต้องมากกว่าหรือเท่ากับฟิลด์หรือค่าที่กำหนด _field_ ทั้งสองต้องมีประเภทเดียวกัน การประเมินสำหรับสตริง ตัวเลข อาร์เรย์ และไฟล์เหมือนกับ [size](#rule-size)

<a name="rule-hex-color"></a>
#### hex_color

ฟิลด์ต้องเป็น[ค่า hex color](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color) ที่ถูกต้อง

<a name="rule-image"></a>
#### image

ฟิลด์ต้องเป็นรูปภาพ (jpg, jpeg, png, bmp, gif หรือ webp)

> [!WARNING]
> SVG ไม่ได้รับอนุญาตโดยค่าเริ่มต้นเนื่องจากความเสี่ยง XSS เพื่ออนุญาต ให้เพิ่ม `allow_svg`: `image:allow_svg`

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

ฟิลด์ต้องอยู่ในรายการค่าที่กำหนด สามารถใช้ `Rule::in` เพื่อสร้าง:

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

เมื่อรวมกับกฎ `array` แต่ละค่าในอาร์เรย์อินพุตต้องอยู่ในรายการ `in`:

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

ฟิลด์ต้องมีอยู่ในรายการค่าของ _anotherfield_

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

ฟิลด์ต้องเป็นอาร์เรย์และต้องมีค่าที่กำหนดอย่างน้อยหนึ่งค่าเป็นคีย์:

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

ฟิลด์ต้องเป็นจำนวนเต็ม

ใช้พารามิเตอร์ `strict` เพื่อให้ฟิลด์ต้องเป็นประเภท integer เท่านั้น จำนวนเต็มที่เป็นสตริงจะไม่ถูกต้อง:

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> กฎนี้ตรวจสอบเฉพาะว่าผ่าน PHP `FILTER_VALIDATE_INT` หรือไม่ สำหรับประเภทตัวเลขแบบเข้มงวด ให้ใช้ร่วมกับ [numeric](#rule-numeric)

<a name="rule-ip"></a>
#### ip

ฟิลด์ต้องเป็นที่อยู่ IP ที่ถูกต้อง

<a name="rule-ipv4"></a>
#### ipv4

ฟิลด์ต้องเป็นที่อยู่ IPv4 ที่ถูกต้อง

<a name="rule-ipv6"></a>
#### ipv6

ฟิลด์ต้องเป็นที่อยู่ IPv6 ที่ถูกต้อง

<a name="rule-json"></a>
#### json

ฟิลด์ต้องเป็นสตริง JSON ที่ถูกต้อง

<a name="rule-lt"></a>
#### lt:_field_

ฟิลด์ต้องน้อยกว่าฟิลด์ที่กำหนด _field_ ทั้งสองต้องมีประเภทเดียวกัน การประเมินสำหรับสตริง ตัวเลข อาร์เรย์ และไฟล์เหมือนกับ [size](#rule-size)

<a name="rule-lte"></a>
#### lte:_field_

ฟิลด์ต้องน้อยกว่าหรือเท่ากับฟิลด์ที่กำหนด _field_ ทั้งสองต้องมีประเภทเดียวกัน การประเมินสำหรับสตริง ตัวเลข อาร์เรย์ และไฟล์เหมือนกับ [size](#rule-size)

<a name="rule-lowercase"></a>
#### lowercase

ฟิลด์ต้องเป็นตัวพิมพ์เล็ก

<a name="rule-list"></a>
#### list

ฟิลด์ต้องเป็นอาร์เรย์ list คีย์ของอาร์เรย์ list ต้องเป็นตัวเลขต่อเนื่องตั้งแต่ 0 ถึง `count($array) - 1`

<a name="rule-mac"></a>
#### mac_address

ฟิลด์ต้องเป็นที่อยู่ MAC ที่ถูกต้อง

<a name="rule-max"></a>
#### max:_value_

ฟิลด์ต้องน้อยกว่าหรือเท่ากับ _value_ การประเมินสำหรับสตริง ตัวเลข อาร์เรย์ และไฟล์เหมือนกับ [size](#rule-size)

<a name="rule-max-digits"></a>
#### max_digits:_value_

ฟิลด์ต้องเป็นจำนวนเต็มที่มีความยาวไม่เกิน _value_

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

ตรวจสอบว่า MIME type ของไฟล์อยู่ในรายการ:

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

MIME type ถูกคาดเดาจากการอ่านเนื้อหาไฟล์ และอาจแตกต่างจาก MIME ที่ไคลเอนต์ให้มา

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

ตรวจสอบว่า MIME type ของไฟล์ตรงกับนามสกุลที่กำหนด:

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

แม้พารามิเตอร์จะเป็นนามสกุล กฎนี้อ่านเนื้อหาไฟล์เพื่อกำหนด MIME การแมปนามสกุลกับ MIME:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### MIME types และ extensions

กฎนี้ไม่ตรวจสอบว่า "นามสกุลไฟล์" ตรงกับ "MIME จริง" หรือไม่ ตัวอย่างเช่น `mimes:png` ถือว่า `photo.txt` ที่มีเนื้อหา PNG เป็นถูกต้อง เพื่อตรวจสอบนามสกุล ให้ใช้ [extensions](#rule-extensions)

<a name="rule-min"></a>
#### min:_value_

ฟิลด์ต้องมากกว่าหรือเท่ากับ _value_ การประเมินสำหรับสตริง ตัวเลข อาร์เรย์ และไฟล์เหมือนกับ [size](#rule-size)

<a name="rule-min-digits"></a>
#### min_digits:_value_

ฟิลด์ต้องเป็นจำนวนเต็มที่มีความยาวไม่น้อยกว่า _value_

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

ฟิลด์ต้องเป็นพหุคูณของ _value_

<a name="rule-missing"></a>
#### missing

ฟิลด์ต้องไม่มีอยู่ในข้อมูลอินพุต

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

เมื่อ _anotherfield_ เท่ากับ _value_ ใดๆ ฟิลด์ต้องไม่มีอยู่

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

เว้นแต่ _anotherfield_ จะเท่ากับ _value_ ใดๆ ฟิลด์ต้องไม่มีอยู่

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

เมื่อฟิลด์ที่ระบุใดๆ มีอยู่ ฟิลด์ต้องไม่มีอยู่

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

เมื่อฟิลด์ที่ระบุทั้งหมดมีอยู่ ฟิลด์ต้องไม่มีอยู่

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

ฟิลด์ต้องไม่อยู่ในรายการค่าที่กำหนด สามารถใช้ `Rule::notIn` เพื่อสร้าง:

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

ฟิลด์ต้องไม่ตรงกับ regular expression ที่กำหนด

กฎนี้ใช้ PHP `preg_match` regex ต้องมีตัวคั่น เช่น `'email' => 'not_regex:/^.+$/i'`

> [!WARNING]
> เมื่อใช้ `regex`/`not_regex` หาก regex มี `|` ให้ใช้รูปแบบ array เพื่อหลีกเลี่ยงความขัดแย้งกับตัวคั่น `|`

<a name="rule-nullable"></a>
#### nullable

ฟิลด์อาจเป็น `null`

<a name="rule-numeric"></a>
#### numeric

ฟิลด์ต้องเป็น [numeric](https://www.php.net/manual/en/function.is-numeric.php)

ใช้พารามิเตอร์ `strict` เพื่ออนุญาตเฉพาะประเภท integer หรือ float เท่านั้น สตริงตัวเลขจะไม่ถูกต้อง:

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

ฟิลด์ต้องมีอยู่ในข้อมูลอินพุต

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

เมื่อ _anotherfield_ เท่ากับ _value_ ใดๆ ฟิลด์ต้องมีอยู่

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

เว้นแต่ _anotherfield_ จะเท่ากับ _value_ ใดๆ ฟิลด์ต้องมีอยู่

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

เมื่อฟิลด์ที่ระบุใดๆ มีอยู่ ฟิลด์ต้องมีอยู่

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

เมื่อฟิลด์ที่ระบุทั้งหมดมีอยู่ ฟิลด์ต้องมีอยู่

<a name="rule-prohibited"></a>
#### prohibited

ฟิลด์ต้องไม่มีหรือว่าง "ว่าง" หมายถึง:

<div class="content-list" markdown="1">

- ค่าเป็น `null`
- ค่าเป็นสตริงว่าง
- ค่าเป็นอาร์เรย์ว่างหรืออ็อบเจกต์ `Countable` ว่าง
- ไฟล์ที่อัปโหลดที่มี path ว่าง

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

เมื่อ _anotherfield_ เท่ากับ _value_ ใดๆ ฟิลด์ต้องไม่มีหรือว่าง "ว่าง" หมายถึง:

<div class="content-list" markdown="1">

- ค่าเป็น `null`
- ค่าเป็นสตริงว่าง
- ค่าเป็นอาร์เรย์ว่างหรืออ็อบเจกต์ `Countable` ว่าง
- ไฟล์ที่อัปโหลดที่มี path ว่าง

</div>

สำหรับเงื่อนไขที่ซับซ้อน ให้ใช้ `Rule::prohibitedIf`:

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

เมื่อ _anotherfield_ เป็น `"yes"`, `"on"`, `1`, `"1"`, `true` หรือ `"true"` ฟิลด์ต้องไม่มีหรือว่าง

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

เมื่อ _anotherfield_ เป็น `"no"`, `"off"`, `0`, `"0"`, `false` หรือ `"false"` ฟิลด์ต้องไม่มีหรือว่าง

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

เว้นแต่ _anotherfield_ จะเท่ากับ _value_ ใดๆ ฟิลด์ต้องไม่มีหรือว่าง "ว่าง" หมายถึง:

<div class="content-list" markdown="1">

- ค่าเป็น `null`
- ค่าเป็นสตริงว่าง
- ค่าเป็นอาร์เรย์ว่างหรืออ็อบเจกต์ `Countable` ว่าง
- ไฟล์ที่อัปโหลดที่มี path ว่าง

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

เมื่อฟิลด์มีอยู่และไม่ว่าง ฟิลด์ทั้งหมดใน _anotherfield_ ต้องไม่มีหรือว่าง "ว่าง" หมายถึง:

<div class="content-list" markdown="1">

- ค่าเป็น `null`
- ค่าเป็นสตริงว่าง
- ค่าเป็นอาร์เรย์ว่างหรืออ็อบเจกต์ `Countable` ว่าง
- ไฟล์ที่อัปโหลดที่มี path ว่าง

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

ฟิลด์ต้องตรงกับ regular expression ที่กำหนด

กฎนี้ใช้ PHP `preg_match` regex ต้องมีตัวคั่น เช่น `'email' => 'regex:/^.+@.+$/i'`

> [!WARNING]
> เมื่อใช้ `regex`/`not_regex` หาก regex มี `|` ให้ใช้รูปแบบ array เพื่อหลีกเลี่ยงความขัดแย้งกับตัวคั่น `|`

<a name="rule-required"></a>
#### required

ฟิลด์ต้องมีอยู่และไม่ว่าง "ว่าง" หมายถึง:

<div class="content-list" markdown="1">

- ค่าเป็น `null`
- ค่าเป็นสตริงว่าง
- ค่าเป็นอาร์เรย์ว่างหรืออ็อบเจกต์ `Countable` ว่าง
- ไฟล์ที่อัปโหลดที่มี path ว่าง

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

เมื่อ _anotherfield_ เท่ากับ _value_ ใดๆ ฟิลด์ต้องมีอยู่และไม่ว่าง

สำหรับเงื่อนไขที่ซับซ้อน ให้ใช้ `Rule::requiredIf`:

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

เมื่อ _anotherfield_ เป็น `"yes"`, `"on"`, `1`, `"1"`, `true` หรือ `"true"` ฟิลด์ต้องมีอยู่และไม่ว่าง

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

เมื่อ _anotherfield_ เป็น `"no"`, `"off"`, `0`, `"0"`, `false` หรือ `"false"` ฟิลด์ต้องมีอยู่และไม่ว่าง

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

เว้นแต่ _anotherfield_ จะเท่ากับ _value_ ใดๆ ฟิลด์ต้องมีอยู่และไม่ว่าง หาก _value_ เป็น `null` (เช่น `required_unless:name,null`) ฟิลด์อาจว่างได้เฉพาะเมื่อฟิลด์เปรียบเทียบเป็น `null` หรือไม่มี

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

เมื่อฟิลด์ที่ระบุใดๆ มีอยู่และไม่ว่าง ฟิลด์ต้องมีอยู่และไม่ว่าง

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

เมื่อฟิลด์ที่ระบุทั้งหมดมีอยู่และไม่ว่าง ฟิลด์ต้องมีอยู่และไม่ว่าง

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

เมื่อฟิลด์ที่ระบุใดๆ ว่างหรือไม่มี ฟิลด์ต้องมีอยู่และไม่ว่าง

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

เมื่อฟิลด์ที่ระบุทั้งหมดว่างหรือไม่มี ฟิลด์ต้องมีอยู่และไม่ว่าง

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

ฟิลด์ต้องเป็นอาร์เรย์และต้องมีคีย์ที่ระบุอย่างน้อยหนึ่งคีย์

<a name="validating-when-present"></a>
#### sometimes

ใช้กฎการตรวจสอบถัดไปเฉพาะเมื่อฟิลด์มีอยู่ มักใช้สำหรับฟิลด์ "ตัวเลือกแต่ต้องถูกต้องเมื่อมี":

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

ฟิลด์ต้องเหมือนกับ _field_

<a name="rule-size"></a>
#### size:_value_

ขนาดฟิลด์ต้องเท่ากับ _value_ ที่กำหนด สำหรับสตริง: จำนวนอักขระ สำหรับตัวเลข: จำนวนเต็มที่ระบุ (ใช้กับ `numeric` หรือ `integer`) สำหรับอาร์เรย์: จำนวนองค์ประกอบ สำหรับไฟล์: ขนาดเป็น KB ตัวอย่าง:

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

ฟิลด์ต้องขึ้นต้นด้วยค่าที่ระบุอย่างน้อยหนึ่งค่า

<a name="rule-string"></a>
#### string

ฟิลด์ต้องเป็นสตริง เพื่ออนุญาต `null` ให้ใช้ร่วมกับ `nullable`

<a name="rule-timezone"></a>
#### timezone

ฟิลด์ต้องเป็นตัวระบุ timezone ที่ถูกต้อง (จาก `DateTimeZone::listIdentifiers`) สามารถส่งพารามิเตอร์ที่เมธอดนั้นรองรับได้:

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

ฟิลด์ต้องไม่ซ้ำในตารางที่ระบุ

**ระบุชื่อตาราง/คอลัมน์ที่กำหนดเอง:**

สามารถระบุชื่อคลาส model โดยตรงได้:

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

สามารถระบุชื่อคอลัมน์ได้ (ใช้ชื่อฟิลด์โดยค่าเริ่มต้นเมื่อไม่ระบุ):

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**ระบุการเชื่อมต่อฐานข้อมูล:**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**ข้าม ID ที่ระบุ:**

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
> `ignore` ไม่ควรรับอินพุตจากผู้ใช้ ใช้เฉพาะ ID ไม่ซ้ำที่ระบบสร้าง (auto-increment ID หรือ model UUID) เท่านั้น มิฉะนั้นอาจมีความเสี่ยง SQL injection

สามารถส่งอินสแตนซ์ model ได้:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

หากคีย์หลักไม่ใช่ `id` ให้ระบุชื่อคีย์หลัก:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

โดยค่าเริ่มต้นใช้ชื่อฟิลด์เป็นคอลัมน์ unique สามารถระบุชื่อคอลัมน์ได้:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**เพิ่มเงื่อนไขเพิ่มเติม:**

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

**ข้ามเรคอร์ดที่ลบแบบ soft-delete:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

หากคอลัมน์ soft delete ไม่ใช่ `deleted_at`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

ฟิลด์ต้องเป็นตัวพิมพ์ใหญ่

<a name="rule-url"></a>
#### url

ฟิลด์ต้องเป็น URL ที่ถูกต้อง

สามารถระบุโปรโตคอลที่อนุญาตได้:

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

ฟิลด์ต้องเป็น [ULID](https://github.com/ulid/spec) ที่ถูกต้อง

<a name="rule-uuid"></a>
#### uuid

ฟิลด์ต้องเป็น RFC 9562 UUID ที่ถูกต้อง (เวอร์ชัน 1, 3, 4, 5, 6, 7 หรือ 8)

สามารถระบุเวอร์ชันได้:

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```



<a name="think-validate"></a>
# ตัวตรวจสอบ top-think/think-validate

## คำอธิบาย

ตัวตรวจสอบอย่างเป็นทางการของ ThinkPHP

## URL โปรเจกต์

https://github.com/top-think/think-validate

## การติดตั้ง

`composer require topthink/think-validate`

## เริ่มต้นอย่างรวดเร็ว

**สร้าง `app/index/validate/User.php`**

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
  
**การใช้งาน**

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

> **หมายเหตุ**
> webman ไม่รองรับเมธอด `Validate::rule()` ของ think-validate

<a name="respect-validation"></a>
# ตัวตรวจสอบ workerman/validation

## คำอธิบาย

โปรเจกต์นี้เป็นเวอร์ชันที่แปลเป็นภาษาท้องถิ่นของ https://github.com/Respect/Validation

## URL โปรเจกต์

https://github.com/walkor/validation
  
  
## การติดตั้ง
 
```php
composer require workerman/validation
```

## เริ่มต้นอย่างรวดเร็ว

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
  
**เข้าถึงผ่าน jQuery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
ผลลัพธ์:

`{"code":500,"msg":"Username may only contain letters (a-z) and numbers (0-9)"}`

คำอธิบาย:

`v::input(array $input, array $rules)` ตรวจสอบและรวบรวมข้อมูล หากการตรวจสอบล้มเหลว จะโยน `Respect\Validation\Exceptions\ValidationException` เมื่อสำเร็จจะคืนค่าข้อมูลที่ตรวจสอบแล้ว (array)

หากโค้ดธุรกิจไม่จับข้อยกเว้นการตรวจสอบ เฟรมเวิร์ก webman จะจับและคืนค่า JSON (เช่น `{"code":500, "msg":"xxx"}`) หรือหน้าข้อยกเว้นปกติตาม HTTP headers หากรูปแบบการตอบกลับไม่ตรงตามความต้องการ สามารถจับ `ValidationException` และคืนค่าข้อมูลที่กำหนดเองได้ ดังตัวอย่างด้านล่าง:

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

## คู่มือตัวตรวจสอบ

```php
use Respect\Validation\Validator as v;

// การตรวจสอบกฎเดียว
$number = 123;
v::numericVal()->validate($number); // true

// การตรวจสอบแบบเชื่อมต่อ
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// รับเหตุผลการล้มเหลวครั้งแรก
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username may only contain letters (a-z) and numbers (0-9)
}

// รับเหตุผลการล้มเหลวทั้งหมด
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

// ข้อความข้อผิดพลาดที่กำหนดเอง
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

// ตรวจสอบอ็อบเจกต์
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// ตรวจสอบอาร์เรย์
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
  
// การตรวจสอบตัวเลือก
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// กฎปฏิเสธ
v::not(v::intVal())->validate(10); // false
```
  
## ความแตกต่างระหว่างเมธอด Validator `validate()` `check()` `assert()`

`validate()` คืนค่า boolean ไม่โยนข้อยกเว้น

`check()` โยนข้อยกเว้นเมื่อการตรวจสอบล้มเหลว รับเหตุผลการล้มเหลวครั้งแรกผ่าน `$exception->getMessage()`

`assert()` โยนข้อยกเว้นเมื่อการตรวจสอบล้มเหลว รับเหตุผลการล้มเหลวทั้งหมดผ่าน `$exception->getFullMessage()`
  
  
## กฎการตรวจสอบทั่วไป

`Alnum()` เฉพาะตัวอักษรและตัวเลข

`Alpha()` เฉพาะตัวอักษร

`ArrayType()` ประเภทอาร์เรย์

`Between(mixed $minimum, mixed $maximum)` ตรวจสอบว่าอินพุตอยู่ระหว่างสองค่า

`BoolType()` ประเภท boolean

`Contains(mixed $expectedValue)` ตรวจสอบว่าอินพุตมีค่าที่กำหนด

`ContainsAny(array $needles)` ตรวจสอบว่าอินพุตมีค่าที่กำหนดอย่างน้อยหนึ่งค่า

`Digit()` ตรวจสอบว่าอินพุตมีเฉพาะตัวเลข

`Domain()` ตรวจสอบชื่อโดเมนที่ถูกต้อง

`Email()` ตรวจสอบที่อยู่อีเมลที่ถูกต้อง

`Extension(string $extension)` ตรวจสอบนามสกุลไฟล์

`FloatType()` ประเภท float

`IntType()` ประเภท integer

`Ip()` ตรวจสอบที่อยู่ IP

`Json()` ตรวจสอบข้อมูล JSON

`Length(int $min, int $max)` ตรวจสอบความยาวอยู่ในช่วง

`LessThan(mixed $compareTo)` ตรวจสอบความยาวน้อยกว่าค่าที่กำหนด

`Lowercase()` ตรวจสอบตัวพิมพ์เล็ก

`MacAddress()` ตรวจสอบที่อยู่ MAC

`NotEmpty()` ตรวจสอบไม่ว่าง

`NullType()` ตรวจสอบ null

`Number()` ตรวจสอบตัวเลข

`ObjectType()` ตรวจสอบประเภทอ็อบเจกต์

`StringType()` ตรวจสอบประเภทสตริง

`Url()` ตรวจสอบ URL
  
ดู https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ สำหรับกฎการตรวจสอบเพิ่มเติม
  
## เพิ่มเติม

เยี่ยมชม https://respect-validation.readthedocs.io/en/2.0/
  
