# Các trình xác thực khác

Có nhiều trình xác thực có sẵn trong composer có thể sử dụng trực tiếp, chẳng hạn như:

#### <a href="#webman-validation"> webman/validation (Khuyến nghị)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# Trình xác thực webman/validation

Dựa trên `illuminate/validation`, cung cấp xác thực thủ công, xác thực bằng annotation, xác thực cấp tham số và bộ quy tắc tái sử dụng.

## Cài đặt

```bash
composer require webman/validation
```

## Khái niệm cơ bản

- **Tái sử dụng bộ quy tắc**: Định nghĩa `rules`, `messages`, `attributes` và `scenes` tái sử dụng bằng cách mở rộng `support\validation\Validator`, có thể tái sử dụng trong cả xác thực thủ công và xác thực bằng annotation.
- **Xác thực annotation cấp phương thức (Attribute)**: Sử dụng PHP 8 attribute `#[Validate]` để liên kết xác thực với các phương thức controller.
- **Xác thực annotation cấp tham số (Attribute)**: Sử dụng PHP 8 attribute `#[Param]` để liên kết xác thực với các tham số phương thức controller.
- **Xử lý ngoại lệ**: Ném `support\validation\ValidationException` khi xác thực thất bại; lớp ngoại lệ có thể cấu hình được.
- **Xác thực cơ sở dữ liệu**: Nếu có xác thực cơ sở dữ liệu, cần cài đặt `composer require webman/database`.

## Xác thực thủ công

### Cách dùng cơ bản

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **Lưu ý**
> `validate()` ném `support\validation\ValidationException` khi xác thực thất bại. Nếu bạn không muốn ném ngoại lệ, hãy dùng phương pháp `fails()` bên dưới để lấy thông báo lỗi.

### Thông báo và thuộc tính tùy chỉnh

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

### Xác thực không ném ngoại lệ (Lấy thông báo lỗi)

Nếu bạn không muốn ném ngoại lệ, hãy dùng `fails()` để kiểm tra và lấy thông báo lỗi qua `errors()` (trả về `MessageBag`):

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

## Tái sử dụng bộ quy tắc (Trình xác thực tùy chỉnh)

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

### Tái sử dụng xác thực thủ công

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### Sử dụng scenes (Tùy chọn)

`scenes` là tính năng tùy chọn; nó chỉ xác thực một tập con các trường khi bạn gọi `withScene(...)`.

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

// Không chỉ định scene -> xác thực tất cả quy tắc
UserValidator::make($data)->validate();

// Chỉ định scene -> chỉ xác thực các trường trong scene đó
UserValidator::make($data)->withScene('create')->validate();
```

## Xác thực bằng Annotation (Cấp phương thức)

### Quy tắc trực tiếp

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

### Tái sử dụng bộ quy tắc

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

### Nhiều lớp xác thực chồng lên nhau

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

### Nguồn dữ liệu xác thực

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

Sử dụng tham số `in` để chỉ định nguồn dữ liệu:

* **query** Tham số truy vấn HTTP request, từ `$request->get()`
* **body** Nội dung body HTTP request, từ `$request->post()`
* **path** Tham số đường dẫn HTTP request, từ `$request->route->param()`

`in` có thể là chuỗi hoặc mảng; khi là mảng, các giá trị được hợp nhất theo thứ tự với giá trị sau ghi đè giá trị trước. Khi không truyền `in`, mặc định là `['query', 'body', 'path']`.


## Xác thực cấp tham số (Param)

### Cách dùng cơ bản

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

### Nguồn dữ liệu xác thực

Tương tự, xác thực cấp tham số cũng hỗ trợ tham số `in` để chỉ định nguồn:

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


### rules hỗ trợ chuỗi hoặc mảng

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

### Thông báo / thuộc tính tùy chỉnh

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

### Tái sử dụng hằng số quy tắc

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

## Kết hợp cấp phương thức + cấp tham số

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

## Suy luận quy tắc tự động (Dựa trên chữ ký tham số)

Khi sử dụng `#[Validate]` trên một phương thức, hoặc bất kỳ tham số nào của phương thức đó sử dụng `#[Param]`, component này **tự động suy luận và bổ sung các quy tắc xác thực cơ bản từ chữ ký tham số phương thức**, sau đó hợp nhất với các quy tắc hiện có trước khi xác thực.

### Ví dụ: Mở rộng tương đương `#[Validate]`

1) Chỉ bật `#[Validate]` mà không viết quy tắc thủ công:

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

Tương đương với:

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

2) Chỉ viết một phần quy tắc, phần còn lại được bổ sung theo chữ ký tham số:

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

Tương đương với:

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

3) Giá trị mặc định / kiểu nullable:

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

Tương đương với:

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

## Xử lý ngoại lệ

### Ngoại lệ mặc định

Xác thực thất bại ném `support\validation\ValidationException` theo mặc định, lớp này mở rộng `Webman\Exception\BusinessException` và không ghi log lỗi.

Hành vi phản hồi mặc định được xử lý bởi `BusinessException::render()`:

- Request thông thường: trả về chuỗi thông báo, ví dụ `token is required.`
- Request JSON: trả về phản hồi JSON, ví dụ `{"code": 422, "msg": "token is required.", "data":....}`

### Tùy chỉnh xử lý qua ngoại lệ tùy chỉnh

- Cấu hình toàn cục: `exception` trong `config/plugin/webman/validation/app.php`

## Hỗ trợ đa ngôn ngữ

Component bao gồm gói ngôn ngữ tiếng Trung và tiếng Anh tích hợp sẵn và hỗ trợ ghi đè theo dự án. Thứ tự tải:

1. Gói ngôn ngữ dự án `resource/translations/{locale}/validation.php`
2. Tích hợp sẵn của component `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Tiếng Anh tích hợp sẵn của Illuminate (dự phòng)

> **Lưu ý**
> Ngôn ngữ mặc định của Webman được cấu hình trong `config/translation.php`, hoặc có thể thay đổi qua `locale('en');`.

### Ví dụ ghi đè cục bộ

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute is not a valid email format.',
];
```

## Tự động tải Middleware

Sau khi cài đặt, component tự động tải middleware xác thực qua `config/plugin/webman/validation/middleware.php`; không cần đăng ký thủ công.

## Tạo từ dòng lệnh

Sử dụng lệnh `make:validator` để tạo các lớp trình xác thực (mặc định xuất ra thư mục `app/validation`).

> **Lưu ý**
> Yêu cầu `composer require webman/console`

### Cách dùng cơ bản

- **Tạo mẫu trống**

```bash
php webman make:validator UserValidator
```

- **Ghi đè file đã tồn tại**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### Tạo quy tắc từ cấu trúc bảng

- **Chỉ định tên bảng để tạo quy tắc cơ bản** (suy luận `$rules` từ kiểu trường/nullable/độ dài v.v.; loại trừ các trường liên quan ORM theo mặc định: laravel dùng `created_at/updated_at/deleted_at`, thinkorm dùng `create_time/update_time/delete_time`)

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **Chỉ định kết nối cơ sở dữ liệu** (trường hợp đa kết nối)

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### Scenes

- **Tạo scenes CRUD**: `create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> Scene `update` bao gồm trường khóa chính (để định vị bản ghi) cộng các trường khác; `delete/detail` mặc định chỉ bao gồm khóa chính.

### Chọn ORM (laravel (illuminate/database) vs think-orm)

- **Tự chọn (mặc định)**: Sử dụng cái nào được cài đặt/cấu hình; khi cả hai tồn tại, mặc định dùng illuminate
- **Bắt buộc chỉ định**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### Ví dụ đầy đủ

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## Unit Tests

Từ thư mục gốc `webman/validation`, chạy:

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## Tham chiếu quy tắc xác thực

<a name="available-validation-rules"></a>
## Các quy tắc xác thực có sẵn

> [!IMPORTANT]
> - Webman Validation dựa trên `illuminate/validation`; tên quy tắc khớp với Laravel và không có quy tắc đặc thù của Webman.
> - Middleware mặc định xác thực dữ liệu từ `$request->all()` (GET+POST) hợp nhất với tham số route, loại trừ file tải lên; đối với quy tắc file, hãy hợp nhất `$request->file()` vào dữ liệu thủ công, hoặc gọi `Validator::make` thủ công.
> - `current_password` phụ thuộc vào auth guard; `exists`/`unique` phụ thuộc vào kết nối cơ sở dữ liệu và query builder; các quy tắc này không khả dụng khi component tương ứng chưa được tích hợp.

Danh sách sau liệt kê tất cả các quy tắc xác thực có sẵn và mục đích của chúng:

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

Trường phải là `"yes"`, `"on"`, `1`, `"1"`, `true`, hoặc `"true"`. Thường dùng cho các tình huống như xác nhận người dùng đồng ý điều khoản dịch vụ.

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

Khi trường khác bằng giá trị chỉ định, trường phải là `"yes"`, `"on"`, `1`, `"1"`, `true`, hoặc `"true"`. Thường dùng cho các tình huống đồng ý có điều kiện.

<a name="rule-active-url"></a>
#### active_url

Trường phải có bản ghi A hoặc AAAA hợp lệ. Quy tắc này trước tiên dùng `parse_url` để trích xuất hostname URL, sau đó xác thực bằng `dns_get_record`.

<a name="rule-after"></a>
#### after:_date_

Trường phải là giá trị sau ngày đã cho. Ngày được truyền vào `strtotime` để chuyển thành `DateTime` hợp lệ:

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

Bạn cũng có thể truyền tên trường khác để so sánh:

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

Bạn có thể dùng fluent rule builder `date`:

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

`afterToday` và `todayOrAfter` biểu thị tiện lợi "phải sau hôm nay" hoặc "phải là hôm nay hoặc sau":

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

Trường phải vào hoặc sau ngày đã cho. Xem [after](#rule-after) để biết thêm chi tiết.

Bạn có thể dùng fluent rule builder `date`:

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

`Rule::anyOf` cho phép chỉ định "thỏa mãn bất kỳ một bộ quy tắc nào". Ví dụ, quy tắc sau có nghĩa `username` phải là địa chỉ email hoặc chuỗi chữ-số/gạch dưới/gạch ngang có ít nhất 6 ký tự:

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

Trường phải là chữ cái Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) và [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)).

Để chỉ cho phép ASCII (`a-z`, `A-Z`), thêm tùy chọn `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

Trường chỉ được chứa chữ cái và số Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)), cộng gạch ngang ASCII (`-`) và gạch dưới (`_`).

Để chỉ cho phép ASCII (`a-z`, `A-Z`, `0-9`), thêm tùy chọn `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

Trường chỉ được chứa chữ cái và số Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)).

Để chỉ cho phép ASCII (`a-z`, `A-Z`, `0-9`), thêm tùy chọn `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

Trường phải là mảng PHP `array`.

Khi quy tắc `array` có tham số bổ sung, các khóa mảng đầu vào phải nằm trong danh sách tham số. Trong ví dụ, khóa `admin` không có trong danh sách cho phép, nên không hợp lệ:

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

Khuyến nghị định nghĩa rõ ràng các khóa mảng được phép trong dự án thực tế.

<a name="rule-ascii"></a>
#### ascii

Trường chỉ được chứa ký tự ASCII 7-bit.

<a name="rule-bail"></a>
#### bail

Dừng xác thực các quy tắc tiếp theo cho trường khi quy tắc đầu tiên thất bại.

Quy tắc này chỉ ảnh hưởng đến trường hiện tại. Để "dừng khi thất bại đầu tiên toàn cục", hãy dùng validator Illuminate trực tiếp và gọi `stopOnFirstFailure()`.

<a name="rule-before"></a>
#### before:_date_

Trường phải trước ngày đã cho. Ngày được truyền vào `strtotime` để chuyển thành `DateTime` hợp lệ. Giống [after](#rule-after), bạn có thể truyền tên trường khác để so sánh.

Bạn có thể dùng fluent rule builder `date`:

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

`beforeToday` và `todayOrBefore` biểu thị tiện lợi "phải trước hôm nay" hoặc "phải là hôm nay hoặc trước":

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

Trường phải vào hoặc trước ngày đã cho. Ngày được truyền vào `strtotime` để chuyển thành `DateTime` hợp lệ. Giống [after](#rule-after), bạn có thể truyền tên trường khác để so sánh.

Bạn có thể dùng fluent rule builder `date`:

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

Kích thước trường phải nằm giữa _min_ và _max_ (bao gồm). Đánh giá cho chuỗi, số, mảng và file giống [size](#rule-size).

<a name="rule-boolean"></a>
#### boolean

Trường phải chuyển đổi được sang boolean. Đầu vào chấp nhận bao gồm `true`, `false`, `1`, `0`, `"1"`, `"0"`.

Dùng tham số `strict` để chỉ cho phép `true` hoặc `false`:

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

Trường phải có trường khớp `{field}_confirmation`. Ví dụ, khi trường là `password`, cần có `password_confirmation`.

Bạn cũng có thể chỉ định tên trường xác nhận tùy chỉnh, ví dụ `confirmed:repeat_username` yêu cầu `repeat_username` khớp với trường hiện tại.

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

Trường phải là mảng và phải chứa tất cả các giá trị tham số đã cho. Quy tắc này thường dùng cho xác thực mảng; bạn có thể dùng `Rule::contains` để tạo:

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

Trường phải là mảng và không được chứa bất kỳ giá trị tham số đã cho nào. Bạn có thể dùng `Rule::doesntContain` để tạo:

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

Trường phải khớp với mật khẩu của người dùng đã xác thực hiện tại. Bạn có thể chỉ định auth guard làm tham số đầu tiên:

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> Quy tắc này phụ thuộc vào component auth và cấu hình guard; không sử dụng khi auth chưa được tích hợp.

<a name="rule-date"></a>
#### date

Trường phải là ngày hợp lệ (không tương đối) mà `strtotime` nhận ra.

<a name="rule-date-equals"></a>
#### date_equals:_date_

Trường phải bằng ngày đã cho. Ngày được truyền vào `strtotime` để chuyển thành `DateTime` hợp lệ.

<a name="rule-date-format"></a>
#### date_format:_format_,...

Trường phải khớp một trong các định dạng đã cho. Dùng `date` hoặc `date_format`. Quy tắc này hỗ trợ tất cả định dạng PHP [DateTime](https://www.php.net/manual/en/class.datetime.php).

Bạn có thể dùng fluent rule builder `date`:

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

Trường phải là số với số chữ số thập phân yêu cầu:

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

Trường phải là `"no"`, `"off"`, `0`, `"0"`, `false`, hoặc `"false"`.

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

Khi trường khác bằng giá trị chỉ định, trường phải là `"no"`, `"off"`, `0`, `"0"`, `false`, hoặc `"false"`.

<a name="rule-different"></a>
#### different:_field_

Trường phải khác với _field_.

<a name="rule-digits"></a>
#### digits:_value_

Trường phải là số nguyên có độ dài _value_.

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

Trường phải là số nguyên có độ dài giữa _min_ và _max_.

<a name="rule-dimensions"></a>
#### dimensions

Trường phải là hình ảnh và thỏa mãn ràng buộc kích thước:

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

Ràng buộc có sẵn: _min\_width_, _max\_width_, _min\_height_, _max\_height_, _width_, _height_, _ratio_.

_ratio_ là tỷ lệ khung hình; có thể biểu thị dưới dạng phân số hoặc float:

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

Quy tắc này có nhiều tham số; khuyến nghị dùng `Rule::dimensions` để tạo:

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

Khi xác thực mảng, giá trị trường không được trùng lặp:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

Mặc định dùng so sánh lỏng. Thêm `strict` cho so sánh chặt:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

Thêm `ignore_case` để bỏ qua sự khác biệt chữ hoa/thường:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

Trường không được bắt đầu bằng bất kỳ giá trị chỉ định nào.

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

Trường không được kết thúc bằng bất kỳ giá trị chỉ định nào.

<a name="rule-email"></a>
#### email

Trường phải là địa chỉ email hợp lệ. Quy tắc này phụ thuộc vào [egulias/email-validator](https://github.com/egulias/EmailValidator), mặc định dùng `RFCValidation`, và có thể dùng các phương thức xác thực khác:

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

Phương thức xác thực có sẵn:

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` - Xác thực email theo đặc tả RFC ([RFC hỗ trợ](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs)).
- `strict`: `NoRFCWarningsValidation` - Thất bại khi có cảnh báo RFC (ví dụ dấu chấm cuối hoặc dấu chấm liên tiếp).
- `dns`: `DNSCheckValidation` - Kiểm tra domain có bản ghi MX hợp lệ.
- `spoof`: `SpoofCheckValidation` - Ngăn ký tự Unicode đồng âm hoặc giả mạo.
- `filter`: `FilterEmailValidation` - Xác thực bằng PHP `filter_var`.
- `filter_unicode`: `FilterEmailValidation::unicode()` - Xác thực `filter_var` cho phép Unicode.

</div>

Bạn có thể dùng fluent rule builder:

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
> `dns` và `spoof` yêu cầu extension PHP `intl`.

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

Trường phải khớp với mã hóa ký tự chỉ định. Quy tắc này dùng `mb_check_encoding` để phát hiện mã hóa file hoặc chuỗi. Có thể dùng với file rule builder:

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

Trường phải kết thúc bằng một trong các giá trị chỉ định.

<a name="rule-enum"></a>
#### enum

`Enum` là quy tắc dựa trên lớp để xác thực giá trị trường là giá trị enum hợp lệ. Truyền tên lớp enum khi tạo. Đối với giá trị nguyên thủy, dùng Backed Enum:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

Dùng `only`/`except` để giới hạn giá trị enum:

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

Dùng `when` cho giới hạn có điều kiện:

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

Trường sẽ bị loại khỏi dữ liệu trả về bởi `validate`/`validated`.

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

Khi _anotherfield_ bằng _value_, trường sẽ bị loại khỏi dữ liệu trả về bởi `validate`/`validated`.

Đối với điều kiện phức tạp, dùng `Rule::excludeIf`:

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

Trừ khi _anotherfield_ bằng _value_, trường sẽ bị loại khỏi dữ liệu trả về bởi `validate`/`validated`. Nếu _value_ là `null` (ví dụ `exclude_unless:name,null`), trường chỉ được giữ khi trường so sánh là `null` hoặc vắng mặt.

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

Khi _anotherfield_ tồn tại, trường sẽ bị loại khỏi dữ liệu trả về bởi `validate`/`validated`.

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

Khi _anotherfield_ không tồn tại, trường sẽ bị loại khỏi dữ liệu trả về bởi `validate`/`validated`.

<a name="rule-exists"></a>
#### exists:_table_,_column_

Trường phải tồn tại trong bảng cơ sở dữ liệu chỉ định.

<a name="basic-usage-of-exists-rule"></a>
#### Cách dùng cơ bản của quy tắc Exists

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

Khi không chỉ định `column`, tên trường được dùng mặc định. Vì vậy ví dụ này xác thực xem cột `state` có tồn tại trong bảng `states` hay không.

<a name="specifying-a-custom-column-name"></a>
#### Chỉ định tên cột tùy chỉnh

Thêm tên cột sau tên bảng:

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

Để chỉ định kết nối cơ sở dữ liệu, thêm tiền tố tên bảng với tên kết nối:

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

Bạn cũng có thể truyền tên lớp model; framework sẽ giải quyết tên bảng:

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

Đối với điều kiện truy vấn tùy chỉnh, dùng builder `Rule`:

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

Bạn cũng có thể chỉ định tên cột trực tiếp trong `Rule::exists`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'state' => [Rule::exists('states', 'abbreviation')],
])->validate();
```

Để xác thực một tập giá trị tồn tại, kết hợp với quy tắc `array`:

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

Khi cả `array` và `exists` có mặt, một truy vấn duy nhất xác thực tất cả giá trị.

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

Xác thực phần mở rộng file tải lên nằm trong danh sách cho phép:

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> Không chỉ dựa vào phần mở rộng để xác thực loại file; hãy dùng kèm [mimes](#rule-mimes) hoặc [mimetypes](#rule-mimetypes).

<a name="rule-file"></a>
#### file

Trường phải là file tải lên thành công.

<a name="rule-filled"></a>
#### filled

Khi trường tồn tại, giá trị của nó không được rỗng.

<a name="rule-gt"></a>
#### gt:_field_

Trường phải lớn hơn _field_ hoặc _value_ đã cho. Cả hai trường phải có cùng kiểu. Đánh giá cho chuỗi, số, mảng và file giống [size](#rule-size).

<a name="rule-gte"></a>
#### gte:_field_

Trường phải lớn hơn hoặc bằng _field_ hoặc _value_ đã cho. Cả hai trường phải có cùng kiểu. Đánh giá cho chuỗi, số, mảng và file giống [size](#rule-size).

<a name="rule-hex-color"></a>
#### hex_color

Trường phải là [giá trị màu hex](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color) hợp lệ.

<a name="rule-image"></a>
#### image

Trường phải là hình ảnh (jpg, jpeg, png, bmp, gif, hoặc webp).

> [!WARNING]
> SVG không được phép mặc định do rủi ro XSS. Để cho phép, thêm `allow_svg`: `image:allow_svg`.

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

Trường phải nằm trong danh sách giá trị đã cho. Bạn có thể dùng `Rule::in` để tạo:

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

Khi kết hợp với quy tắc `array`, mỗi giá trị trong mảng đầu vào phải nằm trong danh sách `in`:

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

Trường phải tồn tại trong danh sách giá trị của _anotherfield_.

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

Trường phải là mảng và phải chứa ít nhất một trong các giá trị đã cho làm khóa:

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

Trường phải là số nguyên.

Dùng tham số `strict` để yêu cầu kiểu trường là integer; số nguyên dạng chuỗi sẽ không hợp lệ:

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> Quy tắc này chỉ xác thực có vượt qua `FILTER_VALIDATE_INT` của PHP hay không; đối với kiểu số chặt chẽ, hãy dùng kèm [numeric](#rule-numeric).

<a name="rule-ip"></a>
#### ip

Trường phải là địa chỉ IP hợp lệ.

<a name="rule-ipv4"></a>
#### ipv4

Trường phải là địa chỉ IPv4 hợp lệ.

<a name="rule-ipv6"></a>
#### ipv6

Trường phải là địa chỉ IPv6 hợp lệ.

<a name="rule-json"></a>
#### json

Trường phải là chuỗi JSON hợp lệ.

<a name="rule-lt"></a>
#### lt:_field_

Trường phải nhỏ hơn _field_ đã cho. Cả hai trường phải có cùng kiểu. Đánh giá cho chuỗi, số, mảng và file giống [size](#rule-size).

<a name="rule-lte"></a>
#### lte:_field_

Trường phải nhỏ hơn hoặc bằng _field_ đã cho. Cả hai trường phải có cùng kiểu. Đánh giá cho chuỗi, số, mảng và file giống [size](#rule-size).

<a name="rule-lowercase"></a>
#### lowercase

Trường phải là chữ thường.

<a name="rule-list"></a>
#### list

Trường phải là mảng danh sách. Khóa mảng danh sách phải là số liên tiếp từ 0 đến `count($array) - 1`.

<a name="rule-mac"></a>
#### mac_address

Trường phải là địa chỉ MAC hợp lệ.

<a name="rule-max"></a>
#### max:_value_

Trường phải nhỏ hơn hoặc bằng _value_. Đánh giá cho chuỗi, số, mảng và file giống [size](#rule-size).

<a name="rule-max-digits"></a>
#### max_digits:_value_

Trường phải là số nguyên có độ dài không vượt quá _value_.

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

Xác thực loại MIME của file nằm trong danh sách:

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

Loại MIME được đoán bằng cách đọc nội dung file và có thể khác với MIME do client cung cấp.

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

Xác thực loại MIME của file tương ứng với phần mở rộng đã cho:

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

Mặc dù tham số là phần mở rộng, quy tắc này đọc nội dung file để xác định MIME. Ánh xạ phần mở rộng sang MIME:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### Loại MIME và phần mở rộng

Quy tắc này không xác thực "phần mở rộng file" khớp với "MIME thực tế". Ví dụ, `mimes:png` coi `photo.txt` có nội dung PNG là hợp lệ. Để xác thực phần mở rộng, dùng [extensions](#rule-extensions).

<a name="rule-min"></a>
#### min:_value_

Trường phải lớn hơn hoặc bằng _value_. Đánh giá cho chuỗi, số, mảng và file giống [size](#rule-size).

<a name="rule-min-digits"></a>
#### min_digits:_value_

Trường phải là số nguyên có độ dài không nhỏ hơn _value_.

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

Trường phải là bội số của _value_.

<a name="rule-missing"></a>
#### missing

Trường không được tồn tại trong dữ liệu đầu vào.

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

Khi _anotherfield_ bằng bất kỳ _value_ nào, trường không được tồn tại.

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

Trừ khi _anotherfield_ bằng bất kỳ _value_ nào, trường không được tồn tại.

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

Khi bất kỳ trường chỉ định nào tồn tại, trường không được tồn tại.

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

Khi tất cả các trường chỉ định tồn tại, trường không được tồn tại.

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

Trường không được nằm trong danh sách giá trị đã cho. Bạn có thể dùng `Rule::notIn` để tạo:

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

Trường không được khớp với biểu thức chính quy đã cho.

Quy tắc này dùng `preg_match` của PHP. Regex phải có delimiter, ví dụ `'email' => 'not_regex:/^.+$/i'`.

> [!WARNING]
> Khi dùng `regex`/`not_regex`, nếu regex chứa `|`, hãy dùng dạng mảng để tránh xung đột với dấu phân tách `|`.

<a name="rule-nullable"></a>
#### nullable

Trường có thể là `null`.

<a name="rule-numeric"></a>
#### numeric

Trường phải là [numeric](https://www.php.net/manual/en/function.is-numeric.php).

Dùng tham số `strict` để chỉ cho phép kiểu integer hoặc float; chuỗi số sẽ không hợp lệ:

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

Trường phải tồn tại trong dữ liệu đầu vào.

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

Khi _anotherfield_ bằng bất kỳ _value_ nào, trường phải tồn tại.

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

Trừ khi _anotherfield_ bằng bất kỳ _value_ nào, trường phải tồn tại.

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

Khi bất kỳ trường chỉ định nào tồn tại, trường phải tồn tại.

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

Khi tất cả các trường chỉ định tồn tại, trường phải tồn tại.

<a name="rule-prohibited"></a>
#### prohibited

Trường phải vắng mặt hoặc rỗng. "Rỗng" có nghĩa là:

<div class="content-list" markdown="1">

- Giá trị là `null`.
- Giá trị là chuỗi rỗng.
- Giá trị là mảng rỗng hoặc đối tượng `Countable` rỗng.
- File tải lên có đường dẫn rỗng.

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

Khi _anotherfield_ bằng bất kỳ _value_ nào, trường phải vắng mặt hoặc rỗng. "Rỗng" có nghĩa là:

<div class="content-list" markdown="1">

- Giá trị là `null`.
- Giá trị là chuỗi rỗng.
- Giá trị là mảng rỗng hoặc đối tượng `Countable` rỗng.
- File tải lên có đường dẫn rỗng.

</div>

Đối với điều kiện phức tạp, dùng `Rule::prohibitedIf`:

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

Khi _anotherfield_ là `"yes"`, `"on"`, `1`, `"1"`, `true`, hoặc `"true"`, trường phải vắng mặt hoặc rỗng.

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

Khi _anotherfield_ là `"no"`, `"off"`, `0`, `"0"`, `false`, hoặc `"false"`, trường phải vắng mặt hoặc rỗng.

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

Trừ khi _anotherfield_ bằng bất kỳ _value_ nào, trường phải vắng mặt hoặc rỗng. "Rỗng" có nghĩa là:

<div class="content-list" markdown="1">

- Giá trị là `null`.
- Giá trị là chuỗi rỗng.
- Giá trị là mảng rỗng hoặc đối tượng `Countable` rỗng.
- File tải lên có đường dẫn rỗng.

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

Khi trường tồn tại và không rỗng, tất cả các trường trong _anotherfield_ phải vắng mặt hoặc rỗng. "Rỗng" có nghĩa là:

<div class="content-list" markdown="1">

- Giá trị là `null`.
- Giá trị là chuỗi rỗng.
- Giá trị là mảng rỗng hoặc đối tượng `Countable` rỗng.
- File tải lên có đường dẫn rỗng.

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

Trường phải khớp với biểu thức chính quy đã cho.

Quy tắc này dùng `preg_match` của PHP. Regex phải có delimiter, ví dụ `'email' => 'regex:/^.+@.+$/i'`.

> [!WARNING]
> Khi dùng `regex`/`not_regex`, nếu regex chứa `|`, hãy dùng dạng mảng để tránh xung đột với dấu phân tách `|`.

<a name="rule-required"></a>
#### required

Trường phải tồn tại và không được rỗng. "Rỗng" có nghĩa là:

<div class="content-list" markdown="1">

- Giá trị là `null`.
- Giá trị là chuỗi rỗng.
- Giá trị là mảng rỗng hoặc đối tượng `Countable` rỗng.
- File tải lên có đường dẫn rỗng.

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

Khi _anotherfield_ bằng bất kỳ _value_ nào, trường phải tồn tại và không được rỗng.

Đối với điều kiện phức tạp, dùng `Rule::requiredIf`:

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

Khi _anotherfield_ là `"yes"`, `"on"`, `1`, `"1"`, `true`, hoặc `"true"`, trường phải tồn tại và không được rỗng.

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

Khi _anotherfield_ là `"no"`, `"off"`, `0`, `"0"`, `false`, hoặc `"false"`, trường phải tồn tại và không được rỗng.

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

Trừ khi _anotherfield_ bằng bất kỳ _value_ nào, trường phải tồn tại và không được rỗng. Nếu _value_ là `null` (ví dụ `required_unless:name,null`), trường chỉ có thể rỗng khi trường so sánh là `null` hoặc vắng mặt.

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

Khi bất kỳ trường chỉ định nào tồn tại và không rỗng, trường phải tồn tại và không được rỗng.

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

Khi tất cả các trường chỉ định tồn tại và không rỗng, trường phải tồn tại và không được rỗng.

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

Khi bất kỳ trường chỉ định nào rỗng hoặc vắng mặt, trường phải tồn tại và không được rỗng.

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

Khi tất cả các trường chỉ định rỗng hoặc vắng mặt, trường phải tồn tại và không được rỗng.

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

Trường phải là mảng và phải chứa ít nhất các khóa chỉ định.

<a name="validating-when-present"></a>
#### sometimes

Chỉ áp dụng các quy tắc xác thực tiếp theo khi trường tồn tại. Thường dùng cho các trường "tùy chọn nhưng phải hợp lệ khi có mặt":

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

Trường phải giống với _field_.

<a name="rule-size"></a>
#### size:_value_

Kích thước trường phải bằng _value_ đã cho. Đối với chuỗi: số ký tự; đối với số: số nguyên chỉ định (dùng với `numeric` hoặc `integer`); đối với mảng: số phần tử; đối với file: kích thước tính bằng KB. Ví dụ:

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

Trường phải bắt đầu bằng một trong các giá trị chỉ định.

<a name="rule-string"></a>
#### string

Trường phải là chuỗi. Để cho phép `null`, dùng với `nullable`.

<a name="rule-timezone"></a>
#### timezone

Trường phải là định danh múi giờ hợp lệ (từ `DateTimeZone::listIdentifiers`). Bạn có thể truyền các tham số được phương thức đó hỗ trợ:

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

Trường phải là duy nhất trong bảng chỉ định.

**Chỉ định tên bảng/cột tùy chỉnh:**

Bạn có thể chỉ định tên lớp model trực tiếp:

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

Bạn có thể chỉ định tên cột (mặc định là tên trường khi không chỉ định):

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**Chỉ định kết nối cơ sở dữ liệu:**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**Bỏ qua ID chỉ định:**

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
> `ignore` không nên nhận đầu vào từ người dùng; chỉ dùng ID duy nhất do hệ thống tạo (ID tự tăng hoặc UUID model), nếu không có thể có rủi ro SQL injection.

Bạn cũng có thể truyền instance model:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

Nếu khóa chính không phải `id`, chỉ định tên khóa chính:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

Mặc định dùng tên trường làm cột unique; bạn cũng có thể chỉ định tên cột:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**Thêm điều kiện bổ sung:**

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

**Bỏ qua bản ghi đã xóa mềm:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

Nếu cột xóa mềm không phải `deleted_at`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

Trường phải là chữ in hoa.

<a name="rule-url"></a>
#### url

Trường phải là URL hợp lệ.

Bạn có thể chỉ định giao thức cho phép:

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

Trường phải là [ULID](https://github.com/ulid/spec) hợp lệ.

<a name="rule-uuid"></a>
#### uuid

Trường phải là UUID RFC 9562 hợp lệ (phiên bản 1, 3, 4, 5, 6, 7, hoặc 8).

Bạn có thể chỉ định phiên bản:

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```



<a name="think-validate"></a>
# Trình xác thực top-think/think-validate

## Mô tả

Trình xác thực chính thức của ThinkPHP

## URL dự án

https://github.com/top-think/think-validate

## Cài đặt

`composer require topthink/think-validate`

## Bắt đầu nhanh

**Tạo `app/index/validate/User.php`**

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
  
**Cách sử dụng**

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

> **Lưu ý**
> webman không hỗ trợ phương thức `Validate::rule()` của think-validate

<a name="respect-validation"></a>
# Trình xác thực workerman/validation

## Mô tả

Dự án này là phiên bản bản địa hóa của https://github.com/Respect/Validation

## URL dự án

https://github.com/walkor/validation
  
  
## Cài đặt
 
```php
composer require workerman/validation
```

## Bắt đầu nhanh

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
  
**Truy cập qua jQuery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
Kết quả:

`{"code":500,"msg":"Username may only contain letters (a-z) and numbers (0-9)"}`

Giải thích:

`v::input(array $input, array $rules)` xác thực và thu thập dữ liệu. Nếu xác thực thất bại, nó ném `Respect\Validation\Exceptions\ValidationException`; khi thành công trả về dữ liệu đã xác thực (mảng).

Nếu mã nghiệp vụ không bắt ngoại lệ xác thực, framework webman sẽ bắt và trả về JSON (như `{"code":500, "msg":"xxx"}`) hoặc trang ngoại lệ thông thường dựa trên header HTTP. Nếu định dạng phản hồi không đáp ứng nhu cầu của bạn, bạn có thể bắt `ValidationException` và trả về dữ liệu tùy chỉnh, như trong ví dụ sau:

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

## Hướng dẫn Trình xác thực

```php
use Respect\Validation\Validator as v;

// Xác thực quy tắc đơn
$number = 123;
v::numericVal()->validate($number); // true

// Xác thực chuỗi
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Lấy lý do thất bại xác thực đầu tiên
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username may only contain letters (a-z) and numbers (0-9)
}

// Lấy tất cả lý do thất bại xác thực
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Sẽ in
    // -  Username must satisfy the following rules
    //     - Username may only contain letters (a-z) and numbers (0-9)
    //     - Username must not contain whitespace
  
    var_export($exception->getMessages());
    // Sẽ in
    // array (
    //   'alnum' => 'Username may only contain letters (a-z) and numbers (0-9)',
    //   'noWhitespace' => 'Username must not contain whitespace',
    // )
}

// Thông báo lỗi tùy chỉnh
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Username may only contain letters and numbers',
        'noWhitespace' => 'Username must not contain spaces',
        'length' => 'length satisfies the rule, so this will not be shown'
    ]));
    // Sẽ in 
    // array(
    //    'alnum' => 'Username may only contain letters and numbers',
    //    'noWhitespace' => 'Username must not contain spaces'
    // )
}

// Xác thực đối tượng
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Xác thực mảng
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
    ->assert($data); // Cũng có thể dùng check() hoặc validate()
  
// Xác thực tùy chọn
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Quy tắc phủ định
v::not(v::intVal())->validate(10); // false
```
  
## Sự khác biệt giữa các phương thức Validator `validate()` `check()` `assert()`

`validate()` trả về boolean, không ném ngoại lệ

`check()` ném ngoại lệ khi xác thực thất bại; lấy lý do thất bại đầu tiên qua `$exception->getMessage()`

`assert()` ném ngoại lệ khi xác thực thất bại; lấy tất cả lý do thất bại qua `$exception->getFullMessage()`
  
  
## Các quy tắc xác thực thường dùng

`Alnum()` Chỉ chữ cái và số

`Alpha()` Chỉ chữ cái

`ArrayType()` Kiểu mảng

`Between(mixed $minimum, mixed $maximum)` Xác thực đầu vào nằm giữa hai giá trị.

`BoolType()` Kiểu boolean

`Contains(mixed $expectedValue)` Xác thực đầu vào chứa giá trị nhất định

`ContainsAny(array $needles)` Xác thực đầu vào chứa ít nhất một giá trị đã định nghĩa

`Digit()` Xác thực đầu vào chỉ chứa chữ số

`Domain()` Xác thực tên miền hợp lệ

`Email()` Xác thực địa chỉ email hợp lệ

`Extension(string $extension)` Xác thực phần mở rộng file

`FloatType()` Kiểu float

`IntType()` Kiểu integer

`Ip()` Xác thực địa chỉ IP

`Json()` Xác thực dữ liệu JSON

`Length(int $min, int $max)` Xác thực độ dài nằm trong phạm vi

`LessThan(mixed $compareTo)` Xác thực độ dài nhỏ hơn giá trị đã cho

`Lowercase()` Xác thực chữ thường

`MacAddress()` Xác thực địa chỉ MAC

`NotEmpty()` Xác thực không rỗng

`NullType()` Xác thực null

`Number()` Xác thực số

`ObjectType()` Kiểu đối tượng

`StringType()` Kiểu chuỗi

`Url()` Xác thực URL
  
Xem https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ để biết thêm các quy tắc xác thực.
  
## Thêm thông tin

Truy cập https://respect-validation.readthedocs.io/en/2.0/
  

