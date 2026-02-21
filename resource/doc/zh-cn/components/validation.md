# 其它验证器
composer有很多验证器可以直接在使用，例如：
#### <a href="#webman-validation"> webman/validation(推荐)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# 验证器 webman/validation
基于 `illuminate/validation`，提供手动验证、注解验证、参数级验证，以及可复用的规则集。

## 安装

```bash
composer require webman/validation
```

## 基本概念

- **规则集复用**：通过继承 `support\validation\Validator` 定义可复用的 `rules` `messages` `attributes` `scenes`，可在手动与注解中复用。
- **方法级注解（Attribute）验证**：使用 PHP 8 属性注解 `#[Validate]` 绑定控制器方法。
- **参数级注解（Attribute）验证**：使用 PHP 8 属性注解 `#[Param]` 绑定控制器方法参数。
- **异常处理**：验证失败抛出 `support\validation\ValidationException`，异常类可通过配置自定义
- **数据库验证**：如果涉及数据库验证，需要安装 `composer require webman/database`

## 手动验证

### 基本用法

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **提示**  
> `validate()` 校验失败会抛出 `support\validation\ValidationException`。如果你不希望抛异常，请使用下方的 `fails()` 写法获取错误信息。

### 自定义 messages 与 attributes

```php
use support\validation\Validator;

$data = ['contact' => 'user@example.com'];

Validator::make(
    $data,
    ['contact' => 'required|email'],
    ['contact.email' => '邮箱格式不正确'],
    ['contact' => '邮箱']
)->validate();
```

### 不抛异常并获取错误信息

如果你不希望抛异常，可以使用 `fails()` 判断，并通过 `errors()`（返回 `MessageBag`）获取错误信息：

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
    // 处理错误...
}
```

## 规则集复用（自定义 Validator）

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
        'email.required' => '邮箱必填',
        'email.email' => '邮箱格式不正确',
    ];

    protected array $attributes = [
        'name' => '姓名',
        'email' => '邮箱',
    ];
}
```

### 手动验证复用

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### 使用 scenes（可选）

`scenes` 是可选能力，只有在你调用 `withScene(...)` 时，才会按场景只验证部分字段。

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

// 不指定场景 -> 验证全部规则
UserValidator::make($data)->validate();

// 指定场景 -> 只验证该场景包含的字段
UserValidator::make($data)->withScene('create')->validate();
```

## 注解验证（方法级）

### 直接规则

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
            'email.required' => '邮箱必填',
            'password.required' => '密码必填',
        ],
        attributes: [
            'email' => '邮箱',
            'password' => '密码',
        ]
    )]
    public function login(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### 复用规则集

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

### 多重验证叠加

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

### 验证数据来源
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

通过`in`参数来指定数据来源，其中：

* **query** http请求的query参数，取自 `$request->get()`
* **body** http请求的包体，取自 `$request->post()`
* **path** http请求的路径参数，取自 `$request->route->param()`

`in`可为字符串或数组；为数组时按顺序合并，后者覆盖前者。未传递`in`时默认等效于 `['query', 'body', 'path']`。


## 参数级验证（Param）

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

### 验证数据来源

类似的，参数级也支持`in`参数指定来源

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


### rules 支持字符串或数组

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

### 自定义 messages / attribute

```php
use support\validation\annotation\Param;

class UserController
{
    public function updateEmail(
        #[Param(
            rules: 'required|email',
            messages: ['email.email' => '邮箱格式不正确'],
            attribute: '邮箱'
        )]
        string $email
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### 规则常量复用

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

## 方法级 + 参数级混合

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

## 自动规则推导（基于参数签名）

当方法上使用 `#[Validate]`，或该方法的任意参数使用了 `#[Param]` 时，本组件会**根据方法参数签名自动推导并补全基础验证规则**，再与已有规则合并后执行验证。

### 示例：`#[Validate]` 等价展开

1) 只开启 `#[Validate]`，不手写规则：

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

等价于：

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

2) 只写了部分规则，其余由参数签名补全：

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

等价于：

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

3) 默认值/可空类型：

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate]
    public function create(string $content = '默认值', ?int $uid = null)
    {
    }
}
```

等价于：

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate(rules: [
        'content' => 'string',
        'uid' => 'integer|nullable',
    ])]
    public function create(string $content = '默认值', ?int $uid = null)
    {
    }
}
```

## 异常处理

### 默认异常

验证失败默认抛出 `support\validation\ValidationException`，继承 `Webman\Exception\BusinessException`，不会记录错误日志。

默认响应行为由 `BusinessException::render()` 处理：

- 普通请求：返回字符串消息，例如 `token 为必填项。`
- JSON 请求：返回 JSON 响应，例如 `{"code": 422, "msg": "token 为必填项。", "data":....}`

### 通过自定义异常修改处理方式

- 全局配置：`config/plugin/webman/validation/app.php` 的 `exception`

## 多语言支持

组件内置中英文语言包，并支持项目覆盖。加载顺序：

1. 项目语言包 `resource/translations/{locale}/validation.php`
2. 组件内置 `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Illuminate 内置英文（兜底）

> **提示**
> webman默认语言由 `config/translation.php` 配置，也可以通过函数 locale('en'); 更改。

### 本地覆盖示例

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute 不是有效的邮件格式。',
];
```

## 中间件自动加载

组件安装后会通过 `config/plugin/webman/validation/middleware.php` 自动加载验证中间件，无需手动注册。

## 命令行生成注解

使用命令 `make:validator` 生成验证器类（默认生成到 `app/validation` 目录）。

> **提示**
> 需要安装 `composer require webman/console`

### 基础用法

- **生成空模板**

```bash
php webman make:validator UserValidator
```

- **覆盖已存在文件**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### 从表结构生成规则

- **指定表名生成基础规则**（会根据字段类型/可空/长度等推导 `$rules`；默认排除字段与 ORM 相关：laravel 为 `created_at/updated_at/deleted_at`，thinkorm 为 `create_time/update_time/delete_time`）

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **指定数据库连接**（多连接场景）

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### 场景（scenes）

- **生成 CRUD 场景**：`create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> `update` 场景会包含主键字段（用于定位记录）以及其余字段；`delete/detail` 默认仅包含主键字段。

### ORM 选择（laravel(illuminate/database) 与 think-orm）

- **自动选择（默认）**：仅安装/配置了哪套就用哪套；都存在时默认用 illuminate
- **强制指定**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### 综合示例

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## 单元测试

进入 `webman/validation` 根目录执行：

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## 所有验证规则参考
<a name="available-validation-rules"></a>
## 可用验证规则

> [!IMPORTANT]
> - Webman Validation 基于 `illuminate/validation`，规则名称与 Laravel 一致，规则本身无 Webman 特化。
> - 中间件默认验证数据来自 `$request->all()`（GET+POST）并合并路由参数，不包含上传文件；涉及文件规则时，请自行将 `$request->file()` 合并到数据中，或手动调用 `Validator::make`。
> - `current_password` 依赖认证守卫，`exists`/`unique` 依赖数据库连接与查询构建器，未接入对应组件时规则不可用。

以下列出所有可用验证规则及其作用：

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

#### 布尔值

<div class="collection-method-list" markdown="1">

[Accepted](#rule-accepted)
[Accepted If](#rule-accepted-if)
[Boolean](#rule-boolean)
[Declined](#rule-declined)
[Declined If](#rule-declined-if)

</div>

#### 字符串

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

#### 数字

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

#### 数组

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

#### 文件

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

#### 数据库

<div class="collection-method-list" markdown="1">

[Exists](#rule-exists)
[Unique](#rule-unique)

</div>

#### 工具类

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

验证字段必须是 `"yes"`、`"on"`、`1`、`"1"`、`true` 或 `"true"`。常用于验证用户是否同意服务条款等场景。

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

当另一个字段等于指定值时，验证字段必须是 `"yes"`、`"on"`、`1`、`"1"`、`true` 或 `"true"`。常用于条件性同意类场景。

<a name="rule-active-url"></a>
#### active_url

验证字段必须具有有效的 A 或 AAAA 记录。该规则会先用 `parse_url` 提取 URL 的主机名，再交给 `dns_get_record` 校验。

<a name="rule-after"></a>
#### after:_date_

验证字段必须是给定日期之后的值。日期会传给 `strtotime` 转为有效的 `DateTime`：

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

也可以传入另一个字段名进行比较：

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

可使用 fluent `date` 规则构造器：

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

`afterToday` 和 `todayOrAfter` 可便捷表达“必须晚于今天”或“必须是今天或之后”：

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

验证字段必须是给定日期之后或等于给定日期。更多说明见 [after](#rule-after) 规则。

可使用 fluent `date` 规则构造器：

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

`Rule::anyOf` 允许指定“满足任意一个规则集即可”。例如，下面规则表示 `username` 要么是邮箱地址，要么是至少 6 位的字母数字下划线/短横线字符串：

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

验证字段必须是 Unicode 字母（[\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) 与 [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)）。

若仅允许 ASCII（`a-z`、`A-Z`），可添加 `ascii` 选项：

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

验证字段只能包含 Unicode 字母数字（[\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=)、[\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)、[\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)），以及 ASCII 短横线（`-`）与下划线（`_`）。

若仅允许 ASCII（`a-z`、`A-Z`、`0-9`），可添加 `ascii` 选项：

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

验证字段只能包含 Unicode 字母数字（[\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=)、[\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)、[\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)）。

若仅允许 ASCII（`a-z`、`A-Z`、`0-9`），可添加 `ascii` 选项：

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

验证字段必须是 PHP `array`。

当 `array` 规则带有额外参数时，输入数组的键必须在参数列表中。示例中 `admin` 键不在允许列表中，因此无效：

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

建议在实际项目中明确数组允许的键。

<a name="rule-ascii"></a>
#### ascii

验证字段只能包含 7-bit ASCII 字符。

<a name="rule-bail"></a>
#### bail

当某字段首个规则验证失败时，停止继续验证该字段的其它规则。

该规则只影响当前字段。若需要“全局首错即停”，请直接使用 Illuminate 的验证器并调用 `stopOnFirstFailure()`。

<a name="rule-before"></a>
#### before:_date_

验证字段必须早于给定日期。日期会传给 `strtotime` 转为有效的 `DateTime`。同 [after](#rule-after) 规则，可传入另一个字段名进行比较。

可使用 fluent `date` 规则构造器：

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

`beforeToday` 与 `todayOrBefore` 可便捷表达“必须早于今天”或“必须是今天或之前”：

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

验证字段必须早于或等于给定日期。日期会传给 `strtotime` 转为有效的 `DateTime`。同 [after](#rule-after) 规则，可传入另一个字段名进行比较。

可使用 fluent `date` 规则构造器：

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

验证字段的大小必须在给定的 _min_ 和 _max_ 之间（含边界）。字符串、数值、数组、文件的评估规则与 [size](#rule-size) 相同。

<a name="rule-boolean"></a>
#### boolean

验证字段必须可转换为布尔值。可接受的输入包括 `true`、`false`、`1`、`0`、`"1"`、`"0"`。

可通过 `strict` 参数仅允许 `true` 或 `false`：

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

验证字段必须有一个匹配字段 `{field}_confirmation`。例如字段为 `password` 时，需要 `password_confirmation`。

也可以指定自定义确认字段名，如 `confirmed:repeat_username` 将要求 `repeat_username` 与当前字段匹配。

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

验证字段必须是数组，且必须包含所有给定参数值。该规则常用于数组校验，可使用 `Rule::contains` 构造：

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

验证字段必须是数组，且不能包含任何给定参数值。可使用 `Rule::doesntContain` 构造：

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

验证字段必须与当前认证用户的密码匹配。可通过第一个参数指定认证 guard：

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> 该规则依赖认证组件与 guard 配置，未接入认证时请勿使用。

<a name="rule-date"></a>
#### date

验证字段必须是 `strtotime` 可识别的有效（非相对）日期。

<a name="rule-date-equals"></a>
#### date_equals:_date_

验证字段必须等于给定日期。日期会传给 `strtotime` 转为有效的 `DateTime`。

<a name="rule-date-format"></a>
#### date_format:_format_,...

验证字段必须匹配给定格式之一。使用 `date` 或 `date_format` 二选一即可。该规则支持 PHP [DateTime](https://www.php.net/manual/en/class.datetime.php) 的所有格式。

可使用 fluent `date` 规则构造器：

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

验证字段必须是数值，且小数位数符合要求：

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

验证字段必须是 `"no"`、`"off"`、`0`、`"0"`、`false` 或 `"false"`。

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

当另一个字段等于指定值时，验证字段必须是 `"no"`、`"off"`、`0`、`"0"`、`false` 或 `"false"`。

<a name="rule-different"></a>
#### different:_field_

验证字段必须与 _field_ 不同。

<a name="rule-digits"></a>
#### digits:_value_

验证字段必须是整数且长度为 _value_。

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

验证字段必须是整数且长度在 _min_ 与 _max_ 之间。

<a name="rule-dimensions"></a>
#### dimensions

验证字段必须是图片，并满足维度约束：

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

可用约束：_min\_width_、_max\_width_、_min\_height_、_max\_height_、_width_、_height_、_ratio_。

_ratio_ 为宽高比，可用分数或浮点表示：

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

该规则参数较多，建议使用 `Rule::dimensions` 构造：

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

当验证数组时，字段值不能重复：

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

默认使用宽松比较。需严格比较可添加 `strict`：

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

可添加 `ignore_case` 忽略大小写差异：

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

验证字段不能以指定值开头。

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

验证字段不能以指定值结尾。

<a name="rule-email"></a>
#### email

验证字段必须是有效邮箱地址。该规则依赖 [egulias/email-validator](https://github.com/egulias/EmailValidator)，默认使用 `RFCValidation`，也可指定其他验证方式：

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

可用验证方式列表：

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` - 按 RFC 规范验证邮箱（[支持的 RFC](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs)）。
- `strict`: `NoRFCWarningsValidation` - RFC 校验时遇到警告即失败（如结尾点号或连续点号）。
- `dns`: `DNSCheckValidation` - 校验域名是否有有效的 MX 记录。
- `spoof`: `SpoofCheckValidation` - 防止同形异义或欺骗性 Unicode 字符。
- `filter`: `FilterEmailValidation` - 使用 PHP `filter_var` 验证。
- `filter_unicode`: `FilterEmailValidation::unicode()` - 允许 Unicode 的 `filter_var` 验证。

</div>

可使用 fluent 规则构造器：

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
> `dns` 与 `spoof` 需要 PHP `intl` 扩展。

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

验证字段必须匹配指定字符编码。该规则使用 `mb_check_encoding` 检测文件或字符串编码。可配合文件规则构造器使用：

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

验证字段必须以指定值之一结尾。

<a name="rule-enum"></a>
#### enum

`Enum` 是基于类的规则，用于验证字段值是否为合法枚举值。构造时传入枚举类名。验证基本类型值时，应使用 Backed Enum：

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

可用 `only`/`except` 限制枚举值：

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

可使用 `when` 进行条件限制：

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

验证字段会从 `validate`/`validated` 返回的数据中排除。

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

当 _anotherfield_ 等于 _value_ 时，验证字段会从 `validate`/`validated` 返回的数据中排除。

如需复杂条件，可使用 `Rule::excludeIf`：

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

除非 _anotherfield_ 等于 _value_，否则验证字段会从 `validate`/`validated` 返回的数据中排除。若 _value_ 为 `null`（如 `exclude_unless:name,null`），则只有当比较字段为 `null` 或不存在时才保留该字段。

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

当 _anotherfield_ 存在时，验证字段会从 `validate`/`validated` 返回的数据中排除。

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

当 _anotherfield_ 不存在时，验证字段会从 `validate`/`validated` 返回的数据中排除。

<a name="rule-exists"></a>
#### exists:_table_,_column_

验证字段必须存在于指定数据库表中。

<a name="basic-usage-of-exists-rule"></a>
#### Exists 规则的基础用法

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

未指定 `column` 时，默认使用字段名。因此该例会验证 `states` 表中 `state` 列是否存在。

<a name="specifying-a-custom-column-name"></a>
#### 指定自定义列名

可在表名后追加列名：

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

如需指定数据库连接，可在表名前加连接名：

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

也可传入模型类名，由框架解析表名：

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

若需自定义查询条件，可使用 `Rule` 规则构造器：

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

验证一组值是否存在时，可结合 `array` 规则：

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

当 `array` 与 `exists` 同时存在时，会生成单条查询验证全部值。

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

验证上传文件的扩展名是否在允许列表内：

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> 不要仅依赖扩展名验证文件类型，建议与 [mimes](#rule-mimes) 或 [mimetypes](#rule-mimetypes) 搭配使用。

<a name="rule-file"></a>
#### file

验证字段必须是成功上传的文件。

<a name="rule-filled"></a>
#### filled

当字段存在时，其值不能为空。

<a name="rule-gt"></a>
#### gt:_field_

验证字段必须大于给定 _field_ 或 _value_。两个字段类型必须一致。字符串、数值、数组、文件的评估规则与 [size](#rule-size) 相同。

<a name="rule-gte"></a>
#### gte:_field_

验证字段必须大于等于给定 _field_ 或 _value_。两个字段类型必须一致。字符串、数值、数组、文件的评估规则与 [size](#rule-size) 相同。

<a name="rule-hex-color"></a>
#### hex_color

验证字段必须是有效的 [十六进制颜色值](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color)。

<a name="rule-image"></a>
#### image

验证字段必须是图片（jpg、jpeg、png、bmp、gif 或 webp）。

> [!WARNING]
> 出于 XSS 风险，默认不允许 SVG。如需允许，可加 `allow_svg`：`image:allow_svg`。

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

验证字段必须在给定值列表中。可使用 `Rule::in` 构造：

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

与 `array` 规则组合时，输入数组的每个值都必须在 `in` 列表内：

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

验证字段必须存在于 _anotherfield_ 的值列表中。

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

验证字段必须是数组，且至少包含给定值之一作为键：

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

验证字段必须是整数。

可使用 `strict` 参数要求字段类型必须为整数，字符串形式的整数将视为无效：

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> 该规则仅验证是否能通过 PHP 的 `FILTER_VALIDATE_INT`，如需强制数值类型，请与 [numeric](#rule-numeric) 配合使用。

<a name="rule-ip"></a>
#### ip

验证字段必须是合法 IP 地址。

<a name="rule-ipv4"></a>
#### ipv4

验证字段必须是合法 IPv4 地址。

<a name="rule-ipv6"></a>
#### ipv6

验证字段必须是合法 IPv6 地址。

<a name="rule-json"></a>
#### json

验证字段必须是有效的 JSON 字符串。

<a name="rule-lt"></a>
#### lt:_field_

验证字段必须小于给定 _field_。两个字段类型必须一致。字符串、数值、数组、文件的评估规则与 [size](#rule-size) 相同。

<a name="rule-lte"></a>
#### lte:_field_

验证字段必须小于等于给定 _field_。两个字段类型必须一致。字符串、数值、数组、文件的评估规则与 [size](#rule-size) 相同。

<a name="rule-lowercase"></a>
#### lowercase

验证字段必须是小写。

<a name="rule-list"></a>
#### list

验证字段必须是列表数组。列表数组的键必须是从 0 到 `count($array) - 1` 的连续数字。

<a name="rule-mac"></a>
#### mac_address

验证字段必须是合法 MAC 地址。

<a name="rule-max"></a>
#### max:_value_

验证字段必须小于或等于 _value_。字符串、数值、数组、文件的评估规则与 [size](#rule-size) 相同。

<a name="rule-max-digits"></a>
#### max_digits:_value_

验证字段必须是整数，且长度不超过 _value_。

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

验证文件的 MIME 类型是否在列表内：

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

MIME 类型通过读取文件内容猜测，可能与客户端提供的 MIME 不一致。

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

验证文件的 MIME 类型是否与给定扩展名对应：

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

尽管参数是扩展名，该规则会读取文件内容判断 MIME。扩展名与 MIME 对照表见：

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### MIME 类型与扩展名

该规则不验证“文件名扩展名”与“实际 MIME”是否一致。例如，`mimes:png` 会将内容为 PNG 的 `photo.txt` 视为合法。如果需要验证扩展名，请使用 [extensions](#rule-extensions)。

<a name="rule-min"></a>
#### min:_value_

验证字段必须大于或等于 _value_。字符串、数值、数组、文件的评估规则与 [size](#rule-size) 相同。

<a name="rule-min-digits"></a>
#### min_digits:_value_

验证字段必须是整数，且长度不少于 _value_。

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

验证字段必须是 _value_ 的倍数。

<a name="rule-missing"></a>
#### missing

验证字段必须不存在于输入数据中。

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

当 _anotherfield_ 等于任一 _value_ 时，验证字段必须不存在。

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

除非 _anotherfield_ 等于任一 _value_，否则验证字段必须不存在。

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

当任意指定字段存在时，验证字段必须不存在。

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

当所有指定字段都存在时，验证字段必须不存在。

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

验证字段必须不在给定值列表中。可使用 `Rule::notIn` 构造：

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

验证字段不能匹配给定正则表达式。

该规则使用 PHP `preg_match`。正则必须带分隔符，例如：`'email' => 'not_regex:/^.+$/i'`。

> [!WARNING]
> 使用 `regex` / `not_regex` 时，如正则包含 `|`，建议用数组形式声明规则，避免与 `|` 分隔符冲突。

<a name="rule-nullable"></a>
#### nullable

验证字段允许为 `null`。

<a name="rule-numeric"></a>
#### numeric

验证字段必须是 [numeric](https://www.php.net/manual/en/function.is-numeric.php)。

可使用 `strict` 参数仅允许整数或浮点类型，数值字符串将视为无效：

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

验证字段必须存在于输入数据中。

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

当 _anotherfield_ 等于任一 _value_ 时，验证字段必须存在。

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

除非 _anotherfield_ 等于任一 _value_，否则验证字段必须存在。

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

当任意指定字段存在时，验证字段必须存在。

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

当所有指定字段都存在时，验证字段必须存在。

<a name="rule-prohibited"></a>
#### prohibited

验证字段必须缺失或为空。字段“为空”指：

<div class="content-list" markdown="1">

- 值为 `null`。
- 值为空字符串。
- 值为空数组或空的 `Countable` 对象。
- 为上传文件且路径为空。

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

当 _anotherfield_ 等于任一 _value_ 时，验证字段必须缺失或为空。字段“为空”指：

<div class="content-list" markdown="1">

- 值为 `null`。
- 值为空字符串。
- 值为空数组或空的 `Countable` 对象。
- 为上传文件且路径为空。

</div>

如需复杂条件，可使用 `Rule::prohibitedIf`：

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

当 _anotherfield_ 为 `"yes"`、`"on"`、`1`、`"1"`、`true` 或 `"true"` 时，验证字段必须缺失或为空。

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

当 _anotherfield_ 为 `"no"`、`"off"`、`0`、`"0"`、`false` 或 `"false"` 时，验证字段必须缺失或为空。

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

除非 _anotherfield_ 等于任一 _value_，否则验证字段必须缺失或为空。字段“为空”指：

<div class="content-list" markdown="1">

- 值为 `null`。
- 值为空字符串。
- 值为空数组或空的 `Countable` 对象。
- 为上传文件且路径为空。

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

当验证字段存在且不为空时，_anotherfield_ 中所有字段必须缺失或为空。字段“为空”指：

<div class="content-list" markdown="1">

- 值为 `null`。
- 值为空字符串。
- 值为空数组或空的 `Countable` 对象。
- 为上传文件且路径为空。

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

验证字段必须匹配给定正则表达式。

该规则使用 PHP `preg_match`。正则必须带分隔符，例如：`'email' => 'regex:/^.+@.+$/i'`。

> [!WARNING]
> 使用 `regex` / `not_regex` 时，如正则包含 `|`，建议用数组形式声明规则，避免与 `|` 分隔符冲突。

<a name="rule-required"></a>
#### required

验证字段必须存在且不能为空。字段“为空”指：

<div class="content-list" markdown="1">

- 值为 `null`。
- 值为空字符串。
- 值为空数组或空的 `Countable` 对象。
- 为上传文件且路径为空。

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

当 _anotherfield_ 等于任一 _value_ 时，验证字段必须存在且不能为空。

如需复杂条件，可使用 `Rule::requiredIf`：

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

当 _anotherfield_ 为 `"yes"`、`"on"`、`1`、`"1"`、`true` 或 `"true"` 时，验证字段必须存在且不能为空。

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

当 _anotherfield_ 为 `"no"`、`"off"`、`0`、`"0"`、`false` 或 `"false"` 时，验证字段必须存在且不能为空。

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

除非 _anotherfield_ 等于任一 _value_，否则验证字段必须存在且不能为空。若 _value_ 为 `null`（如 `required_unless:name,null`），则只有当比较字段为 `null` 或不存在时才允许验证字段为空。

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

当任意指定字段存在且不为空时，验证字段必须存在且不能为空。

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

当所有指定字段都存在且不为空时，验证字段必须存在且不能为空。

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

当任意指定字段为空或不存在时，验证字段必须存在且不能为空。

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

当所有指定字段都为空或不存在时，验证字段必须存在且不能为空。

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

验证字段必须是数组，且至少包含指定的键。

<a name="validating-when-present"></a>
#### sometimes

仅当字段存在时，才应用后续验证规则。常用于“可选但一旦存在就必须合法”的字段：

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

验证字段必须与 _field_ 相同。

<a name="rule-size"></a>
#### size:_value_

验证字段大小必须等于给定 _value_。字符串为字符数；数值为指定整数（需配合 `numeric` 或 `integer`）；数组为元素数；文件为 KB 大小。示例：

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

验证字段必须以指定值之一开头。

<a name="rule-string"></a>
#### string

验证字段必须是字符串。如需允许 `null`，请配合 `nullable` 使用。

<a name="rule-timezone"></a>
#### timezone

验证字段必须是有效时区标识符（来自 `DateTimeZone::listIdentifiers`）。可传入该方法支持的参数：

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

验证字段在指定表中必须唯一。

**指定自定义表/列名：**

可直接指定模型类名：

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

可指定列名（不指定时默认字段名）：

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**指定数据库连接：**

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
> `ignore` 不应接收用户输入，仅应使用系统生成的唯一 ID（自增 ID 或模型 UUID），否则可能存在 SQL 注入风险。

也可传入模型实例：

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

若主键不是 `id`，可指定主键名：

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

默认以字段名作为唯一列，亦可指定列名：

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**添加额外条件：**

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

**忽略软删除记录：**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

若软删除列名不是 `deleted_at`：

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

验证字段必须为大写。

<a name="rule-url"></a>
#### url

验证字段必须是有效 URL。

可指定允许的协议：

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

验证字段必须是有效 [ULID](https://github.com/ulid/spec)。

<a name="rule-uuid"></a>
#### uuid

验证字段必须是有效的 RFC 9562 UUID（版本 1、3、4、5、6、7 或 8）。

可指定版本：

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```



<a name="think-validate"></a>
# 验证器 top-think/think-validate

## 说明
ThinkPHP官方验证器

## 项目地址
https://github.com/top-think/think-validate

## 安装
`composer require topthink/think-validate`

## 快速开始

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
        'name.require' => '名称必须',
        'name.max'     => '名称最多不能超过25个字符',
        'age.number'   => '年龄必须是数字',
        'age.between'  => '年龄只能在1-120之间',
        'email'        => '邮箱格式错误',    
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
> webman里不支持think-validate的`Validate::rule()`方法

<a name="respect-validation"></a>
# 验证器 workerman/validation

## 说明

项目为 https://github.com/Respect/Validation 的汉化版本

## 项目地址

https://github.com/walkor/validation
  
  
## 安装
 
```php
composer require workerman/validation
```

## 快速开始

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
            'nickname' => v::length(1, 64)->setName('昵称'),
            'username' => v::alnum()->length(5, 64)->setName('用户名'),
            'password' => v::length(5, 64)->setName('密码')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
  
**通过jquery访问**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'汤姆', username:'tom cat', password: '123456'}
  });
  ```
  
得到结果：

`{"code":500,"msg":"用户名 只能包含字母（a-z）和数字（0-9）"}`

说明：

`v::input(array $input, array $rules)` 用来验证并收集数据，如果数据验证失败，则抛出`Respect\Validation\Exceptions\ValidationException`异常，验证成功则将返回验证后的数据(数组)。

如果业务代码未捕获验证异常，则webman框架将自动捕获并根据HTTP请求头选择返回json数据(类似`{"code":500, "msg":"xxx"}`)或者普通的异常页面。如返回格式不符合业务需求，开发者可自行捕获`ValidationException`异常并返回需要的数据，类似下面的例子：

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
                'username' => v::alnum()->length(5, 64)->setName('用户名'),
                'password' => v::length(5, 64)->setName('密码')
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

// 单个规则验证
$number = 123;
v::numericVal()->validate($number); // true

// 多个规则链式验证
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// 获得第一个验证失败原因
try {
    $usernameValidator->setName('用户名')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // 用户名 只能包含字母（a-z）和数字（0-9）
}

// 获得所有验证失败的原因
try {
    $usernameValidator->setName('用户名')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // 将会打印
    // -  用户名 必须符合以下规则
    //     - 用户名 只能包含字母（a-z）和数字（0-9）
    //     - 用户名 不能包含空格
  
    var_export($exception->getMessages());
    // 将会打印
    // array (
    //   'alnum' => '用户名 只能包含字母（a-z）和数字（0-9）',
    //   'noWhitespace' => '用户名 不能包含空格',
    // )
}

// 自定义错误提示信息
try {
    $usernameValidator->setName('用户名')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => '用户名只能包含字母和数字',
        'noWhitespace' => '用户名不能有空格',
        'length' => 'length符合规则，所以这条将不会显示'
    ]);
    // 将会打印 
    // array(
    //    'alnum' => '用户名只能包含字母和数字',
    //    'noWhitespace' => '用户名不能有空格'
    // )
}

// 验证对象
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// 验证数组
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
  
// 可选验证
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// 否定规则
v::not(v::intVal())->validate(10); // false
```
  
## Validator 三个方法 `validate()` `check()` `assert()` 区别

`validate()`返回布尔型，不会抛出异常

`check()`验证失败时抛出异常，通过`$exception->getMessage()`第一条验证失败的原因

`assert()`验证失败时抛出异常，通过`$exception->getFullMessage()`可以获得所有验证失败的原因
  
  
## 常用验证规则列表

`Alnum()` 只包含字母和数字

`Alpha()` 只包含字母

`ArrayType()` 数组类型

`Between(mixed $minimum, mixed $maximum)` 验证输入是否在其他两个值之间。

`BoolType()` 验证是否是布尔型

`Contains(mixed $expectedValue)` 验证输入是否包含某些值

`ContainsAny(array $needles)` 验证输入是否至少包含一个定义的值

`Digit()` 验证输入是否只包含数字

`Domain()` 验证是否是合法的域名

`Email()` 验证是否是合法的邮件地址

`Extension(string $extension)` 验证后缀名

`FloatType()` 验证是否是浮点型

`IntType()` 验证是否是整数

`Ip()` 验证是否是ip地址

`Json()` 验证是否是json数据

`Length(int $min, int $max)` 验证长度是否在给定区间

`LessThan(mixed $compareTo)` 验证长度是否小于给定值

`Lowercase()` 验证是否是小写字母

`MacAddress()` 验证是否是mac地址

`NotEmpty()` 验证是否为空

`NullType()` 验证是否为null

`Number()` 验证是否为数字

`ObjectType()` 验证是否为对象

`StringType()` 验证是否为字符串类型

`Url()` 验证是否为url
  
更多验证规则参见 https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ 
  
## 更多内容

访问 https://respect-validation.readthedocs.io/en/2.0/
  

