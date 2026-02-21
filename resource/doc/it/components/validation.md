# Altri validatori

Sono disponibili molti validatori in Composer che possono essere utilizzati direttamente, ad esempio:

#### <a href="#webman-validation"> webman/validation (Consigliato)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# Validatore webman/validation

Basato su `illuminate/validation`, fornisce validazione manuale, validazione tramite annotazioni, validazione a livello di parametro e set di regole riutilizzabili.

## Installazione

```bash
composer require webman/validation
```

## Concetti di base

- **Riuso dei set di regole**: Definire `rules`, `messages`, `attributes` e `scenes` riutilizzabili estendendo `support\validation\Validator`, che possono essere riutilizzati sia nella validazione manuale che in quella tramite annotazioni.
- **Validazione tramite annotazione (attributo) a livello di metodo**: Utilizzare l'attributo PHP 8 `#[Validate]` per associare la validazione ai metodi del controller.
- **Validazione tramite annotazione (attributo) a livello di parametro**: Utilizzare l'attributo PHP 8 `#[Param]` per associare la validazione ai parametri dei metodi del controller.
- **Gestione delle eccezioni**: Lancia `support\validation\ValidationException` in caso di fallimento della validazione; la classe dell'eccezione è configurabile.
- **Validazione database**: Se è coinvolta la validazione del database, è necessario installare `composer require webman/database`.

## Validazione manuale

### Utilizzo di base

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **Nota**
> `validate()` lancia `support\validation\ValidationException` quando la validazione fallisce. Se preferisci non lanciare eccezioni, utilizza l'approccio `fails()` descritto di seguito per ottenere i messaggi di errore.

### Messaggi e attributi personalizzati

```php
use support\validation\Validator;

$data = ['contact' => 'user@example.com'];

Validator::make(
    $data,
    ['contact' => 'required|email'],
    ['contact.email' => 'Formato email non valido'],
    ['contact' => 'Email']
)->validate();
```

### Validare senza eccezione (ottenere messaggi di errore)

Se preferisci non lanciare eccezioni, utilizza `fails()` per verificare e ottenere i messaggi di errore tramite `errors()` (restituisce `MessageBag`):

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
    // gestire gli errori...
}
```

## Riuso dei set di regole (validatore personalizzato)

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
        'name.required' => 'Il nome è obbligatorio',
        'email.required' => 'L\'email è obbligatoria',
        'email.email' => 'Formato email non valido',
    ];

    protected array $attributes = [
        'name' => 'Nome',
        'email' => 'Email',
    ];
}
```

### Riuso nella validazione manuale

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### Utilizzo delle scene (opzionale)

`scenes` è una funzionalità opzionale; valida solo un sottoinsieme di campi quando si chiama `withScene(...)`.

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

// Nessuna scena specificata -> valida tutte le regole
UserValidator::make($data)->validate();

// Scena specificata -> valida solo i campi di quella scena
UserValidator::make($data)->withScene('create')->validate();
```

## Validazione tramite annotazioni (livello metodo)

### Regole dirette

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
            'email.required' => 'L\'email è obbligatoria',
            'password.required' => 'La password è obbligatoria',
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

### Riuso dei set di regole

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

### Sovrapposizioni multiple di validazione

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

### Sorgente dati per la validazione

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

Utilizzare il parametro `in` per specificare la sorgente dei dati:

* **query** parametri query della richiesta HTTP, da `$request->get()`
* **body** corpo della richiesta HTTP, da `$request->post()`
* **path** parametri path della richiesta HTTP, da `$request->route->param()`

`in` può essere una stringa o un array; quando è un array, i valori vengono uniti in ordine con i valori successivi che sovrascrivono quelli precedenti. Quando `in` non viene passato, il valore predefinito è `['query', 'body', 'path']`.


## Validazione a livello di parametro (Param)

### Utilizzo di base

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

### Sorgente dati per la validazione

Analogamente, la validazione a livello di parametro supporta anche il parametro `in` per specificare la sorgente:

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


### rules supporta stringa o array

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

### Messaggi / attributo personalizzati

```php
use support\validation\annotation\Param;

class UserController
{
    public function updateEmail(
        #[Param(
            rules: 'required|email',
            messages: ['email.email' => 'Formato email non valido'],
            attribute: 'Email'
        )]
        string $email
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Riuso di costanti per le regole

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

## Combinazione livello metodo + livello parametro

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

## Inferenza automatica delle regole (basata sulla firma del parametro)

Quando `#[Validate]` viene utilizzato su un metodo, o qualsiasi parametro di quel metodo utilizza `#[Param]`, questo componente **inferisce e completa automaticamente le regole di validazione di base dalla firma del parametro del metodo**, quindi le unisce con le regole esistenti prima della validazione.

### Esempio: espansione equivalente di `#[Validate]`

1) Abilitare solo `#[Validate]` senza scrivere manualmente le regole:

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

Equivalente a:

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

2) Solo regole parziali scritte, il resto completato dalla firma del parametro:

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

Equivalente a:

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

3) Valore predefinito / tipo nullable:

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

Equivalente a:

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

## Gestione delle eccezioni

### Eccezione predefinita

Il fallimento della validazione lancia `support\validation\ValidationException` per impostazione predefinita, che estende `Webman\Exception\BusinessException` e non registra gli errori.

Il comportamento di risposta predefinito è gestito da `BusinessException::render()`:

- Richieste regolari: restituisce messaggio stringa, es. `token is required.`
- Richieste JSON: restituisce risposta JSON, es. `{"code": 422, "msg": "token is required.", "data":....}`

### Personalizzare la gestione tramite eccezione personalizzata

- Configurazione globale: `exception` in `config/plugin/webman/validation/app.php`

## Supporto multilingue

Il componente include pacchetti linguistici cinese e inglese integrati e supporta le sovrascritture del progetto. Ordine di caricamento:

1. Pacchetto linguistico del progetto `resource/translations/{locale}/validation.php`
2. Pacchetto linguistico integrato del componente `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Inglese integrato di Illuminate (fallback)

> **Nota**
> La lingua predefinita di Webman è configurata in `config/translation.php`, oppure può essere modificata tramite `locale('en');`.

### Esempio di sovrascrittura locale

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute is not a valid email format.',
];
```

## Caricamento automatico del middleware

Dopo l'installazione, il componente carica automaticamente il middleware di validazione tramite `config/plugin/webman/validation/middleware.php`; non è necessaria alcuna registrazione manuale.

## Generazione da riga di comando

Utilizzare il comando `make:validator` per generare classi validatore (output predefinito nella directory `app/validation`).

> **Nota**
> Richiede `composer require webman/console`

### Utilizzo di base

- **Generare template vuoto**

```bash
php webman make:validator UserValidator
```

- **Sovrascrivere file esistente**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### Generare regole dalla struttura della tabella

- **Specificare il nome della tabella per generare regole di base** (inferisce `$rules` dal tipo di campo/nullable/lunghezza ecc.; esclude i campi relativi all'ORM per impostazione predefinita: laravel usa `created_at/updated_at/deleted_at`, thinkorm usa `create_time/update_time/delete_time`)

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **Specificare la connessione al database** (scenari con più connessioni)

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### Scene

- **Generare scene CRUD**: `create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> La scena `update` include il campo chiave primaria (per individuare i record) più gli altri campi; `delete/detail` includono solo la chiave primaria per impostazione predefinita.

### Selezione ORM (laravel (illuminate/database) vs think-orm)

- **Selezione automatica (predefinita)**: Utilizza quello installato/configurato; quando entrambi esistono, utilizza illuminate per impostazione predefinita
- **Forzare specifica**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### Esempio completo

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## Test unitari

Dalla directory root di `webman/validation`, eseguire:

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## Riferimento alle regole di validazione

<a name="available-validation-rules"></a>
## Regole di validazione disponibili

> [!IMPORTANT]
> - Webman Validation è basato su `illuminate/validation`; i nomi delle regole corrispondono a Laravel e non ci sono regole specifiche di Webman.
> - Il middleware valida i dati da `$request->all()` (GET+POST) uniti ai parametri di route per impostazione predefinita, escludendo i file caricati; per le regole sui file, unisci `$request->file()` ai dati manualmente, oppure chiama `Validator::make` manualmente.
> - `current_password` dipende dalla guard di autenticazione; `exists`/`unique` dipendono dalla connessione al database e dal query builder; queste regole non sono disponibili quando i componenti corrispondenti non sono integrati.

Di seguito elenco di tutte le regole di validazione disponibili e i loro scopi:

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

Il campo deve essere `"yes"`, `"on"`, `1`, `"1"`, `true` o `"true"`. Utilizzato comunemente per scenari come la verifica dell'accettazione dei termini di servizio da parte dell'utente.

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

Quando un altro campo è uguale al valore specificato, il campo deve essere `"yes"`, `"on"`, `1`, `"1"`, `true` o `"true"`. Utilizzato comunemente per scenari di accettazione condizionale.

<a name="rule-active-url"></a>
#### active_url

Il campo deve avere un record A o AAAA valido. Questa regola utilizza prima `parse_url` per estrarre l'hostname dell'URL, poi valida con `dns_get_record`.

<a name="rule-after"></a>
#### after:_date_

Il campo deve essere un valore successivo alla data indicata. La data viene passata a `strtotime` per convertirla in un `DateTime` valido:

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

È possibile passare anche il nome di un altro campo per il confronto:

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

È possibile utilizzare il builder fluente della regola `date`:

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

`afterToday` e `todayOrAfter` esprimono comodamente "deve essere dopo oggi" o "deve essere oggi o successivo":

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

Il campo deve essere nella data indicata o successiva. Vedi [after](#rule-after) per maggiori dettagli.

È possibile utilizzare il builder fluente della regola `date`:

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

`Rule::anyOf` consente di specificare "soddisfare uno qualsiasi dei set di regole". Ad esempio, la seguente regola significa che `username` deve essere un indirizzo email oppure una stringa alfanumerica/underscore/trattino di almeno 6 caratteri:

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

Il campo deve essere costituito da lettere Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) e [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)).

Per consentire solo ASCII (`a-z`, `A-Z`), aggiungere l'opzione `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

Il campo può contenere solo lettere e numeri Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)), più il trattino ASCII (`-`) e l'underscore (`_`).

Per consentire solo ASCII (`a-z`, `A-Z`, `0-9`), aggiungere l'opzione `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

Il campo può contenere solo lettere e numeri Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)).

Per consentire solo ASCII (`a-z`, `A-Z`, `0-9`), aggiungere l'opzione `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

Il campo deve essere un `array` PHP.

Quando la regola `array` ha parametri aggiuntivi, le chiavi dell'array di input devono essere nell'elenco dei parametri. Nell'esempio, la chiave `admin` non è nell'elenco consentito, quindi non è valida:

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

Si consiglia di definire esplicitamente le chiavi dell'array consentite nei progetti reali.

<a name="rule-ascii"></a>
#### ascii

Il campo può contenere solo caratteri ASCII a 7 bit.

<a name="rule-bail"></a>
#### bail

Interrompere la validazione delle regole successive per il campo quando la prima regola fallisce.

Questa regola influisce solo sul campo corrente. Per "interrompere al primo fallimento globalmente", utilizzare direttamente il validatore di Illuminate e chiamare `stopOnFirstFailure()`.

<a name="rule-before"></a>
#### before:_date_

Il campo deve essere precedente alla data indicata. La data viene passata a `strtotime` per convertirla in un `DateTime` valido. Come [after](#rule-after), è possibile passare il nome di un altro campo per il confronto.

È possibile utilizzare il builder fluente della regola `date`:

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

`beforeToday` e `todayOrBefore` esprimono comodamente "deve essere prima di oggi" o "deve essere oggi o precedente":

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

Il campo deve essere nella data indicata o precedente. La data viene passata a `strtotime` per convertirla in un `DateTime` valido. Come [after](#rule-after), è possibile passare il nome di un altro campo per il confronto.

È possibile utilizzare il builder fluente della regola `date`:

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

La dimensione del campo deve essere compresa tra _min_ e _max_ (inclusi). La valutazione per stringhe, numeri, array e file è la stessa di [size](#rule-size).

<a name="rule-boolean"></a>
#### boolean

Il campo deve essere convertibile in boolean. Gli input accettabili includono `true`, `false`, `1`, `0`, `"1"`, `"0"`.

Utilizzare il parametro `strict` per consentire solo `true` o `false`:

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

Il campo deve avere un campo corrispondente `{field}_confirmation`. Ad esempio, quando il campo è `password`, è richiesto `password_confirmation`.

È possibile specificare anche un nome personalizzato per il campo di conferma, ad es. `confirmed:repeat_username` richiede che `repeat_username` corrisponda al campo corrente.

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

Il campo deve essere un array e deve contenere tutti i valori dei parametri indicati. Questa regola è comunemente utilizzata per la validazione degli array; è possibile usare `Rule::contains` per costruirla:

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

Il campo deve essere un array e non deve contenere nessuno dei valori dei parametri indicati. È possibile usare `Rule::doesntContain` per costruirla:

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

Il campo deve corrispondere alla password dell'utente autenticato corrente. È possibile specificare la guard di autenticazione come primo parametro:

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> Questa regola dipende dal componente auth e dalla configurazione della guard; non utilizzare quando l'autenticazione non è integrata.

<a name="rule-date"></a>
#### date

Il campo deve essere una data valida (non relativa) riconoscibile da `strtotime`.

<a name="rule-date-equals"></a>
#### date_equals:_date_

Il campo deve essere uguale alla data indicata. La data viene passata a `strtotime` per convertirla in un `DateTime` valido.

<a name="rule-date-format"></a>
#### date_format:_format_,...

Il campo deve corrispondere a uno dei formati indicati. Utilizzare `date` oppure `date_format`. Questa regola supporta tutti i formati [DateTime](https://www.php.net/manual/en/class.datetime.php) di PHP.

È possibile utilizzare il builder fluente della regola `date`:

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

Il campo deve essere numerico con il numero di decimali richiesto:

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

Il campo deve essere `"no"`, `"off"`, `0`, `"0"`, `false` o `"false"`.

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

Quando un altro campo è uguale al valore specificato, il campo deve essere `"no"`, `"off"`, `0`, `"0"`, `false` o `"false"`.

<a name="rule-different"></a>
#### different:_field_

Il campo deve essere diverso da _field_.

<a name="rule-digits"></a>
#### digits:_value_

Il campo deve essere un intero con lunghezza _value_.

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

Il campo deve essere un intero con lunghezza compresa tra _min_ e _max_.

<a name="rule-dimensions"></a>
#### dimensions

Il campo deve essere un'immagine e soddisfare i vincoli di dimensione:

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

Vincoli disponibili: _min\_width_, _max\_width_, _min\_height_, _max\_height_, _width_, _height_, _ratio_.

_ratio_ è il rapporto d'aspetto; può essere espresso come frazione o float:

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

Questa regola ha molti parametri; si consiglia di usare `Rule::dimensions` per costruirla:

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

Durante la validazione degli array, i valori del campo non devono essere duplicati:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

Utilizza il confronto loose per impostazione predefinita. Aggiungere `strict` per il confronto stretto:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

Aggiungere `ignore_case` per ignorare le differenze di maiuscole/minuscole:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

Il campo non deve iniziare con nessuno dei valori specificati.

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

Il campo non deve terminare con nessuno dei valori specificati.

<a name="rule-email"></a>
#### email

Il campo deve essere un indirizzo email valido. Questa regola dipende da [egulias/email-validator](https://github.com/egulias/EmailValidator), utilizza `RFCValidation` per impostazione predefinita e può usare altri metodi di validazione:

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

Metodi di validazione disponibili:

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` - Valida l'email secondo le specifiche RFC ([RFC supportati](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs)).
- `strict`: `NoRFCWarningsValidation` - Fallisce sugli avvisi RFC (es. punto finale o punti consecutivi).
- `dns`: `DNSCheckValidation` - Verifica se il dominio ha record MX validi.
- `spoof`: `SpoofCheckValidation` - Previene caratteri Unicode omografi o spoofing.
- `filter`: `FilterEmailValidation` - Valida utilizzando `filter_var` di PHP.
- `filter_unicode`: `FilterEmailValidation::unicode()` - Validazione `filter_var` che consente Unicode.

</div>

È possibile utilizzare il builder fluente delle regole:

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
> `dns` e `spoof` richiedono l'estensione PHP `intl`.

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

Il campo deve corrispondere alla codifica dei caratteri specificata. Questa regola utilizza `mb_check_encoding` per rilevare la codifica di file o stringa. Può essere usata con il builder della regola file:

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

Il campo deve terminare con uno dei valori specificati.

<a name="rule-enum"></a>
#### enum

`Enum` è una regola basata su classe per validare che il valore del campo sia un valore enum valido. Passare il nome della classe enum durante la costruzione. Per valori primitivi, utilizzare Backed Enum:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

Utilizzare `only`/`except` per limitare i valori enum:

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

Utilizzare `when` per restrizioni condizionali:

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

Il campo sarà escluso dai dati restituiti da `validate`/`validated`.

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

Quando _anotherfield_ è uguale a _value_, il campo sarà escluso dai dati restituiti da `validate`/`validated`.

Per condizioni complesse, utilizzare `Rule::excludeIf`:

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

A meno che _anotherfield_ non sia uguale a _value_, il campo sarà escluso dai dati restituiti da `validate`/`validated`. Se _value_ è `null` (es. `exclude_unless:name,null`), il campo viene mantenuto solo quando il campo di confronto è `null` o assente.

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

Quando _anotherfield_ esiste, il campo sarà escluso dai dati restituiti da `validate`/`validated`.

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

Quando _anotherfield_ non esiste, il campo sarà escluso dai dati restituiti da `validate`/`validated`.

<a name="rule-exists"></a>
#### exists:_table_,_column_

Il campo deve esistere nella tabella del database specificata.

<a name="basic-usage-of-exists-rule"></a>
#### Utilizzo base della regola Exists

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

Quando `column` non è specificato, viene utilizzato per impostazione predefinita il nome del campo. Quindi questo esempio valida se la colonna `state` esiste nella tabella `states`.

<a name="specifying-a-custom-column-name"></a>
#### Specificare un nome di colonna personalizzato

Aggiungere il nome della colonna dopo il nome della tabella:

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

Per specificare una connessione al database, anteporre il nome della connessione al nome della tabella:

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

È possibile passare anche un nome di classe modello; il framework risolverà il nome della tabella:

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

Per condizioni di query personalizzate, utilizzare il builder `Rule`:

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

È possibile specificare direttamente il nome della colonna in `Rule::exists`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'state' => [Rule::exists('states', 'abbreviation')],
])->validate();
```

Per validare che un insieme di valori esista, combinare con la regola `array`:

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

Quando sono presenti sia `array` che `exists`, una singola query valida tutti i valori.

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

Valida che l'estensione del file caricato sia nell'elenco consentito:

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> Non fare affidamento solo sull'estensione per la validazione del tipo di file; utilizzare insieme a [mimes](#rule-mimes) o [mimetypes](#rule-mimetypes).

<a name="rule-file"></a>
#### file

Il campo deve essere un file caricato con successo.

<a name="rule-filled"></a>
#### filled

Quando il campo esiste, il suo valore non deve essere vuoto.

<a name="rule-gt"></a>
#### gt:_field_

Il campo deve essere maggiore del _field_ o del _value_ indicato. Entrambi i campi devono avere lo stesso tipo. La valutazione per stringhe, numeri, array e file è la stessa di [size](#rule-size).

<a name="rule-gte"></a>
#### gte:_field_

Il campo deve essere maggiore o uguale al _field_ o al _value_ indicato. Entrambi i campi devono avere lo stesso tipo. La valutazione per stringhe, numeri, array e file è la stessa di [size](#rule-size).

<a name="rule-hex-color"></a>
#### hex_color

Il campo deve essere un [valore colore esadecimale](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color) valido.

<a name="rule-image"></a>
#### image

Il campo deve essere un'immagine (jpg, jpeg, png, bmp, gif o webp).

> [!WARNING]
> SVG non è consentito per impostazione predefinita a causa del rischio XSS. Per consentirlo, aggiungere `allow_svg`: `image:allow_svg`.

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

Il campo deve essere nell'elenco dei valori indicati. È possibile usare `Rule::in` per costruirlo:

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

Quando combinato con la regola `array`, ogni valore nell'array di input deve essere nell'elenco `in`:

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

Il campo deve esistere nell'elenco dei valori di _anotherfield_.

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

Il campo deve essere un array e deve contenere almeno uno dei valori indicati come chiave:

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

Il campo deve essere un intero.

Utilizzare il parametro `strict` per richiedere che il tipo del campo sia integer; gli interi stringa non saranno validi:

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> Questa regola valida solo se supera `FILTER_VALIDATE_INT` di PHP; per tipi numerici stretti, utilizzare insieme a [numeric](#rule-numeric).

<a name="rule-ip"></a>
#### ip

Il campo deve essere un indirizzo IP valido.

<a name="rule-ipv4"></a>
#### ipv4

Il campo deve essere un indirizzo IPv4 valido.

<a name="rule-ipv6"></a>
#### ipv6

Il campo deve essere un indirizzo IPv6 valido.

<a name="rule-json"></a>
#### json

Il campo deve essere una stringa JSON valida.

<a name="rule-lt"></a>
#### lt:_field_

Il campo deve essere minore del _field_ indicato. Entrambi i campi devono avere lo stesso tipo. La valutazione per stringhe, numeri, array e file è la stessa di [size](#rule-size).

<a name="rule-lte"></a>
#### lte:_field_

Il campo deve essere minore o uguale al _field_ indicato. Entrambi i campi devono avere lo stesso tipo. La valutazione per stringhe, numeri, array e file è la stessa di [size](#rule-size).

<a name="rule-lowercase"></a>
#### lowercase

Il campo deve essere in minuscolo.

<a name="rule-list"></a>
#### list

Il campo deve essere un array lista. Le chiavi dell'array lista devono essere numeri consecutivi da 0 a `count($array) - 1`.

<a name="rule-mac"></a>
#### mac_address

Il campo deve essere un indirizzo MAC valido.

<a name="rule-max"></a>
#### max:_value_

Il campo deve essere minore o uguale a _value_. La valutazione per stringhe, numeri, array e file è la stessa di [size](#rule-size).

<a name="rule-max-digits"></a>
#### max_digits:_value_

Il campo deve essere un intero con lunghezza non superiore a _value_.

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

Valida che il tipo MIME del file sia nell'elenco:

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

Il tipo MIME viene determinato leggendo il contenuto del file e può differire dal MIME fornito dal client.

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

Valida che il tipo MIME del file corrisponda all'estensione indicata:

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

Sebbene i parametri siano estensioni, questa regola legge il contenuto del file per determinare il MIME. Mappatura estensione-MIME:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### Tipi MIME e estensioni

Questa regola non valida che "estensione del file" corrisponda a "MIME effettivo". Ad esempio, `mimes:png` considera valido `photo.txt` con contenuto PNG. Per validare l'estensione, utilizzare [extensions](#rule-extensions).

<a name="rule-min"></a>
#### min:_value_

Il campo deve essere maggiore o uguale a _value_. La valutazione per stringhe, numeri, array e file è la stessa di [size](#rule-size).

<a name="rule-min-digits"></a>
#### min_digits:_value_

Il campo deve essere un intero con lunghezza non inferiore a _value_.

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

Il campo deve essere un multiplo di _value_.

<a name="rule-missing"></a>
#### missing

Il campo non deve esistere nei dati di input.

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

Quando _anotherfield_ è uguale a qualsiasi _value_, il campo non deve esistere.

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

A meno che _anotherfield_ non sia uguale a qualsiasi _value_, il campo non deve esistere.

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

Quando uno qualsiasi dei campi specificati esiste, il campo non deve esistere.

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

Quando tutti i campi specificati esistono, il campo non deve esistere.

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

Il campo non deve essere nell'elenco dei valori indicati. È possibile usare `Rule::notIn` per costruirlo:

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

Il campo non deve corrispondere all'espressione regolare indicata.

Questa regola utilizza `preg_match` di PHP. L'espressione regolare deve avere delimitatori, es. `'email' => 'not_regex:/^.+$/i'`.

> [!WARNING]
> Quando si usano `regex`/`not_regex`, se l'espressione regolare contiene `|`, usare la forma array per evitare conflitti con il separatore `|`.

<a name="rule-nullable"></a>
#### nullable

Il campo può essere `null`.

<a name="rule-numeric"></a>
#### numeric

Il campo deve essere [numeric](https://www.php.net/manual/en/function.is-numeric.php).

Utilizzare il parametro `strict` per consentire solo tipi integer o float; le stringhe numeriche non saranno valide:

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

Il campo deve esistere nei dati di input.

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

Quando _anotherfield_ è uguale a qualsiasi _value_, il campo deve esistere.

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

A meno che _anotherfield_ non sia uguale a qualsiasi _value_, il campo deve esistere.

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

Quando uno qualsiasi dei campi specificati esiste, il campo deve esistere.

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

Quando tutti i campi specificati esistono, il campo deve esistere.

<a name="rule-prohibited"></a>
#### prohibited

Il campo deve essere assente o vuoto. "Vuoto" significa:

<div class="content-list" markdown="1">

- Il valore è `null`.
- Il valore è stringa vuota.
- Il valore è array vuoto o oggetto `Countable` vuoto.
- File caricato con percorso vuoto.

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

Quando _anotherfield_ è uguale a qualsiasi _value_, il campo deve essere assente o vuoto. "Vuoto" significa:

<div class="content-list" markdown="1">

- Il valore è `null`.
- Il valore è stringa vuota.
- Il valore è array vuoto o oggetto `Countable` vuoto.
- File caricato con percorso vuoto.

</div>

Per condizioni complesse, utilizzare `Rule::prohibitedIf`:

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

Quando _anotherfield_ è `"yes"`, `"on"`, `1`, `"1"`, `true` o `"true"`, il campo deve essere assente o vuoto.

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

Quando _anotherfield_ è `"no"`, `"off"`, `0`, `"0"`, `false` o `"false"`, il campo deve essere assente o vuoto.

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

A meno che _anotherfield_ non sia uguale a qualsiasi _value_, il campo deve essere assente o vuoto. "Vuoto" significa:

<div class="content-list" markdown="1">

- Il valore è `null`.
- Il valore è stringa vuota.
- Il valore è array vuoto o oggetto `Countable` vuoto.
- File caricato con percorso vuoto.

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

Quando il campo esiste e non è vuoto, tutti i campi in _anotherfield_ devono essere assenti o vuoti. "Vuoto" significa:

<div class="content-list" markdown="1">

- Il valore è `null`.
- Il valore è stringa vuota.
- Il valore è array vuoto o oggetto `Countable` vuoto.
- File caricato con percorso vuoto.

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

Il campo deve corrispondere all'espressione regolare indicata.

Questa regola utilizza `preg_match` di PHP. L'espressione regolare deve avere delimitatori, es. `'email' => 'regex:/^.+@.+$/i'`.

> [!WARNING]
> Quando si usano `regex`/`not_regex`, se l'espressione regolare contiene `|`, usare la forma array per evitare conflitti con il separatore `|`.

<a name="rule-required"></a>
#### required

Il campo deve esistere e non essere vuoto. "Vuoto" significa:

<div class="content-list" markdown="1">

- Il valore è `null`.
- Il valore è stringa vuota.
- Il valore è array vuoto o oggetto `Countable` vuoto.
- File caricato con percorso vuoto.

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

Quando _anotherfield_ è uguale a qualsiasi _value_, il campo deve esistere e non essere vuoto.

Per condizioni complesse, utilizzare `Rule::requiredIf`:

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

Quando _anotherfield_ è `"yes"`, `"on"`, `1`, `"1"`, `true` o `"true"`, il campo deve esistere e non essere vuoto.

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

Quando _anotherfield_ è `"no"`, `"off"`, `0`, `"0"`, `false` o `"false"`, il campo deve esistere e non essere vuoto.

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

A meno che _anotherfield_ non sia uguale a qualsiasi _value_, il campo deve esistere e non essere vuoto. Se _value_ è `null` (es. `required_unless:name,null`), il campo può essere vuoto solo quando il campo di confronto è `null` o assente.

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

Quando uno qualsiasi dei campi specificati esiste e non è vuoto, il campo deve esistere e non essere vuoto.

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

Quando tutti i campi specificati esistono e non sono vuoti, il campo deve esistere e non essere vuoto.

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

Quando uno qualsiasi dei campi specificati è vuoto o assente, il campo deve esistere e non essere vuoto.

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

Quando tutti i campi specificati sono vuoti o assenti, il campo deve esistere e non essere vuoto.

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

Il campo deve essere un array e deve contenere almeno le chiavi specificate.

<a name="validating-when-present"></a>
#### sometimes

Applicare le regole di validazione successive solo quando il campo esiste. Utilizzato comunemente per campi "opzionali ma devono essere validi se presenti":

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

Il campo deve essere uguale a _field_.

<a name="rule-size"></a>
#### size:_value_

La dimensione del campo deve essere uguale al _value_ indicato. Per le stringhe: conteggio caratteri; per i numeri: intero specificato (usare con `numeric` o `integer`); per gli array: conteggio elementi; per i file: dimensione in KB. Esempio:

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

Il campo deve iniziare con uno dei valori specificati.

<a name="rule-string"></a>
#### string

Il campo deve essere una stringa. Per consentire `null`, utilizzare con `nullable`.

<a name="rule-timezone"></a>
#### timezone

Il campo deve essere un identificatore di timezone valido (da `DateTimeZone::listIdentifiers`). È possibile passare i parametri supportati da quel metodo:

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

Il campo deve essere univoco nella tabella specificata.

**Specificare nome tabella/colonna personalizzato:**

È possibile specificare direttamente il nome della classe modello:

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

È possibile specificare il nome della colonna (per impostazione predefinita viene usato il nome del campo se non specificato):

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**Specificare connessione al database:**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**Ignorare ID specificato:**

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
> `ignore` non dovrebbe ricevere input dall'utente; utilizzare solo ID univoci generati dal sistema (ID auto-increment o UUID del modello), altrimenti potrebbe esistere rischio di SQL injection.

È possibile passare anche un'istanza del modello:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

Se la chiave primaria non è `id`, specificare il nome della chiave primaria:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

Per impostazione predefinita utilizza il nome del campo come colonna univoca; è possibile specificare anche il nome della colonna:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**Aggiungere condizioni extra:**

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

**Ignorare record soft-deleted:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

Se la colonna di soft delete non è `deleted_at`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

Il campo deve essere in maiuscolo.

<a name="rule-url"></a>
#### url

Il campo deve essere un URL valido.

È possibile specificare i protocolli consentiti:

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

Il campo deve essere un [ULID](https://github.com/ulid/spec) valido.

<a name="rule-uuid"></a>
#### uuid

Il campo deve essere un UUID RFC 9562 valido (versione 1, 3, 4, 5, 6, 7 o 8).

È possibile specificare la versione:

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```

<a name="think-validate"></a>
# Validatore top-think/think-validate

## Descrizione

Validatore ufficiale ThinkPHP

## URL del progetto
https://github.com/top-think/think-validate

## Installazione

`composer require topthink/think-validate`

## Avvio rapido

**Creare `app/index/validate/User.php`**

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
        'name.require' => 'Il nome è obbligatorio',
        'name.max'     => 'Il nome non può superare i 25 caratteri',
        'age.number'   => 'L\'età deve essere un numero',
        'age.between'  => 'L\'età deve essere compresa tra 1 e 120',
        'email'        => 'Formato email non valido',    
    ];

}
```

**Utilizzo**
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

> **Nota**
> webman non supporta il metodo `Validate::rule()` di think-validate

<a name="respect-validation"></a>
# Validatore workerman/validation

## Descrizione

Questo progetto è una versione localizzata di https://github.com/Respect/Validation

## URL del progetto

https://github.com/walkor/validation
  
  
## Installazione

```php
composer require workerman/validation
```

## Avvio rapido

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

**Accesso tramite jQuery**
 
 ```js
 $.ajax({
     url: 'http://127.0.0.1:8787',
     type: "post",
     dataType: 'json',
     data: {nickname: 'Tom', username: 'tom cat', password: '123456'}
 });
 ```

Risultato:

`{"code":500,"msg":"Nome utente può contenere solo lettere (a-z) e numeri (0-9)"}`

Spiegazione:

`v::input(array $input, array $rules)` valida e raccoglie i dati. Se la validazione fallisce, lancia `Respect\Validation\Exceptions\ValidationException`; in caso di successo restituisce i dati validati (array).

Se il codice di business non cattura l'eccezione di validazione, il framework webman la catturerà e restituirà JSON (come `{"code":500, "msg":"xxx"}`) o una pagina di eccezione normale in base agli header HTTP. Se il formato di risposta non soddisfa le tue esigenze, puoi catturare `ValidationException` e restituire dati personalizzati, come nell'esempio seguente:

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

## Guida al validatore

```php
use Respect\Validation\Validator as v;

// Validazione singola regola
$number = 123;
v::numericVal()->validate($number); // true

// Validazione concatenata
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Ottenere la prima causa di fallimento della validazione
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Nome utente può contenere solo lettere (a-z) e numeri (0-9)
}

// Ottenere tutte le cause di fallimento della validazione
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Stamperà
    // -  Username must satisfy the following rules
    //     - Username may only contain letters (a-z) and numbers (0-9)
    //     - Username must not contain whitespace
  
    var_export($exception->getMessages());
    // Stamperà
    // array (
    //   'alnum' => 'Username may only contain letters (a-z) and numbers (0-9)',
    //   'noWhitespace' => 'Username must not contain whitespace',
    // )
}

// Messaggi di errore personalizzati
try {
    $usernameValidator->setName('Nome utente')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Il nome utente può contenere solo lettere e numeri',
        'noWhitespace' => 'Il nome utente non può contenere spazi',
        'length' => 'La lunghezza è valida. Questo non sarà visualizzato'
    ]);
    // Stampa 
    // array(
    //    'alnum' => 'Il nome utente può contenere solo lettere e numeri',
    //    'noWhitespace' => 'Il nome utente non può contenere spazi'
    // )
}

// Validare oggetto
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Validare array
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
    ->assert($data); // Può anche usare check() o validate()
  
// Validazione opzionale
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Regola di negazione
v::not(v::intVal())->validate(10); // false
```

## Differenza tra i metodi del validatore `validate()` `check()` `assert()`

`validate()` restituisce boolean, non lancia eccezione

`check()` lancia eccezione in caso di fallimento della validazione; ottieni la prima causa di fallimento tramite `$exception->getMessage()`

`assert()` lancia eccezione in caso di fallimento della validazione; ottieni tutte le cause di fallimento tramite `$exception->getFullMessage()`
  
  
## Regole di validazione comuni

`Alnum()` Solo lettere e numeri

`Alpha()` Solo lettere

`ArrayType()` Tipo array

`Between(mixed $minimum, mixed $maximum)` Valida che l'input sia tra due valori.

`BoolType()` Valida tipo booleano

`Contains(mixed $expectedValue)` Valida che l'input contenga un certo valore

`ContainsAny(array $needles)` Valida che l'input contenga almeno un valore definito

`Digit()` Valida che l'input contenga solo cifre

`Domain()` Valida nome dominio valido

`Email()` Valida indirizzo email valido

`Extension(string $extension)` Valida estensione file

`FloatType()` Valida tipo float

`IntType()` Valida tipo intero

`Ip()` Valida indirizzo IP

`Json()` Valida dati JSON

`Length(int $min, int $max)` Valida che la lunghezza sia nell'intervallo

`LessThan(mixed $compareTo)` Valida che la lunghezza sia minore del valore specificato

`Lowercase()` Valida minuscolo

`MacAddress()` Valida indirizzo MAC

`NotEmpty()` Valida non vuoto

`NullType()` Valida null

`Number()` Valida numero

`ObjectType()` Valida tipo oggetto

`StringType()` Valida tipo stringa

`Url()` Valida URL
  
Vedi https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ per ulteriori regole di validazione.
  
## Altro

Visita https://respect-validation.readthedocs.io/en/2.0/
