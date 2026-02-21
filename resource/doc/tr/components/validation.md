# Diğer Doğrulayıcılar

Composer'da doğrudan kullanılabilecek birçok doğrulayıcı mevcuttur:

#### <a href="#webman-validation"> webman/validation (Önerilen)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# Doğrulayıcı webman/validation

`illuminate/validation` tabanlıdır; manuel doğrulama, annotation doğrulama, parametre düzeyinde doğrulama ve yeniden kullanılabilir kural setleri sağlar.

## Kurulum

```bash
composer require webman/validation
```

## Temel Kavramlar

- **Kural Seti Yeniden Kullanımı**: `support\validation\Validator` sınıfını genişleterek yeniden kullanılabilir `rules`, `messages`, `attributes` ve `scenes` tanımlayın; hem manuel hem de annotation doğrulamada kullanılabilir.
- **Metot Düzeyinde Annotation (Attribute) Doğrulama**: PHP 8 attribute `#[Validate]` ile doğrulamayı controller metotlarına bağlayın.
- **Parametre Düzeyinde Annotation (Attribute) Doğrulama**: PHP 8 attribute `#[Param]` ile doğrulamayı controller metot parametrelerine bağlayın.
- **İstisna İşleme**: Doğrulama başarısız olduğunda `support\validation\ValidationException` fırlatır; istisna sınıfı yapılandırılabilir.
- **Veritabanı Doğrulaması**: Veritabanı doğrulaması söz konusuysa `composer require webman/database` kurmanız gerekir.

## Manuel Doğrulama

### Temel Kullanım

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **Not**
> `validate()` doğrulama başarısız olduğunda `support\validation\ValidationException` fırlatır. İstisna fırlatmak istemiyorsanız, hata mesajlarını almak için aşağıdaki `fails()` yaklaşımını kullanın.

### Özel mesajlar ve nitelikler

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

### İstisna Olmadan Doğrulama (Hata Mesajlarını Alma)

İstisna fırlatmak istemiyorsanız, `fails()` ile kontrol edin ve `errors()` (MessageBag döndürür) üzerinden hata mesajlarını alın:

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

## Kural Seti Yeniden Kullanımı (Özel Doğrulayıcı)

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

### Manuel Doğrulama Yeniden Kullanımı

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### Sahne kullanımı (İsteğe Bağlı)

`scenes` isteğe bağlı bir özelliktir; `withScene(...)` çağırdığınızda yalnızca alanların bir alt kümesini doğrular.

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

// Sahne belirtilmedi -> tüm kuralları doğrula
UserValidator::make($data)->validate();

// Sahne belirtildi -> yalnızca o sahnedeki alanları doğrula
UserValidator::make($data)->withScene('create')->validate();
```

## Annotation Doğrulama (Metot Düzeyinde)

### Doğrudan Kurallar

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

### Kural Setlerini Yeniden Kullanma

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

### Çoklu Doğrulama Katmanları

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

### Doğrulama Veri Kaynağı

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

`in` parametresi ile veri kaynağını belirtin:

* **query** HTTP istek sorgu parametreleri, `$request->get()` üzerinden
* **body** HTTP istek gövdesi, `$request->post()` üzerinden
* **path** HTTP istek yol parametreleri, `$request->route->param()` üzerinden

`in` bir string veya dizi olabilir; dizi olduğunda değerler sırayla birleştirilir ve sonraki değerler öncekilerin üzerine yazar. `in` geçirilmediğinde varsayılan olarak `['query', 'body', 'path']` kullanılır.


## Parametre Düzeyinde Doğrulama (Param)

### Temel Kullanım

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

### Doğrulama Veri Kaynağı

Benzer şekilde, parametre düzeyinde doğrulama da kaynağı belirtmek için `in` parametresini destekler:

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


### rules string veya array destekler

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

### Özel mesajlar / nitelik

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

### Kural Sabiti Yeniden Kullanımı

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

## Metot Düzeyinde + Parametre Düzeyinde Birleşik

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

## Otomatik Kural Çıkarımı (Parametre İmzasına Dayalı)

Bir metotta `#[Validate]` kullanıldığında veya o metodun herhangi bir parametresi `#[Param]` kullandığında, bu bileşen **metot parametre imzasından temel doğrulama kurallarını otomatik olarak çıkarır ve tamamlar**, ardından doğrulamadan önce mevcut kurallarla birleştirir.

### Örnek: `#[Validate]` eşdeğer genişletme

1) Yalnızca `#[Validate]` etkinleştirilmiş, kurallar manuel yazılmamış:

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

Şuna eşdeğer:

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

2) Yalnızca kısmi kurallar yazılmış, geri kalanı parametre imzasıyla tamamlanıyor:

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

Şuna eşdeğer:

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

3) Varsayılan değer / nullable tip:

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

Şuna eşdeğer:

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

## İstisna İşleme

### Varsayılan İstisna

Doğrulama başarısızlığı varsayılan olarak `support\validation\ValidationException` fırlatır; bu sınıf `Webman\Exception\BusinessException` sınıfını genişletir ve hata günlüğe kaydedilmez.

Varsayılan yanıt davranışı `BusinessException::render()` tarafından işlenir:

- Normal istekler: string mesaj döndürür, örn. `token is required.`
- JSON istekleri: JSON yanıt döndürür, örn. `{"code": 422, "msg": "token is required.", "data":....}`

### Özel istisna ile özelleştirme

- Global yapılandırma: `config/plugin/webman/validation/app.php` içindeki `exception`

## Çoklu Dil Desteği

Bileşen yerleşik Çince ve İngilizce dil paketleri içerir ve proje geçersiz kılmalarını destekler. Yükleme sırası:

1. Proje dil paketi `resource/translations/{locale}/validation.php`
2. Bileşen yerleşik `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Illuminate yerleşik İngilizce (yedek)

> **Not**
> Webman varsayılan dili `config/translation.php` içinde yapılandırılır veya `locale('en');` ile değiştirilebilir.

### Yerel Geçersiz Kılma Örneği

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute is not a valid email format.',
];
```

## Middleware Otomatik Yükleme

Kurulumdan sonra bileşen, `config/plugin/webman/validation/middleware.php` üzerinden doğrulama middleware'ini otomatik yükler; manuel kayıt gerekmez.

## Komut Satırı Oluşturma

Doğrulayıcı sınıfları oluşturmak için `make:validator` komutunu kullanın (varsayılan çıktı `app/validation` dizinine).

> **Not**
> `composer require webman/console` gerekir

### Temel Kullanım

- **Boş şablon oluştur**

```bash
php webman make:validator UserValidator
```

- **Mevcut dosyanın üzerine yaz**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### Tablo yapısından kurallar oluşturma

- **Temel kurallar oluşturmak için tablo adı belirtin** (alan tipi/nullable/uzunluk vb.den `$rules` çıkarır; varsayılan olarak ORM ile ilgili alanları hariç tutar: laravel `created_at/updated_at/deleted_at`, thinkorm `create_time/update_time/delete_time` kullanır)

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **Veritabanı bağlantısı belirtin** (çoklu bağlantı senaryoları)

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### Sahne

- **CRUD sahneleri oluştur**: `create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> `update` sahnesi birincil anahtar alanını (kayıt bulmak için) artı diğer alanları içerir; `delete/detail` varsayılan olarak yalnızca birincil anahtarı içerir.

### ORM seçimi (laravel (illuminate/database) vs think-orm)

- **Otomatik seçim (varsayılan)**: Kurulu/yapılandırılan kullanılır; her ikisi de mevcutsa varsayılan olarak illuminate kullanılır
- **Zorla belirt**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### Tam örnek

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## Birim Testleri

`webman/validation` kök dizininden çalıştırın:

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## Doğrulama Kuralları Referansı

<a name="available-validation-rules"></a>
## Mevcut Doğrulama Kuralları

> [!IMPORTANT]
> - Webman Validation `illuminate/validation` tabanlıdır; kural adları Laravel ile eşleşir ve Webman'e özgü kurallar yoktur.
> - Middleware varsayılan olarak `$request->all()` (GET+POST) ile rota parametrelerinin birleşiminden gelen veriyi doğrular, yüklenen dosyalar hariç; dosya kuralları için `$request->file()` veriyi kendiniz birleştirin veya `Validator::make`'i manuel çağırın.
> - `current_password` auth guard'a bağlıdır; `exists`/`unique` veritabanı bağlantısına ve sorgu oluşturucuya bağlıdır; bu kurallar ilgili bileşenler entegre edilmediğinde kullanılamaz.

Aşağıda tüm mevcut doğrulama kuralları ve amaçları listelenmiştir:

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

Alan `"yes"`, `"on"`, `1`, `"1"`, `true` veya `"true"` olmalıdır. Genellikle kullanıcının hizmet şartlarını kabul ettiğini doğrulama gibi senaryolarda kullanılır.

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

Başka bir alan belirtilen değere eşit olduğunda, alan `"yes"`, `"on"`, `1`, `"1"`, `true` veya `"true"` olmalıdır. Genellikle koşullu kabul senaryolarında kullanılır.

<a name="rule-active-url"></a>
#### active_url

Alan geçerli bir A veya AAAA kaydına sahip olmalıdır. Bu kural önce URL host adını çıkarmak için `parse_url` kullanır, ardından `dns_get_record` ile doğrular.

<a name="rule-after"></a>
#### after:_date_

Alan verilen tarihten sonra bir değer olmalıdır. Tarih geçerli bir `DateTime`'a dönüştürmek için `strtotime`'a geçirilir:

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

Karşılaştırma için başka bir alan adı da geçirebilirsiniz:

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

Akıcı `date` kural oluşturucusunu kullanabilirsiniz:

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

`afterToday` ve `todayOrAfter` "bugünden sonra olmalı" veya "bugün veya sonrası olmalı" ifadelerini kolayca belirtir:

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

Alan verilen tarihte veya sonrasında olmalıdır. Daha fazla ayrıntı için [after](#rule-after) bölümüne bakın.

Akıcı `date` kural oluşturucusunu kullanabilirsiniz:

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

`Rule::anyOf` "herhangi bir kural setini karşıla" belirtmeye izin verir. Örneğin aşağıdaki kural `username`'in ya bir e-posta adresi ya da en az 6 karakterlik alfanumerik/alt çizgi/tire dizisi olması gerektiği anlamına gelir:

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

Alan Unicode harflerden oluşmalıdır ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) ve [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)).

Yalnızca ASCII (`a-z`, `A-Z`) izin vermek için `ascii` seçeneğini ekleyin:

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

Alan yalnızca Unicode harfler ve sayılar ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)) artı ASCII tire (`-`) ve alt çizgi (`_`) içerebilir.

Yalnızca ASCII (`a-z`, `A-Z`, `0-9`) izin vermek için `ascii` seçeneğini ekleyin:

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

Alan yalnızca Unicode harfler ve sayılar ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)) içerebilir.

Yalnızca ASCII (`a-z`, `A-Z`, `0-9`) izin vermek için `ascii` seçeneğini ekleyin:

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

Alan bir PHP `array` olmalıdır.

`array` kuralı ek parametrelere sahip olduğunda, giriş dizi anahtarları parametre listesinde olmalıdır. Örnekte `admin` anahtarı izin verilen listede değil, bu yüzden geçersizdir:

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

Gerçek projelerde izin verilen dizi anahtarlarını açıkça tanımlamanız önerilir.

<a name="rule-ascii"></a>
#### ascii

Alan yalnızca 7-bit ASCII karakterler içerebilir.

<a name="rule-bail"></a>
#### bail

İlk kural başarısız olduğunda alan için diğer kuralların doğrulanmasını durdurun.

Bu kural yalnızca mevcut alanı etkiler. "Genel olarak ilk başarısızlıkta dur" için Illuminate doğrulayıcısını doğrudan kullanın ve `stopOnFirstFailure()` çağırın.

<a name="rule-before"></a>
#### before:_date_

Alan verilen tarihten önce olmalıdır. Tarih geçerli bir `DateTime`'a dönüştürmek için `strtotime`'a geçirilir. [after](#rule-after) gibi karşılaştırma için başka bir alan adı geçirebilirsiniz.

Akıcı `date` kural oluşturucusunu kullanabilirsiniz:

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

`beforeToday` ve `todayOrBefore` "bugünden önce olmalı" veya "bugün veya öncesi olmalı" ifadelerini kolayca belirtir:

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

Alan verilen tarihte veya öncesinde olmalıdır. Tarih geçerli bir `DateTime`'a dönüştürmek için `strtotime`'a geçirilir. [after](#rule-after) gibi karşılaştırma için başka bir alan adı geçirebilirsiniz.

Akıcı `date` kural oluşturucusunu kullanabilirsiniz:

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

Alan boyutu _min_ ve _max_ (dahil) arasında olmalıdır. String, sayı, dizi ve dosyalar için değerlendirme [size](#rule-size) ile aynıdır.

<a name="rule-boolean"></a>
#### boolean

Alan boolean'a dönüştürülebilir olmalıdır. Kabul edilebilir girişler: `true`, `false`, `1`, `0`, `"1"`, `"0"`.

Yalnızca `true` veya `false` izin vermek için `strict` parametresini kullanın:

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

Alan eşleşen bir `{field}_confirmation` alanına sahip olmalıdır. Örneğin alan `password` olduğunda `password_confirmation` gerekir.

Özel onay alan adı da belirtebilirsiniz, örn. `confirmed:repeat_username` mevcut alanla eşleşmesi için `repeat_username` gerektirir.

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

Alan bir dizi olmalı ve verilen parametre değerlerinin tümünü içermelidir. Bu kural genellikle dizi doğrulaması için kullanılır; oluşturmak için `Rule::contains` kullanabilirsiniz:

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

Alan bir dizi olmalı ve verilen parametre değerlerinden hiçbirini içermemelidir. Oluşturmak için `Rule::doesntContain` kullanabilirsiniz:

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

Alan mevcut kimliği doğrulanmış kullanıcının şifresiyle eşleşmelidir. Auth guard'ı ilk parametre olarak belirtebilirsiniz:

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> Bu kural auth bileşenine ve guard yapılandırmasına bağlıdır; auth entegre edilmediğinde kullanmayın.

<a name="rule-date"></a>
#### date

Alan `strtotime` tarafından tanınan geçerli (göreli olmayan) bir tarih olmalıdır.

<a name="rule-date-equals"></a>
#### date_equals:_date_

Alan verilen tarihe eşit olmalıdır. Tarih geçerli bir `DateTime`'a dönüştürmek için `strtotime`'a geçirilir.

<a name="rule-date-format"></a>
#### date_format:_format_,...

Alan verilen formatlardan birine uymalıdır. `date` veya `date_format` kullanın. Bu kural tüm PHP [DateTime](https://www.php.net/manual/en/class.datetime.php) formatlarını destekler.

Akıcı `date` kural oluşturucusunu kullanabilirsiniz:

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

Alan gerekli ondalık basamaklara sahip sayısal olmalıdır:

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

Alan `"no"`, `"off"`, `0`, `"0"`, `false` veya `"false"` olmalıdır.

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

Başka bir alan belirtilen değere eşit olduğunda, alan `"no"`, `"off"`, `0`, `"0"`, `false` veya `"false"` olmalıdır.

<a name="rule-different"></a>
#### different:_field_

Alan _field_ ile farklı olmalıdır.

<a name="rule-digits"></a>
#### digits:_value_

Alan _value_ uzunluğunda bir tam sayı olmalıdır.

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

Alan _min_ ve _max_ arasında uzunlukta bir tam sayı olmalıdır.

<a name="rule-dimensions"></a>
#### dimensions

Alan bir resim olmalı ve boyut kısıtlamalarını karşılamalıdır:

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

Mevcut kısıtlamalar: _min\_width_, _max\_width_, _min\_height_, _max\_height_, _width_, _height_, _ratio_.

_ratio_ en-boy oranıdır; kesir veya float olarak ifade edilebilir:

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

Bu kuralın birçok parametresi vardır; oluşturmak için `Rule::dimensions` kullanmanız önerilir:

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

Diziler doğrulanırken alan değerleri tekrarlanmamalıdır:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

Varsayılan olarak gevşek karşılaştırma kullanır. Kesin karşılaştırma için `strict` ekleyin:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

Büyük/küçük harf farklarını yok saymak için `ignore_case` ekleyin:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

Alan belirtilen değerlerden hiçbiriyle başlamamalıdır.

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

Alan belirtilen değerlerden hiçbiriyle bitmemelidir.

<a name="rule-email"></a>
#### email

Alan geçerli bir e-posta adresi olmalıdır. Bu kural [egulias/email-validator](https://github.com/egulias/EmailValidator)'a bağlıdır, varsayılan olarak `RFCValidation` kullanır ve diğer doğrulama yöntemlerini kullanabilir:

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

Mevcut doğrulama yöntemleri:

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` - RFC spesifikasyonuna göre e-posta doğrula ([desteklenen RFC'ler](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs)).
- `strict`: `NoRFCWarningsValidation` - RFC uyarılarında başarısız (örn. sondaki nokta veya ardışık noktalar).
- `dns`: `DNSCheckValidation` - Etki alanının geçerli MX kayıtlarına sahip olup olmadığını kontrol et.
- `spoof`: `SpoofCheckValidation` - Homograf veya sahtecilik Unicode karakterlerini önle.
- `filter`: `FilterEmailValidation` - PHP `filter_var` kullanarak doğrula.
- `filter_unicode`: `FilterEmailValidation::unicode()` - Unicode'a izin veren `filter_var` doğrulaması.

</div>

Akıcı kural oluşturucusunu kullanabilirsiniz:

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
> `dns` ve `spoof` PHP `intl` uzantısını gerektirir.

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

Alan belirtilen karakter kodlamasıyla eşleşmelidir. Bu kural dosya veya string kodlamasını tespit etmek için `mb_check_encoding` kullanır. Dosya kural oluşturucusu ile kullanılabilir:

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

Alan belirtilen değerlerden biriyle bitmelidir.

<a name="rule-enum"></a>
#### enum

`Enum`, alan değerinin geçerli bir enum değeri olduğunu doğrulayan sınıf tabanlı bir kuraldır. Oluştururken enum sınıf adını geçirin. İlkel değerler için Backed Enum kullanın:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

Enum değerlerini kısıtlamak için `only`/`except` kullanın:

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

Koşullu kısıtlamalar için `when` kullanın:

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

Alan `validate`/`validated` tarafından döndürülen veriden hariç tutulacaktır.

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

_anotherfield_ _value_ ile eşit olduğunda, alan `validate`/`validated` tarafından döndürülen veriden hariç tutulacaktır.

Karmaşık koşullar için `Rule::excludeIf` kullanın:

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

_anotherfield_ _value_ ile eşit olmadıkça, alan `validate`/`validated` tarafından döndürülen veriden hariç tutulacaktır. _value_ `null` ise (örn. `exclude_unless:name,null`), alan yalnızca karşılaştırma alanı `null` veya yok olduğunda tutulur.

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

_anotherfield_ mevcut olduğunda, alan `validate`/`validated` tarafından döndürülen veriden hariç tutulacaktır.

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

_anotherfield_ mevcut olmadığında, alan `validate`/`validated` tarafından döndürülen veriden hariç tutulacaktır.

<a name="rule-exists"></a>
#### exists:_table_,_column_

Alan belirtilen veritabanı tablosunda mevcut olmalıdır.

<a name="basic-usage-of-exists-rule"></a>
#### Exists kuralının temel kullanımı

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

`column` belirtilmediğinde varsayılan olarak alan adı kullanılır. Bu örnek `state` sütununun `states` tablosunda mevcut olup olmadığını doğrular.

<a name="specifying-a-custom-column-name"></a>
#### Özel sütun adı belirtme

Tablo adından sonra sütun adını ekleyin:

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

Veritabanı bağlantısı belirtmek için tablo adından önce bağlantı adını ekleyin:

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

Model sınıf adı da geçirebilirsiniz; framework tablo adını çözer:

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

Özel sorgu koşulları için `Rule` oluşturucusunu kullanın:

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

`Rule::exists` içinde sütun adını doğrudan da belirtebilirsiniz:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'state' => [Rule::exists('states', 'abbreviation')],
])->validate();
```

Bir değer setinin mevcut olduğunu doğrulamak için `array` kuralı ile birleştirin:

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

Hem `array` hem `exists` mevcut olduğunda, tek bir sorgu tüm değerleri doğrular.

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

Yüklenen dosya uzantısının izin verilen listede olduğunu doğrular:

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> Dosya tipi doğrulaması için yalnızca uzantıya güvenmeyin; [mimes](#rule-mimes) veya [mimetypes](#rule-mimetypes) ile birlikte kullanın.

<a name="rule-file"></a>
#### file

Alan başarıyla yüklenmiş bir dosya olmalıdır.

<a name="rule-filled"></a>
#### filled

Alan mevcut olduğunda değeri boş olmamalıdır.

<a name="rule-gt"></a>
#### gt:_field_

Alan verilen _field_ veya _value_ değerinden büyük olmalıdır. Her iki alan da aynı tipte olmalıdır. String, sayı, dizi ve dosyalar için değerlendirme [size](#rule-size) ile aynıdır.

<a name="rule-gte"></a>
#### gte:_field_

Alan verilen _field_ veya _value_ değerinden büyük veya eşit olmalıdır. Her iki alan da aynı tipte olmalıdır. String, sayı, dizi ve dosyalar için değerlendirme [size](#rule-size) ile aynıdır.

<a name="rule-hex-color"></a>
#### hex_color

Alan geçerli bir [hex renk değeri](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color) olmalıdır.

<a name="rule-image"></a>
#### image

Alan bir resim olmalıdır (jpg, jpeg, png, bmp, gif veya webp).

> [!WARNING]
> XSS riski nedeniyle SVG varsayılan olarak izin verilmez. İzin vermek için `allow_svg` ekleyin: `image:allow_svg`.

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

Alan verilen değer listesinde olmalıdır. Oluşturmak için `Rule::in` kullanabilirsiniz:

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

`array` kuralı ile birleştirildiğinde, giriş dizisindeki her değer `in` listesinde olmalıdır:

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

Alan _anotherfield_ değer listesinde mevcut olmalıdır.

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

Alan bir dizi olmalı ve verilen değerlerden en az birini anahtar olarak içermelidir:

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

Alan bir tam sayı olmalıdır.

Alan tipinin tam sayı olmasını gerektirmek için `strict` parametresini kullanın; string tam sayılar geçersiz olacaktır:

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> Bu kural yalnızca PHP'nin `FILTER_VALIDATE_INT`'inden geçip geçmediğini doğrular; kesin sayısal tipler için [numeric](#rule-numeric) ile birlikte kullanın.

<a name="rule-ip"></a>
#### ip

Alan geçerli bir IP adresi olmalıdır.

<a name="rule-ipv4"></a>
#### ipv4

Alan geçerli bir IPv4 adresi olmalıdır.

<a name="rule-ipv6"></a>
#### ipv6

Alan geçerli bir IPv6 adresi olmalıdır.

<a name="rule-json"></a>
#### json

Alan geçerli bir JSON string olmalıdır.

<a name="rule-lt"></a>
#### lt:_field_

Alan verilen _field_ değerinden küçük olmalıdır. Her iki alan da aynı tipte olmalıdır. String, sayı, dizi ve dosyalar için değerlendirme [size](#rule-size) ile aynıdır.

<a name="rule-lte"></a>
#### lte:_field_

Alan verilen _field_ değerinden küçük veya eşit olmalıdır. Her iki alan da aynı tipte olmalıdır. String, sayı, dizi ve dosyalar için değerlendirme [size](#rule-size) ile aynıdır.

<a name="rule-lowercase"></a>
#### lowercase

Alan küçük harf olmalıdır.

<a name="rule-list"></a>
#### list

Alan bir liste dizisi olmalıdır. Liste dizi anahtarları 0'dan `count($array) - 1`'e kadar ardışık sayılar olmalıdır.

<a name="rule-mac"></a>
#### mac_address

Alan geçerli bir MAC adresi olmalıdır.

<a name="rule-max"></a>
#### max:_value_

Alan _value_ değerinden küçük veya eşit olmalıdır. String, sayı, dizi ve dosyalar için değerlendirme [size](#rule-size) ile aynıdır.

<a name="rule-max-digits"></a>
#### max_digits:_value_

Alan _value_ değerini aşmayan uzunlukta bir tam sayı olmalıdır.

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

Dosyanın MIME tipinin listede olduğunu doğrular:

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

MIME tipi dosya içeriği okunarak tahmin edilir ve istemci tarafından sağlanan MIME'dan farklı olabilir.

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

Dosyanın MIME tipinin verilen uzantıya karşılık geldiğini doğrular:

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

Parametreler uzantı olsa da bu kural MIME'ı belirlemek için dosya içeriğini okur. Uzantı-MIME eşlemesi:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### MIME tipleri ve uzantılar

Bu kural "dosya uzantısı"nın "gerçek MIME" ile eşleştiğini doğrulamaz. Örneğin `mimes:png` PNG içeriğine sahip `photo.txt` dosyasını geçerli sayar. Uzantıyı doğrulamak için [extensions](#rule-extensions) kullanın.

<a name="rule-min"></a>
#### min:_value_

Alan _value_ değerinden büyük veya eşit olmalıdır. String, sayı, dizi ve dosyalar için değerlendirme [size](#rule-size) ile aynıdır.

<a name="rule-min-digits"></a>
#### min_digits:_value_

Alan _value_ değerinden az olmayan uzunlukta bir tam sayı olmalıdır.

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

Alan _value_ değerinin katı olmalıdır.

<a name="rule-missing"></a>
#### missing

Alan giriş verisinde mevcut olmamalıdır.

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

_anotherfield_ herhangi bir _value_ değerine eşit olduğunda, alan mevcut olmamalıdır.

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

_anotherfield_ herhangi bir _value_ değerine eşit olmadıkça, alan mevcut olmamalıdır.

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

Belirtilen alanlardan herhangi biri mevcut olduğunda, alan mevcut olmamalıdır.

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

Belirtilen alanların tümü mevcut olduğunda, alan mevcut olmamalıdır.

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

Alan verilen değer listesinde olmamalıdır. Oluşturmak için `Rule::notIn` kullanabilirsiniz:

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

Alan verilen düzenli ifadeyle eşleşmemelidir.

Bu kural PHP `preg_match` kullanır. Regex sınırlayıcılara sahip olmalıdır, örn. `'email' => 'not_regex:/^.+$/i'`.

> [!WARNING]
> `regex`/`not_regex` kullanırken, regex `|` içeriyorsa `|` ayırıcısıyla çakışmayı önlemek için dizi formu kullanın.

<a name="rule-nullable"></a>
#### nullable

Alan `null` olabilir.

<a name="rule-numeric"></a>
#### numeric

Alan [sayısal](https://www.php.net/manual/en/function.is-numeric.php) olmalıdır.

Yalnızca tam sayı veya float tiplerine izin vermek için `strict` parametresini kullanın; sayısal stringler geçersiz olacaktır:

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

Alan giriş verisinde mevcut olmalıdır.

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

_anotherfield_ herhangi bir _value_ değerine eşit olduğunda, alan mevcut olmalıdır.

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

_anotherfield_ herhangi bir _value_ değerine eşit olmadıkça, alan mevcut olmalıdır.

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

Belirtilen alanlardan herhangi biri mevcut olduğunda, alan mevcut olmalıdır.

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

Belirtilen alanların tümü mevcut olduğunda, alan mevcut olmalıdır.

<a name="rule-prohibited"></a>
#### prohibited

Alan eksik veya boş olmalıdır. "Boş" şu anlama gelir:

<div class="content-list" markdown="1">

- Değer `null`.
- Değer boş string.
- Değer boş dizi veya boş `Countable` nesnesi.
- Boş yola sahip yüklenmiş dosya.

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

_anotherfield_ herhangi bir _value_ değerine eşit olduğunda, alan eksik veya boş olmalıdır. "Boş" şu anlama gelir:

<div class="content-list" markdown="1">

- Değer `null`.
- Değer boş string.
- Değer boş dizi veya boş `Countable` nesnesi.
- Boş yola sahip yüklenmiş dosya.

</div>

Karmaşık koşullar için `Rule::prohibitedIf` kullanın:

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

_anotherfield_ `"yes"`, `"on"`, `1`, `"1"`, `true` veya `"true"` olduğunda, alan eksik veya boş olmalıdır.

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

_anotherfield_ `"no"`, `"off"`, `0`, `"0"`, `false` veya `"false"` olduğunda, alan eksik veya boş olmalıdır.

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

_anotherfield_ herhangi bir _value_ değerine eşit olmadıkça, alan eksik veya boş olmalıdır. "Boş" şu anlama gelir:

<div class="content-list" markdown="1">

- Değer `null`.
- Değer boş string.
- Değer boş dizi veya boş `Countable` nesnesi.
- Boş yola sahip yüklenmiş dosya.

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

Alan mevcut ve boş değilse, _anotherfield_ içindeki tüm alanlar eksik veya boş olmalıdır. "Boş" şu anlama gelir:

<div class="content-list" markdown="1">

- Değer `null`.
- Değer boş string.
- Değer boş dizi veya boş `Countable` nesnesi.
- Boş yola sahip yüklenmiş dosya.

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

Alan verilen düzenli ifadeyle eşleşmelidir.

Bu kural PHP `preg_match` kullanır. Regex sınırlayıcılara sahip olmalıdır, örn. `'email' => 'regex:/^.+@.+$/i'`.

> [!WARNING]
> `regex`/`not_regex` kullanırken, regex `|` içeriyorsa `|` ayırıcısıyla çakışmayı önlemek için dizi formu kullanın.

<a name="rule-required"></a>
#### required

Alan mevcut olmalı ve boş olmamalıdır. "Boş" şu anlama gelir:

<div class="content-list" markdown="1">

- Değer `null`.
- Değer boş string.
- Değer boş dizi veya boş `Countable` nesnesi.
- Boş yola sahip yüklenmiş dosya.

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

_anotherfield_ herhangi bir _value_ değerine eşit olduğunda, alan mevcut olmalı ve boş olmamalıdır.

Karmaşık koşullar için `Rule::requiredIf` kullanın:

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

_anotherfield_ `"yes"`, `"on"`, `1`, `"1"`, `true` veya `"true"` olduğunda, alan mevcut olmalı ve boş olmamalıdır.

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

_anotherfield_ `"no"`, `"off"`, `0`, `"0"`, `false` veya `"false"` olduğunda, alan mevcut olmalı ve boş olmamalıdır.

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

_anotherfield_ herhangi bir _value_ değerine eşit olmadıkça, alan mevcut olmalı ve boş olmamalıdır. _value_ `null` ise (örn. `required_unless:name,null`), alan yalnızca karşılaştırma alanı `null` veya yok olduğunda boş olabilir.

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

Belirtilen alanlardan herhangi biri mevcut ve boş değilse, alan mevcut olmalı ve boş olmamalıdır.

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

Belirtilen alanların tümü mevcut ve boş değilse, alan mevcut olmalı ve boş olmamalıdır.

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

Belirtilen alanlardan herhangi biri boş veya yoksa, alan mevcut olmalı ve boş olmamalıdır.

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

Belirtilen alanların tümü boş veya yoksa, alan mevcut olmalı ve boş olmamalıdır.

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

Alan bir dizi olmalı ve en az belirtilen anahtarları içermelidir.

<a name="validating-when-present"></a>
#### sometimes

Yalnızca alan mevcut olduğunda sonraki doğrulama kurallarını uygulayın. Genellikle "isteğe bağlı ama mevcut olduğunda geçerli olmalı" alanları için kullanılır:

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

Alan _field_ ile aynı olmalıdır.

<a name="rule-size"></a>
#### size:_value_

Alan boyutu verilen _value_ değerine eşit olmalıdır. Stringler için: karakter sayısı; sayılar için: belirtilen tam sayı (`numeric` veya `integer` ile kullanın); diziler için: öğe sayısı; dosyalar için: KB cinsinden boyut. Örnek:

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

Alan belirtilen değerlerden biriyle başlamalıdır.

<a name="rule-string"></a>
#### string

Alan bir string olmalıdır. `null` izin vermek için `nullable` ile birlikte kullanın.

<a name="rule-timezone"></a>
#### timezone

Alan geçerli bir saat dilimi tanımlayıcısı olmalıdır (`DateTimeZone::listIdentifiers`'dan). Bu metodu destekleyen parametreleri geçirebilirsiniz:

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

Alan belirtilen tabloda benzersiz olmalıdır.

**Özel tablo/sütun adı belirtin:**

Model sınıf adını doğrudan belirtebilirsiniz:

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

Sütun adını belirtebilirsiniz (belirtilmediğinde varsayılan olarak alan adı kullanılır):

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**Veritabanı bağlantısı belirtin:**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**Belirtilen ID'yi yoksay:**

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
> `ignore` kullanıcı girişi almamalıdır; yalnızca sistem tarafından oluşturulan benzersiz ID'ler (otomatik artan ID veya model UUID) kullanın, aksi halde SQL enjeksiyon riski olabilir.

Model örneği de geçirebilirsiniz:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

Birincil anahtar `id` değilse, birincil anahtar adını belirtin:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

Varsayılan olarak benzersiz sütun olarak alan adını kullanır; sütun adını da belirtebilirsiniz:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**Ek koşullar ekleyin:**

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

**Yumuşak silinen kayıtları yoksay:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

Yumuşak silme sütunu `deleted_at` değilse:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

Alan büyük harf olmalıdır.

<a name="rule-url"></a>
#### url

Alan geçerli bir URL olmalıdır.

İzin verilen protokolleri belirtebilirsiniz:

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

Alan geçerli bir [ULID](https://github.com/ulid/spec) olmalıdır.

<a name="rule-uuid"></a>
#### uuid

Alan geçerli bir RFC 9562 UUID (sürüm 1, 3, 4, 5, 6, 7 veya 8) olmalıdır.

Sürümü belirtebilirsiniz:

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```



<a name="think-validate"></a>
# Doğrulayıcı top-think/think-validate

## Açıklama

Resmi ThinkPHP doğrulayıcısı

## Proje URL'si

https://github.com/top-think/think-validate

## Kurulum

`composer require topthink/think-validate`

## Hızlı Başlangıç

**`app/index/validate/User.php` oluşturun**

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
  
**Kullanım**

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

> **Not**
> webman, think-validate'in `Validate::rule()` metodunu desteklemez

<a name="respect-validation"></a>
# Doğrulayıcı workerman/validation

## Açıklama

Bu proje https://github.com/Respect/Validation adresinin yerelleştirilmiş sürümüdür

## Proje URL'si

https://github.com/walkor/validation
  
  
## Kurulum
 
```php
composer require workerman/validation
```

## Hızlı Başlangıç

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
  
**jQuery ile erişim**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
Sonuç:

`{"code":500,"msg":"Username may only contain letters (a-z) and numbers (0-9)"}`

Açıklama:

`v::input(array $input, array $rules)` veriyi doğrular ve toplar. Doğrulama başarısız olursa `Respect\Validation\Exceptions\ValidationException` fırlatır; başarılı olursa doğrulanmış veriyi (dizi) döndürür.

İş mantığı doğrulama istisnasını yakalamazsa, webman framework yakalar ve HTTP başlıklarına göre JSON (örn. `{"code":500, "msg":"xxx"}`) veya normal istisna sayfası döndürür. Yanıt formatı ihtiyaçlarınızı karşılamıyorsa, aşağıdaki örnekteki gibi `ValidationException`'ı yakalayıp özel veri döndürebilirsiniz:

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

## Doğrulayıcı Kılavuzu

```php
use Respect\Validation\Validator as v;

// Tek kural doğrulama
$number = 123;
v::numericVal()->validate($number); // true

// Zincirleme doğrulama
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// İlk doğrulama başarısızlık nedenini al
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username may only contain letters (a-z) and numbers (0-9)
}

// Tüm doğrulama başarısızlık nedenlerini al
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Yazdıracak
    // -  Username must satisfy the following rules
    //     - Username may only contain letters (a-z) and numbers (0-9)
    //     - Username must not contain whitespace
  
    var_export($exception->getMessages());
    // Yazdıracak
    // array (
    //   'alnum' => 'Username may only contain letters (a-z) and numbers (0-9)',
    //   'noWhitespace' => 'Username must not contain whitespace',
    // )
}

// Özel hata mesajları
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Username may only contain letters and numbers',
        'noWhitespace' => 'Username must not contain spaces',
        'length' => 'length satisfies the rule, so this will not be shown'
    ]));
    // Yazdıracak 
    // array(
    //    'alnum' => 'Username may only contain letters and numbers',
    //    'noWhitespace' => 'Username must not contain spaces'
    // )
}

// Nesne doğrulama
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Dizi doğrulama
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
    ->assert($data); // check() veya validate() da kullanılabilir
  
// İsteğe bağlı doğrulama
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Olumsuzlama kuralı
v::not(v::intVal())->validate(10); // false
```
  
## Doğrulayıcı metodları `validate()` `check()` `assert()` arasındaki fark

`validate()` boolean döndürür, istisna fırlatmaz

`check()` doğrulama başarısız olduğunda istisna fırlatır; ilk başarısızlık nedenini `$exception->getMessage()` ile alın

`assert()` doğrulama başarısız olduğunda istisna fırlatır; tüm başarısızlık nedenlerini `$exception->getFullMessage()` ile alın
  
  
## Yaygın Doğrulama Kuralları

`Alnum()` Yalnızca harfler ve sayılar

`Alpha()` Yalnızca harfler

`ArrayType()` Dizi tipi

`Between(mixed $minimum, mixed $maximum)` Girişin iki değer arasında olduğunu doğrular.

`BoolType()` Boolean tipi doğrular

`Contains(mixed $expectedValue)` Girişin belirli değeri içerdiğini doğrular

`ContainsAny(array $needles)` Girişin en az bir tanımlı değer içerdiğini doğrular

`Digit()` Girişin yalnızca rakam içerdiğini doğrular

`Domain()` Geçerli etki alanı adı doğrular

`Email()` Geçerli e-posta adresi doğrular

`Extension(string $extension)` Dosya uzantısı doğrular

`FloatType()` Float tipi doğrular

`IntType()` Tam sayı tipi doğrular

`Ip()` IP adresi doğrular

`Json()` JSON verisi doğrular

`Length(int $min, int $max)` Uzunluğun aralıkta olduğunu doğrular

`LessThan(mixed $compareTo)` Uzunluğun verilen değerden küçük olduğunu doğrular

`Lowercase()` Küçük harf doğrular

`MacAddress()` MAC adresi doğrular

`NotEmpty()` Boş olmadığını doğrular

`NullType()` Null doğrular

`Number()` Sayı doğrular

`ObjectType()` Nesne tipi doğrular

`StringType()` String tipi doğrular

`Url()` URL doğrular
  
Daha fazla doğrulama kuralı için https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ adresine bakın.
  
## Daha Fazla

https://respect-validation.readthedocs.io/en/2.0/ adresini ziyaret edin.
  
