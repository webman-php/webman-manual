# 기타 검증기

Composer에서 직접 사용할 수 있는 다양한 검증기가 있습니다. 예를 들어:

#### <a href="#webman-validation"> webman/validation (권장)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# 검증기 webman/validation

`illuminate/validation` 기반으로, 수동 검증, 어노테이션 검증, 매개변수 수준 검증, 재사용 가능한 규칙 세트를 제공합니다.

## 설치

```bash
composer require webman/validation
```

## 기본 개념

- **규칙 세트 재사용**: `support\validation\Validator`를 확장하여 재사용 가능한 `rules`, `messages`, `attributes`, `scenes`를 정의하며, 수동 검증과 어노테이션 검증 모두에서 재사용할 수 있습니다.
- **메서드 수준 어노테이션(Attribute) 검증**: PHP 8 attribute `#[Validate]`를 사용하여 컨트롤러 메서드에 검증을 바인딩합니다.
- **매개변수 수준 어노테이션(Attribute) 검증**: PHP 8 attribute `#[Param]`을 사용하여 컨트롤러 메서드 매개변수에 검증을 바인딩합니다.
- **예외 처리**: 검증 실패 시 `support\validation\ValidationException`을 발생시킵니다. 예외 클래스는 설정 가능합니다.
- **데이터베이스 검증**: 데이터베이스 검증이 필요한 경우 `composer require webman/database`를 설치해야 합니다.

## 수동 검증

### 기본 사용법

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **참고**
> `validate()`는 검증 실패 시 `support\validation\ValidationException`을 발생시킵니다. 예외를 발생시키지 않으려면 아래의 `fails()` 방식을 사용하여 오류 메시지를 얻을 수 있습니다.

### 사용자 정의 메시지 및 속성

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

### 예외 없이 검증 (오류 메시지 얻기)

예외를 발생시키지 않으려면 `fails()`로 확인하고 `errors()`를 통해 오류 메시지를 얻습니다 (`MessageBag` 반환):

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

## 규칙 세트 재사용 (사용자 정의 검증기)

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

### 수동 검증 재사용

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### 장면(scenes) 사용 (선택 사항)

`scenes`는 선택적 기능입니다. `withScene(...)`을 호출할 때 필드의 일부만 검증합니다.

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

// 장면 미지정 -> 모든 규칙 검증
UserValidator::make($data)->validate();

// 장면 지정 -> 해당 장면의 필드만 검증
UserValidator::make($data)->withScene('create')->validate();
```

## 어노테이션 검증 (메서드 수준)

### 직접 규칙

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

### 규칙 세트 재사용

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

### 다중 검증 중첩

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

### 검증 데이터 소스

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

`in` 매개변수로 데이터 소스를 지정합니다:

* **query** HTTP 요청 쿼리 매개변수, `$request->get()`에서
* **body** HTTP 요청 본문, `$request->post()`에서
* **path** HTTP 요청 경로 매개변수, `$request->route->param()`에서

`in`은 문자열 또는 배열일 수 있습니다. 배열인 경우 값이 순서대로 병합되며 나중 값이 이전 값을 덮어씁니다. `in`을 전달하지 않으면 기본값은 `['query', 'body', 'path']`입니다.


## 매개변수 수준 검증 (Param)

### 기본 사용법

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

### 검증 데이터 소스

마찬가지로 매개변수 수준 검증도 소스를 지정하는 `in` 매개변수를 지원합니다:

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


### rules는 문자열 또는 배열 지원

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

### 사용자 정의 메시지 / 속성

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

### 규칙 상수 재사용

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

## 메서드 수준 + 매개변수 수준 조합

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

## 자동 규칙 추론 (매개변수 시그니처 기반)

메서드에 `#[Validate]`가 사용되거나 해당 메서드의 매개변수 중 하나가 `#[Param]`을 사용하면, 이 컴포넌트는 **메서드 매개변수 시그니처에서 기본 검증 규칙을 자동으로 추론하여 완성**한 후 기존 규칙과 병합하여 검증합니다.

### 예시: `#[Validate]` 동등 확장

1) 규칙을 수동으로 작성하지 않고 `#[Validate]`만 활성화:

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

다음과 동일:

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

2) 일부 규칙만 작성하고 나머지는 매개변수 시그니처로 완성:

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

다음과 동일:

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

3) 기본값 / nullable 타입:

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

다음과 동일:

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

## 예외 처리

### 기본 예외

검증 실패 시 기본적으로 `support\validation\ValidationException`을 발생시키며, 이는 `Webman\Exception\BusinessException`을 확장하며 오류를 로깅하지 않습니다.

기본 응답 동작은 `BusinessException::render()`에서 처리합니다:

- 일반 요청: 문자열 메시지 반환, 예: `token is required.`
- JSON 요청: JSON 응답 반환, 예: `{"code": 422, "msg": "token is required.", "data":....}`

### 사용자 정의 예외를 통한 처리 사용자 정의

- 전역 설정: `config/plugin/webman/validation/app.php`의 `exception`

## 다국어 지원

이 컴포넌트에는 내장 중국어 및 영어 언어 팩이 포함되어 있으며 프로젝트 오버라이드를 지원합니다. 로드 순서:

1. 프로젝트 언어 팩 `resource/translations/{locale}/validation.php`
2. 컴포넌트 내장 `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Illuminate 내장 영어 (폴백)

> **참고**
> Webman 기본 언어는 `config/translation.php`에서 설정하거나 `locale('en');`으로 변경할 수 있습니다.

### 로컬 오버라이드 예시

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute is not a valid email format.',
];
```

## 미들웨어 자동 로딩

설치 후 컴포넌트는 `config/plugin/webman/validation/middleware.php`를 통해 검증 미들웨어를 자동 로딩합니다. 수동 등록이 필요하지 않습니다.

## 명령줄 생성

`make:validator` 명령을 사용하여 검증기 클래스를 생성합니다 (기본 출력 디렉터리: `app/validation`).

> **참고**
> `composer require webman/console` 필요

### 기본 사용법

- **빈 템플릿 생성**

```bash
php webman make:validator UserValidator
```

- **기존 파일 덮어쓰기**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### 테이블 구조에서 규칙 생성

- **테이블 이름 지정하여 기본 규칙 생성** (필드 타입/nullable/길이 등에서 `$rules` 추론; 기본적으로 ORM 관련 필드 제외: laravel은 `created_at/updated_at/deleted_at`, thinkorm은 `create_time/update_time/delete_time` 사용)

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **데이터베이스 연결 지정** (다중 연결 시나리오)

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### 장면(Scenes)

- **CRUD 장면 생성**: `create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> `update` 장면에는 기본 키 필드(레코드 식별용)와 기타 필드가 포함됩니다. `delete/detail`에는 기본적으로 기본 키만 포함됩니다.

### ORM 선택 (laravel (illuminate/database) vs think-orm)

- **자동 선택 (기본값)**: 설치/설정된 것을 사용; 둘 다 존재하면 기본적으로 illuminate 사용
- **강제 지정**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### 전체 예시

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## 단위 테스트

`webman/validation` 루트 디렉터리에서 실행:

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## 검증 규칙 참조

<a name="available-validation-rules"></a>
## 사용 가능한 검증 규칙

> [!IMPORTANT]
> - Webman Validation은 `illuminate/validation` 기반이며, 규칙 이름은 Laravel과 일치하며 Webman 전용 규칙은 없습니다.
> - 미들웨어는 기본적으로 경로 매개변수와 병합된 `$request->all()` (GET+POST)의 데이터를 검증하며, 업로드된 파일은 제외합니다. 파일 규칙의 경우 `$request->file()`을 데이터에 직접 병합하거나 `Validator::make`를 수동으로 호출하세요.
> - `current_password`는 auth guard에 의존합니다. `exists`/`unique`는 데이터베이스 연결 및 쿼리 빌더에 의존합니다. 해당 컴포넌트가 통합되지 않은 경우 이러한 규칙을 사용할 수 없습니다.

다음은 사용 가능한 모든 검증 규칙과 용도입니다:

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

필드는 `"yes"`, `"on"`, `1`, `"1"`, `true`, 또는 `"true"`여야 합니다. 서비스 약관 동의 확인 등에 일반적으로 사용됩니다.

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

다른 필드가 지정된 값과 같을 때 필드는 `"yes"`, `"on"`, `1`, `"1"`, `true`, 또는 `"true"`여야 합니다. 조건부 동의 시나리오에 일반적으로 사용됩니다.

<a name="rule-active-url"></a>
#### active_url

필드에 유효한 A 또는 AAAA 레코드가 있어야 합니다. 이 규칙은 먼저 `parse_url`로 URL 호스트명을 추출한 후 `dns_get_record`로 검증합니다.

<a name="rule-after"></a>
#### after:_date_

필드는 주어진 날짜 이후의 값이어야 합니다. 날짜는 유효한 `DateTime`으로 변환하기 위해 `strtotime`에 전달됩니다:

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

비교를 위해 다른 필드 이름을 전달할 수도 있습니다:

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

fluent `date` 규칙 빌더를 사용할 수 있습니다:

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

`afterToday`와 `todayOrAfter`는 "오늘 이후여야 함" 또는 "오늘 또는 그 이후여야 함"을 편리하게 표현합니다:

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

필드는 주어진 날짜 또는 그 이후여야 합니다. 자세한 내용은 [after](#rule-after)를 참조하세요.

fluent `date` 규칙 빌더를 사용할 수 있습니다:

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

`Rule::anyOf`는 "규칙 세트 중 하나를 만족"하도록 지정할 수 있습니다. 예를 들어 다음 규칙은 `username`이 이메일 주소이거나 6자 이상의 영숫자/밑줄/대시 문자열이어야 함을 의미합니다:

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

필드는 유니코드 문자([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) 및 [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=))여야 합니다.

ASCII(`a-z`, `A-Z`)만 허용하려면 `ascii` 옵션을 추가하세요:

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

필드는 유니코드 문자와 숫자([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)) 및 ASCII 하이픈(`-`)과 밑줄(`_`)만 포함할 수 있습니다.

ASCII(`a-z`, `A-Z`, `0-9`)만 허용하려면 `ascii` 옵션을 추가하세요:

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

필드는 유니코드 문자와 숫자([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=))만 포함할 수 있습니다.

ASCII(`a-z`, `A-Z`, `0-9`)만 허용하려면 `ascii` 옵션을 추가하세요:

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

필드는 PHP `array`여야 합니다.

`array` 규칙에 추가 매개변수가 있으면 입력 배열 키가 매개변수 목록에 있어야 합니다. 예시에서 `admin` 키는 허용 목록에 없으므로 유효하지 않습니다:

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

실제 프로젝트에서는 허용 배열 키를 명시적으로 정의하는 것이 권장됩니다.

<a name="rule-ascii"></a>
#### ascii

필드는 7비트 ASCII 문자만 포함할 수 있습니다.

<a name="rule-bail"></a>
#### bail

첫 번째 규칙이 실패하면 해당 필드에 대한 추가 규칙 검증을 중단합니다.

이 규칙은 현재 필드에만 영향을 미칩니다. "전역적으로 첫 번째 실패 시 중단"하려면 Illuminate 검증기를 직접 사용하고 `stopOnFirstFailure()`를 호출하세요.

<a name="rule-before"></a>
#### before:_date_

필드는 주어진 날짜 이전이어야 합니다. 날짜는 유효한 `DateTime`으로 변환하기 위해 `strtotime`에 전달됩니다. [after](#rule-after)처럼 비교를 위해 다른 필드 이름을 전달할 수 있습니다.

fluent `date` 규칙 빌더를 사용할 수 있습니다:

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

`beforeToday`와 `todayOrBefore`는 "오늘 이전이어야 함" 또는 "오늘 또는 그 이전이어야 함"을 편리하게 표현합니다:

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

필드는 주어진 날짜 또는 그 이전이어야 합니다. 날짜는 유효한 `DateTime`으로 변환하기 위해 `strtotime`에 전달됩니다. [after](#rule-after)처럼 비교를 위해 다른 필드 이름을 전달할 수 있습니다.

fluent `date` 규칙 빌더를 사용할 수 있습니다:

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

필드 크기는 _min_과 _max_(포함) 사이여야 합니다. 문자열, 숫자, 배열, 파일에 대한 평가는 [size](#rule-size)와 동일합니다.

<a name="rule-boolean"></a>
#### boolean

필드는 boolean으로 변환 가능해야 합니다. 허용되는 입력에는 `true`, `false`, `1`, `0`, `"1"`, `"0"`이 포함됩니다.

`strict` 매개변수를 사용하여 `true` 또는 `false`만 허용:

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

필드에 일치하는 `{field}_confirmation` 필드가 있어야 합니다. 예를 들어 필드가 `password`일 때 `password_confirmation`이 필요합니다.

사용자 정의 확인 필드 이름을 지정할 수도 있습니다. 예: `confirmed:repeat_username`은 `repeat_username`이 현재 필드와 일치해야 합니다.

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

필드는 배열이어야 하며 주어진 매개변수 값을 모두 포함해야 합니다. 이 규칙은 배열 검증에 일반적으로 사용됩니다. `Rule::contains`로 구성할 수 있습니다:

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

필드는 배열이어야 하며 주어진 매개변수 값 중 하나도 포함하지 않아야 합니다. `Rule::doesntContain`으로 구성할 수 있습니다:

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

필드는 현재 인증된 사용자의 비밀번호와 일치해야 합니다. 첫 번째 매개변수로 auth guard를 지정할 수 있습니다:

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> 이 규칙은 auth 컴포넌트 및 guard 설정에 의존합니다. auth가 통합되지 않은 경우 사용하지 마세요.

<a name="rule-date"></a>
#### date

필드는 `strtotime`으로 인식 가능한 유효한(비상대적) 날짜여야 합니다.

<a name="rule-date-equals"></a>
#### date_equals:_date_

필드는 주어진 날짜와 같아야 합니다. 날짜는 유효한 `DateTime`으로 변환하기 위해 `strtotime`에 전달됩니다.

<a name="rule-date-format"></a>
#### date_format:_format_,...

필드는 주어진 형식 중 하나와 일치해야 합니다. `date` 또는 `date_format` 중 하나를 사용하세요. 이 규칙은 모든 PHP [DateTime](https://www.php.net/manual/en/class.datetime.php) 형식을 지원합니다.

fluent `date` 규칙 빌더를 사용할 수 있습니다:

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

필드는 필요한 소수 자릿수를 가진 숫자여야 합니다:

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

필드는 `"no"`, `"off"`, `0`, `"0"`, `false`, 또는 `"false"`여야 합니다.

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

다른 필드가 지정된 값과 같을 때 필드는 `"no"`, `"off"`, `0`, `"0"`, `false`, 또는 `"false"`여야 합니다.

<a name="rule-different"></a>
#### different:_field_

필드는 _field_와 달라야 합니다.

<a name="rule-digits"></a>
#### digits:_value_

필드는 길이 _value_의 정수여야 합니다.

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

필드는 길이가 _min_과 _max_ 사이인 정수여야 합니다.

<a name="rule-dimensions"></a>
#### dimensions

필드는 이미지여야 하며 차원 제약을 만족해야 합니다:

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

사용 가능한 제약: _min\_width_, _max\_width_, _min\_height_, _max\_height_, _width_, _height_, _ratio_.

_ratio_는 종횡비이며 분수 또는 float로 표현할 수 있습니다:

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

이 규칙에는 매개변수가 많으므로 `Rule::dimensions`로 구성하는 것이 권장됩니다:

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

배열 검증 시 필드 값이 중복되면 안 됩니다:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

기본적으로 느슨한 비교를 사용합니다. 엄격한 비교를 위해 `strict` 추가:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

대소문자 차이를 무시하려면 `ignore_case` 추가:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

필드는 지정된 값 중 하나로 시작하면 안 됩니다.

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

필드는 지정된 값 중 하나로 끝나면 안 됩니다.

<a name="rule-email"></a>
#### email

필드는 유효한 이메일 주소여야 합니다. 이 규칙은 [egulias/email-validator](https://github.com/egulias/EmailValidator)에 의존하며, 기본적으로 `RFCValidation`을 사용하고 다른 검증 방법을 사용할 수 있습니다:

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

사용 가능한 검증 방법:

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` - RFC 사양에 따라 이메일 검증 ([지원 RFC](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs)).
- `strict`: `NoRFCWarningsValidation` - RFC 경고 시 실패 (예: 후행 점 또는 연속 점).
- `dns`: `DNSCheckValidation` - 도메인에 유효한 MX 레코드가 있는지 확인.
- `spoof`: `SpoofCheckValidation` - 동형 문자 또는 스푸핑 유니코드 문자 방지.
- `filter`: `FilterEmailValidation` - PHP `filter_var`로 검증.
- `filter_unicode`: `FilterEmailValidation::unicode()` - 유니코드를 허용하는 `filter_var` 검증.

</div>

fluent 규칙 빌더를 사용할 수 있습니다:

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
> `dns`와 `spoof`는 PHP `intl` 확장이 필요합니다.

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

필드는 지정된 문자 인코딩과 일치해야 합니다. 이 규칙은 파일 또는 문자열 인코딩 감지에 `mb_check_encoding`을 사용합니다. 파일 규칙 빌더와 함께 사용할 수 있습니다:

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

필드는 지정된 값 중 하나로 끝나야 합니다.

<a name="rule-enum"></a>
#### enum

`Enum`은 필드 값이 유효한 enum 값인지 검증하는 클래스 기반 규칙입니다. 구성 시 enum 클래스 이름을 전달합니다. 기본값의 경우 Backed Enum을 사용하세요:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

`only`/`except`로 enum 값을 제한:

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

조건부 제한에 `when` 사용:

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

필드는 `validate`/`validated`에서 반환되는 데이터에서 제외됩니다.

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

_anotherfield_가 _value_와 같을 때 필드는 `validate`/`validated`에서 반환되는 데이터에서 제외됩니다.

복잡한 조건의 경우 `Rule::excludeIf` 사용:

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

_anotherfield_가 _value_와 같지 않으면 필드는 `validate`/`validated`에서 반환되는 데이터에서 제외됩니다. _value_가 `null`인 경우(예: `exclude_unless:name,null`) 비교 필드가 `null`이거나 없을 때만 필드가 유지됩니다.

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

_anotherfield_가 존재할 때 필드는 `validate`/`validated`에서 반환되는 데이터에서 제외됩니다.

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

_anotherfield_가 존재하지 않을 때 필드는 `validate`/`validated`에서 반환되는 데이터에서 제외됩니다.

<a name="rule-exists"></a>
#### exists:_table_,_column_

필드는 지정된 데이터베이스 테이블에 존재해야 합니다.

<a name="basic-usage-of-exists-rule"></a>
#### Exists 규칙 기본 사용법

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

`column`을 지정하지 않으면 필드 이름이 기본으로 사용됩니다. 따라서 이 예시는 `states` 테이블에 `state` 컬럼이 존재하는지 검증합니다.

<a name="specifying-a-custom-column-name"></a>
#### 사용자 정의 컬럼 이름 지정

테이블 이름 뒤에 컬럼 이름 추가:

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

데이터베이스 연결을 지정하려면 테이블 이름 앞에 연결 이름 접두사:

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

모델 클래스 이름을 전달할 수도 있습니다. 프레임워크가 테이블 이름을 해석합니다:

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

사용자 정의 쿼리 조건의 경우 `Rule` 빌더 사용:

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

`Rule::exists`에서 직접 컬럼 이름을 지정할 수도 있습니다:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'state' => [Rule::exists('states', 'abbreviation')],
])->validate();
```

값 집합이 존재하는지 검증하려면 `array` 규칙과 결합:

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

`array`와 `exists`가 모두 있으면 단일 쿼리로 모든 값을 검증합니다.

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

업로드된 파일 확장자가 허용 목록에 있는지 검증합니다:

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> 파일 타입 검증에 확장자만 의존하지 마세요. [mimes](#rule-mimes) 또는 [mimetypes](#rule-mimetypes)와 함께 사용하세요.

<a name="rule-file"></a>
#### file

필드는 성공적으로 업로드된 파일이어야 합니다.

<a name="rule-filled"></a>
#### filled

필드가 존재할 때 값이 비어 있으면 안 됩니다.

<a name="rule-gt"></a>
#### gt:_field_

필드는 주어진 _field_ 또는 _value_보다 커야 합니다. 두 필드는 같은 타입이어야 합니다. 문자열, 숫자, 배열, 파일에 대한 평가는 [size](#rule-size)와 동일합니다.

<a name="rule-gte"></a>
#### gte:_field_

필드는 주어진 _field_ 또는 _value_보다 크거나 같아야 합니다. 두 필드는 같은 타입이어야 합니다. 문자열, 숫자, 배열, 파일에 대한 평가는 [size](#rule-size)와 동일합니다.

<a name="rule-hex-color"></a>
#### hex_color

필드는 유효한 [hex color value](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color)여야 합니다.

<a name="rule-image"></a>
#### image

필드는 이미지(jpg, jpeg, png, bmp, gif 또는 webp)여야 합니다.

> [!WARNING]
> XSS 위험으로 인해 기본적으로 SVG는 허용되지 않습니다. 허용하려면 `allow_svg` 추가: `image:allow_svg`.

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

필드는 주어진 값 목록에 있어야 합니다. `Rule::in`으로 구성할 수 있습니다:

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

`array` 규칙과 결합하면 입력 배열의 각 값이 `in` 목록에 있어야 합니다:

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

필드는 _anotherfield_의 값 목록에 존재해야 합니다.

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

필드는 배열이어야 하며 주어진 값 중 하나 이상을 키로 포함해야 합니다:

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

필드는 정수여야 합니다.

필드 타입이 정수여야 하도록 `strict` 매개변수 사용; 문자열 정수는 유효하지 않음:

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> 이 규칙은 PHP의 `FILTER_VALIDATE_INT`를 통과하는지만 검증합니다. 엄격한 숫자 타입의 경우 [numeric](#rule-numeric)과 함께 사용하세요.

<a name="rule-ip"></a>
#### ip

필드는 유효한 IP 주소여야 합니다.

<a name="rule-ipv4"></a>
#### ipv4

필드는 유효한 IPv4 주소여야 합니다.

<a name="rule-ipv6"></a>
#### ipv6

필드는 유효한 IPv6 주소여야 합니다.

<a name="rule-json"></a>
#### json

필드는 유효한 JSON 문자열이어야 합니다.

<a name="rule-lt"></a>
#### lt:_field_

필드는 주어진 _field_보다 작아야 합니다. 두 필드는 같은 타입이어야 합니다. 문자열, 숫자, 배열, 파일에 대한 평가는 [size](#rule-size)와 동일합니다.

<a name="rule-lte"></a>
#### lte:_field_

필드는 주어진 _field_보다 작거나 같아야 합니다. 두 필드는 같은 타입이어야 합니다. 문자열, 숫자, 배열, 파일에 대한 평가는 [size](#rule-size)와 동일합니다.

<a name="rule-lowercase"></a>
#### lowercase

필드는 소문자여야 합니다.

<a name="rule-list"></a>
#### list

필드는 리스트 배열이어야 합니다. 리스트 배열 키는 0부터 `count($array) - 1`까지의 연속된 숫자여야 합니다.

<a name="rule-mac"></a>
#### mac_address

필드는 유효한 MAC 주소여야 합니다.

<a name="rule-max"></a>
#### max:_value_

필드는 _value_보다 작거나 같아야 합니다. 문자열, 숫자, 배열, 파일에 대한 평가는 [size](#rule-size)와 동일합니다.

<a name="rule-max-digits"></a>
#### max_digits:_value_

필드는 길이가 _value_를 초과하지 않는 정수여야 합니다.

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

파일의 MIME 타입이 목록에 있는지 검증합니다:

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

MIME 타입은 파일 내용을 읽어 추측하며 클라이언트 제공 MIME과 다를 수 있습니다.

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

파일의 MIME 타입이 주어진 확장자에 해당하는지 검증합니다:

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

매개변수는 확장자이지만 이 규칙은 MIME을 결정하기 위해 파일 내용을 읽습니다. 확장자-MIME 매핑:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### MIME 타입과 확장자

이 규칙은 "파일 확장자"가 "실제 MIME"과 일치하는지 검증하지 않습니다. 예를 들어 `mimes:png`는 PNG 내용의 `photo.txt`를 유효로 처리합니다. 확장자를 검증하려면 [extensions](#rule-extensions)를 사용하세요.

<a name="rule-min"></a>
#### min:_value_

필드는 _value_보다 크거나 같아야 합니다. 문자열, 숫자, 배열, 파일에 대한 평가는 [size](#rule-size)와 동일합니다.

<a name="rule-min-digits"></a>
#### min_digits:_value_

필드는 길이가 _value_보다 작지 않은 정수여야 합니다.

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

필드는 _value_의 배수여야 합니다.

<a name="rule-missing"></a>
#### missing

필드는 입력 데이터에 존재하면 안 됩니다.

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

_anotherfield_가 _value_ 중 하나와 같을 때 필드는 존재하면 안 됩니다.

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

_anotherfield_가 _value_ 중 하나와 같지 않으면 필드는 존재하면 안 됩니다.

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

지정된 필드 중 하나가 존재할 때 필드는 존재하면 안 됩니다.

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

지정된 모든 필드가 존재할 때 필드는 존재하면 안 됩니다.

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

필드는 주어진 값 목록에 있으면 안 됩니다. `Rule::notIn`으로 구성할 수 있습니다:

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

필드는 주어진 정규식과 일치하면 안 됩니다.

이 규칙은 PHP `preg_match`를 사용합니다. 정규식에는 구분자가 있어야 합니다. 예: `'email' => 'not_regex:/^.+$/i'`.

> [!WARNING]
> `regex`/`not_regex` 사용 시 정규식에 `|`가 포함되면 `|` 구분자와의 충돌을 피하기 위해 배열 형식을 사용하세요.

<a name="rule-nullable"></a>
#### nullable

필드는 `null`일 수 있습니다.

<a name="rule-numeric"></a>
#### numeric

필드는 [numeric](https://www.php.net/manual/en/function.is-numeric.php)이어야 합니다.

`strict` 매개변수를 사용하여 정수 또는 float 타입만 허용; 숫자 문자열은 유효하지 않음:

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

필드는 입력 데이터에 존재해야 합니다.

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

_anotherfield_가 _value_ 중 하나와 같을 때 필드는 존재해야 합니다.

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

_anotherfield_가 _value_ 중 하나와 같지 않으면 필드는 존재해야 합니다.

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

지정된 필드 중 하나가 존재할 때 필드는 존재해야 합니다.

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

지정된 모든 필드가 존재할 때 필드는 존재해야 합니다.

<a name="rule-prohibited"></a>
#### prohibited

필드는 없거나 비어 있어야 합니다. "비어 있음"의 의미:

<div class="content-list" markdown="1">

- 값이 `null`.
- 값이 빈 문자열.
- 값이 빈 배열 또는 빈 `Countable` 객체.
- 빈 경로의 업로드된 파일.

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

_anotherfield_가 _value_ 중 하나와 같을 때 필드는 없거나 비어 있어야 합니다. "비어 있음"의 의미:

<div class="content-list" markdown="1">

- 값이 `null`.
- 값이 빈 문자열.
- 값이 빈 배열 또는 빈 `Countable` 객체.
- 빈 경로의 업로드된 파일.

</div>

복잡한 조건의 경우 `Rule::prohibitedIf` 사용:

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

_anotherfield_가 `"yes"`, `"on"`, `1`, `"1"`, `true`, 또는 `"true"`일 때 필드는 없거나 비어 있어야 합니다.

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

_anotherfield_가 `"no"`, `"off"`, `0`, `"0"`, `false`, 또는 `"false"`일 때 필드는 없거나 비어 있어야 합니다.

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

_anotherfield_가 _value_ 중 하나와 같지 않으면 필드는 없거나 비어 있어야 합니다. "비어 있음"의 의미:

<div class="content-list" markdown="1">

- 값이 `null`.
- 값이 빈 문자열.
- 값이 빈 배열 또는 빈 `Countable` 객체.
- 빈 경로의 업로드된 파일.

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

필드가 존재하고 비어 있지 않을 때 _anotherfield_의 모든 필드는 없거나 비어 있어야 합니다. "비어 있음"의 의미:

<div class="content-list" markdown="1">

- 값이 `null`.
- 값이 빈 문자열.
- 값이 빈 배열 또는 빈 `Countable` 객체.
- 빈 경로의 업로드된 파일.

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

필드는 주어진 정규식과 일치해야 합니다.

이 규칙은 PHP `preg_match`를 사용합니다. 정규식에는 구분자가 있어야 합니다. 예: `'email' => 'regex:/^.+@.+$/i'`.

> [!WARNING]
> `regex`/`not_regex` 사용 시 정규식에 `|`가 포함되면 `|` 구분자와의 충돌을 피하기 위해 배열 형식을 사용하세요.

<a name="rule-required"></a>
#### required

필드는 존재하고 비어 있으면 안 됩니다. "비어 있음"의 의미:

<div class="content-list" markdown="1">

- 값이 `null`.
- 값이 빈 문자열.
- 값이 빈 배열 또는 빈 `Countable` 객체.
- 빈 경로의 업로드된 파일.

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

_anotherfield_가 _value_ 중 하나와 같을 때 필드는 존재하고 비어 있으면 안 됩니다.

복잡한 조건의 경우 `Rule::requiredIf` 사용:

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

_anotherfield_가 `"yes"`, `"on"`, `1`, `"1"`, `true`, 또는 `"true"`일 때 필드는 존재하고 비어 있으면 안 됩니다.

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

_anotherfield_가 `"no"`, `"off"`, `0`, `"0"`, `false`, 또는 `"false"`일 때 필드는 존재하고 비어 있으면 안 됩니다.

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

_anotherfield_가 _value_ 중 하나와 같지 않으면 필드는 존재하고 비어 있으면 안 됩니다. _value_가 `null`인 경우(예: `required_unless:name,null`) 비교 필드가 `null`이거나 없을 때만 필드가 비어 있을 수 있습니다.

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

지정된 필드 중 하나가 존재하고 비어 있지 않을 때 필드는 존재하고 비어 있으면 안 됩니다.

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

지정된 모든 필드가 존재하고 비어 있지 않을 때 필드는 존재하고 비어 있으면 안 됩니다.

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

지정된 필드 중 하나가 비어 있거나 없을 때 필드는 존재하고 비어 있으면 안 됩니다.

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

지정된 모든 필드가 비어 있거나 없을 때 필드는 존재하고 비어 있으면 안 됩니다.

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

필드는 배열이어야 하며 지정된 키를 최소한 포함해야 합니다.

<a name="validating-when-present"></a>
#### sometimes

필드가 존재할 때만 후속 검증 규칙을 적용합니다. "선택 사항이지만 존재할 때는 유효해야 함" 필드에 일반적으로 사용됩니다:

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

필드는 _field_와 같아야 합니다.

<a name="rule-size"></a>
#### size:_value_

필드 크기는 주어진 _value_와 같아야 합니다. 문자열: 문자 수; 숫자: 지정된 정수 (`numeric` 또는 `integer`와 함께 사용); 배열: 요소 수; 파일: KB 단위 크기. 예시:

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

필드는 지정된 값 중 하나로 시작해야 합니다.

<a name="rule-string"></a>
#### string

필드는 문자열이어야 합니다. `null`을 허용하려면 `nullable`과 함께 사용하세요.

<a name="rule-timezone"></a>
#### timezone

필드는 유효한 타임존 식별자(`DateTimeZone::listIdentifiers`에서)여야 합니다. 해당 메서드에서 지원하는 매개변수를 전달할 수 있습니다:

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

필드는 지정된 테이블에서 고유해야 합니다.

**사용자 정의 테이블/컬럼 이름 지정:**

모델 클래스 이름을 직접 지정할 수 있습니다:

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

컬럼 이름을 지정할 수 있습니다 (지정하지 않으면 필드 이름이 기본값):

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**데이터베이스 연결 지정:**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**지정된 ID 무시:**

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
> `ignore`는 사용자 입력을 받으면 안 됩니다. 시스템 생성 고유 ID(자동 증가 ID 또는 모델 UUID)만 사용하세요. 그렇지 않으면 SQL 인젝션 위험이 있을 수 있습니다.

모델 인스턴스도 전달할 수 있습니다:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

기본 키가 `id`가 아니면 기본 키 이름 지정:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

기본적으로 필드 이름을 고유 컬럼으로 사용합니다. 컬럼 이름도 지정할 수 있습니다:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**추가 조건 추가:**

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

**소프트 삭제된 레코드 무시:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

소프트 삭제 컬럼이 `deleted_at`이 아닌 경우:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

필드는 대문자여야 합니다.

<a name="rule-url"></a>
#### url

필드는 유효한 URL이어야 합니다.

허용 프로토콜 지정 가능:

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

필드는 유효한 [ULID](https://github.com/ulid/spec)여야 합니다.

<a name="rule-uuid"></a>
#### uuid

필드는 유효한 RFC 9562 UUID(버전 1, 3, 4, 5, 6, 7 또는 8)여야 합니다.

버전 지정 가능:

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```



<a name="think-validate"></a>
# 검증기 top-think/think-validate

## 설명

ThinkPHP 공식 검증기

## 프로젝트 URL

https://github.com/top-think/think-validate

## 설치

`composer require topthink/think-validate`

## 빠른 시작

**`app/index/validate/User.php` 생성**

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
  
**사용법**

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

> **참고**
> webman은 think-validate의 `Validate::rule()` 메서드를 지원하지 않습니다.

<a name="respect-validation"></a>
# 검증기 workerman/validation

## 설명

이 프로젝트는 https://github.com/Respect/Validation 의 현지화 버전입니다.

## 프로젝트 URL

https://github.com/walkor/validation
  
  
## 설치
 
```php
composer require workerman/validation
```

## 빠른 시작

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
  
**jQuery로 접근**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
결과:

`{"code":500,"msg":"Username may only contain letters (a-z) and numbers (0-9)"}`

설명:

`v::input(array $input, array $rules)`는 데이터를 검증하고 수집합니다. 검증 실패 시 `Respect\Validation\Exceptions\ValidationException`을 발생시킵니다. 성공 시 검증된 데이터(배열)를 반환합니다.

비즈니스 코드가 검증 예외를 캐치하지 않으면 webman 프레임워크가 캐치하여 HTTP 헤더에 따라 JSON(예: `{"code":500, "msg":"xxx"}`) 또는 일반 예외 페이지를 반환합니다. 응답 형식이 요구 사항을 충족하지 않으면 아래 예시처럼 `ValidationException`을 캐치하여 사용자 정의 데이터를 반환할 수 있습니다:

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

## 검증기 가이드

```php
use Respect\Validation\Validator as v;

// 단일 규칙 검증
$number = 123;
v::numericVal()->validate($number); // true

// 체인 검증
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// 첫 번째 검증 실패 사유 얻기
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username may only contain letters (a-z) and numbers (0-9)
}

// 모든 검증 실패 사유 얻기
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // 출력:
    // -  Username must satisfy the following rules
    //     - Username may only contain letters (a-z) and numbers (0-9)
    //     - Username must not contain whitespace
  
    var_export($exception->getMessages());
    // 출력:
    // array (
    //   'alnum' => 'Username may only contain letters (a-z) and numbers (0-9)',
    //   'noWhitespace' => 'Username must not contain whitespace',
    // )
}

// 사용자 정의 오류 메시지
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Username may only contain letters and numbers',
        'noWhitespace' => 'Username must not contain spaces',
        'length' => 'length satisfies the rule, so this will not be shown'
    ]);
    // 출력 
    // array(
    //    'alnum' => 'Username may only contain letters and numbers',
    //    'noWhitespace' => 'Username must not contain spaces'
    // )
}

// 객체 검증
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// 배열 검증
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
    ->assert($data); // check() 또는 validate()도 사용 가능
  
// 선택적 검증
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// 부정 규칙
v::not(v::intVal())->validate(10); // false
```
  
## 검증기 메서드 `validate()` `check()` `assert()` 차이

`validate()`는 boolean을 반환하며 예외를 발생시키지 않음

`check()`는 검증 실패 시 예외 발생; `$exception->getMessage()`로 첫 번째 실패 사유 얻기

`assert()`는 검증 실패 시 예외 발생; `$exception->getFullMessage()`로 모든 실패 사유 얻기
  
  
## 일반 검증 규칙

`Alnum()` 문자와 숫자만

`Alpha()` 문자만

`ArrayType()` 배열 타입

`Between(mixed $minimum, mixed $maximum)` 입력이 두 값 사이인지 검증합니다.

`BoolType()` boolean 타입 검증

`Contains(mixed $expectedValue)` 입력에 특정 값이 포함되는지 검증

`ContainsAny(array $needles)` 입력에 정의된 값 중 하나 이상이 포함되는지 검증

`Digit()` 입력에 숫자만 포함되는지 검증

`Domain()` 유효한 도메인 이름 검증

`Email()` 유효한 이메일 주소 검증

`Extension(string $extension)` 파일 확장자 검증

`FloatType()` float 타입 검증

`IntType()` integer 타입 검증

`Ip()` IP 주소 검증

`Json()` JSON 데이터 검증

`Length(int $min, int $max)` 길이가 범위 내인지 검증

`LessThan(mixed $compareTo)` 길이가 주어진 값보다 작은지 검증

`Lowercase()` 소문자 검증

`MacAddress()` MAC 주소 검증

`NotEmpty()` 비어 있지 않은지 검증

`NullType()` null 검증

`Number()` 숫자 검증

`ObjectType()` 객체 타입 검증

`StringType()` 문자열 타입 검증

`Url()` URL 검증
  
더 많은 검증 규칙은 https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ 를 참조하세요.
  
## 더 알아보기

https://respect-validation.readthedocs.io/en/2.0/ 방문
  
