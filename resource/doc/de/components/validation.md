# Weitere Validatoren

Es gibt viele Validatoren, die direkt über Composer verfügbar sind und verwendet werden können, zum Beispiel:

#### <a href="#webman-validation"> webman/validation (Empfohlen)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# Validator webman/validation

Basiert auf `illuminate/validation`, bietet manuelle Validierung, Annotations-Validierung, parameterbezogene Validierung und wiederverwendbare Regelsätze.

## Installation

```bash
composer require webman/validation
```

## Grundkonzepte

- **Regelsatz-Wiederverwendung**: Definieren Sie wiederverwendbare `rules`, `messages`, `attributes` und `scenes` durch Erweiterung von `support\validation\Validator`, die sowohl bei manueller als auch bei Annotations-Validierung wiederverwendet werden können.
- **Methodenbezogene Annotations-Validierung (Attribute)**: Verwenden Sie das PHP-8-Attribut `#[Validate]`, um die Validierung an Controller-Methoden zu binden.
- **Parameterbezogene Annotations-Validierung (Attribute)**: Verwenden Sie das PHP-8-Attribut `#[Param]`, um die Validierung an Controller-Methodenparameter zu binden.
- **Ausnahmebehandlung**: Wirft bei Validierungsfehlern `support\validation\ValidationException`; die Ausnahmeklasse ist konfigurierbar.
- **Datenbankvalidierung**: Bei Datenbankvalidierung muss `composer require webman/database` installiert werden.

## Manuelle Validierung

### Grundlegende Verwendung

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **Hinweis**
> `validate()` wirft bei Validierungsfehlern `support\validation\ValidationException`. Wenn Sie keine Ausnahmen werfen möchten, verwenden Sie die unten beschriebene `fails()`-Methode, um Fehlermeldungen zu erhalten.

### Benutzerdefinierte Meldungen und Attribute

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

### Validierung ohne Ausnahme (Fehlermeldungen abrufen)

Wenn Sie keine Ausnahmen werfen möchten, verwenden Sie `fails()` zur Prüfung und rufen Sie Fehlermeldungen über `errors()` ab (gibt `MessageBag` zurück):

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
    // Fehler behandeln...
}
```

## Regelsatz-Wiederverwendung (Benutzerdefinierter Validator)

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

### Wiederverwendung bei manueller Validierung

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### Szenen verwenden (Optional)

`scenes` ist eine optionale Funktion; sie validiert nur eine Teilmenge der Felder, wenn Sie `withScene(...)` aufrufen.

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

// Keine Szene angegeben -> alle Regeln validieren
UserValidator::make($data)->validate();

// Szene angegeben -> nur Felder dieser Szene validieren
UserValidator::make($data)->withScene('create')->validate();
```

## Annotations-Validierung (Methodenebene)

### Direkte Regeln

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

### Regelsätze wiederverwenden

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

### Mehrfache Validierungsüberlagerungen

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

### Validierungsdatenquelle

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

Mit dem Parameter `in` wird die Datenquelle angegeben:

* **query** HTTP-Abfrageparameter, aus `$request->get()`
* **body** HTTP-Anfragekörper, aus `$request->post()`
* **path** HTTP-Pfadparameter, aus `$request->route->param()`

`in` kann ein String oder Array sein; bei einem Array werden die Werte der Reihe nach zusammengeführt, wobei spätere Werte frühere überschreiben. Wenn `in` nicht übergeben wird, ist der Standardwert `['query', 'body', 'path']`.


## Parameterbezogene Validierung (Param)

### Grundlegende Verwendung

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

### Validierungsdatenquelle

Auch die parameterbezogene Validierung unterstützt den Parameter `in` zur Angabe der Quelle:

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


### rules unterstützt String oder Array

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

### Benutzerdefinierte Meldungen / Attribut

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

### Regel-Konstanten-Wiederverwendung

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

## Methodenebene + Parameterebene kombiniert

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

## Automatische Regelableitung (basierend auf Parametersignatur)

Wenn `#[Validate]` für eine Methode verwendet wird oder ein beliebiger Parameter dieser Methode `#[Param]` nutzt, leitet diese Komponente **automatisch grundlegende Validierungsregeln aus der Methodenparametersignatur ab und ergänzt sie**, bevor sie mit bestehenden Regeln zusammengeführt und validiert werden.

### Beispiel: Äquivalente Erweiterung von `#[Validate]`

1) Nur `#[Validate]` aktivieren ohne manuelle Regeln:

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

Entspricht:

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

2) Nur teilweise Regeln geschrieben, Rest durch Parametersignatur ergänzt:

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

Entspricht:

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

3) Standardwert / nullable-Typ:

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

Entspricht:

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

## Ausnahmebehandlung

### Standardausnahme

Bei Validierungsfehlern wird standardmäßig `support\validation\ValidationException` geworfen, die von `Webman\Exception\BusinessException` erbt und keine Fehler protokolliert.

Das Standard-Antwortverhalten wird von `BusinessException::render()` behandelt:

- Normale Anfragen: gibt String-Nachricht zurück, z. B. `token is required.`
- JSON-Anfragen: gibt JSON-Antwort zurück, z. B. `{"code": 422, "msg": "token is required.", "data":....}`

### Anpassung über benutzerdefinierte Ausnahme

- Globale Konfiguration: `exception` in `config/plugin/webman/validation/app.php`

## Mehrsprachige Unterstützung

Die Komponente enthält eingebaute chinesische und englische Sprachpakete und unterstützt Projektüberschreibungen. Lade-Reihenfolge:

1. Projekt-Sprachpaket `resource/translations/{locale}/validation.php`
2. Komponenten-eingebaut `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Illuminate eingebaut Englisch (Fallback)

> **Hinweis**
> Die Webman-Standardsprache wird in `config/translation.php` konfiguriert oder kann über `locale('en');` geändert werden.

### Beispiel für lokale Überschreibung

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute is not a valid email format.',
];
```

## Middleware-Autoload

Nach der Installation lädt die Komponente die Validierungs-Middleware automatisch über `config/plugin/webman/validation/middleware.php`; keine manuelle Registrierung erforderlich.

## Befehlszeilen-Generierung

Verwenden Sie den Befehl `make:validator`, um Validator-Klassen zu erzeugen (Standardausgabe in das Verzeichnis `app/validation`).

> **Hinweis**
> Erfordert `composer require webman/console`

### Grundlegende Verwendung

- **Leeres Template erzeugen**

```bash
php webman make:validator UserValidator
```

- **Vorhandene Datei überschreiben**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### Regeln aus Tabellenstruktur erzeugen

- **Tabellennamen angeben, um Basisregeln zu erzeugen** (leitet `$rules` aus Feldtyp/nullable/Länge etc. ab; schließt ORM-bezogene Felder standardmäßig aus: Laravel verwendet `created_at/updated_at/deleted_at`, ThinkORM verwendet `create_time/update_time/delete_time`)

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **Datenbankverbindung angeben** (Mehrfachverbindungs-Szenarien)

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### Szenen

- **CRUD-Szenen erzeugen**: `create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> Die Szene `update` enthält das Primärschlüsselfeld (zur Datensatzsuche) plus weitere Felder; `delete/detail` enthalten standardmäßig nur den Primärschlüssel.

### ORM-Auswahl (laravel (illuminate/database) vs think-orm)

- **Automatische Auswahl (Standard)**: Verwendet das installierte/konfigurierte ORM; bei beiden vorhanden wird standardmäßig Illuminate verwendet
- **Erzwingen**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### Vollständiges Beispiel

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## Unit-Tests

Im Stammverzeichnis von `webman/validation` ausführen:

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## Validierungsregeln-Referenz

<a name="available-validation-rules"></a>
## Verfügbare Validierungsregeln

> [!IMPORTANT]
> - Webman Validation basiert auf `illuminate/validation`; Regelnamen entsprechen Laravel und es gibt keine Webman-spezifischen Regeln.
> - Die Middleware validiert standardmäßig Daten aus `$request->all()` (GET+POST) zusammengeführt mit Routenparametern, ohne hochgeladene Dateien; für Dateiregeln müssen Sie `$request->file()` manuell in die Daten mergen oder `Validator::make` aufrufen.
> - `current_password` hängt vom Auth-Guard ab; `exists`/`unique` hängen von Datenbankverbindung und Query Builder ab; diese Regeln sind nicht verfügbar, wenn die entsprechenden Komponenten nicht integriert sind.

Die folgende Liste enthält alle verfügbaren Validierungsregeln und deren Zweck:

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

Das Feld muss `"yes"`, `"on"`, `1`, `"1"`, `true` oder `"true"` sein. Wird häufig für Szenarien wie die Bestätigung der Nutzungsbedingungen verwendet.

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

Wenn ein anderes Feld dem angegebenen Wert entspricht, muss das Feld `"yes"`, `"on"`, `1`, `"1"`, `true` oder `"true"` sein. Wird häufig für bedingte Zustimmungsszenarien verwendet.

<a name="rule-active-url"></a>
#### active_url

Das Feld muss einen gültigen A- oder AAAA-Datensatz haben. Diese Regel verwendet zuerst `parse_url`, um den URL-Hostnamen zu extrahieren, und validiert dann mit `dns_get_record`.

<a name="rule-after"></a>
#### after:_date_

Das Feld muss einen Wert nach dem angegebenen Datum haben. Das Datum wird an `strtotime` übergeben, um es in ein gültiges `DateTime` zu konvertieren:

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

Sie können auch einen anderen Feldnamen zum Vergleich übergeben:

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

Sie können den fließenden `date`-Regel-Builder verwenden:

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

`afterToday` und `todayOrAfter` drücken bequem „muss nach heute sein“ bzw. „muss heute oder später sein“ aus:

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

Das Feld muss am oder nach dem angegebenen Datum liegen. Siehe [after](#rule-after) für weitere Details.

Sie können den fließenden `date`-Regel-Builder verwenden:

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

`Rule::anyOf` erlaubt die Angabe „mindestens eine Regelsatz-Bedingung erfüllen“. Die folgende Regel bedeutet z. B., dass `username` entweder eine E-Mail-Adresse oder eine alphanumerische/Unterstrich/Bindestrich-Zeichenkette von mindestens 6 Zeichen sein muss:

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

Das Feld muss Unicode-Buchstaben enthalten ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) und [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)).

Um nur ASCII (`a-z`, `A-Z`) zu erlauben, fügen Sie die Option `ascii` hinzu:

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

Das Feld darf nur Unicode-Buchstaben und -Zahlen enthalten ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)), plus ASCII-Bindestrich (`-`) und Unterstrich (`_`).

Um nur ASCII (`a-z`, `A-Z`, `0-9`) zu erlauben, fügen Sie die Option `ascii` hinzu:

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

Das Feld darf nur Unicode-Buchstaben und -Zahlen enthalten ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)).

Um nur ASCII (`a-z`, `A-Z`, `0-9`) zu erlauben, fügen Sie die Option `ascii` hinzu:

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

Das Feld muss ein PHP-`array` sein.

Wenn die `array`-Regel zusätzliche Parameter hat, müssen die Eingabe-Array-Schlüssel in der Parameterliste stehen. Im Beispiel ist der Schlüssel `admin` nicht in der erlaubten Liste, daher ist er ungültig:

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

Es wird empfohlen, in realen Projekten erlaubte Array-Schlüssel explizit zu definieren.

<a name="rule-ascii"></a>
#### ascii

Das Feld darf nur 7-Bit-ASCII-Zeichen enthalten.

<a name="rule-bail"></a>
#### bail

Die weitere Validierung von Regeln für das Feld stoppen, wenn die erste Regel fehlschlägt.

Diese Regel betrifft nur das aktuelle Feld. Für „bei erstem Fehler global stoppen“ verwenden Sie den Illuminate-Validator direkt und rufen Sie `stopOnFirstFailure()` auf.

<a name="rule-before"></a>
#### before:_date_

Das Feld muss vor dem angegebenen Datum liegen. Das Datum wird an `strtotime` übergeben, um es in ein gültiges `DateTime` zu konvertieren. Wie bei [after](#rule-after) können Sie einen anderen Feldnamen zum Vergleich übergeben.

Sie können den fließenden `date`-Regel-Builder verwenden:

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

`beforeToday` und `todayOrBefore` drücken bequem „muss vor heute sein“ bzw. „muss heute oder früher sein“ aus:

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

Das Feld muss am oder vor dem angegebenen Datum liegen. Das Datum wird an `strtotime` übergeben, um es in ein gültiges `DateTime` zu konvertieren. Wie bei [after](#rule-after) können Sie einen anderen Feldnamen zum Vergleich übergeben.

Sie können den fließenden `date`-Regel-Builder verwenden:

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

Die Feldgröße muss zwischen _min_ und _max_ (inklusive) liegen. Die Auswertung für Strings, Zahlen, Arrays und Dateien entspricht [size](#rule-size).

<a name="rule-boolean"></a>
#### boolean

Das Feld muss in einen Boolean konvertierbar sein. Akzeptable Eingaben sind `true`, `false`, `1`, `0`, `"1"`, `"0"`.

Verwenden Sie den Parameter `strict`, um nur `true` oder `false` zu erlauben:

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

Das Feld muss ein übereinstimmendes `{field}_confirmation`-Feld haben. Wenn das Feld z. B. `password` ist, ist `password_confirmation` erforderlich.

Sie können auch einen benutzerdefinierten Bestätigungsfeldnamen angeben, z. B. erfordert `confirmed:repeat_username`, dass `repeat_username` dem aktuellen Feld entspricht.

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

Das Feld muss ein Array sein und alle angegebenen Parameterwerte enthalten. Diese Regel wird häufig für Array-Validierung verwendet; Sie können `Rule::contains` zur Konstruktion verwenden:

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

Das Feld muss ein Array sein und darf keinen der angegebenen Parameterwerte enthalten. Sie können `Rule::doesntContain` zur Konstruktion verwenden:

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

Das Feld muss dem Passwort des aktuell authentifizierten Benutzers entsprechen. Sie können den Auth-Guard als ersten Parameter angeben:

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> Diese Regel hängt von der Auth-Komponente und Guard-Konfiguration ab; nicht verwenden, wenn Auth nicht integriert ist.

<a name="rule-date"></a>
#### date

Das Feld muss ein gültiges (nicht-relatives) Datum sein, das von `strtotime` erkannt wird.

<a name="rule-date-equals"></a>
#### date_equals:_date_

Das Feld muss dem angegebenen Datum entsprechen. Das Datum wird an `strtotime` übergeben, um es in ein gültiges `DateTime` zu konvertieren.

<a name="rule-date-format"></a>
#### date_format:_format_,...

Das Feld muss einem der angegebenen Formate entsprechen. Verwenden Sie entweder `date` oder `date_format`. Diese Regel unterstützt alle PHP-[DateTime](https://www.php.net/manual/en/class.datetime.php)-Formate.

Sie können den fließenden `date`-Regel-Builder verwenden:

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

Das Feld muss numerisch sein mit der erforderlichen Anzahl Dezimalstellen:

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

Das Feld muss `"no"`, `"off"`, `0`, `"0"`, `false` oder `"false"` sein.

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

Wenn ein anderes Feld dem angegebenen Wert entspricht, muss das Feld `"no"`, `"off"`, `0`, `"0"`, `false` oder `"false"` sein.

<a name="rule-different"></a>
#### different:_field_

Das Feld muss sich von _field_ unterscheiden.

<a name="rule-digits"></a>
#### digits:_value_

Das Feld muss eine ganze Zahl mit der Länge _value_ sein.

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

Das Feld muss eine ganze Zahl mit einer Länge zwischen _min_ und _max_ sein.

<a name="rule-dimensions"></a>
#### dimensions

Das Feld muss ein Bild sein und Dimensionsbeschränkungen erfüllen:

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

Verfügbare Beschränkungen: _min\_width_, _max\_width_, _min\_height_, _max\_height_, _width_, _height_, _ratio_.

_ratio_ ist das Seitenverhältnis; es kann als Bruch oder Float angegeben werden:

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

Diese Regel hat viele Parameter; es wird empfohlen, `Rule::dimensions` zur Konstruktion zu verwenden:

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

Bei der Validierung von Arrays dürfen Feldwerte nicht doppelt vorkommen:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

Verwendet standardmäßig lockeren Vergleich. Fügen Sie `strict` für strikten Vergleich hinzu:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

Fügen Sie `ignore_case` hinzu, um Groß-/Kleinschreibung zu ignorieren:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

Das Feld darf nicht mit einem der angegebenen Werte beginnen.

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

Das Feld darf nicht mit einem der angegebenen Werte enden.

<a name="rule-email"></a>
#### email

Das Feld muss eine gültige E-Mail-Adresse sein. Diese Regel hängt von [egulias/email-validator](https://github.com/egulias/EmailValidator) ab, verwendet standardmäßig `RFCValidation` und kann andere Validierungsmethoden verwenden:

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

Verfügbare Validierungsmethoden:

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` - E-Mail gemäß RFC-Spezifikation validieren ([unterstützte RFCs](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs)).
- `strict`: `NoRFCWarningsValidation` - Bei RFC-Warnungen fehlschlagen (z. B. abschließender Punkt oder aufeinanderfolgende Punkte).
- `dns`: `DNSCheckValidation` - Prüfen, ob die Domain gültige MX-Datensätze hat.
- `spoof`: `SpoofCheckValidation` - Homograph- oder Spoofing-Unicode-Zeichen verhindern.
- `filter`: `FilterEmailValidation` - Validierung mit PHP `filter_var`.
- `filter_unicode`: `FilterEmailValidation::unicode()` - `filter_var`-Validierung mit Unicode-Unterstützung.

</div>

Sie können den fließenden Regel-Builder verwenden:

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
> `dns` und `spoof` erfordern die PHP-Erweiterung `intl`.

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

Das Feld muss der angegebenen Zeichenkodierung entsprechen. Diese Regel verwendet `mb_check_encoding`, um die Datei- oder String-Kodierung zu erkennen. Kann mit dem File-Regel-Builder verwendet werden:

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

Das Feld muss mit einem der angegebenen Werte enden.

<a name="rule-enum"></a>
#### enum

`Enum` ist eine klassenbasierte Regel zur Validierung, dass der Feldwert ein gültiger Enum-Wert ist. Übergeben Sie den Enum-Klassennamen bei der Konstruktion. Für primitive Werte verwenden Sie Backed Enum:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

Verwenden Sie `only`/`except`, um Enum-Werte einzuschränken:

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

Verwenden Sie `when` für bedingte Einschränkungen:

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

Das Feld wird aus den von `validate`/`validated` zurückgegebenen Daten ausgeschlossen.

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

Wenn _anotherfield_ _value_ entspricht, wird das Feld aus den von `validate`/`validated` zurückgegebenen Daten ausgeschlossen.

Für komplexe Bedingungen verwenden Sie `Rule::excludeIf`:

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

Außer wenn _anotherfield_ _value_ entspricht, wird das Feld aus den von `validate`/`validated` zurückgegebenen Daten ausgeschlossen. Wenn _value_ `null` ist (z. B. `exclude_unless:name,null`), wird das Feld nur behalten, wenn das Vergleichsfeld `null` oder nicht vorhanden ist.

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

Wenn _anotherfield_ existiert, wird das Feld aus den von `validate`/`validated` zurückgegebenen Daten ausgeschlossen.

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

Wenn _anotherfield_ nicht existiert, wird das Feld aus den von `validate`/`validated` zurückgegebenen Daten ausgeschlossen.

<a name="rule-exists"></a>
#### exists:_table_,_column_

Das Feld muss in der angegebenen Datenbanktabelle existieren.

<a name="basic-usage-of-exists-rule"></a>
#### Grundlegende Verwendung der Exists-Regel

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

Wenn `column` nicht angegeben ist, wird standardmäßig der Feldname verwendet. Dieses Beispiel validiert also, ob die Spalte `state` in der Tabelle `states` existiert.

<a name="specifying-a-custom-column-name"></a>
#### Benutzerdefinierten Spaltennamen angeben

Fügen Sie den Spaltennamen nach dem Tabellennamen an:

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

Um eine Datenbankverbindung anzugeben, stellen Sie dem Tabellennamen den Verbindungsnamen voran:

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

Sie können auch einen Modellklassennamen übergeben; das Framework löst den Tabellennamen auf:

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

Für benutzerdefinierte Abfragebedingungen verwenden Sie den `Rule`-Builder:

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

Sie können den Spaltennamen auch direkt in `Rule::exists` angeben:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'state' => [Rule::exists('states', 'abbreviation')],
])->validate();
```

Um zu validieren, dass eine Menge von Werten existiert, kombinieren Sie mit der `array`-Regel:

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

Wenn sowohl `array` als auch `exists` vorhanden sind, validiert eine einzelne Abfrage alle Werte.

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

Validiert, dass die Dateierweiterung der hochgeladenen Datei in der erlaubten Liste steht:

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> Verlassen Sie sich nicht allein auf die Erweiterung für die Dateityp-Validierung; verwenden Sie sie zusammen mit [mimes](#rule-mimes) oder [mimetypes](#rule-mimetypes).

<a name="rule-file"></a>
#### file

Das Feld muss eine erfolgreich hochgeladene Datei sein.

<a name="rule-filled"></a>
#### filled

Wenn das Feld existiert, darf sein Wert nicht leer sein.

<a name="rule-gt"></a>
#### gt:_field_

Das Feld muss größer als das angegebene _field_ oder _value_ sein. Beide Felder müssen denselben Typ haben. Die Auswertung für Strings, Zahlen, Arrays und Dateien entspricht [size](#rule-size).

<a name="rule-gte"></a>
#### gte:_field_

Das Feld muss größer oder gleich dem angegebenen _field_ oder _value_ sein. Beide Felder müssen denselben Typ haben. Die Auswertung für Strings, Zahlen, Arrays und Dateien entspricht [size](#rule-size).

<a name="rule-hex-color"></a>
#### hex_color

Das Feld muss ein gültiger [Hex-Farbwert](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color) sein.

<a name="rule-image"></a>
#### image

Das Feld muss ein Bild sein (jpg, jpeg, png, bmp, gif oder webp).

> [!WARNING]
> SVG ist standardmäßig wegen XSS-Risiko nicht erlaubt. Zum Erlauben fügen Sie `allow_svg` hinzu: `image:allow_svg`.

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

Das Feld muss in der angegebenen Werteliste stehen. Sie können `Rule::in` zur Konstruktion verwenden:

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

In Kombination mit der `array`-Regel muss jeder Wert im Eingabe-Array in der `in`-Liste stehen:

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

Das Feld muss in der Werteliste von _anotherfield_ existieren.

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

Das Feld muss ein Array sein und mindestens einen der angegebenen Werte als Schlüssel enthalten:

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

Das Feld muss eine ganze Zahl sein.

Verwenden Sie den Parameter `strict`, um zu verlangen, dass der Feldtyp integer ist; String-Ganzzahlen sind dann ungültig:

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> Diese Regel validiert nur, ob sie PHPs `FILTER_VALIDATE_INT` besteht; für strikte numerische Typen verwenden Sie sie zusammen mit [numeric](#rule-numeric).

<a name="rule-ip"></a>
#### ip

Das Feld muss eine gültige IP-Adresse sein.

<a name="rule-ipv4"></a>
#### ipv4

Das Feld muss eine gültige IPv4-Adresse sein.

<a name="rule-ipv6"></a>
#### ipv6

Das Feld muss eine gültige IPv6-Adresse sein.

<a name="rule-json"></a>
#### json

Das Feld muss eine gültige JSON-Zeichenkette sein.

<a name="rule-lt"></a>
#### lt:_field_

Das Feld muss kleiner als das angegebene _field_ sein. Beide Felder müssen denselben Typ haben. Die Auswertung für Strings, Zahlen, Arrays und Dateien entspricht [size](#rule-size).

<a name="rule-lte"></a>
#### lte:_field_

Das Feld muss kleiner oder gleich dem angegebenen _field_ sein. Beide Felder müssen denselben Typ haben. Die Auswertung für Strings, Zahlen, Arrays und Dateien entspricht [size](#rule-size).

<a name="rule-lowercase"></a>
#### lowercase

Das Feld muss in Kleinbuchstaben sein.

<a name="rule-list"></a>
#### list

Das Feld muss ein Listen-Array sein. Listen-Array-Schlüssel müssen aufeinanderfolgende Zahlen von 0 bis `count($array) - 1` sein.

<a name="rule-mac"></a>
#### mac_address

Das Feld muss eine gültige MAC-Adresse sein.

<a name="rule-max"></a>
#### max:_value_

Das Feld muss kleiner oder gleich _value_ sein. Die Auswertung für Strings, Zahlen, Arrays und Dateien entspricht [size](#rule-size).

<a name="rule-max-digits"></a>
#### max_digits:_value_

Das Feld muss eine ganze Zahl mit einer Länge von höchstens _value_ sein.

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

Validiert, dass der MIME-Typ der Datei in der Liste steht:

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

Der MIME-Typ wird durch Lesen des Dateiinhalts ermittelt und kann vom clientseitig bereitgestellten MIME abweichen.

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

Validiert, dass der MIME-Typ der Datei der angegebenen Erweiterung entspricht:

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

Obwohl die Parameter Erweiterungen sind, liest diese Regel den Dateiinhalt, um den MIME-Typ zu ermitteln. Erweiterungs-zu-MIME-Zuordnung:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### MIME-Typen und Erweiterungen

Diese Regel validiert nicht, dass „Dateierweiterung“ mit „tatsächlichem MIME“ übereinstimmt. Zum Beispiel behandelt `mimes:png` `photo.txt` mit PNG-Inhalt als gültig. Zur Validierung der Erweiterung verwenden Sie [extensions](#rule-extensions).

<a name="rule-min"></a>
#### min:_value_

Das Feld muss größer oder gleich _value_ sein. Die Auswertung für Strings, Zahlen, Arrays und Dateien entspricht [size](#rule-size).

<a name="rule-min-digits"></a>
#### min_digits:_value_

Das Feld muss eine ganze Zahl mit einer Länge von mindestens _value_ sein.

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

Das Feld muss ein Vielfaches von _value_ sein.

<a name="rule-missing"></a>
#### missing

Das Feld darf nicht in den Eingabedaten existieren.

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

Wenn _anotherfield_ einem der _value_-Werte entspricht, darf das Feld nicht existieren.

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

Außer wenn _anotherfield_ einem der _value_-Werte entspricht, darf das Feld nicht existieren.

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

Wenn eines der angegebenen Felder existiert, darf das Feld nicht existieren.

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

Wenn alle angegebenen Felder existieren, darf das Feld nicht existieren.

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

Das Feld darf nicht in der angegebenen Werteliste stehen. Sie können `Rule::notIn` zur Konstruktion verwenden:

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

Das Feld darf nicht dem angegebenen regulären Ausdruck entsprechen.

Diese Regel verwendet PHP `preg_match`. Der Regex muss Trennzeichen haben, z. B. `'email' => 'not_regex:/^.+$/i'`.

> [!WARNING]
> Bei Verwendung von `regex`/`not_regex`: Wenn der Regex `|` enthält, verwenden Sie die Array-Form, um Konflikte mit dem `|`-Separator zu vermeiden.

<a name="rule-nullable"></a>
#### nullable

Das Feld darf `null` sein.

<a name="rule-numeric"></a>
#### numeric

Das Feld muss [numerisch](https://www.php.net/manual/en/function.is-numeric.php) sein.

Verwenden Sie den Parameter `strict`, um nur Integer- oder Float-Typen zu erlauben; numerische Strings sind dann ungültig:

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

Das Feld muss in den Eingabedaten existieren.

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

Wenn _anotherfield_ einem der _value_-Werte entspricht, muss das Feld existieren.

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

Außer wenn _anotherfield_ einem der _value_-Werte entspricht, muss das Feld existieren.

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

Wenn eines der angegebenen Felder existiert, muss das Feld existieren.

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

Wenn alle angegebenen Felder existieren, muss das Feld existieren.

<a name="rule-prohibited"></a>
#### prohibited

Das Feld muss fehlen oder leer sein. „Leer“ bedeutet:

<div class="content-list" markdown="1">

- Wert ist `null`.
- Wert ist leerer String.
- Wert ist leeres Array oder leeres `Countable`-Objekt.
- Hochgeladene Datei mit leerem Pfad.

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

Wenn _anotherfield_ einem der _value_-Werte entspricht, muss das Feld fehlen oder leer sein. „Leer“ bedeutet:

<div class="content-list" markdown="1">

- Wert ist `null`.
- Wert ist leerer String.
- Wert ist leeres Array oder leeres `Countable`-Objekt.
- Hochgeladene Datei mit leerem Pfad.

</div>

Für komplexe Bedingungen verwenden Sie `Rule::prohibitedIf`:

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

Wenn _anotherfield_ `"yes"`, `"on"`, `1`, `"1"`, `true` oder `"true"` ist, muss das Feld fehlen oder leer sein.

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

Wenn _anotherfield_ `"no"`, `"off"`, `0`, `"0"`, `false` oder `"false"` ist, muss das Feld fehlen oder leer sein.

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

Außer wenn _anotherfield_ einem der _value_-Werte entspricht, muss das Feld fehlen oder leer sein. „Leer“ bedeutet:

<div class="content-list" markdown="1">

- Wert ist `null`.
- Wert ist leerer String.
- Wert ist leeres Array oder leeres `Countable`-Objekt.
- Hochgeladene Datei mit leerem Pfad.

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

Wenn das Feld existiert und nicht leer ist, müssen alle Felder in _anotherfield_ fehlen oder leer sein. „Leer“ bedeutet:

<div class="content-list" markdown="1">

- Wert ist `null`.
- Wert ist leerer String.
- Wert ist leeres Array oder leeres `Countable`-Objekt.
- Hochgeladene Datei mit leerem Pfad.

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

Das Feld muss dem angegebenen regulären Ausdruck entsprechen.

Diese Regel verwendet PHP `preg_match`. Der Regex muss Trennzeichen haben, z. B. `'email' => 'regex:/^.+@.+$/i'`.

> [!WARNING]
> Bei Verwendung von `regex`/`not_regex`: Wenn der Regex `|` enthält, verwenden Sie die Array-Form, um Konflikte mit dem `|`-Separator zu vermeiden.

<a name="rule-required"></a>
#### required

Das Feld muss existieren und darf nicht leer sein. „Leer“ bedeutet:

<div class="content-list" markdown="1">

- Wert ist `null`.
- Wert ist leerer String.
- Wert ist leeres Array oder leeres `Countable`-Objekt.
- Hochgeladene Datei mit leerem Pfad.

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

Wenn _anotherfield_ einem der _value_-Werte entspricht, muss das Feld existieren und darf nicht leer sein.

Für komplexe Bedingungen verwenden Sie `Rule::requiredIf`:

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

Wenn _anotherfield_ `"yes"`, `"on"`, `1`, `"1"`, `true` oder `"true"` ist, muss das Feld existieren und darf nicht leer sein.

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

Wenn _anotherfield_ `"no"`, `"off"`, `0`, `"0"`, `false` oder `"false"` ist, muss das Feld existieren und darf nicht leer sein.

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

Außer wenn _anotherfield_ einem der _value_-Werte entspricht, muss das Feld existieren und darf nicht leer sein. Wenn _value_ `null` ist (z. B. `required_unless:name,null`), darf das Feld nur leer sein, wenn das Vergleichsfeld `null` oder nicht vorhanden ist.

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

Wenn eines der angegebenen Felder existiert und nicht leer ist, muss das Feld existieren und darf nicht leer sein.

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

Wenn alle angegebenen Felder existieren und nicht leer sind, muss das Feld existieren und darf nicht leer sein.

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

Wenn eines der angegebenen Felder leer oder nicht vorhanden ist, muss das Feld existieren und darf nicht leer sein.

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

Wenn alle angegebenen Felder leer oder nicht vorhanden sind, muss das Feld existieren und darf nicht leer sein.

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

Das Feld muss ein Array sein und mindestens die angegebenen Schlüssel enthalten.

<a name="validating-when-present"></a>
#### sometimes

Wenden Sie nachfolgende Validierungsregeln nur an, wenn das Feld existiert. Wird häufig für „optional, aber bei Vorhandensein gültig“-Felder verwendet:

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

Das Feld muss mit _field_ übereinstimmen.

<a name="rule-size"></a>
#### size:_value_

Die Feldgröße muss dem angegebenen _value_ entsprechen. Für Strings: Zeichenanzahl; für Zahlen: angegebene Ganzzahl (mit `numeric` oder `integer` verwenden); für Arrays: Elementanzahl; für Dateien: Größe in KB. Beispiel:

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

Das Feld muss mit einem der angegebenen Werte beginnen.

<a name="rule-string"></a>
#### string

Das Feld muss eine Zeichenkette sein. Um `null` zu erlauben, verwenden Sie es zusammen mit `nullable`.

<a name="rule-timezone"></a>
#### timezone

Das Feld muss eine gültige Zeitzonenkennung sein (aus `DateTimeZone::listIdentifiers`). Sie können Parameter übergeben, die von dieser Methode unterstützt werden:

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

Das Feld muss in der angegebenen Tabelle eindeutig sein.

**Benutzerdefinierten Tabellen-/Spaltennamen angeben:**

Sie können den Modellklassennamen direkt angeben:

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

Sie können den Spaltennamen angeben (Standard ist der Feldname, wenn nicht angegeben):

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**Datenbankverbindung angeben:**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**Bestimmte ID ignorieren:**

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
> `ignore` sollte keine Benutzereingabe erhalten; verwenden Sie nur systemgenerierte eindeutige IDs (Auto-Increment-ID oder Modell-UUID), sonst kann SQL-Injection-Risiko bestehen.

Sie können auch eine Modellinstanz übergeben:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

Wenn der Primärschlüssel nicht `id` ist, geben Sie den Primärschlüsselnamen an:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

Standardmäßig wird der Feldname als eindeutige Spalte verwendet; Sie können auch den Spaltennamen angeben:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**Zusätzliche Bedingungen hinzufügen:**

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

**Soft-Deleted-Datensätze ignorieren:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

Wenn die Soft-Delete-Spalte nicht `deleted_at` ist:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

Das Feld muss in Großbuchstaben sein.

<a name="rule-url"></a>
#### url

Das Feld muss eine gültige URL sein.

Sie können erlaubte Protokolle angeben:

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

Das Feld muss eine gültige [ULID](https://github.com/ulid/spec) sein.

<a name="rule-uuid"></a>
#### uuid

Das Feld muss eine gültige RFC-9562-UUID sein (Version 1, 3, 4, 5, 6, 7 oder 8).

Sie können die Version angeben:

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```



<a name="think-validate"></a>
# Validator top-think/think-validate

## Beschreibung

Offizieller ThinkPHP-Validator

## Projekt-URL

https://github.com/top-think/think-validate

## Installation

`composer require topthink/think-validate`

## Schnellstart

**Erstellen Sie `app/index/validate/User.php`**

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
  
**Verwendung**

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

> **Hinweis**
> webman unterstützt die Methode `Validate::rule()` von think-validate nicht

<a name="respect-validation"></a>
# Validator workerman/validation

## Beschreibung

Dieses Projekt ist eine lokalisierte Version von https://github.com/Respect/Validation

## Projekt-URL

https://github.com/walkor/validation
  
  
## Installation
 
```php
composer require workerman/validation
```

## Schnellstart

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
  
**Zugriff über jQuery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
Ergebnis:

`{"code":500,"msg":"Username may only contain letters (a-z) and numbers (0-9)"}`

Erklärung:

`v::input(array $input, array $rules)` validiert und sammelt Daten. Bei Validierungsfehlern wird `Respect\Validation\Exceptions\ValidationException` geworfen; bei Erfolg werden die validierten Daten (Array) zurückgegeben.

Wenn der Geschäftscode die Validierungsausnahme nicht abfängt, fängt das Webman-Framework sie ab und gibt JSON (z. B. `{"code":500, "msg":"xxx"}`) oder eine normale Ausnahmeseite basierend auf den HTTP-Headern zurück. Wenn das Antwortformat Ihren Anforderungen nicht entspricht, können Sie `ValidationException` abfangen und benutzerdefinierte Daten zurückgeben, wie im folgenden Beispiel:

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

## Validator-Anleitung

```php
use Respect\Validation\Validator as v;

// Einzelne Regelvalidierung
$number = 123;
v::numericVal()->validate($number); // true

// Verkettete Validierung
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Ersten Validierungsfehlergrund abrufen
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username may only contain letters (a-z) and numbers (0-9)
}

// Alle Validierungsfehlergründe abrufen
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Gibt aus
    // -  Username must satisfy the following rules
    //     - Username may only contain letters (a-z) and numbers (0-9)
    //     - Username must not contain whitespace
  
    var_export($exception->getMessages());
    // Gibt aus
    // array (
    //   'alnum' => 'Username may only contain letters (a-z) and numbers (0-9)',
    //   'noWhitespace' => 'Username must not contain whitespace',
    // )
}

// Benutzerdefinierte Fehlermeldungen
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Username may only contain letters and numbers',
        'noWhitespace' => 'Username must not contain spaces',
        'length' => 'length satisfies the rule, so this will not be shown'
    ]));
    // Gibt aus 
    // array(
    //    'alnum' => 'Username may only contain letters and numbers',
    //    'noWhitespace' => 'Username must not contain spaces'
    // )
}

// Objekt validieren
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Array validieren
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
    ->assert($data); // Kann auch check() oder validate() verwenden
  
// Optionale Validierung
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Negationsregel
v::not(v::intVal())->validate(10); // false
```
  
## Unterschied zwischen den Validator-Methoden `validate()` `check()` `assert()`

`validate()` gibt einen Boolean zurück, wirft keine Ausnahme

`check()` wirft bei Validierungsfehlern eine Ausnahme; ersten Fehlergrund über `$exception->getMessage()` abrufen

`assert()` wirft bei Validierungsfehlern eine Ausnahme; alle Fehlergründe über `$exception->getFullMessage()` abrufen
  
  
## Häufig verwendete Validierungsregeln

`Alnum()` Nur Buchstaben und Zahlen

`Alpha()` Nur Buchstaben

`ArrayType()` Array-Typ

`Between(mixed $minimum, mixed $maximum)` Validiert, dass die Eingabe zwischen zwei Werten liegt.

`BoolType()` Validiert Boolean-Typ

`Contains(mixed $expectedValue)` Validiert, dass die Eingabe einen bestimmten Wert enthält

`ContainsAny(array $needles)` Validiert, dass die Eingabe mindestens einen definierten Wert enthält

`Digit()` Validiert, dass die Eingabe nur Ziffern enthält

`Domain()` Validiert gültigen Domänennamen

`Email()` Validiert gültige E-Mail-Adresse

`Extension(string $extension)` Validiert Dateierweiterung

`FloatType()` Validiert Float-Typ

`IntType()` Validiert Integer-Typ

`Ip()` Validiert IP-Adresse

`Json()` Validiert JSON-Daten

`Length(int $min, int $max)` Validiert, dass die Länge im Bereich liegt

`LessThan(mixed $compareTo)` Validiert, dass die Länge kleiner als der angegebene Wert ist

`Lowercase()` Validiert Kleinbuchstaben

`MacAddress()` Validiert MAC-Adresse

`NotEmpty()` Validiert nicht leer

`NullType()` Validiert null

`Number()` Validiert Zahl

`ObjectType()` Validiert Objekttyp

`StringType()` Validiert String-Typ

`Url()` Validiert URL
  
Weitere Validierungsregeln unter https://respect-validation.readthedocs.io/en/2.0/list-of-rules/
  
## Mehr

Besuchen Sie https://respect-validation.readthedocs.io/en/2.0/
  
