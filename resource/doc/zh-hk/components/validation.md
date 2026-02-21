# 其他驗證器
composer有很多驗證器可以直接在使用，例如：
#### <a href="#webman-validation"> webman/validation(推薦)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# 驗證器 webman/validation
基於 `illuminate/validation`，提供手動驗證、註解驗證、參數級驗證，以及可複用的規則集。

## 安裝

```bash
composer require webman/validation
```

## 基本概念

- **規則集複用**：透過繼承 `support\validation\Validator` 定義可複用的 `rules` `messages` `attributes` `scenes`，可在手動與註解中複用。
- **方法級註解（Attribute）驗證**：使用 PHP 8 屬性註解 `#[Validate]` 綁定控制器方法。
- **參數級註解（Attribute）驗證**：使用 PHP 8 屬性註解 `#[Param]` 綁定控制器方法參數。
- **異常處理**：驗證失敗拋出 `support\validation\ValidationException`，異常類可透過設定自訂
- **資料庫驗證**：如果涉及資料庫驗證，需要安裝 `composer require webman/database`

## 手動驗證

### 基本用法

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **提示**  
> `validate()` 校驗失敗會拋出 `support\validation\ValidationException`。如果你不希望拋異常，請使用下方的 `fails()` 寫法獲取錯誤資訊。

### 自訂 messages 與 attributes

```php
use support\validation\Validator;

$data = ['contact' => 'user@example.com'];

Validator::make(
    $data,
    ['contact' => 'required|email'],
    ['contact.email' => '郵箱格式不正確'],
    ['contact' => '郵箱']
)->validate();
```

### 不拋異常並獲取錯誤資訊

如果你不希望拋異常，可以使用 `fails()` 判斷，並透過 `errors()`（返回 `MessageBag`）獲取錯誤資訊：

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
    // 處理錯誤...
}
```

## 規則集複用（自訂 Validator）

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
        'name.required' => '姓名必填',
        'email.required' => '郵箱必填',
        'email.email' => '郵箱格式不正確',
    ];

    protected array $attributes = [
        'name' => '姓名',
        'email' => '郵箱',
    ];
}
```

### 手動驗證複用

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### 使用 scenes（可選）

`scenes` 是可選能力，只有在你呼叫 `withScene(...)` 時，才會按場景只驗證部分欄位。

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

// 不指定場景 -> 驗證全部規則
UserValidator::make($data)->validate();

// 指定場景 -> 只驗證該場景包含的欄位
UserValidator::make($data)->withScene('create')->validate();
```

## 註解驗證（方法級）

### 直接規則

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
            'email.required' => '郵箱必填',
            'password.required' => '密碼必填',
        ],
        attributes: [
            'email' => '郵箱',
            'password' => '密碼',
        ]
    )]
    public function login(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### 複用規則集

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

### 多重驗證疊加

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

### 驗證資料來源
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

透過`in`參數來指定資料來源，其中：

* **query** http請求的query參數，取自 `$request->get()`
* **body** http請求的包體，取自 `$request->post()`
* **path** http請求的路徑參數，取自 `$request->route->param()`

`in`可為字串或陣列；為陣列時按順序合併，後者覆蓋前者。未傳遞`in`時預設等效於 `['query', 'body', 'path']`。


## 參數級驗證（Param）

### 基本用法

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

### 驗證資料來源

類似的，參數級也支援`in`參數指定來源

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


### rules 支援字串或陣列

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

### 自訂 messages / attribute

```php
use support\validation\annotation\Param;

class UserController
{
    public function updateEmail(
        #[Param(
            rules: 'required|email',
            messages: ['email.email' => '郵箱格式不正確'],
            attribute: '郵箱'
        )]
        string $email
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### 規則常數複用

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

## 方法級 + 參數級混合

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

## 自動規則推導（基於參數簽名）

當方法上使用 `#[Validate]`，或該方法的任意參數使用了 `#[Param]` 時，本元件會**根據方法參數簽名自動推導並補全基礎驗證規則**，再與已有規則合併後執行驗證。

### 範例：`#[Validate]` 等價展開

1) 只開啟 `#[Validate]`，不手寫規則：

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

等價於：

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

2) 只寫了部分規則，其餘由參數簽名補全：

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

等價於：

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

3) 預設值/可空類型：

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate]
    public function create(string $content = '預設值', ?int $uid = null)
    {
    }
}
```

等價於：

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate(rules: [
        'content' => 'string',
        'uid' => 'integer|nullable',
    ])]
    public function create(string $content = '預設值', ?int $uid = null)
    {
    }
}
```

## 異常處理

### 預設異常

驗證失敗預設拋出 `support\validation\ValidationException`，繼承 `Webman\Exception\BusinessException`，不會記錄錯誤日誌。

預設回應行為由 `BusinessException::render()` 處理：

- 普通請求：返回字串訊息，例如 `token 為必填項。`
- JSON 請求：返回 JSON 回應，例如 `{"code": 422, "msg": "token 為必填項。", "data":....}`

### 透過自訂異常修改處理方式

- 全域設定：`config/plugin/webman/validation/app.php` 的 `exception`

## 多語言支援

元件內建中英文語言包，並支援專案覆蓋。載入順序：

1. 專案語言包 `resource/translations/{locale}/validation.php`
2. 元件內建 `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Illuminate 內建英文（兜底）

> **提示**
> webman預設語言由 `config/translation.php` 設定，也可以透過函數 locale('en'); 變更。

### 本地覆蓋範例

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute 不是有效的郵件格式。',
];
```

## 中介軟體自動載入

元件安裝後會透過 `config/plugin/webman/validation/middleware.php` 自動載入驗證中介軟體，無需手動註冊。

## 命令列生成註解

使用命令 `make:validator` 生成驗證器類（預設生成到 `app/validation` 目錄）。

> **提示**
> 需要安裝 `composer require webman/console`

### 基礎用法

- **生成空範本**

```bash
php webman make:validator UserValidator
```

- **覆蓋已存在檔案**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### 從表結構生成規則

- **指定表名生成基礎規則**（會根據欄位類型/可空/長度等推導 `$rules`；預設排除欄位與 ORM 相關：laravel 為 `created_at/updated_at/deleted_at`，thinkorm 為 `create_time/update_time/delete_time`）

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **指定資料庫連接**（多連接場景）

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### 場景（scenes）

- **生成 CRUD 場景**：`create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> `update` 場景會包含主鍵欄位（用於定位記錄）以及其餘欄位；`delete/detail` 預設僅包含主鍵欄位。

### ORM 選擇（laravel(illuminate/database) 與 think-orm）

- **自動選擇（預設）**：僅安裝/設定了哪套就用哪套；都存在時預設用 illuminate
- **強制指定**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### 綜合範例

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## 單元測試

進入 `webman/validation` 根目錄執行：

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## 所有驗證規則參考
<a name="available-validation-rules"></a>
## 可用驗證規則

> [!IMPORTANT]
> - Webman Validation 基於 `illuminate/validation`，規則名稱與 Laravel 一致，規則本身無 Webman 特化。
> - 中介軟體預設驗證資料來自 `$request->all()`（GET+POST）並合併路由參數，不包含上傳檔案；涉及檔案規則時，請自行將 `$request->file()` 合併到資料中，或手動呼叫 `Validator::make`。
> - `current_password` 依賴認證守衛，`exists`/`unique` 依賴資料庫連接與查詢建構子，未接入對應元件時規則不可用。

以下列出所有可用驗證規則及其作用：

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

#### 布林值

<div class="collection-method-list" markdown="1">

[Accepted](#rule-accepted)
[Accepted If](#rule-accepted-if)
[Boolean](#rule-boolean)
[Declined](#rule-declined)
[Declined If](#rule-declined-if)

</div>

#### 字串

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

#### 數字

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

#### 陣列

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

#### 日期

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

#### 檔案

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

#### 資料庫

<div class="collection-method-list" markdown="1">

[Exists](#rule-exists)
[Unique](#rule-unique)

</div>

#### 工具類

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

驗證欄位必須是 `"yes"`、`"on"`、`1`、`"1"`、`true` 或 `"true"`。常用於驗證使用者是否同意服務條款等場景。

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

當另一個欄位等於指定值時，驗證欄位必須是 `"yes"`、`"on"`、`1`、`"1"`、`true` 或 `"true"`。常用於條件性同意類場景。

<a name="rule-active-url"></a>
#### active_url

驗證欄位必須具有有效的 A 或 AAAA 記錄。該規則會先用 `parse_url` 提取 URL 的主機名，再交給 `dns_get_record` 校驗。

<a name="rule-after"></a>
#### after:_date_

驗證欄位必須是給定日期之後的值。日期會傳給 `strtotime` 轉為有效的 `DateTime`：

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

也可以傳入另一個欄位名進行比較：

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

可使用 fluent `date` 規則建構子：

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

`afterToday` 和 `todayOrAfter` 可便捷表達「必須晚於今天」或「必須是今天或之後」：

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

驗證欄位必須是給定日期之後或等於給定日期。更多說明見 [after](#rule-after) 規則。

可使用 fluent `date` 規則建構子：

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

`Rule::anyOf` 允許指定「滿足任意一個規則集即可」。例如，下面規則表示 `username` 要麼是電子郵件地址，要麼是至少 6 位的字母數字底線/短橫線字串：

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

驗證欄位必須是 Unicode 字母（[\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) 與 [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)）。

若僅允許 ASCII（`a-z`、`A-Z`），可新增 `ascii` 選項：

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

驗證欄位只能包含 Unicode 字母數字（[\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=)、[\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)、[\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)），以及 ASCII 短橫線（`-`）與底線（`_`）。

若僅允許 ASCII（`a-z`、`A-Z`、`0-9`），可新增 `ascii` 選項：

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

驗證欄位只能包含 Unicode 字母數字（[\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=)、[\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)、[\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)）。

若僅允許 ASCII（`a-z`、`A-Z`、`0-9`），可新增 `ascii` 選項：

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

驗證欄位必須是 PHP `array`。

當 `array` 規則帶有額外參數時，輸入陣列的鍵必須在參數清單中。範例中 `admin` 鍵不在允許清單中，因此無效：

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

建議在實際專案中明確陣列允許的鍵。

<a name="rule-ascii"></a>
#### ascii

驗證欄位只能包含 7-bit ASCII 字元。

<a name="rule-bail"></a>
#### bail

當某欄位首個規則驗證失敗時，停止繼續驗證該欄位的其他規則。

該規則只影響當前欄位。若需要「全域首錯即停」，請直接使用 Illuminate 的驗證器並呼叫 `stopOnFirstFailure()`。

<a name="rule-before"></a>
#### before:_date_

驗證欄位必須早於給定日期。日期會傳給 `strtotime` 轉為有效的 `DateTime`。同 [after](#rule-after) 規則，可傳入另一個欄位名進行比較。

可使用 fluent `date` 規則建構子：

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

`beforeToday` 與 `todayOrBefore` 可便捷表達「必須早於今天」或「必須是今天或之前」：

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

驗證欄位必須早於或等於給定日期。日期會傳給 `strtotime` 轉為有效的 `DateTime`。同 [after](#rule-after) 規則，可傳入另一個欄位名進行比較。

可使用 fluent `date` 規則建構子：

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

驗證欄位的大小必須在給定的 _min_ 和 _max_ 之間（含邊界）。字串、數值、陣列、檔案的評估規則與 [size](#rule-size) 相同。

<a name="rule-boolean"></a>
#### boolean

驗證欄位必須可轉換為布林值。可接受的輸入包括 `true`、`false`、`1`、`0`、`"1"`、`"0"`。

可透過 `strict` 參數僅允許 `true` 或 `false`：

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

驗證欄位必須有一個匹配欄位 `{field}_confirmation`。例如欄位為 `password` 時，需要 `password_confirmation`。

也可以指定自訂確認欄位名稱，如 `confirmed:repeat_username` 將要求 `repeat_username` 與當前欄位匹配。

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

驗證欄位必須是陣列，且必須包含所有給定參數值。該規則常用於陣列校驗，可使用 `Rule::contains` 建構：

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

驗證欄位必須是陣列，且不能包含任何給定參數值。可使用 `Rule::doesntContain` 建構：

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

驗證欄位必須與當前認證使用者的密碼匹配。可透過第一個參數指定認證 guard：

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> 該規則依賴認證元件與 guard 設定，未接入認證時請勿使用。

<a name="rule-date"></a>
#### date

驗證欄位必須是 `strtotime` 可識別的有效（非相對）日期。

<a name="rule-date-equals"></a>
#### date_equals:_date_

驗證欄位必須等於給定日期。日期會傳給 `strtotime` 轉為有效的 `DateTime`。

<a name="rule-date-format"></a>
#### date_format:_format_,...

驗證欄位必須匹配給定格式之一。使用 `date` 或 `date_format` 二選一即可。該規則支援 PHP [DateTime](https://www.php.net/manual/en/class.datetime.php) 的所有格式。

可使用 fluent `date` 規則建構子：

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

驗證欄位必須是數值，且小數位數符合要求：

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

驗證欄位必須是 `"no"`、`"off"`、`0`、`"0"`、`false` 或 `"false"`。

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

當另一個欄位等於指定值時，驗證欄位必須是 `"no"`、`"off"`、`0`、`"0"`、`false` 或 `"false"`。

<a name="rule-different"></a>
#### different:_field_

驗證欄位必須與 _field_ 不同。

<a name="rule-digits"></a>
#### digits:_value_

驗證欄位必須是整數且長度為 _value_。

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

驗證欄位必須是整數且長度在 _min_ 與 _max_ 之間。

<a name="rule-dimensions"></a>
#### dimensions

驗證欄位必須是圖片，並滿足維度約束：

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

可用約束：_min\_width_、_max\_width_、_min\_height_、_max\_height_、_width_、_height_、_ratio_。

_ratio_ 為寬高比，可用分數或浮點表示：

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

該規則參數較多，建議使用 `Rule::dimensions` 建構：

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

當驗證陣列時，欄位值不能重複：

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

預設使用寬鬆比較。需嚴格比較可新增 `strict`：

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

可新增 `ignore_case` 忽略大小寫差異：

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

驗證欄位不能以指定值開頭。

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

驗證欄位不能以指定值結尾。

<a name="rule-email"></a>
#### email

驗證欄位必須是有效電子郵件地址。該規則依賴 [egulias/email-validator](https://github.com/egulias/EmailValidator)，預設使用 `RFCValidation`，也可指定其他驗證方式：

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

可用驗證方式清單：

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` - 按 RFC 規範驗證郵箱（[支援的 RFC](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs)）。
- `strict`: `NoRFCWarningsValidation` - RFC 校驗時遇到警告即失敗（如結尾點號或連續點號）。
- `dns`: `DNSCheckValidation` - 校驗網域是否有有效的 MX 記錄。
- `spoof`: `SpoofCheckValidation` - 防止同形異義或欺騙性 Unicode 字元。
- `filter`: `FilterEmailValidation` - 使用 PHP `filter_var` 驗證。
- `filter_unicode`: `FilterEmailValidation::unicode()` - 允許 Unicode 的 `filter_var` 驗證。

</div>

可使用 fluent 規則建構子：

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
> `dns` 與 `spoof` 需要 PHP `intl` 擴充。

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

驗證欄位必須匹配指定字元編碼。該規則使用 `mb_check_encoding` 檢測檔案或字串編碼。可配合檔案規則建構子使用：

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

驗證欄位必須以指定值之一結尾。

<a name="rule-enum"></a>
#### enum

`Enum` 是基於類的規則，用於驗證欄位值是否為合法列舉值。建構時傳入列舉類別名稱。驗證基本型別值時，應使用 Backed Enum：

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

可用 `only`/`except` 限制列舉值：

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

可使用 `when` 進行條件限制：

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

驗證欄位會從 `validate`/`validated` 返回的資料中排除。

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

當 _anotherfield_ 等於 _value_ 時，驗證欄位會從 `validate`/`validated` 返回的資料中排除。

如需複雜條件，可使用 `Rule::excludeIf`：

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

除非 _anotherfield_ 等於 _value_，否則驗證欄位會從 `validate`/`validated` 返回的資料中排除。若 _value_ 為 `null`（如 `exclude_unless:name,null`），則只有當比較欄位為 `null` 或不存在時才保留該欄位。

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

當 _anotherfield_ 存在時，驗證欄位會從 `validate`/`validated` 返回的資料中排除。

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

當 _anotherfield_ 不存在時，驗證欄位會從 `validate`/`validated` 返回的資料中排除。

<a name="rule-exists"></a>
#### exists:_table_,_column_

驗證欄位必須存在於指定資料庫表中。

<a name="basic-usage-of-exists-rule"></a>
#### Exists 規則的基礎用法

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

未指定 `column` 時，預設使用欄位名稱。因此該例會驗證 `states` 表中 `state` 列是否存在。

<a name="specifying-a-custom-column-name"></a>
#### 指定自訂列名

可在表名後追加列名：

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

如需指定資料庫連接，可在表名前加連接名稱：

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

也可傳入模型類別名稱，由框架解析表名：

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

若需自訂查詢條件，可使用 `Rule` 規則建構子：

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

也可在 `Rule::exists` 中直接指定列名：

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'state' => [Rule::exists('states', 'abbreviation')],
])->validate();
```

驗證一組值是否存在時，可結合 `array` 規則：

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

當 `array` 與 `exists` 同時存在時，會生成單條查詢驗證全部值。

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

驗證上傳檔案的副檔名是否在允許清單內：

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> 不要僅依賴副檔名驗證檔案類型，建議與 [mimes](#rule-mimes) 或 [mimetypes](#rule-mimetypes) 搭配使用。

<a name="rule-file"></a>
#### file

驗證欄位必須是成功上傳的檔案。

<a name="rule-filled"></a>
#### filled

當欄位存在時，其值不能為空。

<a name="rule-gt"></a>
#### gt:_field_

驗證欄位必須大於給定 _field_ 或 _value_。兩個欄位類型必須一致。字串、數值、陣列、檔案的評估規則與 [size](#rule-size) 相同。

<a name="rule-gte"></a>
#### gte:_field_

驗證欄位必須大於等於給定 _field_ 或 _value_。兩個欄位類型必須一致。字串、數值、陣列、檔案的評估規則與 [size](#rule-size) 相同。

<a name="rule-hex-color"></a>
#### hex_color

驗證欄位必須是有效的 [十六進位顏色值](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color)。

<a name="rule-image"></a>
#### image

驗證欄位必須是圖片（jpg、jpeg、png、bmp、gif 或 webp）。

> [!WARNING]
> 出於 XSS 風險，預設不允許 SVG。如需允許，可加 `allow_svg`：`image:allow_svg`。

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

驗證欄位必須在給定值清單中。可使用 `Rule::in` 建構：

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

與 `array` 規則組合時，輸入陣列的每個值都必須在 `in` 清單內：

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

驗證欄位必須存在於 _anotherfield_ 的值清單中。

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

驗證欄位必須是陣列，且至少包含給定值之一作為鍵：

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

驗證欄位必須是整數。

可使用 `strict` 參數要求欄位類型必須為整數，字串形式的整數將視為無效：

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> 該規則僅驗證是否能透過 PHP 的 `FILTER_VALIDATE_INT`，如需強制數值類型，請與 [numeric](#rule-numeric) 配合使用。

<a name="rule-ip"></a>
#### ip

驗證欄位必須是合法 IP 地址。

<a name="rule-ipv4"></a>
#### ipv4

驗證欄位必須是合法 IPv4 地址。

<a name="rule-ipv6"></a>
#### ipv6

驗證欄位必須是合法 IPv6 地址。

<a name="rule-json"></a>
#### json

驗證欄位必須是有效的 JSON 字串。

<a name="rule-lt"></a>
#### lt:_field_

驗證欄位必須小於給定 _field_。兩個欄位類型必須一致。字串、數值、陣列、檔案的評估規則與 [size](#rule-size) 相同。

<a name="rule-lte"></a>
#### lte:_field_

驗證欄位必須小於等於給定 _field_。兩個欄位類型必須一致。字串、數值、陣列、檔案的評估規則與 [size](#rule-size) 相同。

<a name="rule-lowercase"></a>
#### lowercase

驗證欄位必須是小寫。

<a name="rule-list"></a>
#### list

驗證欄位必須是清單陣列。清單陣列的鍵必須是從 0 到 `count($array) - 1` 的連續數字。

<a name="rule-mac"></a>
#### mac_address

驗證欄位必須是合法 MAC 地址。

<a name="rule-max"></a>
#### max:_value_

驗證欄位必須小於或等於 _value_。字串、數值、陣列、檔案的評估規則與 [size](#rule-size) 相同。

<a name="rule-max-digits"></a>
#### max_digits:_value_

驗證欄位必須是整數，且長度不超過 _value_。

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

驗證檔案的 MIME 類型是否在清單內：

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

MIME 類型透過讀取檔案內容猜測，可能與用戶端提供的 MIME 不一致。

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

驗證檔案的 MIME 類型是否與給定副檔名對應：

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

儘管參數是副檔名，該規則會讀取檔案內容判斷 MIME。副檔名與 MIME 對照表見：

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### MIME 類型與副檔名

該規則不驗證「檔案名稱副檔名」與「實際 MIME」是否一致。例如，`mimes:png` 會將內容為 PNG 的 `photo.txt` 視為合法。如果需要驗證副檔名，請使用 [extensions](#rule-extensions)。

<a name="rule-min"></a>
#### min:_value_

驗證欄位必須大於或等於 _value_。字串、數值、陣列、檔案的評估規則與 [size](#rule-size) 相同。

<a name="rule-min-digits"></a>
#### min_digits:_value_

驗證欄位必須是整數，且長度不少於 _value_。

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

驗證欄位必須是 _value_ 的倍數。

<a name="rule-missing"></a>
#### missing

驗證欄位必須不存在於輸入資料中。

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

當 _anotherfield_ 等於任一 _value_ 時，驗證欄位必須不存在。

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

除非 _anotherfield_ 等於任一 _value_，否則驗證欄位必須不存在。

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

當任意指定欄位存在時，驗證欄位必須不存在。

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

當所有指定欄位都存在時，驗證欄位必須不存在。

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

驗證欄位必須不在給定值清單中。可使用 `Rule::notIn` 建構：

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

驗證欄位不能匹配給定正則運算式。

該規則使用 PHP `preg_match`。正則必須帶分隔符，例如：`'email' => 'not_regex:/^.+$/i'`。

> [!WARNING]
> 使用 `regex` / `not_regex` 時，如正則包含 `|`，建議用陣列形式宣告規則，避免與 `|` 分隔符衝突。

<a name="rule-nullable"></a>
#### nullable

驗證欄位允許為 `null`。

<a name="rule-numeric"></a>
#### numeric

驗證欄位必須是 [numeric](https://www.php.net/manual/en/function.is-numeric.php)。

可使用 `strict` 參數僅允許整數或浮點類型，數值字串將視為無效：

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

驗證欄位必須存在於輸入資料中。

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

當 _anotherfield_ 等於任一 _value_ 時，驗證欄位必須存在。

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

除非 _anotherfield_ 等於任一 _value_，否則驗證欄位必須存在。

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

當任意指定欄位存在時，驗證欄位必須存在。

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

當所有指定欄位都存在時，驗證欄位必須存在。

<a name="rule-prohibited"></a>
#### prohibited

驗證欄位必須缺失或為空。欄位「為空」指：

<div class="content-list" markdown="1">

- 值為 `null`。
- 值為空字串。
- 值為空陣列或空的 `Countable` 物件。
- 為上傳檔案且路徑為空。

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

當 _anotherfield_ 等於任一 _value_ 時，驗證欄位必須缺失或為空。欄位「為空」指：

<div class="content-list" markdown="1">

- 值為 `null`。
- 值為空字串。
- 值為空陣列或空的 `Countable` 物件。
- 為上傳檔案且路徑為空。

</div>

如需複雜條件，可使用 `Rule::prohibitedIf`：

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

當 _anotherfield_ 為 `"yes"`、`"on"`、`1`、`"1"`、`true` 或 `"true"` 時，驗證欄位必須缺失或為空。

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

當 _anotherfield_ 為 `"no"`、`"off"`、`0`、`"0"`、`false` 或 `"false"` 時，驗證欄位必須缺失或為空。

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

除非 _anotherfield_ 等於任一 _value_，否則驗證欄位必須缺失或為空。欄位「為空」指：

<div class="content-list" markdown="1">

- 值為 `null`。
- 值為空字串。
- 值為空陣列或空的 `Countable` 物件。
- 為上傳檔案且路徑為空。

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

當驗證欄位存在且不為空時，_anotherfield_ 中所有欄位必須缺失或為空。欄位「為空」指：

<div class="content-list" markdown="1">

- 值為 `null`。
- 值為空字串。
- 值為空陣列或空的 `Countable` 物件。
- 為上傳檔案且路徑為空。

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

驗證欄位必須匹配給定正則運算式。

該規則使用 PHP `preg_match`。正則必須帶分隔符，例如：`'email' => 'regex:/^.+@.+$/i'`。

> [!WARNING]
> 使用 `regex` / `not_regex` 時，如正則包含 `|`，建議用陣列形式宣告規則，避免與 `|` 分隔符衝突。

<a name="rule-required"></a>
#### required

驗證欄位必須存在且不能為空。欄位「為空」指：

<div class="content-list" markdown="1">

- 值為 `null`。
- 值為空字串。
- 值為空陣列或空的 `Countable` 物件。
- 為上傳檔案且路徑為空。

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

當 _anotherfield_ 等於任一 _value_ 時，驗證欄位必須存在且不能為空。

如需複雜條件，可使用 `Rule::requiredIf`：

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

當 _anotherfield_ 為 `"yes"`、`"on"`、`1`、`"1"`、`true` 或 `"true"` 時，驗證欄位必須存在且不能為空。

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

當 _anotherfield_ 為 `"no"`、`"off"`、`0`、`"0"`、`false` 或 `"false"` 時，驗證欄位必須存在且不能為空。

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

除非 _anotherfield_ 等於任一 _value_，否則驗證欄位必須存在且不能為空。若 _value_ 為 `null`（如 `required_unless:name,null`），則只有當比較欄位為 `null` 或不存在時才允許驗證欄位為空。

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

當任意指定欄位存在且不為空時，驗證欄位必須存在且不能為空。

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

當所有指定欄位都存在且不為空時，驗證欄位必須存在且不能為空。

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

當任意指定欄位為空或不存在時，驗證欄位必須存在且不能為空。

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

當所有指定欄位都為空或不存在時，驗證欄位必須存在且不能為空。

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

驗證欄位必須是陣列，且至少包含指定的鍵。

<a name="validating-when-present"></a>
#### sometimes

僅當欄位存在時，才套用後續驗證規則。常用於「可選但一旦存在就必須合法」的欄位：

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

驗證欄位必須與 _field_ 相同。

<a name="rule-size"></a>
#### size:_value_

驗證欄位大小必須等於給定 _value_。字串為字元數；數值為指定整數（需配合 `numeric` 或 `integer`）；陣列為元素數；檔案為 KB 大小。範例：

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

驗證欄位必須以指定值之一開頭。

<a name="rule-string"></a>
#### string

驗證欄位必須是字串。如需允許 `null`，請配合 `nullable` 使用。

<a name="rule-timezone"></a>
#### timezone

驗證欄位必須是有效時區識別碼（來自 `DateTimeZone::listIdentifiers`）。可傳入該方法支援的參數：

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

驗證欄位在指定表中必須唯一。

**指定自訂表/列名：**

可直接指定模型類別名稱：

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

可指定列名（不指定時預設欄位名稱）：

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**指定資料庫連接：**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**忽略指定 ID：**

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
> `ignore` 不應接收使用者輸入，僅應使用系統生成的唯一 ID（自增 ID 或模型 UUID），否則可能存在 SQL 注入風險。

也可傳入模型實例：

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

若主鍵不是 `id`，可指定主鍵名稱：

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

預設以欄位名稱作為唯一列，亦可指定列名：

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**新增額外條件：**

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

**忽略軟刪除記錄：**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

若軟刪除列名不是 `deleted_at`：

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

驗證欄位必須為大寫。

<a name="rule-url"></a>
#### url

驗證欄位必須是有效 URL。

可指定允許的協定：

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

驗證欄位必須是有效 [ULID](https://github.com/ulid/spec)。

<a name="rule-uuid"></a>
#### uuid

驗證欄位必須是有效的 RFC 9562 UUID（版本 1、3、4、5、6、7 或 8）。

可指定版本：

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```



<a name="think-validate"></a>
# 驗證器 top-think/think-validate

## 說明
ThinkPHP官方驗證器

## 專案地址
https://github.com/top-think/think-validate

## 安裝
`composer require topthink/think-validate`

## 快速開始

**新建 `app/index/validate/User.php`**

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
        'name.require' => '名稱必須',
        'name.max'     => '名稱最多不能超過25個字元',
        'age.number'   => '年齡必須是數字',
        'age.between'  => '年齡只能在1-120之間',
        'email'        => '郵箱格式錯誤',    
    ];

}
```
  
**使用**
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

> **注意**
> webman裡不支援think-validate的`Validate::rule()`方法

<a name="respect-validation"></a>
# 驗證器 workerman/validation

## 說明

專案為 https://github.com/Respect/Validation 的漢化版本

## 專案地址

https://github.com/walkor/validation
  
  
## 安裝
 
```php
composer require workerman/validation
```

## 快速開始

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
            'nickname' => v::length(1, 64)->setName('暱稱'),
            'username' => v::alnum()->length(5, 64)->setName('使用者名稱'),
            'password' => v::length(5, 64)->setName('密碼')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
  
**透過jquery存取**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'湯姆', username:'tom cat', password: '123456'}
  });
  ```
  
得到結果：

`{"code":500,"msg":"使用者名稱 只能包含字母（a-z）和數字（0-9）"}`

說明：

`v::input(array $input, array $rules)` 用來驗證並收集資料，如果資料驗證失敗，則拋出`Respect\Validation\Exceptions\ValidationException`異常，驗證成功則將返回驗證後的資料(陣列)。

如果業務程式碼未捕獲驗證異常，則webman框架將自動捕獲並根據HTTP請求標頭選擇返回json資料(類似`{"code":500, "msg":"xxx"}`)或者普通的異常頁面。如返回格式不符合業務需求，開發者可自行捕獲`ValidationException`異常並返回需要的資料，類似下面的範例：

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
                'username' => v::alnum()->length(5, 64)->setName('使用者名稱'),
                'password' => v::length(5, 64)->setName('密碼')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```

## Validator 功能指南

```php
use Respect\Validation\Validator as v;

// 單個規則驗證
$number = 123;
v::numericVal()->validate($number); // true

// 多個規則鏈式驗證
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// 獲得第一個驗證失敗原因
try {
    $usernameValidator->setName('使用者名稱')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // 使用者名稱 只能包含字母（a-z）和數字（0-9）
}

// 獲得所有驗證失敗的原因
try {
    $usernameValidator->setName('使用者名稱')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // 將會列印
    // -  使用者名稱 必須符合以下規則
    //     - 使用者名稱 只能包含字母（a-z）和數字（0-9）
    //     - 使用者名稱 不能包含空格
  
    var_export($exception->getMessages());
    // 將會列印
    // array (
    //   'alnum' => '使用者名稱 只能包含字母（a-z）和數字（0-9）',
    //   'noWhitespace' => '使用者名稱 不能包含空格',
    // )
}

// 自訂錯誤提示資訊
try {
    $usernameValidator->setName('使用者名稱')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => '使用者名稱只能包含字母和數字',
        'noWhitespace' => '使用者名稱不能有空格',
        'length' => 'length符合規則，所以這條將不會顯示'
    ]);
    // 將會列印 
    // array(
    //    'alnum' => '使用者名稱只能包含字母和數字',
    //    'noWhitespace' => '使用者名稱不能有空格'
    // )
}

// 驗證物件
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// 驗證陣列
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
    ->assert($data); // 也可以用 check() 或 validate()
  
// 可選驗證
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// 否定規則
v::not(v::intVal())->validate(10); // false
```
  
## Validator 三個方法 `validate()` `check()` `assert()` 區別

`validate()`返回布林型，不會拋出異常

`check()`驗證失敗時拋出異常，透過`$exception->getMessage()`第一條驗證失敗的原因

`assert()`驗證失敗時拋出異常，透過`$exception->getFullMessage()`可以獲得所有驗證失敗的原因
  
  
## 常用驗證規則清單

`Alnum()` 只包含字母和數字

`Alpha()` 只包含字母

`ArrayType()` 陣列類型

`Between(mixed $minimum, mixed $maximum)` 驗證輸入是否在其他兩個值之間。

`BoolType()` 驗證是否是布林型

`Contains(mixed $expectedValue)` 驗證輸入是否包含某些值

`ContainsAny(array $needles)` 驗證輸入是否至少包含一個定義的值

`Digit()` 驗證輸入是否只包含數字

`Domain()` 驗證是否是合法的網域

`Email()` 驗證是否是合法的郵件地址

`Extension(string $extension)` 驗證後綴名

`FloatType()` 驗證是否是浮點型

`IntType()` 驗證是否是整數

`Ip()` 驗證是否是ip地址

`Json()` 驗證是否是json資料

`Length(int $min, int $max)` 驗證長度是否在給定區間

`LessThan(mixed $compareTo)` 驗證長度是否小於給定值

`Lowercase()` 驗證是否是小寫字母

`MacAddress()` 驗證是否是mac地址

`NotEmpty()` 驗證是否為空

`NullType()` 驗證是否為null

`Number()` 驗證是否為數字

`ObjectType()` 驗證是否為物件

`StringType()` 驗證是否為字串類型

`Url()` 驗證是否為url
  
更多驗證規則參見 https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ 
  
## 更多內容

存取 https://respect-validation.readthedocs.io/en/2.0/
  
