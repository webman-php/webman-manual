# Otros validadores

Hay muchos validadores disponibles en composer que pueden usarse directamente, como:

#### <a href="#webman-validation"> webman/validation (Recomendado)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# Validador webman/validation

Basado en `illuminate/validation`, proporciona validación manual, validación por anotaciones, validación a nivel de parámetros y conjuntos de reglas reutilizables.

## Instalación

```bash
composer require webman/validation
```

## Conceptos básicos

- **Reutilización de conjuntos de reglas**: Define `rules`, `messages`, `attributes` y `scenes` reutilizables extendiendo `support\validation\Validator`, que pueden reutilizarse tanto en validación manual como por anotaciones.
- **Validación por anotaciones (atributos) a nivel de método**: Usa el atributo de PHP 8 `#[Validate]` para vincular la validación a los métodos del controlador.
- **Validación por anotaciones (atributos) a nivel de parámetro**: Usa el atributo de PHP 8 `#[Param]` para vincular la validación a los parámetros de los métodos del controlador.
- **Manejo de excepciones**: Lanza `support\validation\ValidationException` cuando falla la validación; la clase de excepción es configurable.
- **Validación en base de datos**: Si la validación implica base de datos, necesitas instalar `composer require webman/database`.

## Validación manual

### Uso básico

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **Nota**
> `validate()` lanza `support\validation\ValidationException` cuando falla la validación. Si prefieres no lanzar excepciones, usa el enfoque con `fails()` que se muestra a continuación para obtener los mensajes de error.

### Mensajes y atributos personalizados

```php
use support\validation\Validator;

$data = ['contact' => 'user@example.com'];

Validator::make(
    $data,
    ['contact' => 'required|email'],
    ['contact.email' => 'Formato de email inválido'],
    ['contact' => 'Email']
)->validate();
```

### Validar sin excepción (obtener mensajes de error)

Si prefieres no lanzar excepciones, usa `fails()` para comprobar y obtener los mensajes de error mediante `errors()` (devuelve `MessageBag`):

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
    // manejar errores...
}
```

## Reutilización de conjuntos de reglas (validador personalizado)

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
        'name.required' => 'El nombre es obligatorio',
        'email.required' => 'El email es obligatorio',
        'email.email' => 'Formato de email inválido',
    ];

    protected array $attributes = [
        'name' => 'Nombre',
        'email' => 'Email',
    ];
}
```

### Reutilización en validación manual

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### Uso de escenas (opcional)

`scenes` es una característica opcional; solo valida un subconjunto de campos cuando llamas a `withScene(...)`.

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

// Sin escena especificada -> valida todas las reglas
UserValidator::make($data)->validate();

// Escena especificada -> valida solo los campos de esa escena
UserValidator::make($data)->withScene('create')->validate();
```

## Validación por anotaciones (nivel de método)

### Reglas directas

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
            'email.required' => 'El email es obligatorio',
            'password.required' => 'La contraseña es obligatoria',
        ],
        attributes: [
            'email' => 'Email',
            'password' => 'Contraseña',
        ]
    )]
    public function login(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Reutilizar conjuntos de reglas

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

### Múltiples validaciones superpuestas

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

### Fuente de datos de validación

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

Usa el parámetro `in` para especificar la fuente de datos:

* **query** Parámetros de consulta de la petición HTTP, desde `$request->get()`
* **body** Cuerpo de la petición HTTP, desde `$request->post()`
* **path** Parámetros de ruta de la petición HTTP, desde `$request->route->param()`

`in` puede ser una cadena o un array; cuando es un array, los valores se fusionan en orden y los posteriores sobrescriben a los anteriores. Cuando no se pasa `in`, por defecto es `['query', 'body', 'path']`.


## Validación a nivel de parámetro (Param)

### Uso básico

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

### Fuente de datos de validación

De forma similar, la validación a nivel de parámetro también admite el parámetro `in` para especificar la fuente:

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


### rules admite string o array

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

### Mensajes / atributo personalizados

```php
use support\validation\annotation\Param;

class UserController
{
    public function updateEmail(
        #[Param(
            rules: 'required|email',
            messages: ['email.email' => 'Formato de email inválido'],
            attribute: 'Email'
        )]
        string $email
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Reutilización de constantes de reglas

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

## Combinación de validación a nivel de método y de parámetro

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

## Inferencia automática de reglas (basada en la firma del parámetro)

Cuando se usa `#[Validate]` en un método, o cualquier parámetro de ese método usa `#[Param]`, este componente **infiere y completa automáticamente las reglas de validación básicas a partir de la firma de los parámetros del método**, y luego las fusiona con las reglas existentes antes de validar.

### Ejemplo: expansión equivalente de `#[Validate]`

1) Solo habilitar `#[Validate]` sin escribir reglas manualmente:

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

2) Solo reglas parciales escritas, el resto completado por la firma del parámetro:

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

3) Valor por defecto / tipo nullable:

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

## Manejo de excepciones

### Excepción por defecto

El fallo de validación lanza `support\validation\ValidationException` por defecto, que extiende `Webman\Exception\BusinessException` y no registra errores.

El comportamiento de respuesta por defecto lo maneja `BusinessException::render()`:

- Peticiones normales: devuelve mensaje de texto, ej. `token is required.`
- Peticiones JSON: devuelve respuesta JSON, ej. `{"code": 422, "msg": "token is required.", "data":....}`

### Personalizar el manejo mediante excepción personalizada

- Configuración global: `exception` en `config/plugin/webman/validation/app.php`

## Soporte multilingüe

El componente incluye paquetes de idioma chino e inglés integrados y admite sobrescrituras del proyecto. Orden de carga:

1. Paquete de idioma del proyecto `resource/translations/{locale}/validation.php`
2. Integrado en el componente `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Inglés integrado de Illuminate (respaldo)

> **Nota**
> El idioma por defecto de Webman se configura en `config/translation.php`, o puede cambiarse mediante `locale('en');`.

### Ejemplo de sobrescritura local

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute is not a valid email format.',
];
```

## Carga automática del middleware

Tras la instalación, el componente carga automáticamente el middleware de validación mediante `config/plugin/webman/validation/middleware.php`; no es necesario registrarlo manualmente.

## Generación por línea de comandos

Usa el comando `make:validator` para generar clases de validador (por defecto en el directorio `app/validation`).

> **Nota**
> Requiere `composer require webman/console`

### Uso básico

- **Generar plantilla vacía**

```bash
php webman make:validator UserValidator
```

- **Sobrescribir archivo existente**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### Generar reglas desde la estructura de la tabla

- **Especificar nombre de tabla para generar reglas base** (infiere `$rules` desde tipo de campo/nullable/longitud etc.; excluye campos relacionados con ORM por defecto: laravel usa `created_at/updated_at/deleted_at`, thinkorm usa `create_time/update_time/delete_time`)

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **Especificar conexión de base de datos** (escenarios con múltiples conexiones)

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### Escenas

- **Generar escenas CRUD**: `create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> La escena `update` incluye el campo de clave primaria (para localizar registros) más otros campos; `delete/detail` incluyen solo la clave primaria por defecto.

### Selección de ORM (laravel (illuminate/database) vs think-orm)

- **Auto-selección (por defecto)**: Usa el que esté instalado/configurado; cuando ambos existen, usa illuminate por defecto
- **Forzar especificación**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### Ejemplo completo

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## Pruebas unitarias

Desde el directorio raíz de `webman/validation`, ejecuta:

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## Referencia de reglas de validación

<a name="available-validation-rules"></a>
## Reglas de validación disponibles

> [!IMPORTANT]
> - Webman Validation está basado en `illuminate/validation`; los nombres de las reglas coinciden con Laravel y no hay reglas específicas de Webman.
> - El middleware valida datos de `$request->all()` (GET+POST) fusionados con parámetros de ruta por defecto, excluyendo archivos subidos; para reglas de archivos, fusiona `$request->file()` en los datos manualmente, o llama a `Validator::make` manualmente.
> - `current_password` depende del guard de autenticación; `exists`/`unique` dependen de la conexión a base de datos y el query builder; estas reglas no están disponibles cuando los componentes correspondientes no están integrados.

La siguiente lista muestra todas las reglas de validación disponibles y sus propósitos:

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

El campo debe ser `"yes"`, `"on"`, `1`, `"1"`, `true`, o `"true"`. Comúnmente usado para escenarios como verificar el acuerdo del usuario con los términos de servicio.

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

Cuando otro campo es igual al valor especificado, el campo debe ser `"yes"`, `"on"`, `1`, `"1"`, `true`, o `"true"`. Comúnmente usado para escenarios de acuerdo condicional.

<a name="rule-active-url"></a>
#### active_url

El campo debe tener un registro A o AAAA válido. Esta regla usa primero `parse_url` para extraer el hostname de la URL, luego valida con `dns_get_record`.

<a name="rule-after"></a>
#### after:_date_

El campo debe ser un valor posterior a la fecha dada. La fecha se pasa a `strtotime` para convertirla en un `DateTime` válido:

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

También puedes pasar otro nombre de campo para comparación:

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

Puedes usar el constructor fluido de la regla `date`:

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

`afterToday` y `todayOrAfter` expresan convenientemente "debe ser después de hoy" o "debe ser hoy o posterior":

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

El campo debe ser igual o posterior a la fecha dada. Consulta [after](#rule-after) para más detalles.

Puedes usar el constructor fluido de la regla `date`:

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

`Rule::anyOf` permite especificar "cumplir cualquiera de los conjuntos de reglas". Por ejemplo, la siguiente regla significa que `username` debe ser una dirección de email o una cadena alfanumérica/guion bajo/guion de al menos 6 caracteres:

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

El campo debe ser letras Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) y [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)).

Para permitir solo ASCII (`a-z`, `A-Z`), añade la opción `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

El campo solo puede contener letras y números Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)), más guion (`-`) y guion bajo (`_`) ASCII.

Para permitir solo ASCII (`a-z`, `A-Z`, `0-9`), añade la opción `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

El campo solo puede contener letras y números Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)).

Para permitir solo ASCII (`a-z`, `A-Z`, `0-9`), añade la opción `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

El campo debe ser un `array` de PHP.

Cuando la regla `array` tiene parámetros extra, las claves del array de entrada deben estar en la lista de parámetros. En el ejemplo, la clave `admin` no está en la lista permitida, por lo que es inválida:

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

Se recomienda definir explícitamente las claves de array permitidas en proyectos reales.

<a name="rule-ascii"></a>
#### ascii

El campo solo puede contener caracteres ASCII de 7 bits.

<a name="rule-bail"></a>
#### bail

Detener la validación de reglas posteriores para el campo cuando la primera regla falla.

Esta regla solo afecta al campo actual. Para "detener en el primer fallo globalmente", usa el validador de Illuminate directamente y llama a `stopOnFirstFailure()`.

<a name="rule-before"></a>
#### before:_date_

El campo debe ser anterior a la fecha dada. La fecha se pasa a `strtotime` para convertirla en un `DateTime` válido. Como [after](#rule-after), puedes pasar otro nombre de campo para comparación.

Puedes usar el constructor fluido de la regla `date`:

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

`beforeToday` y `todayOrBefore` expresan convenientemente "debe ser antes de hoy" o "debe ser hoy o anterior":

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

El campo debe ser igual o anterior a la fecha dada. La fecha se pasa a `strtotime` para convertirla en un `DateTime` válido. Como [after](#rule-after), puedes pasar otro nombre de campo para comparación.

Puedes usar el constructor fluido de la regla `date`:

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

El tamaño del campo debe estar entre _min_ y _max_ (inclusive). La evaluación para cadenas, números, arrays y archivos es la misma que [size](#rule-size).

<a name="rule-boolean"></a>
#### boolean

El campo debe ser convertible a booleano. Las entradas aceptables incluyen `true`, `false`, `1`, `0`, `"1"`, `"0"`.

Usa el parámetro `strict` para permitir solo `true` o `false`:

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

El campo debe tener un campo coincidente `{field}_confirmation`. Por ejemplo, cuando el campo es `password`, se requiere `password_confirmation`.

También puedes especificar un nombre de campo de confirmación personalizado, ej. `confirmed:repeat_username` requiere que `repeat_username` coincida con el campo actual.

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

El campo debe ser un array y debe contener todos los valores de parámetro dados. Esta regla se usa comúnmente para validación de arrays; puedes usar `Rule::contains` para construirla:

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

El campo debe ser un array y no debe contener ninguno de los valores de parámetro dados. Puedes usar `Rule::doesntContain` para construirla:

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

El campo debe coincidir con la contraseña del usuario autenticado actual. Puedes especificar el guard de autenticación como primer parámetro:

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> Esta regla depende del componente de autenticación y la configuración del guard; no uses cuando la autenticación no está integrada.

<a name="rule-date"></a>
#### date

El campo debe ser una fecha válida (no relativa) reconocible por `strtotime`.

<a name="rule-date-equals"></a>
#### date_equals:_date_

El campo debe ser igual a la fecha dada. La fecha se pasa a `strtotime` para convertirla en un `DateTime` válido.

<a name="rule-date-format"></a>
#### date_format:_format_,...

El campo debe coincidir con uno de los formatos dados. Usa `date` o `date_format`. Esta regla admite todos los formatos de [DateTime](https://www.php.net/manual/en/class.datetime.php) de PHP.

Puedes usar el constructor fluido de la regla `date`:

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

El campo debe ser numérico con los decimales requeridos:

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

El campo debe ser `"no"`, `"off"`, `0`, `"0"`, `false`, o `"false"`.

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

Cuando otro campo es igual al valor especificado, el campo debe ser `"no"`, `"off"`, `0`, `"0"`, `false`, o `"false"`.

<a name="rule-different"></a>
#### different:_field_

El campo debe ser diferente de _field_.

<a name="rule-digits"></a>
#### digits:_value_

El campo debe ser un entero con longitud _value_.

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

El campo debe ser un entero con longitud entre _min_ y _max_.

<a name="rule-dimensions"></a>
#### dimensions

El campo debe ser una imagen y satisfacer las restricciones de dimensiones:

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

Restricciones disponibles: _min\_width_, _max\_width_, _min\_height_, _max\_height_, _width_, _height_, _ratio_.

_ratio_ es la relación de aspecto; puede expresarse como fracción o float:

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

Esta regla tiene muchos parámetros; se recomienda usar `Rule::dimensions` para construirla:

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

Al validar arrays, los valores del campo no deben estar duplicados:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

Usa comparación flexible por defecto. Añade `strict` para comparación estricta:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

Añade `ignore_case` para ignorar diferencias de mayúsculas/minúsculas:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

El campo no debe comenzar con ninguno de los valores especificados.

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

El campo no debe terminar con ninguno de los valores especificados.

<a name="rule-email"></a>
#### email

El campo debe ser una dirección de email válida. Esta regla depende de [egulias/email-validator](https://github.com/egulias/EmailValidator), usa `RFCValidation` por defecto, y puede usar otros métodos de validación:

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

Métodos de validación disponibles:

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` - Valida el email según la especificación RFC ([RFCs soportados](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs)).
- `strict`: `NoRFCWarningsValidation` - Falla en advertencias RFC (ej. punto final o puntos consecutivos).
- `dns`: `DNSCheckValidation` - Comprueba si el dominio tiene registros MX válidos.
- `spoof`: `SpoofCheckValidation` - Previene caracteres Unicode homógrafos o de suplantación.
- `filter`: `FilterEmailValidation` - Valida usando `filter_var` de PHP.
- `filter_unicode`: `FilterEmailValidation::unicode()` - Validación con `filter_var` permitiendo Unicode.

</div>

Puedes usar el constructor fluido de reglas:

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
> `dns` y `spoof` requieren la extensión PHP `intl`.

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

El campo debe coincidir con la codificación de caracteres especificada. Esta regla usa `mb_check_encoding` para detectar la codificación del archivo o cadena. Puede usarse con el constructor de reglas de archivo:

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

El campo debe terminar con uno de los valores especificados.

<a name="rule-enum"></a>
#### enum

`Enum` es una regla basada en clase para validar que el valor del campo es un valor enum válido. Pasa el nombre de la clase enum al construir. Para valores primitivos, usa Backed Enum:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

Usa `only`/`except` para restringir los valores enum:

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

Usa `when` para restricciones condicionales:

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

El campo será excluido de los datos devueltos por `validate`/`validated`.

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

Cuando _anotherfield_ es igual a _value_, el campo será excluido de los datos devueltos por `validate`/`validated`.

Para condiciones complejas, usa `Rule::excludeIf`:

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

A menos que _anotherfield_ sea igual a _value_, el campo será excluido de los datos devueltos por `validate`/`validated`. Si _value_ es `null` (ej. `exclude_unless:name,null`), el campo solo se mantiene cuando el campo de comparación es `null` o está ausente.

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

Cuando _anotherfield_ existe, el campo será excluido de los datos devueltos por `validate`/`validated`.

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

Cuando _anotherfield_ no existe, el campo será excluido de los datos devueltos por `validate`/`validated`.

<a name="rule-exists"></a>
#### exists:_table_,_column_

El campo debe existir en la tabla de base de datos especificada.

<a name="basic-usage-of-exists-rule"></a>
#### Uso básico de la regla Exists

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

Cuando `column` no se especifica, se usa el nombre del campo por defecto. Así que este ejemplo valida si la columna `state` existe en la tabla `states`.

<a name="specifying-a-custom-column-name"></a>
#### Especificar un nombre de columna personalizado

Añade el nombre de la columna después del nombre de la tabla:

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

Para especificar una conexión de base de datos, prefija el nombre de la tabla con el nombre de la conexión:

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

También puedes pasar un nombre de clase de modelo; el framework resolverá el nombre de la tabla:

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

Para condiciones de consulta personalizadas, usa el constructor `Rule`:

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

También puedes especificar el nombre de la columna directamente en `Rule::exists`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'state' => [Rule::exists('states', 'abbreviation')],
])->validate();
```

Para validar que un conjunto de valores exista, combina con la regla `array`:

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

Cuando tanto `array` como `exists` están presentes, una sola consulta valida todos los valores.

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

Valida que la extensión del archivo subido esté en la lista permitida:

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> No confíes solo en la extensión para validar el tipo de archivo; usa junto con [mimes](#rule-mimes) o [mimetypes](#rule-mimetypes).

<a name="rule-file"></a>
#### file

El campo debe ser un archivo subido correctamente.

<a name="rule-filled"></a>
#### filled

Cuando el campo existe, su valor no debe estar vacío.

<a name="rule-gt"></a>
#### gt:_field_

El campo debe ser mayor que el _field_ o _value_ dado. Ambos campos deben tener el mismo tipo. La evaluación para cadenas, números, arrays y archivos es la misma que [size](#rule-size).

<a name="rule-gte"></a>
#### gte:_field_

El campo debe ser mayor o igual que el _field_ o _value_ dado. Ambos campos deben tener el mismo tipo. La evaluación para cadenas, números, arrays y archivos es la misma que [size](#rule-size).

<a name="rule-hex-color"></a>
#### hex_color

El campo debe ser un [valor de color hexadecimal](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color) válido.

<a name="rule-image"></a>
#### image

El campo debe ser una imagen (jpg, jpeg, png, bmp, gif o webp).

> [!WARNING]
> SVG no está permitido por defecto debido al riesgo de XSS. Para permitirlo, añade `allow_svg`: `image:allow_svg`.

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

El campo debe estar en la lista de valores dada. Puedes usar `Rule::in` para construirla:

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

Cuando se combina con la regla `array`, cada valor del array de entrada debe estar en la lista `in`:

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

El campo debe existir en la lista de valores de _anotherfield_.

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

El campo debe ser un array y debe contener al menos uno de los valores dados como clave:

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

El campo debe ser un entero.

Usa el parámetro `strict` para requerir que el tipo del campo sea entero; los enteros en forma de cadena serán inválidos:

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> Esta regla solo valida si pasa el `FILTER_VALIDATE_INT` de PHP; para tipos numéricos estrictos, usa junto con [numeric](#rule-numeric).

<a name="rule-ip"></a>
#### ip

El campo debe ser una dirección IP válida.

<a name="rule-ipv4"></a>
#### ipv4

El campo debe ser una dirección IPv4 válida.

<a name="rule-ipv6"></a>
#### ipv6

El campo debe ser una dirección IPv6 válida.

<a name="rule-json"></a>
#### json

El campo debe ser una cadena JSON válida.

<a name="rule-lt"></a>
#### lt:_field_

El campo debe ser menor que el _field_ dado. Ambos campos deben tener el mismo tipo. La evaluación para cadenas, números, arrays y archivos es la misma que [size](#rule-size).

<a name="rule-lte"></a>
#### lte:_field_

El campo debe ser menor o igual que el _field_ dado. Ambos campos deben tener el mismo tipo. La evaluación para cadenas, números, arrays y archivos es la misma que [size](#rule-size).

<a name="rule-lowercase"></a>
#### lowercase

El campo debe estar en minúsculas.

<a name="rule-list"></a>
#### list

El campo debe ser un array de lista. Las claves del array de lista deben ser números consecutivos desde 0 hasta `count($array) - 1`.

<a name="rule-mac"></a>
#### mac_address

El campo debe ser una dirección MAC válida.

<a name="rule-max"></a>
#### max:_value_

El campo debe ser menor o igual a _value_. La evaluación para cadenas, números, arrays y archivos es la misma que [size](#rule-size).

<a name="rule-max-digits"></a>
#### max_digits:_value_

El campo debe ser un entero con longitud que no exceda _value_.

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

Valida que el tipo MIME del archivo esté en la lista:

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

El tipo MIME se determina leyendo el contenido del archivo y puede diferir del MIME proporcionado por el cliente.

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

Valida que el tipo MIME del archivo corresponda a la extensión dada:

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

Aunque los parámetros son extensiones, esta regla lee el contenido del archivo para determinar el MIME. Mapeo extensión-a-MIME:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### Tipos MIME y extensiones

Esta regla no valida que la "extensión del archivo" coincida con el "MIME real". Por ejemplo, `mimes:png` trata `photo.txt` con contenido PNG como válido. Para validar la extensión, usa [extensions](#rule-extensions).

<a name="rule-min"></a>
#### min:_value_

El campo debe ser mayor o igual a _value_. La evaluación para cadenas, números, arrays y archivos es la misma que [size](#rule-size).

<a name="rule-min-digits"></a>
#### min_digits:_value_

El campo debe ser un entero con longitud no menor a _value_.

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

El campo debe ser un múltiplo de _value_.

<a name="rule-missing"></a>
#### missing

El campo no debe existir en los datos de entrada.

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

Cuando _anotherfield_ es igual a cualquier _value_, el campo no debe existir.

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

A menos que _anotherfield_ sea igual a cualquier _value_, el campo no debe existir.

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

Cuando existe cualquier campo especificado, el campo no debe existir.

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

Cuando existen todos los campos especificados, el campo no debe existir.

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

El campo no debe estar en la lista de valores dada. Puedes usar `Rule::notIn` para construirla:

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

El campo no debe coincidir con la expresión regular dada.

Esta regla usa `preg_match` de PHP. La regex debe tener delimitadores, ej. `'email' => 'not_regex:/^.+$/i'`.

> [!WARNING]
> Al usar `regex`/`not_regex`, si la regex contiene `|`, usa la forma array para evitar conflicto con el separador `|`.

<a name="rule-nullable"></a>
#### nullable

El campo puede ser `null`.

<a name="rule-numeric"></a>
#### numeric

El campo debe ser [numérico](https://www.php.net/manual/en/function.is-numeric.php).

Usa el parámetro `strict` para permitir solo tipos integer o float; las cadenas numéricas serán inválidas:

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

El campo debe existir en los datos de entrada.

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

Cuando _anotherfield_ es igual a cualquier _value_, el campo debe existir.

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

A menos que _anotherfield_ sea igual a cualquier _value_, el campo debe existir.

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

Cuando existe cualquier campo especificado, el campo debe existir.

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

Cuando existen todos los campos especificados, el campo debe existir.

<a name="rule-prohibited"></a>
#### prohibited

El campo debe estar ausente o vacío. "Vacío" significa:

<div class="content-list" markdown="1">

- El valor es `null`.
- El valor es cadena vacía.
- El valor es array vacío u objeto Countable vacío.
- Archivo subido con ruta vacía.

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

Cuando _anotherfield_ es igual a cualquier _value_, el campo debe estar ausente o vacío. "Vacío" significa:

<div class="content-list" markdown="1">

- El valor es `null`.
- El valor es cadena vacía.
- El valor es array vacío u objeto Countable vacío.
- Archivo subido con ruta vacía.

</div>

Para condiciones complejas, usa `Rule::prohibitedIf`:

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

Cuando _anotherfield_ es `"yes"`, `"on"`, `1`, `"1"`, `true`, o `"true"`, el campo debe estar ausente o vacío.

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

Cuando _anotherfield_ es `"no"`, `"off"`, `0`, `"0"`, `false`, o `"false"`, el campo debe estar ausente o vacío.

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

A menos que _anotherfield_ sea igual a cualquier _value_, el campo debe estar ausente o vacío. "Vacío" significa:

<div class="content-list" markdown="1">

- El valor es `null`.
- El valor es cadena vacía.
- El valor es array vacío u objeto Countable vacío.
- Archivo subido con ruta vacía.

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

Cuando el campo existe y no está vacío, todos los campos en _anotherfield_ deben estar ausentes o vacíos. "Vacío" significa:

<div class="content-list" markdown="1">

- El valor es `null`.
- El valor es cadena vacía.
- El valor es array vacío u objeto Countable vacío.
- Archivo subido con ruta vacía.

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

El campo debe coincidir con la expresión regular dada.

Esta regla usa `preg_match` de PHP. La regex debe tener delimitadores, ej. `'email' => 'regex:/^.+@.+$/i'`.

> [!WARNING]
> Al usar `regex`/`not_regex`, si la regex contiene `|`, usa la forma array para evitar conflicto con el separador `|`.

<a name="rule-required"></a>
#### required

El campo debe existir y no estar vacío. "Vacío" significa:

<div class="content-list" markdown="1">

- El valor es `null`.
- El valor es cadena vacía.
- El valor es array vacío u objeto Countable vacío.
- Archivo subido con ruta vacía.

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

Cuando _anotherfield_ es igual a cualquier _value_, el campo debe existir y no estar vacío.

Para condiciones complejas, usa `Rule::requiredIf`:

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

Cuando _anotherfield_ es `"yes"`, `"on"`, `1`, `"1"`, `true`, o `"true"`, el campo debe existir y no estar vacío.

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

Cuando _anotherfield_ es `"no"`, `"off"`, `0`, `"0"`, `false`, o `"false"`, el campo debe existir y no estar vacío.

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

A menos que _anotherfield_ sea igual a cualquier _value_, el campo debe existir y no estar vacío. Si _value_ es `null` (ej. `required_unless:name,null`), el campo puede estar vacío solo cuando el campo de comparación es `null` o está ausente.

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

Cuando existe y no está vacío cualquier campo especificado, el campo debe existir y no estar vacío.

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

Cuando existen y no están vacíos todos los campos especificados, el campo debe existir y no estar vacío.

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

Cuando cualquier campo especificado está vacío o ausente, el campo debe existir y no estar vacío.

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

Cuando todos los campos especificados están vacíos o ausentes, el campo debe existir y no estar vacío.

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

El campo debe ser un array y debe contener al menos las claves especificadas.

<a name="validating-when-present"></a>
#### sometimes

Aplicar reglas de validación posteriores solo cuando el campo existe. Comúnmente usado para campos "opcionales pero deben ser válidos cuando están presentes":

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

El campo debe ser igual a _field_.

<a name="rule-size"></a>
#### size:_value_

El tamaño del campo debe ser igual al _value_ dado. Para cadenas: número de caracteres; para números: entero especificado (usar con `numeric` o `integer`); para arrays: número de elementos; para archivos: tamaño en KB. Ejemplo:

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

El campo debe comenzar con uno de los valores especificados.

<a name="rule-string"></a>
#### string

El campo debe ser una cadena. Para permitir `null`, usa junto con `nullable`.

<a name="rule-timezone"></a>
#### timezone

El campo debe ser un identificador de zona horaria válido (de `DateTimeZone::listIdentifiers`). Puedes pasar parámetros soportados por ese método:

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

El campo debe ser único en la tabla especificada.

**Especificar nombre de tabla/columna personalizado:**

Puedes especificar directamente el nombre de la clase del modelo:

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

Puedes especificar el nombre de la columna (por defecto usa el nombre del campo cuando no se especifica):

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**Especificar conexión de base de datos:**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**Ignorar ID especificado:**

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
> `ignore` no debe recibir entrada del usuario; usa solo IDs únicos generados por el sistema (ID autoincremental o UUID del modelo), de lo contrario puede existir riesgo de inyección SQL.

También puedes pasar una instancia del modelo:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

Si la clave primaria no es `id`, especifica el nombre de la clave primaria:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

Por defecto usa el nombre del campo como columna única; también puedes especificar el nombre de la columna:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**Añadir condiciones extra:**

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

**Ignorar registros con borrado lógico:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

Si la columna de borrado lógico no es `deleted_at`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

El campo debe estar en mayúsculas.

<a name="rule-url"></a>
#### url

El campo debe ser una URL válida.

Puedes especificar los protocolos permitidos:

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

El campo debe ser un [ULID](https://github.com/ulid/spec) válido.

<a name="rule-uuid"></a>
#### uuid

El campo debe ser un UUID RFC 9562 válido (versión 1, 3, 4, 5, 6, 7 u 8).

Puedes especificar la versión:

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```



<a name="think-validate"></a>
# Validador top-think/think-validate

## Descripción

Validador oficial de ThinkPHP

## URL del proyecto

https://github.com/top-think/think-validate

## Instalación

`composer require topthink/think-validate`

## Inicio rápido

**Crear `app/index/validate/User.php`**

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
        'name.require' => 'El nombre es obligatorio',
        'name.max'     => 'El nombre no puede exceder 25 caracteres',
        'age.number'   => 'La edad debe ser un número',
        'age.between'  => 'La edad debe estar entre 1 y 120',
        'email'        => 'Formato de email inválido',    
    ];

}
```
  
**Uso**

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
> webman no soporta el método `Validate::rule()` de think-validate

<a name="respect-validation"></a>
# Validador workerman/validation

## Descripción

Este proyecto es una versión localizada de https://github.com/Respect/Validation

## URL del proyecto

https://github.com/walkor/validation
  
  
## Instalación
 
```php
composer require workerman/validation
```

## Inicio rápido

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
  
**Acceso vía jQuery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
Resultado:

`{"code":500,"msg":"Username may only contain letters (a-z) and numbers (0-9)"}`

Explicación:

`v::input(array $input, array $rules)` valida y recopila datos. Si la validación falla, lanza `Respect\Validation\Exceptions\ValidationException`; si tiene éxito devuelve los datos validados (array).

Si el código de negocio no captura la excepción de validación, el framework webman la capturará y devolverá JSON (como `{"code":500, "msg":"xxx"}`) o una página de excepción normal según los encabezados HTTP. Si el formato de respuesta no cumple tus necesidades, puedes capturar `ValidationException` y devolver datos personalizados, como en el ejemplo siguiente:

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

## Guía del validador

```php
use Respect\Validation\Validator as v;

// Validación de regla única
$number = 123;
v::numericVal()->validate($number); // true

// Validación encadenada
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Obtener primera razón de fallo de validación
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username may only contain letters (a-z) and numbers (0-9)
}

// Obtener todas las razones de fallo de validación
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Imprimirá
    // -  Username must satisfy the following rules
    //     - Username may only contain letters (a-z) and numbers (0-9)
    //     - Username must not contain whitespace
  
    var_export($exception->getMessages());
    // Imprimirá
    // array (
    //   'alnum' => 'Username may only contain letters (a-z) and numbers (0-9)',
    //   'noWhitespace' => 'Username must not contain whitespace',
    // )
}

// Mensajes de error personalizados
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Username may only contain letters and numbers',
        'noWhitespace' => 'Username must not contain spaces',
        'length' => 'length satisfies the rule, so this will not be shown'
    ]));
    // Imprimirá 
    // array(
    //    'alnum' => 'Username may only contain letters and numbers',
    //    'noWhitespace' => 'Username must not contain spaces'
    // )
}

// Validar objeto
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Validar array
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
    ->assert($data); // También puede usar check() o validate()
  
// Validación opcional
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Regla de negación
v::not(v::intVal())->validate(10); // false
```
  
## Diferencia entre los métodos del validador `validate()` `check()` `assert()`

`validate()` devuelve booleano, no lanza excepción

`check()` lanza excepción cuando falla la validación; obtén la primera razón de fallo mediante `$exception->getMessage()`

`assert()` lanza excepción cuando falla la validación; obtén todas las razones de fallo mediante `$exception->getFullMessage()`
  
  
## Reglas de validación comunes

`Alnum()` Solo letras y números

`Alpha()` Solo letras

`ArrayType()` Tipo array

`Between(mixed $minimum, mixed $maximum)` Valida que la entrada esté entre dos valores.

`BoolType()` Tipo booleano

`Contains(mixed $expectedValue)` Valida que la entrada contenga cierto valor

`ContainsAny(array $needles)` Valida que la entrada contenga al menos un valor definido

`Digit()` Valida que la entrada contenga solo dígitos

`Domain()` Valida nombre de dominio válido

`Email()` Valida dirección de email válida

`Extension(string $extension)` Valida extensión de archivo

`FloatType()` Tipo float

`IntType()` Tipo entero

`Ip()` Valida dirección IP

`Json()` Valida datos JSON

`Length(int $min, int $max)` Valida que la longitud esté dentro del rango

`LessThan(mixed $compareTo)` Valida que la longitud sea menor que el valor dado

`Lowercase()` Minúsculas

`MacAddress()` Dirección MAC

`NotEmpty()` No vacío

`NullType()` Null

`Number()` Número

`ObjectType()` Tipo objeto

`StringType()` Tipo cadena

`Url()` URL
  
Consulta https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ para más reglas de validación.
  
## Más

Visita https://respect-validation.readthedocs.io/en/2.0/
  
